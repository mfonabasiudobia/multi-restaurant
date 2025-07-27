<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Retrieves a paginated list of categories with their associated products.
     *
     * @param  Request  $request  The HTTP request object.
     * @return JsonResponse The JSON response containing the categories and the total count.
     */
    public function index(Request $request)
    {
        $query = Category::query()->with('subCategories')->active();

        // If shop_id is provided, only get categories that have products in this shop
        if ($request->filled('shop_id') && $request->with_products) {
            $query->whereHas('products', function ($q) use ($request) {
                $q->where('shop_id', $request->shop_id)
                    ->where('is_active', 1)
                    ->where('is_approve', 1);
            });
        }

        $categories = $query->get();
        // $rootShop = generaleSetting('rootShop');
        // $categories = CategoryRepository::query()->active()
        // ->whereHas('shops', function ($query) use ($rootShop) {
        //     return $query->where('shop_id', $rootShop->id);
        // })->whereHas('products', function ($product) {
        //     return $product->where('is_active', true);
        // })->withCount('products')->orderByDesc('products_count')->take(10)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => CategoryResource::collection($categories),
            ]
        ]);
    }

    /**
     * Retrieves and displays the products of a specific category.
     *
     * @param  int  $id  The ID of the category.
     * @param  Request  $request  The HTTP request object.
     * @return JsonResponse The JSON response containing the category products.
     *
     * @throws None
     */
    public function show(Request $request)
    {
        $page = (int) ($request->page ?? 1);
        $perPage = (int) ($request->per_page ?? 10);

        $search = $request->search;
        $shopID = $request->shop_id;
        $categoryID = $request->category_id;
        $subCategoryID = $request->sub_category_id;
        $rating = $request->rating;
        $sortType = $request->sort_type;
        $minPrice = $request->min_price;
        $maxPrice = $request->max_price;
        $sizeId = $request->size;
        $quality = $request->quality; // New quality filter
        $season = $request->seasons; // New quality filter


        $category = $categoryID ? CategoryRepository::find($categoryID) : null;

        $generaleSetting = generaleSetting();
        $shop = ($generaleSetting?->shop_type === 'single') ? generaleSetting('rootShop') : null;

        $productsQuery = ProductRepository::query()
            ->withCount('orders as orders_count')
            ->withAvg('reviews as average_rating', 'rating')
            ->where('quantity', '>', 0)
            ->latest()
            ->when($search, fn($query) => $query->where('name', 'like', "%{$search}%"))
            ->when($shop, fn($query) => $query->where('shop_id', $shop->id))
            ->when($shopID && !$shop, fn($query) => $query->where('shop_id', $shopID))
            ->when($categoryID, fn($query) => $query->whereHas('categories', fn($q) => $q->where('id', $categoryID)))
            ->when($subCategoryID && ($subCategoryID != 0 || $subCategoryID != null), fn($query) => $query->whereHas('subCategories', fn($q) => $q->where('id', $subCategoryID)))
            ->when($minPrice, fn($query) => $query->where('price', '>=', $minPrice))
            ->when($maxPrice, fn($query) => $query->where('price', '<=', $maxPrice))
            ->when($sizeId, fn($query) => $query->whereHas('sizes', fn($q) => $q->where('id', $sizeId)))
            ->when($rating, fn($query) => $query->havingRaw('average_rating >= ? AND average_rating < ?', [floatval($rating), floatval($rating) + 1]))
            ->when($quality, fn($query) => $query->where('quality_id', $quality)) // New filter
            ->when($season, fn($query) => $query->where('season_id', $season)) // New filter
            ->isActive();

        // Apply sorting
        match ($sortType) {
            'heigh_to_low'   => $productsQuery->orderBy('price', 'desc'),
            'low_to_high'    => $productsQuery->orderBy('price', 'asc'),
            'top_selling'    => $productsQuery->orderByDesc('orders_count'),
            'popular_product' => $productsQuery->orderByDesc('orders_count')->orderByDesc('average_rating'),
            'newest', 'just_for_you' => $productsQuery->orderByDesc('id'),
            default          => null,
        };

        // Pagination
        $total = $productsQuery->count();
        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

        return $this->json('Category products', [
            'total' => $total,
            'category' => CategoryResource::make($category),
            'products' => ProductResource::collection($products),
        ]);
    }


    /**
     * Get categories that have products in a specific shop
     */
    public function shopCategories(Request $request)
    {
        try {
            $query = Category::query()
                ->active()
                ->with('subCategories')
                ->select('categories.*')
                ->join('products', 'products.category_id', '=', 'categories.id')
                ->where('products.shop_id', $request->shop_id)
                ->where('products.is_active', 1)
                ->where('products.is_approve', 1)
                ->groupBy('categories.id')
                ->orderBy('categories.name');

            $categories = $query->get();

            \Log::info('Shop categories query:', [
                'shop_id' => $request->shop_id,
                'categories_count' => $categories->count()
            ]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'categories' => CategoryResource::collection($categories)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching shop categories: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching categories'
            ], 500);
        }
    }
}
