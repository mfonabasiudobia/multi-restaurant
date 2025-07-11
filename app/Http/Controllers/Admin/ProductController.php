<?php

namespace App\Http\Controllers\Admin;

use App\Events\ProductApproveEvent;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\NotificationRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Services\TwilioService;
use App\Services\SmsGatewayService;
use App\Models\SubCategory;

use App\Models\Category;
use App\Models\Media;
use App\Repositories\FlashSaleRepository;
use App\Repositories\VatTaxRepository;
use App\Models\Season;
use App\Models\Quality;
use App\Models\Video;
use App\Http\Requests\ProductRequest;
use App\Repositories\ProductRepository;
use App\Events\AdminProductRequestEvent;
use App\Repositories\LogisticsRepository;
use App\Models\DeviceKey;
use App\Services\NotificationServices;
use Illuminate\Support\Facades\DB;
use App\Repositories\MediaRepository;


class ProductController extends Controller
{
    
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Log filter parameters for debugging
        \Log::info('Filter parameters:', [
            'status' => $request->status,
            'shop' => $request->shop,
            'category_id' => $request->category_id, 
            'search' => $request->search,
            'is_active' => $request->is_active
        ]);

        // If my_shop parameter is present, only show products from user's shop
        if ($request->my_shop) {
            $userShopId = auth()->user()->shop_id;
            $query->where('shop_id', $userShopId);
        }

