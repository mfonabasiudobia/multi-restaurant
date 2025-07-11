<?php

namespace App\Http\Controllers\Shop;

use App\Events\AdminProductRequestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use App\Repositories\FlashSaleRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\VatTaxRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Video;
use App\Repositories\VideoRepository;
use App\Models\Season;
use App\Models\Quality;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display the product list.
     */
    public function index(Request $request)
    {
        // Log filter parameters for debugging
        \Log::info('Shop Filter parameters:', $request->all());

        $rootShop = generaleSetting('rootShop');
        $shop = generaleSetting('shop');

        $query = $shop?->products()->with(['categories', 'media', 'videos']);

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('categories.id', $request->category);
            });
        }

        // Season filter
        if ($request->filled('season')) {
            $query->where('season_id', $request->season);
        }

        // Quality filter
        if ($request->filled('quality')) {
            $query->where('quality_id', $request->quality);
        }

        // Status filter
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

        $products = $query->latest()
                         ->paginate(20)
                         ->withQueryString();

        // Process videos for each product
        $products->through(function ($product) {
            $product->videos = $product->processedVideos();
            return $product;
        });

        // Get data for filters
        $categories = $rootShop?->categories()->get();
        $seasons = Season::isActive()->get();
        $qualities = Quality::isActive()->get();
        $flashSale = FlashSaleRepository::getIncoming();

        return view('shop.product.index', compact(
            'products',
            'categories',
            'seasons',
            'qualities',
            'flashSale'
        ));
    }

    /**
     * Display the product details.
     */
    public function show(Product $product)
    {
        $shop = generaleSetting('shop');
        $rootShop = generaleSetting('rootShop');
        $season = $product->season;
        $quality = $product->quality;
        
        return view('shop.product.show', compact('product', 'shop', 'rootShop', 'season', 'quality'));
    }

    /**
     * Create new product.
     */
    public function create()
    {
        $shop = generaleSetting('shop');
        
        // Check if shop has an active package
        if (!$shop->package) {
            return redirect()->route('shop.package.payment')
                ->withError(__('Please subscribe to a package before creating products.'));
        }

        // Check if package is paid
        if (!$shop->package->is_paid) {
            return redirect()->route('shop.package.payment')
                ->withError(__('Please complete your package payment before creating products.'));
        }

        // Get all approved package payments that are still valid (not expired)
        $approvedPayments = \App\Models\PackagePayment::where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>=', now())  
            ->with('package')
            ->get();
        
        // Calculate total product limit from all valid package payments
        $totalLimit = $approvedPayments->sum(function($payment) {
            return $payment->package->product_limit ?? 0;
        });
        
        $productsCount = $shop->products()->count();
        
        if ($productsCount >= $totalLimit) {
            return redirect()->route('shop.package.payment')
                ->withError(__('You have reached your total product limit of ') . $totalLimit . 
                        __('. Please purchase an additional package.'));
        }

        $rootShop = generaleSetting('rootShop');

        // get categories and other data
        $categories = $rootShop?->categories()->active()->get();
        $units = $rootShop?->units()->isActive()->get();
        $sizes = $rootShop?->sizes()->isActive()->get();
        $seasons = Season::isActive()->get();
        $qualities = Quality::isActive()->get();
        $taxs = VatTaxRepository::getActiveVatTaxes();

        return view('shop.product.create', compact(
            'categories', 
            'units', 
            'sizes', 
            'taxs', 
            'seasons', 
            'qualities',
            'totalLimit',
            'productsCount',
            'approvedPayments'
        ));
    }

    /**
     * Store new product.
     */
    public function store(ProductRequest $request)
    {
        $shop = generaleSetting('shop');

        // Recheck package and limits
        if (!$shop->package || !$shop->package->is_paid) {
            return back()->withInput()
                ->withError(__('Please subscribe to a valid package before creating products.'));
        }

        // Get all approved package payments that are still valid (not expired)
        $approvedPayments = \App\Models\PackagePayment::where('shop_id', $shop->id)
            ->where('status', 'approved')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>=', now())
            ->with('package')
            ->get();
        
        // Calculate total product limit from all valid package payments
        $totalLimit = $approvedPayments->sum(function($payment) {
            return $payment->package->product_limit ?? 0;
        });
        
        $productsCount = $shop->products()->count();
        
        if ($productsCount >= $totalLimit) {
            return back()->withInput()
                ->withError(__('You have reached your total product limit of ') . $totalLimit . 
                        __('. Please purchase an additional package.'));
        }

        $skuCode = $shop?->products()->where('code', $request->code)->exists();

        if ($skuCode) {
            return back()->withInput()->withErrors(['code' => __('Product code already exists!')])
                ->with('error', __('Product code already exists!'));
        }

        try {
            DB::beginTransaction();

            // Pass the request object directly, not the validated data
            $product = ProductRepository::storeByRequest($request);

            DB::commit();

            /** @var User $user */
            $user = auth()->user();
            $isRootUser = $user?->hasRole('root');

            // admin notification message
            if (! $isRootUser && generaleSetting('setting')->shop_type != 'single') {
                $message = 'New Product Request';
                try {
                    AdminProductRequestEvent::dispatch($message);
                } catch (\Throwable $th) {
                    \Log::error('Error dispatching AdminProductRequestEvent: ' . $th->getMessage());
                }

                $data = (object) [
                    'title' => $message,
                    'content' => 'New Product Request from '.$shop->name,
                    'url' => '/admin/products?status=1',
                    'icon' => 'bi-shop',
                    'type' => 'success',
                ];
                // store notification
                NotificationRepository::storeByRequest($data);
            }

            return to_route('shop.product.index')->withSuccess(__('Product created successfully!'));

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database query error:', ['error' => $e->getMessage()]);
            DB::rollBack();

            // Handle specific database errors
            $errorCode = $e->errorInfo[1] ?? 0;

            switch ($errorCode) {
                case 1062:
                    $message = __('Duplicate entry. Please check your data.');
                    break;
                case 1452:
                    $message = __('Invalid reference. Please check all selected values.');
                    break;
                default:
                    $message = __('Database error. Please try again.');
            }

            return back()
                ->withInput()
                ->withError($message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product creation error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withError(__('An error occurred while creating the product. Please try again.'));
        }
    }

    /**
     * Display the product edit form.
     */
    public function edit(Product $product)
    {
        \Log::info('Edit product: ' . $product->id);
        $shop = generaleSetting('shop');
        $rootShop = generaleSetting('rootShop');

        // get units, sizes and categories
        $categories = $rootShop?->categories()->active()->get();
        $units = $rootShop?->units()->isActive()->get();
        $sizes = $rootShop?->sizes()->isActive()->get();

        $categoryId = $product->categories()->latest('id')->first()?->id;

        $subCategories = [];
        if ($categoryId) {
            $subCategories = SubCategory::whereHas('categories', function ($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })->isActive()->get();
        }

        $taxs = VatTaxRepository::getActiveVatTaxes();

        // Make sure videos are loaded and processed
        $videos = $product->videos ? $product->processedVideos() : collect([]);
        $seasons = Season::isActive()->get();
        $qualities = Quality::isActive()->get();
        \Log::info($product);
        \Log::info($taxs);
        \Log::info($videos);
        \Log::info($seasons);
        \Log::info($qualities);
        \Log::info($subCategories);
        \Log::info($units);
        \Log::info($sizes);
        \Log::info($categories);
        \Log::info($shop);
        \Log::info($rootShop);
        

        return view('shop.product.edit', compact(
            'product', 
            'categories', 
            'units', 
            'sizes', 
            'subCategories', 
            'taxs', 
            'videos',
            'seasons', 
            'qualities'
        ));
    }

    /**
     * Update the product.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $shop = generaleSetting('shop');
        \Log::info("Product Update");
        \Log::info($request->all());

        $skuCode = $shop?->products()->where('code', $request->code)
            ->where('id', '!=', $product->id)->exists();

        if ($skuCode) {
            return back()->withInput()->withErrors(['code' => __('Product code already exists!')])
                ->with('error', __('Product code already exists!'));
        }

        // Add validation for logistics fields if needed
        $request->validate([
            'bag_number' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'row' => 'nullable|integer'
        ]);

        try {
            DB::beginTransaction();

            // Pass the request object directly, not the validated data
            ProductRepository::updateByRequest($request, $product);

            DB::commit();

            /** @var User $user */
            $user = auth()->user();
            $isRootUser = $user?->hasRole('root');

            // admin notification message
            if (! $isRootUser && generaleSetting('setting')->shop_type != 'single') {
                $message = 'Product Updated Request';
                try {
                    AdminProductRequestEvent::dispatch($message);
                } catch (\Throwable $th) {
                    \Log::error('Error dispatching AdminProductRequestEvent: ' . $th->getMessage());
                }

                $data = (object) [
                    'title' => $message,
                    'content' => 'Product Updated Request from '.$shop->name,
                    'url' => '/admin/products?status=1',
                    'icon' => 'bi-shop',
                    'type' => 'success',
                ];
                // store notification
                NotificationRepository::storeByRequest($data);
            }

            return to_route('shop.product.index')->withSuccess(__('Product updated successfully!'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Product update error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withError(__('An error occurred while updating the product: ') . $e->getMessage());
        }
    }

    /**
     * Remove a video from the product.
     */
    public function removeVideo($id)
    {
        \Log::info('Remove video: ' . $id);
        try {
            // Use VideoRepository to remove video
            if (!VideoRepository::removeVideo($id)) {
                return back()->withErrors(['error' => __('Failed to delete video.')]);
            }

            return back()->withSuccess(__('Video deleted successfully.'));
        } catch (\Exception $e) {
            \Log::error('Failed to delete video: ' . $e->getMessage());

            return back()->withErrors(['error' => __('Failed to delete video.')]);
        }
    }

    /**
     * delete thumbnail
     */
    public function thumbnailDestroy(Product $product, Media $media)
    {
        $product->medias()->detach($media->id);
        if (Storage::exists($media->src)) {
            Storage::delete($media->src);
        }

        $media->delete();

        return back()->withSuccess(__('Thumbnail deleted successfully!'));
    }

    /**
     * status toggle a product
     */
    public function statusToggle(Product $product)
    {
        if (! $product->is_approve) {
            return back()->withError(__('Sorry! Your Product is not approved yet!'));
        }

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return back()->withSuccess(__('Status updated successfully'));
    }

    /**
     * generate barcode
     */
    public function generateBarcode(Product $product)
    {
        if (! $product->code) {
            return back()->withError(__('Sorry! Your Product code is not generated yet!'));
        }

        $quantitys = request('qty', 4);

        return view('shop.product.barcode', compact('product', 'quantitys'));
    }
}
