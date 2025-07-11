<?php

use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BusinessSetupController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactUsController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerNotificationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryChargeController;
use App\Http\Controllers\Admin\EmployeeManageController;
use App\Http\Controllers\Admin\FirebaseController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\GeneraleSettingController;
use App\Http\Controllers\Admin\GoogleReCaptchaController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LegalpageController;
use App\Http\Controllers\Admin\MailConfigurationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PusherConfigController;
use App\Http\Controllers\Admin\ReviewsController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\SMSGatewaySetupController;
use App\Http\Controllers\Admin\SocialAuthController;
use App\Http\Controllers\Admin\SocialLinkController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\ThemeColorController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\VatTaxController;
use App\Http\Controllers\Admin\VerifyManageController;
use App\Http\Controllers\Admin\WithdrawController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Admin\QualityController;
use App\Http\Controllers\Admin\SubpageController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\DeliveryWeightController;
use App\Http\Controllers\Admin\LogisticsController;
use App\Http\Controllers\Admin\InnerAdController;
use App\Http\Controllers\Admin\FrontendTranslationController;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
*/

Route::name('admin.')->group(function () {
    // Login
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'index')->name('login')->middleware('guest');
        Route::post('/login', 'login')->name('login.submit');
    });

    Route::middleware(['auth', 'checkPermission'])->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::controller(SubpageController::class)->group(function () {
            Route::get('/subpages', 'index')->name('subpages.index');
            Route::get('/subpage/create', 'create')->name('subpages.create');
            Route::post('/subpage/store', 'store')->name('subpages.store');
            Route::get('/subpage/{slug}', 'show')->name('subpages.show');
            Route::put('/subpage/{subpage}/update', 'update')->name('subpages.update');
            Route::get('/subpage/{subpage}/edit', 'edit')->name('subpages.edit');
            Route::delete('/subpage/{subpage}/delete', 'destroy')->name('subpages.destroy');
        });

        //inner ads
        Route::controller(InnerAdController::class)->group(function () {
            Route::get('/inner-ads', 'index')->name('inner-ads.index');
            Route::get('/inner-ads/create', 'create')->name('inner-ads.create');
            Route::post('/inner-ads/store', 'store')->name('inner-ads.store');
            Route::get('/inner-ads/{innerAd}/edit', 'edit')->name('inner-ads.edit');
            Route::put('/inner-ads/{innerAd}/update', 'update')->name('inner-ads.update');
            Route::get('/inner-ads/{innerAd}/destroy', 'destroy')->name('inner-ads.destroy');
            Route::get('/inner-ads/{innerAd}/toggle', 'toggle')->name('inner-ads.toggle');
        }); 

        //banner
        Route::controller(BannerController::class)->group(function () {
            Route::get('/banners', 'index')->name('banner.index');
            Route::get('/banner/create', 'create')->name('banner.create');
            Route::post('/banner/store', 'store')->name('banner.store');
            Route::get('/banner/{banner}/edit', 'edit')->name('banner.edit');
            Route::put('/banner/{banner}/update', 'update')->name('banner.update');
            Route::get('/banner/{banner}/toogle', 'statusToggle')->name('banner.toggle');
            Route::get('/banner/{banner}/destroy', 'destroy')->name('banner.destroy');
        });
        // Route::post('/products/{product}/send-whatsapp', [ProductController::class, 'sendWhatsAppNotification']);

        // ads routes
        Route::controller(AdController::class)->group(function () {
            Route::get('/ads', 'index')->name('ad.index');
            Route::get('/ads/create', 'create')->name('ad.create');
            Route::post('/ads/store', 'store')->name('ad.store');
            Route::get('/ads/{ad}/edit', 'edit')->name('ad.edit');
            Route::put('/ads/{ad}/update', 'update')->name('ad.update');
            Route::get('/ads/{ad}/toogle', 'statusToggle')->name('ad.toggle');
            Route::get('/ads/{ad}/destroy', 'destroy')->name('ad.destroy');
        });

        // Shops
        Route::controller(ShopController::class)->group(function () {
            Route::get('/shops', 'index')->name('shop.index');
            Route::get('/shops/create', 'create')->name('shop.create');
            Route::post('/shops/store', 'store')->name('shop.store');
            Route::get('/shops/{shop}/edit', 'edit')->name('shop.edit');
            Route::post('/shops/{shop}/update', 'update')->name('shop.update');
            Route::get('/shops/{shop}', 'show')->name('shop.show');
            Route::post('/shops/{shop}/deny', [ShopController::class, 'denyShop'])->name('shop.deny');
            Route::get('/shops/{shop}/status-toggle', 'statusToggle')->name('shop.status.toggle');
            Route::get('/shops/{shop}/orders', 'orders')->name('shop.orders');
            Route::get('/shops/{shop}/products', 'products')->name('shop.products');
            Route::get('/shops/{shop}/categories', 'categories')->name('shop.category');
            Route::get('/shops/{shop}/reviews', 'reviews')->name('shop.reviews');
            Route::post('/shops/{shop}/reset-password', 'resetPassword')->name('shop.reset.password');
        });

        Route::controller(SeasonController::class)->group(function () {
            Route::get('/seasons', 'index')->name('season.index');
            Route::post('/season/store', 'store')->name('season.store');
            Route::put('/season/{season}/update', 'update')->name('season.update');
            Route::get('/season/{season}/toogle', 'toggle')->name('season.toggle');
        });

        Route::controller(QualityController::class)->group(function () {
            Route::get('/qualities', 'index')->name('quality.index');
            Route::post('/quality/store', 'store')->name('quality.store');
            Route::put('/quality/{quality}/update', 'update')->name('quality.update');
            Route::get('/quality/{quality}/toggle', 'toggle')->name('quality.toggle'); // Ensure this route exists
        });

        // reviews
        Route::controller(ReviewsController::class)->group(function () {
            Route::get('/reviews', 'index')->name('review.index');
            Route::get('/review/{review}/toggle', 'toggleReview')->name('review.toggle');
        });

        // Orders
        Route::controller(OrderController::class)->group(function () {
            Route::get('/orders/{status?}', 'index')->name('order.index');
            Route::get('/orders/{order}/show', 'show')->name('order.show');
            Route::get('order/{order}/payment-proof/download',  'downloadPaymentProof')->name('order.downloadPaymentProof');
            Route::get('/orders/{order}/status-change', 'statusChange')->name('order.status.change');
            Route::get('/orders/{order}/payment-status-toggle', 'paymentStatusToggle')->name('order.payment.status.toggle');
        });

        // Categories
        Route::controller(CategoryController::class)->group(function () {
            Route::get('/categories', 'index')->name('category.index');
            Route::get('/category/create', 'create')->name('category.create');
            Route::post('/category/store', 'store')->name('category.store');
            Route::get('/category/{category}/edit', 'edit')->name('category.edit');
            Route::put('/category/{category}/update', 'update')->name('category.update');
            Route::delete('/category/{category}/destroy', 'destroy')->name('category.destroy');
            Route::get('/category/{category}/toogle', 'statusToggle')->name('category.toggle');
        });

        //sub categories route
        Route::controller(SubCategoryController::class)->group(function () {
            Route::get('/subcategories', 'index')->name('subcategory.index');
            Route::get('/subcategory/create', 'create')->name('subcategory.create');
            Route::post('/subcategory/store', 'store')->name('subcategory.store');
            Route::get('/subcategory/{subCategory}/edit', 'edit')->name('subcategory.edit');
            Route::put('/subcategory/{subCategory}/update', 'update')->name('subcategory.update');
            Route::delete('/subcategory/{subCategory}/destroy', 'destroy')->name('subcategory.destroy');
            Route::get('/subcategory/{subCategory}/toogle', 'statusToggle')->name('subcategory.toggle');
        });

        //size
        Route::controller(SizeController::class)->group(function () {
            Route::get('/sizes', 'index')->name('size.index');
            Route::post('/size/store', 'store')->name('size.store');
            Route::put('/size/{size}/update', 'update')->name('size.update');
            Route::delete('/size/{size}/destroy', 'destroy')->name('size.destroy');
            Route::get('/size/{size}/toogle', 'statusToggle')->name('size.toggle');
        });

        //unit
        Route::controller(UnitController::class)->group(function () {
            Route::get('/units', 'index')->name('unit.index');
            Route::post('/unit/store', 'store')->name('unit.store');
            Route::put('/unit/{unit}/update', 'update')->name('unit.update');
            Route::delete('/unit/{unit}/destroy', 'destroy')->name('unit.destroy');
            Route::get('/unit/{unit}/toogle', 'statusToggle')->name('unit.toggle');
        });

        // Products
        Route::controller(ProductController::class)->group(function () {
            Route::get('/products', 'index')->name('product.index');
            Route::get('/products/{product}/approve', 'approve')->name('product.approve');
            Route::get('/product/{product}/show', 'show')->name('product.show');
            Route::get('/products/{product}/edit', 'edit')->name('product.edit');
            Route::put('/products/{product}', 'update')->name('product.update');
            Route::delete('/products/{product}/delete', 'destroy')->name('product.destroy');
            Route::get('/products/{product}/barcode', 'generateBarcode')->name('product.barcode');
            Route::get('/products/video/{video}/remove', 'removeVideo')->name('product.remove-videos');
            Route::get('/products/create', 'create')->name('product.create');
            Route::post('/products/store', 'store')->name('product.store');
            Route::post('/products/{product}/toggle-status', 'toggleStatus')->name('product.toggle-status');
            Route::post('/products/{product}/send-whatsapp', 'sendWhatsAppNotification')->name('product.send-whatsapp');
            
            // New Item Request routes
            Route::get('/item-requests', 'itemRequests')->name('product.item-requests');
            Route::get('/update-requests', 'updateRequests')->name('product.update-requests');
            Route::get('/approved-items', 'approvedItems')->name('product.approved-items');
        });

        //legal page routes
        Route::controller(LegalpageController::class)->group(function () {
            Route::get('/legalpage/{slug}', 'index')->name('legalpage.index');
            Route::get('/legalpage/{slug}/edit', 'edit')->name('legalpage.edit');
            Route::post('/legalpage/{slug}', 'update')->name('legalpage.update');
        });

        //Generate Settings
        Route::controller(GeneraleSettingController::class)->group(function () {
            Route::get('/generale-setting', 'index')->name('generale-setting.index');
            Route::post('/generale-setting', 'update')->name('generale-setting.update');
        });

        // business settings
        Route::controller(BusinessSetupController::class)->group(function () {
            Route::get('/business-setting', 'index')->name('business-setting.index');
            Route::post('/business-setting', 'update')->name('business-setting.update');

            Route::get('/business-shop', 'shop')->name('business-setting.shop');
            Route::post('/business-shop', 'shopUpdate')->name('business-setting.shop.update');

            Route::get('/business-withdraw', 'withdraw')->name('business-setting.withdraw');
            Route::post('/business-withdraw', 'withdrawUpdate')->name('business-setting.withdraw.update');

            Route::get('/business-shop/toggle-pos', 'togglePOS')->name('business-setting.shop.toggle-pos');
            Route::get('/business-shop/toggle-register', 'toggleRegister')->name('business-setting.shop.toggle-register');
        });

        // social links
        Route::controller(SocialLinkController::class)->group(function () {
            Route::get('/social-links', 'index')->name('socialLink.index');
            Route::post('/social-links/{socialLink}', 'update')->name('socialLink.update');
        });

        // theme color
        Route::controller(ThemeColorController::class)->group(function () {
            Route::get('/theme-color', 'index')->name('themeColor.index');
            Route::post('/theme-color', 'update')->name('themeColor.update');
            Route::post('/theme-color/change', 'change')->name('themeColor.change');
        });

        // delivery charges
        Route::controller(DeliveryChargeController::class)->group(function () {
            Route::get('/delivery-charge', 'index')->name('deliveryCharge.index');
            Route::get('/delivery-charge/create', 'create')->name('deliveryCharge.create');
            Route::post('/delivery-charge/store', 'store')->name('deliveryCharge.store');
            Route::get('/delivery-charge/{deliveryCharge}/edit', 'edit')->name('deliveryCharge.edit');
            Route::put('/delivery-charge/{deliveryCharge}/update', 'update')->name('deliveryCharge.update');
            Route::get('/delivery-charge/{deliveryCharge}/destroy', 'destroy')->name('deliveryCharge.destroy');
        });

        // Coupons
        Route::controller(CouponController::class)->group(function () {
            Route::get('/coupons', 'index')->name('coupon.index');
            Route::get('/coupon/create', 'create')->name('coupon.create');
            Route::post('/coupon/store', 'store')->name('coupon.store');
            Route::get('/coupon/{coupon}/edit', 'edit')->name('coupon.edit');
            Route::put('/coupon/{coupon}/update', 'update')->name('coupon.update');
            Route::get('/coupon/{coupon}/destroy', 'destroy')->name('coupon.destroy');
            Route::get('/coupon/{coupon}/toogle', 'statusToggle')->name('coupon.toggle');
        });

        // Logout
        Route::controller(LoginController::class)->group(function () {
            Route::post('/logout', 'logout')->name('logout');
        });

        // notification route
       
        Route::controller(NotificationController::class)->group(function () {
            Route::get('/new-notifications', 'index')->name('dashboard.notification');
            Route::get('/notifications', 'show')->name('notification.show');
            Route::get('/notification/{notification}/read', 'markAsRead')->name('notification.read');
            Route::get('/notification/{notification}/destroy', 'destroy')->name('notification.destroy');
            Route::get('/notification/read-all', 'markAllAsRead')->name('notification.readAll');
        });

        // Pusher Configuration
        Route::controller(PusherConfigController::class)->group(function () {
            Route::get('/pusher-configuration', 'index')->name('pusher.index');
            Route::post('/pusher-configuration', 'update')->name('pusher.update');
        });

        //  mail configuration
        Route::controller(MailConfigurationController::class)->group(function () {
            Route::get('/mail-configuration', 'index')->name('mailConfig.index');
            Route::put('/mail-configuration', 'update')->name('mailConfig.update');
        });

        // payment gateway
        Route::controller(PaymentGatewayController::class)->group(function () {
            Route::get('/payment-gateway', 'index')->name('paymentGateway.index');
            Route::post('/payment-gateway/{paymentGateway}/update', 'update')->name('paymentGateway.update');
            Route::get('/payment-gateway/{paymentGateway}/toggle', 'toggle')->name('paymentGateway.toggle');
            Route::post('/bank-details/update',  'updateBankDetails')->name('paymentGateway.updateBankDetails');

        });

        //  SMS Gateway
        Route::controller(SMSGatewaySetupController::class)->group(function () {
            Route::get('/sms-gateway', 'index')->name('sms-gateway.index');
            Route::put('/sms-gateway', 'update')->name('sms-gateway.update');
        });

        // contact us
        Route::controller(ContactUsController::class)->group(function () {
            Route::get('/contact-us', 'index')->name('contactUs.index');
            Route::post('/contact-us/{contactUs?}', 'update')->name('contactUs.update');
        });

        // support route
        Route::controller(SupportController::class)->group(function () {
            Route::get('/supports', 'index')->name('support.index');
            Route::get('/support/{support}/delete', 'delete')->name('support.delete');
        });

        //withdrawal route
        Route::controller(WithdrawController::class)->group(function () {
            Route::get('/withdraws', 'index')->name('withdraw.index');
            Route::get('/withdraw/{withdraw}/show', 'show')->name('withdraw.show');
            Route::post('/withdraw/{withdraw}/update', 'update')->name('withdraw.update');
        });

        //profile
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'index')->name('profile.index');
            Route::get('/profile/edit', 'edit')->name('profile.edit');
            Route::put('/profile/update', 'update')->name('profile.update');
            Route::get('/profile/change-password', 'changePassword')->name('profile.change-password');
            Route::put('/profile/change-password/update', 'updatePassword')->name('profile.change-password.update');
        });

        // rider route
        Route::controller(RiderController::class)->group(function () {
            Route::get('/riders', 'index')->name('rider.index');
            Route::get('/riders/create', 'create')->name('rider.create');
            Route::post('/riders/store', 'store')->name('rider.store');
            Route::get('/riders/{user}', 'show')->name('rider.show');
            Route::get('/riders/{user}/edit', 'edit')->name('rider.edit');
            Route::put('/riders/{user}/update', 'update')->name('rider.update');
            Route::get('/riders/{user}/destroy', 'destroy')->name('rider.destroy');
            Route::get('/riders/{user}/toogle', 'statusToggle')->name('rider.toggle');
            Route::post('/riders/{order}/assign-order', 'assignOrder')->name('rider.assign.order');
        });

        // customer route
        Route::controller(CustomerController::class)->group(function () {
            Route::get('/customers', 'index')->name('customer.index');
            Route::get('/customers/create', 'create')->name('customer.create');
            Route::post('/customers/store', 'store')->name('customer.store');
            Route::get('/customers/{user}', 'show')->name('customer.show');
            Route::get('/customers/{user}/edit', 'edit')->name('customer.edit');
            Route::put('/customers/{user}/update', 'update')->name('customer.update');
            Route::get('/customers/{user}/destroy', 'destroy')->name('customer.destroy');
            Route::get('/customers/{user}/toogle', 'statusToggle')->name('customer.toggle');
            Route::post('/toggle-whatsapp-status/{customerId}', 'toggleWhatsAppStatus')->name('customer.toggle-whatsapp');
        });

        // firebase route
        Route::controller(FirebaseController::class)->group(function () {
            Route::get('/firebase-config', 'index')->name('firebase.index');
            Route::post('/firebase-config', 'update')->name('firebase.update');
        });

        // language routes
        Route::controller(LanguageController::class)->group(function () {
            Route::get('/language', 'index')->name('language.index');
            Route::get('/language/create', 'create')->name('language.create');
            Route::post('/language/store', 'store')->name('language.store');
            Route::get('/language/{language}/edit', 'edit')->name('language.edit');
            Route::put('/language/{language}/update', 'update')->name('language.update');
            Route::get('/language/{language}/delete', 'delete')->name('language.delete');
            Route::post('/language/{language}/export', 'export')->name('language.export');
            Route::post('/language/{language}/import', 'import')->name('language.import');
            Route::post('/language/change', 'changeLanguage')->name('language.change');
            Route::post('/language/{language}/translation', [LanguageController::class, 'storeTranslation'])
                ->name('language.translation.store');
            Route::post('/language/{language}/translation/update', [LanguageController::class, 'updateTranslations'])
                ->name('language.translation.update');
        });

        // Customer Notification route
        Route::controller(CustomerNotificationController::class)->group(function () {
            Route::get('/customer-notifications', 'index')->name('customerNotification.index');
            Route::get('/customer-notification/filter', 'filter')->name('customerNotification.filter');
            Route::post('/customer-notification-send', 'send')->name('customerNotification.send');
        });

        // employee management route
        Route::controller(EmployeeManageController::class)->group(function () {
            Route::get('/employees', 'index')->name('employee.index');
            Route::get('/employee/create', 'create')->name('employee.create');
            Route::post('/employee/store', 'store')->name('employee.store');
            Route::put('/employee/{user}/update', 'update')->name('employee.update');
            Route::get('/employee/{user}/destroy', 'destroy')->name('employee.destroy');
            Route::post('employee/{user}/reset-password', 'resetPassword')->name('employee.reset-password');
            Route::get('/employee/{user}/permission', 'permission')->name('employee.permission');
            Route::post('/employee/{user}/permission', 'updatePermission')->name('employee.permission.update');
        });

        // role permission route
        Route::controller(RolePermissionController::class)->group(function () {
            Route::get('/roles', 'index')->name('role.index');
            Route::post('/role/store', 'store')->name('role.store');
            Route::put('/role/{role}/update', 'update')->name('role.update');
            Route::get('/role/{role}/destroy', 'destroy')->name('role.destroy');
            Route::get('/role/{role}/permission', 'rolePermission')->name('role.permission');
            Route::post('/role/{role}/permission', 'updateRolePermission')->name('role.permission.update');
        });

        // verification management route
        Route::controller(VerifyManageController::class)->group(function () {
            Route::get('/verifications', 'index')->name('verification.index');
            Route::post('/verification/update', 'update')->name('verification.update');
        });

        // social login route
        Route::controller(SocialAuthController::class)->group(function () {
            Route::get('/social-auth', 'index')->name('socialAuth.index');
            Route::post('/social-auth/{socialAuth}/update', 'update')->name('socialAuth.update');
            Route::get('/social-auth/{socialAuth}/toggle', 'toggle')->name('socialAuth.toggle');
        });

        // flash sale route
        Route::controller(FlashSaleController::class)->group(function () {
            Route::get('/flash-sales', 'index')->name('flashSale.index');
            Route::get('/flash-sale/create', 'create')->name('flashSale.create');
            Route::post('/flash-sale/store', 'store')->name('flashSale.store');
            Route::get('/flash-sale/{flashSale}/edit', 'edit')->name('flashSale.edit');
            Route::put('/flash-sale/{flashSale}/update', 'update')->name('flashSale.update');
            Route::get('/flash-sale/{flashSale}/product', 'show')->name('flashSale.product');
            Route::get('/flash-sale/{flashSale}/destroy', 'destroy')->name('flashSale.destroy');
            Route::get('/flash-sale/{flashSale}/toogle', 'statusToggle')->name('flashSale.toggle');
        });

        // google reCaptcha route
        Route::controller(GoogleReCaptchaController::class)->group(function () {
            Route::get('/google-re-captcha', 'index')->name('googleReCaptcha.index');
            Route::post('/google-re-captcha', 'update')->name('googleReCaptcha.update');
        });

        // vat and tax route
        Route::controller(VatTaxController::class)->group(function () {
            Route::get('/vat-taxes', 'index')->name('vatTax.index');
            Route::post('/vat-taxe/order-tax', 'orderTaxUpdate')->name('vatTax.order.update');
            Route::Post('/vat-taxe/store', 'store')->name('vatTax.store');
            Route::put('/vat-taxe/{vatTax}/update', 'update')->name('vatTax.update');
            Route::get('/vat-taxe/{vatTax}/toggle', 'toggle')->name('vatTax.toggle');
            Route::get('/vat-taxe/{vatTax}/destroy', 'destroy')->name('vatTax.destroy');
        });

        // Package Management
        Route::controller(PackageController::class)->group(function () {
            Route::get('/packages', 'index')->name('packages.index');
            Route::get('/packages/create', 'create')->name('packages.create');
            Route::post('/packages', 'store')->name('packages.store');
            Route::get('/packages/{package}/edit', 'edit')->name('packages.edit');
            Route::put('/packages/{package}', 'update')->name('packages.update');
            Route::delete('/packages/{package}', 'destroy')->name('packages.destroy');
            Route::get('/packages/{package}/toggle', 'toggle')->name('packages.toggle');
            // Package Payments
            Route::get('/package/payments', 'payments')->name('package.payments');
            Route::post('/package/payment/{payment}/approve', 'approvePayment')->name('package.payment.approve');
            Route::post('/package/payment/{payment}/reject', 'rejectPayment')->name('package.payment.reject');
        });

        // Delivery Weight routes
        Route::group(['prefix' => 'delivery-weight', 'as' => 'delivery-weight.'], function () {
            Route::get('/', [DeliveryWeightController::class, 'index'])->name('index');
            Route::post('/', [DeliveryWeightController::class, 'store'])->name('store');
            Route::put('/{deliveryWeight}', [DeliveryWeightController::class, 'update'])->name('update');
            Route::delete('/{deliveryWeight}', [DeliveryWeightController::class, 'destroy'])->name('destroy');
        });

        // Logistics Management
        Route::controller(LogisticsController::class)->group(function () {
            Route::get('/logistics', 'index')->name('logistics.index');
            Route::get('/logistics/create', 'create')->name('logistics.create');
            Route::post('/logistics', 'store')->name('logistics.store');
            Route::get('/logistics/{logistics}/edit', 'edit')->name('logistics.edit');
            Route::put('/logistics/{logistics}', 'update')->name('logistics.update');
            Route::delete('/logistics/{logistics}', 'destroy')->name('logistics.destroy');
        });

        // Frontend Translations
        Route::controller(FrontendTranslationController::class)->group(function () {
            Route::get('/frontend-translations', 'index')->name('frontend-translations.index');
            Route::get('/frontend-translations/{locale}/edit', 'edit')->name('frontend-translations.edit');
            Route::post('/frontend-translations/{locale}', 'update')->name('frontend-translations.update');
            Route::delete('/frontend-translations/{locale}/{key}', 'destroy')->name('frontend-translations.destroy');
        });
    });
});

        // Categories
       