<?php

namespace App\Http\Controllers\API;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FlashSaleResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShopResource;
use App\Models\Ad;
use App\Models\GeneraleSetting;
use App\Models\User;
use App\Repositories\BannerRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\FlashSaleRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ShopRepository;
use Illuminate\Http\Request;
use App\Models\Quality;
use App\Models\Season;
use App\Models\Product;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Index method for retrieving banners, categories, and popular products.
     *
     * @return Some_Return_Value
     */
    public function index(Request $request)
    {
        $startTime = microtime(true);

        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 8;
        $skip = ($page * $perPage) - $perPage;

        $generaleSetting = generaleSetting('setting');
        $rootShop = generaleSetting('rootShop');
        $shop = null;
        if ($generaleSetting?->shop_type == 'single') {
            $shop = $rootShop;
        }

        $bannersStartTime = microtime(true);
        $banners = BannerRepository::query()->whereNull('shop_id')->active()->get();
        $bannersEndTime = microtime(true);


        $categoriesStartTime = microtime(true);
        $categories = CategoryRepository::query()->active()
            ->whereHas('shops', function ($query) use ($rootShop) {
                return $query->where('shop_id', $rootShop->id);
            })
            /*->whereHas('products', function ($product) {
                return $product->where('is_active', true);
            })*/
            ->withCount('products')->orderByDesc('products_count')->take(10)->get();
        $categoriesEndTime = microtime(true);


        $popularProductsStartTime = microtime(true);
        $popularProducts = ProductRepository::query()->isActive()
            ->when($shop, function ($query) use ($shop) {
                return $query->where('shop_id', $shop->id);
            })->withCount('orders as orders_count')
            ->withAvg('reviews as average_rating', 'rating')
            ->orderByDesc('average_rating')
            ->orderByDesc('orders_count')
            ->take(6)->get();
        $popularProductsEndTime = microtime(true);


        $justForYouStartTime = microtime(true);
        $justForYou = ProductRepository::query()->isActive()->latest('id')->when($shop, function ($query) use ($shop) {
            return $query->where('shop_id', $shop->id);
        });
        $total = $justForYou->count();
        $justForYou = $justForYou->skip($skip)->take($perPage)->get();
        $justForYouEndTime = microtime(true);

        $shopsStartTime = microtime(true);
        $shops = collect([]);
        if ($generaleSetting?->shop_type != 'single') {
            $shops = ShopRepository::query()->isActive()->whereHas('products', function ($query) {
                return $query->isActive();
            })->withCount('orders')->withAvg('reviews as average_rating', 'rating')->orderByDesc('average_rating')->orderByDesc('orders_count')->take(8)->get();
        }
        $shopsEndTime = microtime(true);


        $adsStartTime = microtime(true);
        $ads = Ad::where('status', 1)->latest('id')->take(6)->get();
        $adsEndTime = microtime(true);


        $incomingFlashSaleStartTime = microtime(true);
        $incomingFlashSale = FlashSaleRepository::getIncoming();
        $incomingFlashSaleEndTime = microtime(true);


        $runningFlashSaleStartTime = microtime(true);
        $runningFlashSale = FlashSaleRepository::getRunning();
        $runningFlashSaleEndTime = microtime(true);


        $latestProductsStartTime = microtime(true);
        $latestProducts = ProductRepository::query()->isActive()
            ->when($shop, function ($query) use ($shop) {
                return $query->where('shop_id', $shop->id);
            })
            ->latest()
            ->take(60)
            ->get();
        $latestProductsEndTime = microtime(true);


        // Get random products
        $randomProductsStartTime = microtime(true);
        $randomProducts = ProductRepository::query()->isActive()
            ->when($shop, function ($query) use ($shop) {
                return $query->where('shop_id', $shop->id);
            })
            ->inRandomOrder()
            ->take(20)
            ->get();
        $randomProductsEndTime = microtime(true);


        $response = $this->json('home', [
            'banners' => BannerResource::collection($banners),
            'ads' => BannerResource::collection($ads),
            'categories' => CategoryResource::collection($categories),
            'shops' => ShopResource::collection($shops),
            'popular_products' => ProductResource::collection($randomProducts),
            'latest_products' => ProductResource::collection($latestProducts),
            'random_products' => ProductResource::collection($randomProducts),
            'just_for_you' => [
                'total' => $total,
                'products' => ProductResource::collection($justForYou),
            ],
            'incoming_flash_sale' => $incomingFlashSale ? FlashSaleResource::make($incomingFlashSale) : null,
            'running_flash_sale' => $runningFlashSale ? FlashSaleResource::make($runningFlashSale)->toArray(request(), 'true', 'true') : null,
            'filter_categories' => [
                'qualities' => [
                    'title' => 'Calitate',
                    'items' => Quality::where('is_active', true)
                        ->get()
                        ->map(function ($quality) {
                            $count = Product::where('quality_id', $quality->id)
                                ->where('is_active', 1)
                                ->count();
                            return [
                                'id' => $quality->id,
                                'name' => $quality->name,
                                'count' => $count,
                                'route' => '/products?quality=' . $quality->id
                            ];
                        })->toArray()
                ],
                'seasons' => [
                    'title' => 'Anotimpuri',
                    'items' => Season::where('is_active', true)
                        ->get()
                        ->map(function ($season) {
                            $count = Product::where('season_id', $season->id)
                                ->where('is_active', 1)
                                ->count();
                            return [
                                'id' => $season->id,
                                'name' => $season->name,
                                'count' => $count,
                                'route' => '/products?season=' . $season->id
                            ];
                        })->toArray()
                ]
            ]
        ]);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        \Log::info('Home page execution time: ' . $executionTime . ' seconds');
        \Log::info('Banners execution time: ' . ($bannersEndTime - $bannersStartTime) . ' seconds');
        \Log::info('Categories execution time: ' . ($categoriesEndTime - $categoriesStartTime) . ' seconds');
        \Log::info('Popular products execution time: ' . ($popularProductsEndTime - $popularProductsStartTime) . ' seconds');


        return $response;
    }

    /**
     * Get recently viewed products for the current user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentlyViews()
    {
        $generaleSetting = GeneraleSetting::first();

        $shop = null;
        if ($generaleSetting?->shop_type == 'single') {
            $shop = User::role(Roles::ROOT->value)->first()?->shop;
        }

        /**
         * @var \App\Models\User $user
         */
        $user = auth()->user();

        $products = $user->recentlyViewedProducts()->when($shop, function ($query) use ($shop) {
            return $query->where('shop_id', $shop->id);
        })->where('is_active', true)->orderBy('pivot_updated_at', 'desc')->take(10)->get();

        return $this->json('recently viewed products', [
            'products' => ProductResource::collection($products),
        ]);
    }

    public function productsByQuality($id)
    {
        $quality = Quality::findOrFail($id);
        $products = Product::where('quality_id', $id)
            ->isActive()
            ->paginate(20);

        return $this->json('products_by_quality', [
            'quality' => $quality,
            'products' => ProductResource::collection($products)
        ]);
    }

    public function productsBySeason($id)
    {
        $season = Season::findOrFail($id);
        $products = Product::where('season_id', $id)
            ->isActive()
            ->paginate(20);

        return $this->json('products_by_season', [
            'season' => $season,
            'products' => ProductResource::collection($products)
        ]);
    }

    /**
     * Get filter categories data for debugging
     */
    public function getFilterCategories()
    {
        $qualities = \App\Models\Quality::where('is_active', true)
            ->get()
            ->map(function ($quality) {
                $count = \App\Models\Product::where('quality_id', $quality->id)
                    ->where('is_active', 1)
                    ->count();
                return [
                    'id' => $quality->id,
                    'name' => $quality->name,
                    'count' => $count,
                    'route' => '/products?quality=' . $quality->id
                ];
            });

        $seasons = \App\Models\Season::where('is_active', true)
            ->get()
            ->map(function ($season) {
                $count = \App\Models\Product::where('season_id', $season->id)
                    ->where('is_active', 1)
                    ->count();
                return [
                    'id' => $season->id,
                    'name' => $season->name,
                    'count' => $count,
                    'route' => '/products?season=' . $season->id
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => [
                'filter_categories' => [
                    'qualities' => [
                        'title' => 'Calitate',
                        'items' => $qualities
                    ],
                    'seasons' => [
                        'title' => 'Anotimpuri',
                        'items' => $seasons
                    ]
                ]
            ]
        ]);
    }
}
