<?php

//  route/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DefectInspectionFormController;
use App\Http\Controllers\DiscountFeeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobTaskController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\OTPRequestController;
use App\Http\Controllers\PMCategoryController;
use App\Http\Controllers\QCFormController;
use App\Http\Controllers\RegistrationFormController;
use App\Http\Controllers\RenoProgressController;
use App\Http\Controllers\UserController;
use App\Http\Resources\UserResource;
use App\Models\Package;
use App\Models\Sale;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Route::get('/user', function (Request $request) {
    return new UserResource($request->user());
})->middleware('auth:sanctum');

Route::get('/invoices/public/view/{id}', [InvoiceController::class, 'showPublicInvoice'])->name('invoice.public.show');
Route::get('/payex/paymentIntent/invoice/{invoiceId}', [PaymentController::class, 'paymentIntent']);
Route::post('/payex/paymentIntent/invoice/{invoiceId}/payment/success', [PaymentController::class, 'paymentSuccess']);

Route::post('/sms-otp/request/{encryptedMobile}', [OTPRequestController::class, 'requestOtp'])->name('otp.request');
Route::post('/sms-otp/verify/login', [OTPRequestController::class, 'verifyLoginOtp'])->name('otp.verify.login');
Route::post('/sms-otp/verify/', [OTPRequestController::class, 'verifyOtp'])->name('otp.verify');


Route::get('/owner/check/list/user/{phone}', [UserController::class, 'verifyExistsPhoneUser']);

// PUBLIC PROPERTIES
Route::get('/public/properties', [PropertyController::class, 'getPublicProperties']);
Route::post('/owner/reno-registration-form/overview/submit', [RegistrationFormController::class, 'submitForm']);

// Confirm Order
Route::get('/orders/{id}/confirm', [OrderController::class, 'confirmOrder'])->name('orders.confirmOrder');

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('operation/login', 'operationLogin');
    Route::post('credential/verify', 'isAuthenticated');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json(new UserResource($request->user()));
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');

    Route::get('/order/public/view/{id}', [OrderController::class, 'showOrderOverview']);
    Route::get('/owner/order/{id}', [OrderController::class, 'showOwnerOrder']);
    Route::get('/owner/orders', [OrderController::class, 'getOwnerOrders']);
    Route::get('/owner/form/reno-registration-forms', [RegistrationFormController::class, 'retrieveRegistrationForms']);
    Route::get('/owner/form/reno-registration-forms/{id}', [RegistrationFormController::class, 'showRegistrationForm']);

    // Change Invoice Link Status
    Route::put('/invoices/{invoiceId}/link/status/{status}', [InvoiceController::class, 'changeLinkStatus'])->name('invoice.status.change');

    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/product/category', PMCategoryController::class);
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
    Route::apiResource('/reno-progress', RenoProgressController::class);

    Route::get('/users/type/{type}', [UserController::class, 'getUsersWithType']);
    Route::get('/owner/reno-registration-form/{id}/status/approve', [RegistrationFormController::class, 'approveForm']);
    Route::get('/owner/reno-registration-form/{id}/status/reject', [RegistrationFormController::class, 'rejectForm']);

    Route::get('/reno-progress/{id}/task/{taskId}/supply/toggle', [JobTaskController::class, 'toggleSupplyStatus']);
    Route::get('/reno-progress/{id}/task/{taskId}/install/toggle', [JobTaskController::class, 'toggleInstallStatus']);
    Route::get('/reno-progress/{id}/task/{taskId}/status/{status}', [JobTaskController::class, 'changeTaskStatus']);
    Route::post('/reno-progress/{id}/task/{taskId}/owner-comment/change', [JobTaskController::class, 'changeOwnerComment']);
    Route::post('/reno-progress/{id}/task/{taskId}/internal-comment/change', [JobTaskController::class, 'changeInternalComment']);
    Route::post('/reno-progress/{id}/task/{taskId}/documents/upload', [JobTaskController::class, 'uploadDocuments']);
    Route::get('/reno-progress/{id}/task/{taskId}/documents/fetch', [JobTaskController::class, 'fetchTaskDocuments']);
    Route::get('/reno-progress/{id}/task/{taskId}/documents/{documentIndex}/remove', [JobTaskController::class, 'removeTaskDocument']);


    Route::get('/op/properties', [PropertyController::class, 'getOperationProperties']);
    Route::get('/op/reno/progress/{id}/properties', [RenoProgressController::class, 'getProgressFormDetail']);

    Route::post('/op/reno/qc-form/submit', [QCFormController::class, 'submitForm']);
    Route::get('/op/reno/qc-forms', [QCFormController::class, 'index']);
    Route::get('/op/reno/qc-forms/{id}/fetch', [QCFormController::class, 'fetch']);

    Route::post('/op/reno/defect-inspection-form/submit', [DefectInspectionFormController::class, 'submitForm']);
    Route::get('/op/reno/defect-inspection-form/{id}', [DefectInspectionFormController::class, 'show']);
    Route::get('/op/reno/defect-inspection-forms', [DefectInspectionFormController::class, 'index']);
    Route::get('/op/reno/defect-inspection-forms/{id}/fetch', [DefectInspectionFormController::class, 'fetch']);

    // TEST
    Route::get('/data', [MyController::class, 'getData']);
    Route::get('/test', function () {

        $package = Package::find(1);

        // $package->products()->attach(2);

        $newPackage = $package->products()->withPivot('created_at')->get();

        $package->products = $newPackage;

        return $package;
    });

    // DEV TOOLS
    Route::get('/dev/database/refresh', function () {
        Artisan::call('migrate:refresh --seed');

        return 'Database migrated and seeded successfully!';
    });

    Route::get('/dev/storage/clear', function () {

        try {
            $path = storage_path('app/public/uploads');

            if (File::exists($path)) {
                File::deleteDirectory($path);
            }

            return 'Uploads directory cleared successfully!';

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    });
});


Route::get('/tmp/progress/generate', function () {
    $sale = Sale::find(1);

    $sale->status = 'partial-paid';

    $sale->save();
});
