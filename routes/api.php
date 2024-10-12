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
use App\Http\Controllers\SessionController;
use App\Models\Package;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/invoices/public/view/{id}', [InvoiceController::class, 'showPublicInvoice'])->name('invoice.public.show');
Route::get('/payex/paymentIntent/invoice/{invoiceId}', [PaymentController::class, 'paymentIntent']);
Route::post('/payex/paymentIntent/invoice/{invoiceId}/payment/success', [PaymentController::class, 'paymentSuccess']);

Route::get('/order/public/view/{id}', [OrderController::class, 'showOrderOverview']);
Route::post('/sms-otp/request', [OTPRequestController::class, 'requestOtp'])->name('otp.request');
Route::post('/sms-otp/verify', [OTPRequestController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/session/check', [SessionController::class, 'checkSession'])->name('session.check');

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('credential/verify', 'isAuthenticated');
});
         
Route::middleware('auth:sanctum')->group( function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/data', [MyController::class, 'getData']);
    Route::post('/logout', [AuthController::class, 'logout']);
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

    // Confirm Order
    Route::get('/orders/{id}/confirm', [OrderController::class, 'confirmOrder'])->name('orders.confirmOrder');

    // Change Invoice Link Status
    Route::put('/invoices/{invoiceId}/link/status/{status}', [InvoiceController::class, 'changeLinkStatus'])->name('invoice.status.change');

    Route::get('/test', function () {
        
        $package = Package::find(1);

        // $package->products()->attach(2);

        $newPackage = $package->products()->withPivot('created_at')->get();

        $package->products = $newPackage;

        return $package;
    });
});