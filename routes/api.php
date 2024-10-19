<?php

//  route/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiscountFeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\OTPRequestController;
use App\Http\Controllers\RegistrationFormController;
use App\Http\Controllers\UserController;
use App\Models\Package;
use App\Models\RegistrationForm;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/invoices/public/view/{id}', [InvoiceController::class, 'showPublicInvoice'])->name('invoice.public.show');
Route::get('/payex/paymentIntent/invoice/{invoiceId}', [PaymentController::class, 'paymentIntent']);
Route::post('/payex/paymentIntent/invoice/{invoiceId}/payment/success', [PaymentController::class, 'paymentSuccess']);

Route::post('/sms-otp/request/{encryptedMobile}', [OTPRequestController::class, 'requestOtp'])->name('otp.request');
Route::post('/sms-otp/verify/login', [OTPRequestController::class, 'verifyLoginOtp'])->name('otp.verify.login');
Route::post('/sms-otp/verify/', [OTPRequestController::class, 'verifyOtp'])->name('otp.verify');


// PUBLIC PROPERTIES
Route::get('/public/properties', [PropertyController::class, 'getPublicProperties']);
Route::post('/owner/reno-registration-form/overview/submit', [RegistrationFormController::class, 'submitForm']);

// Confirm Order
Route::get('/orders/{id}/confirm', [OrderController::class, 'confirmOrder'])->name('orders.confirmOrder');

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('credential/verify', 'isAuthenticated');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');
    
    Route::get('/order/public/view/{id}', [OrderController::class, 'showOrderOverview']);
    Route::get('/owner/order/{id}', [OrderController::class, 'showOwnerOrder']);
    Route::get('/owner/orders', [OrderController::class, 'getOwnerOrders']);

    // Change Invoice Link Status
    Route::put('/invoices/{invoiceId}/link/status/{status}', [InvoiceController::class, 'changeLinkStatus'])->name('invoice.status.change');

    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/product/category', ProductCategoryController::class);
    Route::apiResource('/packages', PackageController::class);
    Route::apiResource('/quotations', QuotationController::class);
    Route::apiResource('/contacts', ContactController::class);
    Route::apiResource('/properties', PropertyController::class);
    Route::apiResource('/orders', OrderController::class);
    Route::apiResource('/sales', SaleController::class);
    Route::apiResource('/discountFees', DiscountFeeController::class);
    Route::apiResource('/invoices', InvoiceController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/owner/reno-registration-form', RegistrationFormController::class);

    // TEST
    Route::get('/data', [MyController::class, 'getData']);
    Route::get('/test', function () {
        
        $package = Package::find(1);

        // $package->products()->attach(2);

        $newPackage = $package->products()->withPivot('created_at')->get();

        $package->products = $newPackage;

        return $package;
    });
});