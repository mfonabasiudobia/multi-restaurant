<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\Gateway\Bkash\ExecutePaymentController;
use App\Http\Controllers\Gateway\PaymentGatewayController;
use App\Http\Controllers\Gateway\PayTabs\ProcessController;
use App\Http\Controllers\PassportStorageSupportController;
use App\Http\Controllers\Shop\Auth\LoginController;
use App\Http\Controllers\Shop\Auth\RegisterController;
use App\Http\Controllers\Shop\NotificationController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ProfileController;
use App\Http\Controllers\Admin\PackageController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Change language
Route::get('/change-language', function () {
    if (request()->language) {
        App::setLocale(request()->language);
        session()->put('locale', request()->language);
    }

    return back();
})->name('change.language');

// Install Passport and storage routes
Route::controller(PassportStorageSupportController::class)->group(function () {
    Route::get('/install-passport', 'index')->name('passport.install.index');
    Route::get('/seeder-run', 'seederRun')->name('seeder.run.index');
    Route::get('/storage-install', 'storageInstall')->name('storage.install.index');
});

Route::controller(AuthController::class)->group(function () {
    Route::get('/auth/{provider}/callback', 'callback');
    Route::post('/auth/{provider}/callback', 'callback');
});

// Payment gateway routes
Route::controller(PaymentGatewayController::class)->group(function () {
    //payment routes
    Route::get('/order/{payment}/payment', 'payment')->name('order.payment');

    //success and cancel routes for payment
    Route::get('/order/{payment}/payment/success', 'paymentSuccess')->name('order.payment.success');
    Route::get('/order/{payment}/payment/cancel', 'paymentCancel')->name('order.payment.cancel');

    //success and cancel routes for callback
    Route::get('/payment/{payment}/callback-success', 'success')->name('payment.success');
    Route::get('/payment/{payment}/callback-cancel', 'cancel')->name('payment.cancel');

    //success and cancel routes for callback
    Route::post('/payment/{payment}/callback-success', 'success')->name('payment.success');
    Route::post('/payment/{payment}/callback-cancel', 'cancel')->name('payment.cancel');
});

// Bkash Payment execute
Route::get('/bkash-payment/{payment}/execute', [ExecutePaymentController::class, 'index'])->name('bkash.payment.execute');

// Paytabs payment execute
// Route::get('/paytabs/{payment}/callback', [ProcessController::class, 'callback'])->name('paytabs.payment.callback');
Route::post('/paytabs/{payment}/callback', [ProcessController::class, 'callback'])->name('paytabs.payment.callback');

// Shop Registration Routes
Route::get('shop/register', [RegisterController::class, 'create'])->name('shop.register');
Route::post('shop/register', [RegisterController::class, 'store'])->name('shop.register.store');

// Admin notification routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('notifications', [App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notification.show');
    Route::get('notifications/read-all', [App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notification.readAll');
    Route::get('profile', 'Admin\ProfileController@index')->name('profile.index');
    Route::post('package/payment/{id}/approve', [PackageController::class, 'approvePayment'])
        ->name('admin.package.payment.approve');
    Route::post('package/payment/{id}/reject', [PackageController::class, 'rejectPayment'])
        ->name('admin.package.payment.reject');
   
    // Add other notification routes as needed
});




// Shop notification routes
Route::middleware(['auth', 'role:shop|root'])->prefix('shop')->name('shop.')->group(function () {
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    
    // Fix: Change the controller namespace from Admin to Shop
    Route::get('package/payment/stripe/success', [\App\Http\Controllers\Shop\PackageController::class, 'stripeSuccess'])
        ->name('package.stripe.success');
    Route::get('package/payment/stripe/cancel', [\App\Http\Controllers\Shop\PackageController::class, 'stripeCancel'])
        ->name('package.stripe.cancel');
    // Add other notification routes as needed
});

// handle frontend page load
Route::get('/{any}', function () {
    
    

    // manage admin and shop routes
    if (request()->is('admin/*', 'shop/*')) {
        return abort(404);
    }

    return view('app');
})->where('any', '.*');

Route::middleware(['auth', 'check.package.limit'])->group(function () {
    Route::get('shop/product/create', [ProductController::class, 'create'])->name('shop.product.create');
    Route::post('shop/product/store', [ProductController::class, 'store'])->name('shop.product.store');
});


