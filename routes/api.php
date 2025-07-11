<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\FlashSaleController;
use App\Http\Controllers\API\GiftController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\LegalPageController;
use App\Http\Controllers\API\MasterController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\SubCategoryController;
use App\Http\Controllers\API\SupportController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\BankDetailController;
use App\Http\Controllers\Admin\SubpageController;
use App\Http\Controllers\API\Shop\PackageController;
use App\Http\Controllers\API\DeliveryChargeController;
use App\Http\Controllers\API\NotificationPreferenceController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\API\StripeWebhookController;
use App\Http\Controllers\API\TranslationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// auth token route
Route::controller(SocialAuthController::class)->group(function () {
    Route::post('/social-auth', 'login');
    Route::post('/auth/{provider}/token', 'handleTokenExchange');
});

//subpage route
Route::controller(SubpageController::class)->group(function () {
    Route::get('/subpages', 'index1');
   
});

// country route
Route::controller(CountryController::class)->group(function () {
    Route::get('/countries', 'index');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/registration', 'register');
    Route::post('/login', 'login');
    Route::post('/verify-otp-register', 'verifyOTP');
});

Route::controller(ForgotPasswordController::class)->group(function () {
    Route::post('/send-otp', 'resendOTP');
    Route::post('/verify-otp', 'verifyOtp');
    Route::post('/reset-password', 'resetPassword');
});

//legal page route
Route::controller(LegalPageController::class)->group(function () {
    Route::get('/legal-pages/{slug}', 'index');
    Route::get('/contact-us', 'contactUs');
});

//support route
Route::controller(SupportController::class)->group(function () {
    Route::post('/support', 'store');
});

//master route
Route::controller(MasterController::class)->group(function () {
    Route::get('/master', 'index');
});

//home route
Route::controller(HomeController::class)->group(function () {
    Route::get('/home', 'index');
    Route::get('/recently-views', 'recentlyViews');
    Route::get('/filter-categories', 'getFilterCategories');
});

//Banner route
Route::controller(BannerController::class)->group(function () {
    Route::get('/banners', 'index');
});

//category route
Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'index');
    Route::get('/category-products', 'show');
});

//sub category route
Route::controller(SubCategoryController::class)->group(function () {
    Route::get('/sub-categories', 'index');
    Route::get('/category-subcategories', 'getByCategory');
});

//product route
Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index');
    Route::get('/product-details', 'show');
    Route::get('/products/quality/{id}', 'productsByQuality')->name('products.by.quality');
    Route::get('/products/season/{id}', 'productsBySeason')->name('products.by.season');
    Route::get('/products/filter', 'filter');
    Route::get('/products/filter/advanced', 'advancedFilter');
    Route::get('/products/shop/filter', 'shopFilter');
});

//review route
Route::controller(ReviewController::class)->group(function () {
    Route::get('/reviews', 'index');
});

//change password route
Route::controller(UserController::class)->group(function () {
    Route::post('/change-password/{customer}', 'changePasswordById');
    Route::post ('/change-phone-number/{customer}', 'updatePhoneNumberById');
    //for delete or block user
    Route::post('/block-or-delete-user/{customer}', 'blockOrDeleteUserById');
});

//shop route
Route::controller(ShopController::class)->group(function () {
    Route::get('/shops', 'index');
    Route::get('/shops/top', 'topShops');
    Route::get('/shop-categories', 'shopCategory');
    Route::get('/shops/popular-products', 'popularProducts');
    Route::get('/shops/{shop}', 'show');
});