        // Shop filter
        if ($request->filled('shop')) {
            $query->where('shop_id', $request->shop);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Active status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool)$request->is_active);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhereHas('categories', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }

        // Approval status filters
        if ($request->filled('status')) {
            if ($request->status === '0') {
                // New item requests
                $query->where('is_approve', false)
                      ->where('is_new', true);
                
                // Set page title
                $pageTitle = __('Item Requests');
            } elseif ($request->status === '1') {
                // Update requests
                $query->where('is_approve', false)
                      ->where('is_new', false);
                
                // Set page title
                $pageTitle = __('Update Requests');
            }
        } elseif ($request->approve === 'true') {
            // Approved items
            $query->where('is_approve', true);
            
            // Set page title
            $pageTitle = __('Accepted Items');
        } else {
            // Default view - all products
            $pageTitle = request('my_shop') ? __('My Shop Products') : __('All Products');
        }

        // Get paginated results with relationships
        $products = $query->with(['categories', 'media', 'videos', 'shop'])
                         ->latest()
                         ->paginate(20)
                         ->withQueryString();

        // Process videos for each product
        $products->through(function ($product) {
            $product->videos = $product->processedVideos();
            return $product;
        });

        // Get data for filters
        $categories = Category::all();
        $shops = ShopRepository::query()->isActive()->get();

        return view('admin.product.index', compact(
            'products',
            'shops', 
            'categories',
            'pageTitle'
        ));
    }

    public function show(Product $product)
    {
        $product->load('logistics');
        \Log::info('product', $product->toArray());

        return view('admin.product.show', compact('product'));
    }

    /**
     * Approve the product.
     */
    public function approve(Product $product)
    {
        // Check if product is already approved
        if ($product->is_approve) {
            return back()->withError(__('Product is already approved'));
        }

        // Update product status
        $product->update([
            'is_approve' => true,
            'is_new' => false,
            'is_active' => true,
        ]);

        // Prepare notification data
        $message = $product->is_new ? 'New Product Approved' : 'Product Update Approved';
        $content = $product->is_new ? 'Your new product has been approved' : 'Your product update has been approved';

        try {
            ProductApproveEvent::dispatch($message, $product->shop_id);
        } catch (\Throwable $th) {
            \Log::error('Failed to dispatch ProductApproveEvent: ' . $th->getMessage());
        }

        // Store notification
        $data = (object) [
            'title' => $message,
            'content' => $content,
            'url' => '/shop/product/'.$product->id.'/show',
            'icon' => 'bi-bag-check-fill',
            'type' => 'success',
            'shop_id' => $product->shop_id,
        ];
        
        NotificationRepository::storeByRequest($data);

        // Send WhatsApp notification if enabled
        if (config('services.twilio.enabled')) {
            try {
                $this->sendWhatsAppNotification($product);
            } catch (\Throwable $th) {
                \Log::error('Failed to send WhatsApp notification: ' . $th->getMessage());
            }
        }

        return back()->withSuccess(__('Product approved successfully'));
    }

    public function destroy(Product $product)
    {
        try {
            \Log::info('Deleting product', ['product_id' => $product->id]);
            
            $shopID = $product->shop_id;
            
            // Delete associated logistics entry if exists
            if ($product->bag_number) {
                $logistics = LogisticsRepository::findByBagNumber($product->bag_number);
                if ($logistics) {
                    LogisticsRepository::delete($logistics);
                }
            }
            
            // Delete media files
            foreach ($product->medias as $media) {
                if (Storage::exists($media->src)) {
                    Storage::delete($media->src);
                }
            }
            
            // Delete videos and their thumbnails
            foreach ($product->videos as $video) {
                if (Storage::exists($video->src)) {
                    Storage::delete($video->src);
                }
                if (Storage::exists($video->thumbnail)) {
                    Storage::delete($video->thumbnail);
                }
            }
            
            // Delete associated relationships
            $product->medias()->delete();
            $product->videos()->delete();
            $product->sizes()->delete();
            $product->reviews()->delete();
            $product->categories()->detach();
            
            // Finally delete the product
            $product->delete();
            
            return redirect()
                ->back()
                ->withSuccess(__('Product deleted successfully'));
                
        } catch (\Exception $e) {
            \Log::error('Error deleting product: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withError(__('Failed to delete product. Please try again.'));
        }
    }

    /**
     * generate barcode
     */
    public function generateBarcode(Product $product)
    {
        if (!$product->code) {
            return back()->withError(__('Sorry! Your Product code is not generated yet!'));
        }

        $quantitys = request('qty', 4);

        return view('admin.product.barcode', compact('product', 'quantitys'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $rootShop = generaleSetting('rootShop');
        \Log::info($rootShop);
        \Log::info($product);
        
        // Get all active shops for admin to select from
        $shops = ShopRepository::query()->isActive()->get();

        // Console log product sizes
        \Log::info('Product sizes:', ['sizes' => $product->sizes()->get()]);

        
       
        $categories = $rootShop?->categories()->active()->get();
        $units = $rootShop?->units()->isActive()->get();
        $sizes = $rootShop?->sizes()->isActive()->get();
        

        $categoryId = $product->categories()->latest('id')->first()->id;

        $subCategories = SubCategory::whereHas('categories', function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        })->isActive()->get();

        $taxs = VatTaxRepository::getActiveVatTaxes();

        $videos = $product->processedVideos();
        $seasons = Season::isActive()->get();
        $qualities = Quality::isActive()->get();

        return view('admin.product.edit', compact('product', 'categories', 'units', 'sizes', 'subCategories', 'taxs', 'videos', 'seasons', 'qualities', 'shops'));
    }

    /**
     * Update the product.
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            // Pass the request object directly, not the validated data
            ProductRepository::updateByRequest($request, $product);

            DB::commit();

            return redirect()
                ->route('admin.product.index')
                ->withSuccess(__('Product updated successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withError(__('An error occurred while updating the product. Please try again.'));
        }
    }

    private function getDuplicateErrorMessage($errorMessage)
    {
        if (strpos($errorMessage, 'logistics_bag_number_unique') !== false) {
            return __('This bag number is already in use. Please use a different bag number.');
        }
        if (strpos($errorMessage, 'products_code_unique') !== false) {
            return __('This product code is already in use. Please generate a new code.');
        }
        return __('A duplicate entry was found. Please check your input.');
    }

    private function handleMediaUpdates($product, $request)
    {
        \Log::info('Handling media updates for product: ' . $product->id);

        // Handle thumbnail
        if ($request->hasFile('additionThumbnail')) {
            // Delete old thumbnail if exists
            
            foreach ($request->file('additionThumbnail') as $image) {
                MediaRepository::storeByRequest($image, 'products', 'additionThumbnail');
            }
            
        }

        // Handle additional images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                MediaRepository::storeByRequest($image, 'products', 'image');
            }
        }

        // Handle videos
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $this->handleVideoUpload($product, $video);
            }
        }
    }

    public function removeVideo(Video $video)
    {
        if ($video->src && Storage::exists($video->src)) {
            Storage::delete($video->src);
        }
        if ($video->thumbnail && Storage::exists($video->thumbnail)) {
            Storage::delete($video->thumbnail);
        }
        $video->delete();
        
        return back()->withSuccess(__('Video removed successfully'));
    }

    public function create()
    {
        try {
            $rootShop = generaleSetting('rootShop');
            \Log::info('Root Shop:', ['rootShop' => $rootShop]);

            $shops = ShopRepository::query()->isActive()->get();
            \Log::info('Active Shops:', ['count' => $shops->count()]);

            $categories = $rootShop?->categories()->active()->get();
            \Log::info('Categories:', ['count' => $categories?->count() ?? 0]);

            $units = $rootShop?->units()->isActive()->get();
            \Log::info('Units:', ['count' => $units?->count() ?? 0]);

            $sizes = $rootShop?->sizes()->isActive()->get();
            \Log::info('Sizes:', ['count' => $sizes?->count() ?? 0]);

            $seasons = Season::isActive()->get();
            \Log::info('Seasons:', ['count' => $seasons->count()]);

            $qualities = Quality::isActive()->get();
            \Log::info('Qualities:', ['count' => $qualities->count()]);

            $taxs = VatTaxRepository::getActiveVatTaxes();
            \Log::info('Taxes:', ['count' => count($taxs)]);

            // Check if any required data is missing
            if (!$categories) {
                \Log::warning('No categories found for root shop');
            }
            if (!$units) {
                \Log::warning('No units found for root shop');
            }
            if ($seasons->isEmpty()) {
                \Log::warning('No active seasons found');
            }
            if ($qualities->isEmpty()) {
                \Log::warning('No active qualities found');
            }

            return view('admin.product.create', compact(
                'categories', 
                'units', 
                'sizes', 
                'taxs', 
                'seasons', 
                'qualities', 
                'shops'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in create product view:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withError(__('Error loading product creation form. Please try again.'));
        }
    }

    /**
     * store new product.
     */
    public function store(ProductRequest $request)
    {
        try {
            DB::beginTransaction();

            // Pass the request object directly, not the validated data
            $product = ProductRepository::storeByRequest($request);

            DB::commit();

            return redirect()
                ->route('admin.product.index')
                ->withSuccess(__('Product created successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product creation error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withError(__('An error occurred while creating the product. Please try again.'));
        }
    }

    private function generateUniqueCode()
    {
        do {
            $code = mt_rand(100000, 999999);
        } while (Product::where('code', $code)->exists());
        
        return $code;
    }

    public function toggleStatus(Product $product)
    {
        try {
            $product->update([
                'is_active' => !$product->is_active
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Product status updated successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Something went wrong!')
            ], 500);
        }
    }

    public function sendWhatsAppNotification(Product $product, TwilioService $twilioService)
    {
        try {
            // Get users who have enabled WhatsApp notifications
            $users = User::role('customer')
                ->whereHas('notificationPreferences', function($query) {
                    $query->where('whatsapp_enabled', true);
                })
                ->whereNotNull('phone')
                ->get();

            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No users with WhatsApp notifications enabled found.'
                ]);
            }

            // WhatsApp message without emojis
            $whatsappMessage = "*New Product Alert!*\n\n";
            $whatsappMessage .= "*{$product->name}*\n";
            $whatsappMessage .= "Price: " . format_price($product->price) . "\n";
            if ($product->discount) {
                $whatsappMessage .= "Discount: {$product->discount}% OFF!\n";
            }
            $whatsappMessage .= "\nCheck it out now: " . url("/products/{$product->id}");

            // Database-safe message without emojis
            $dbMessage = "New Product Alert!\n\n";
            $dbMessage .= "{$product->name}\n";
            $dbMessage .= "Price: " . format_price($product->price) . "\n";
            if ($product->discount) {
                $dbMessage .= "Discount: {$product->discount}% OFF!\n";
            }
            $dbMessage .= "\nCheck it out now: " . url("/products/{$product->id}");

            $successCount = 0;
            $failureCount = 0;

            foreach ($users as $user) {
                try {
                    // Format phone number for WhatsApp - ensure it has country code
                    $phone = ltrim($user->phone_code, '+') . ltrim($user->phone, '0');
                    
                    // Send WhatsApp message using Twilio
                    $twilioService->sendWhatsApp($phone, $whatsappMessage);
                    $successCount++;

                    // Create notification record with emoji-free content
                    $notify = (object) [
                        'title' => 'New Product Available',
                        'content' => $dbMessage,
                        'user_id' => $user->id,
                        'type' => 'product',
                        'url' => "/products/{$product->id}/details",
                        'icon' => 'bi-whatsapp'
                    ];
                    
                    NotificationRepository::storeByRequest($notify);

                } catch (\Exception $e) {
                    \Log::error("Failed to send WhatsApp notification to {$user->phone}: " . $e->getMessage());
                    $failureCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "WhatsApp notifications sent successfully to {$successCount} users. Failed for {$failureCount} users."
            ]);

        } catch (\Exception $e) {
            \Log::error('Error sending WhatsApp notifications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send WhatsApp notifications. ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display new item requests.
     */
    public function itemRequests(Request $request)
    {
        $query = Product::query()
            ->where('is_approve', false)
            ->where('is_new', true);

        // Apply filters
        $this->applyFilters($query, $request);

        $products = $query->with(['categories', 'media', 'videos', 'shop'])
                         ->latest()
                         ->paginate(20)
                         ->withQueryString();

        // Process videos for each product
        $products->through(function ($product) {
            $product->videos = $product->processedVideos();
            return $product;
        });

        // Get data for filters
        $categories = Category::all();
        $shops = ShopRepository::query()->isActive()->get();
        $pageTitle = __('Item Requests');

        return view('admin.product.index', compact(
            'products',
            'shops', 
            'categories',
            'pageTitle'
        ));
    }

    /**
     * Display update requests.
     */
    public function updateRequests(Request $request)
    {
        $query = Product::query()
            ->where('is_approve', false)
            ->where('is_new', false);

        // Apply filters
        $this->applyFilters($query, $request);

        $products = $query->with(['categories', 'media', 'videos', 'shop'])
                         ->latest()
                         ->paginate(20)
                         ->withQueryString();

        // Process videos for each product
        $products->through(function ($product) {
            $product->videos = $product->processedVideos();
            return $product;
        });

        // Get data for filters
        $categories = Category::all();
        $shops = ShopRepository::query()->isActive()->get();
        $pageTitle = __('Update Requests');

        return view('admin.product.index', compact(
            'products',
            'shops', 
            'categories',
            'pageTitle'
        ));
    }

    /**
     * Display approved items.
     */
    public function approvedItems(Request $request)
    {
        $query = Product::query()
            ->where('is_approve', true);

        // Apply filters
        $this->applyFilters($query, $request);

        $products = $query->with(['categories', 'media', 'videos', 'shop'])
                         ->latest()
                         ->paginate(20)
                         ->withQueryString();

        // Process videos for each product
        $products->through(function ($product) {
            $product->videos = $product->processedVideos();
            return $product;
        });

        // Get data for filters
        $categories = Category::all();
        $shops = ShopRepository::query()->isActive()->get();
        $pageTitle = __('Accepted Items');

        return view('admin.product.index', compact(
            'products',
            'shops', 
            'categories',
            'pageTitle'
        ));
    }

    /**
     * Apply common filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Shop filter
        if ($request->filled('shop')) {
            $query->where('shop_id', $request->shop);
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Active status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool)$request->is_active);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhereHas('categories', function($sq) use ($request) {
                      $sq->where('name', 'like', "%{$request->search}%");
                  });
            });
        }
    }

    private function handleMediaUpload($product, $file, $type)
    {
        // Define the storage path
        $path = $file->store('products', 's3');

        // Create a new media record
        $product->media()->create([
            'src' => $path,
            'type' => $type,
        ]);

        \Log::info("Media uploaded for product: {$product->id}, type: {$type}, path: {$path}");
    }
}
