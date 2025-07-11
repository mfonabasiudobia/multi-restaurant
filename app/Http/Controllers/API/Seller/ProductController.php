<?php

namespace App\Http\Controllers\API\Seller;

use App\Events\AdminProductRequestEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdditionthumbnailDeleteRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ColorResource;
use App\Http\Resources\SellerProductDetailsResource;
use App\Http\Resources\SellerProductResource;
use App\Http\Resources\SizeResource;
use App\Http\Resources\UnitResource;
use App\Models\Media;
use App\Models\Product;
use App\Repositories\NotificationRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display the product list.
     */
    public function index(Request $request)
    {
        // get category and search from request
        $category = $request->category;
        $search = $request->search;

        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 20;
        $skip = ($page * $perPage) - $perPage;

        // filter products based on category and search
        $products = auth()->user()->shop?->products()->when($category, function ($query) use ($category) {
            return $query->whereHas('categories', function ($query) use ($category) {
                return $query->where('category_id', $category);
            });
        })->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->latest('id');

        $total = $products->count();
        $products = $products->skip($skip)->take($perPage)->get();

        return $this->json('Product list', [
            'total' => $total,
            'products' => SellerProductResource::collection($products),
        ]);
    }

    /**
     * Display the product details.
     */
    public function show(Product $product)
    {
        return $this->json('Product details', [
            'product' => SellerProductDetailsResource::make($product),
        ]);
    }

    public function createData()
    {
        $shop = generaleSetting('rootShop');

        $categories = $shop?->categories()->active()->get();
        $units = $shop?->units()->isActive()->get();
        $sizes = $shop?->sizes()->isActive()->get();

        return $this->json('create product data', [
            'categories' => CategoryResource::collection($categories),
            'sizes' => SizeResource::collection($sizes),
            'units' => UnitResource::collection($units),
        ]);
    }

    /**
     * store new product.
     */
    public function store(ProductRequest $request)
    {
        $user = auth()->user();
        \Log::info($request->all());
    
        // Validate product code uniqueness within the user's shop
        $skuCode = $user->shop?->products()->where('code', $request->code)->exists();
        if ($skuCode) {
            return $this->json('Product code already exists', [
                'errors' => (object) [
                    'code' => ['The product code already exists'],
                ],
            ], 422);
        }
    
        // Create the product using ProductRepository or manually
        $product = ProductRepository::storeByRequest($request);
    
        // Handle video upload
        
    
        // Notification logic for admins (optional)
        $isRootUser = $user?->hasRole('root');
        if (!$isRootUser) {
            $message = 'New product created request';
            AdminProductRequestEvent::dispatch($message);
    
            NotificationRepository::storeByRequest((object) [
                'title' => $message,
                'content' => 'New product created request from ' . auth()->user()->shop?->name,
                'url' => '/admin/products?status=0',
                'icon' => 'bi-shop',
                'type' => 'success',
            ]);
        }
    
        return $this->json('Product created successfully', [
            'product' => SellerProductResource::make($product),
        ]);
    }

    /**
     * Update the product.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $user = auth()->user();
        \Log::info($request->all());
    
        // Check if the product code is unique
        $skuCode = $user?->shop?->products()
            ->where('code', $request->code)
            ->where('id', '!=', $product->id)
            ->exists();
    
        if ($skuCode) {
            return $this->json('Product code already exists', [
                'errors' => (object) [
                    'code' => ['The product code already exists'],
                ],
            ], 422);
        }
    
        // Update product using repository or manually
        $product = ProductRepository::updateByRequest($request, $product);
    
     
   
    
        // Admin notification logic (optional)
        $isRootUser = $user?->hasRole('root');
        if (!$isRootUser) {
            $message = 'Product updated request';
            AdminProductRequestEvent::dispatch($message);
    
            NotificationRepository::storeByRequest((object) [
                'title' => $message,
                'content' => 'Product updated request from ' . auth()->user()->shop?->name,
                'url' => '/admin/products?status=1',
                'icon' => 'bi-shop',
                'type' => 'success',
            ]);
        }
    
        return $this->json('Product updated successfully', [
            'product' => SellerProductResource::make($product),
        ]);
    }
    /**
     * delete thumbnail
     */
    public function thumbnailDestroy(AdditionthumbnailDeleteRequest $request)
    {
        $product = Product::find($request->product_id);

        $media = Media::find($request->thumbnail_id);

        $product->medias()->detach($media->id);

        if (Storage::exists($media->src)) {
            Storage::delete($media->src);
        }

        $media->delete();

        return $this->json('Thumbnail deleted successfully!', [
            'product' => SellerProductDetailsResource::make($product),
        ]);
    }

    /**
     * Remove video from the product.
     */
    public function removeVideo(Product $product, Media $video)
    {
        $product->medias()->detach($video->id);

        if (Storage::exists($video->src)) {
            Storage::delete($video->src);
        }

        $video->delete();

        return $this->json('Video deleted successfully!', [
            'product' => SellerProductDetailsResource::make($product),
        ]);
    }
    /**
     * status toggle a product
     */
    public function statusToggle(Product $product)
    {
        if (! $product->is_approve) {
            return $this->json(__('Sorry! Your Product is not approved yet!'), [], 422);
        }

        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        $product->refresh();

        return $this->json(__('Status updated successfully'), [
            'product' => SellerProductResource::make($product),
        ]);
    }
}