Route::middleware(['auth:sanctum', 'role:customer'])->group(function () {

    //user route
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/bank-details', [BankDetailController::class, 'getBankDetails']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    
    Route::controller(UserController::class)->group(function () {
        Route::get('/profile', 'index');
        Route::post('/update-profile', 'update');
        Route::post('/change-password', 'changePassword');
    });

    //recently view route
    Route::controller(HomeController::class)->group(function () {
        Route::get('/recently-views', 'recentlyViews');
    });

    //favorite add or remove route
    Route::controller(ProductController::class)->group(function () {
        Route::post('/favorite-add-or-remove', 'addFavorite');
        Route::get('/favorite-products', 'favoriteProducts');
        Route::post('/product-review', 'storeReview');
    });

    //order route
    Route::controller(OrderController::class)->group(function () {
        Route::get('/orders', 'index');
        Route::post('/place-order', 'store');
        Route::get('/order-details', 'show');
        Route::post('/orders/cancel', 'cancel');
        Route::post('/place-order/again', 'reOrder');
        Route::get('/order-payment/{order}/{paymentMethod?}', 'payment');
    });

    //order route for version 1
    Route::controller(OrderController::class)->prefix('/v1')->group(function () {
        Route::post('/place-order', 'store');
    });

    //coupon route
    Route::controller(CouponController::class)->group(function () {
        Route::get('/get-vouchers', 'index');
        Route::post('/vouchers-collect', 'store')->name('voucher.collect');
        Route::get('/get-collected-vouchers', 'collectedVouchers');
        Route::post('/apply-voucher', 'applyVoucher')->name('voucher.apply');
        Route::post('/coupons/apply', 'getDiscount');
    });

    //address route
    Route::controller(AddressController::class)->group(function () {
        Route::get('/addresses', 'index');
        Route::post('/address/store', 'store');
        Route::post('/address/{address}/update', 'update');
        Route::delete('/address/{address}/delete', 'destroy');
    });

    //cart route
    Route::controller(CartController::class)->group(function () {
        Route::get('/carts', 'index');
        Route::post('/cart/store', 'store');
        Route::post('/cart/increment', 'increment');
        Route::post('/cart/decrement', 'decrement');
        Route::post('/cart/delete', 'destroy');
        Route::post('/cart/checkout', 'checkout');
    });

    // gift route
    Route::controller(GiftController::class)->group(function () {
        Route::get('/gifts', 'index');
        Route::post('/gift/store', 'store');
        Route::post('/gift/update', 'update');
        Route::delete('/gift/delete', 'destroy');
    });

    // Notification preferences routes
    Route::get('/user/notification-preferences', [NotificationPreferenceController::class, 'getPreferences']);
    Route::post('/user/notification-preferences', [NotificationPreferenceController::class, 'updatePreferences']);
});

// flash sale route
Route::controller(FlashSaleController::class)->group(function () {
    Route::get('/flash-sales', 'index');
    Route::get('/flash-sale/{flashSale}/details', 'show');
});

// language route
Route::get('/lang/{locale}', [TranslationController::class, 'getTranslations']);

Route::get('/test', function() {
    return response()->json(['message' => 'API is working']);
});

Route::middleware(['auth:sanctum'])->prefix('shop')->group(function () {
    Route::get('/package/status', [PackageController::class, 'checkStatus']);
    Route::post('/package/payment', [PackageController::class, 'processPayment']);
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

// Payment related routes
Route::post('/verify-payment', [OrderController::class, 'verifyPayment']);
Route::post('/abandoned-payment', [OrderController::class, 'abandonedPayment']);
Route::get('/check-payment-status/{order}', [OrderController::class, 'checkPaymentStatus']);
Route::post('/refresh-payment-session', [OrderController::class, 'refreshPaymentSession']);
Route::post('/update-payment-method', [OrderController::class, 'updatePaymentMethod']);
Route::get('/stripe-public-key', [OrderController::class, 'getStripePublicKey']);

// Add this route
Route::get('weight-unit', function() {
    $unit = \App\Models\Unit::where('is_weight', true)
        ->whereNull('shop_id')
        ->first();
    return response()->json(['unit' => $unit ? $unit->name : 'KG']);
});

// Add new routes for managing frontend translations (protected by admin middleware)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/translations/{locale}', [TranslationController::class, 'updateTranslation']);
    Route::delete('/translations/{locale}', [TranslationController::class, 'deleteTranslation']);
});

// Add these routes to your api.php file
Route::get('/translations/{locale}', function ($locale) {
    $path = base_path("lang/frontend/{$locale}.json");
    
    // If frontend translation doesn't exist, fallback to regular translations
    if (!File::exists($path)) {
        $path = base_path("lang/{$locale}.json");
    }
    
    if (File::exists($path)) {
        return response()->json(json_decode(File::get($path), true));
    }

    // Return empty object if no translations found
    return response()->json([]);
});
