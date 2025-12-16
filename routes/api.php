<?php

//  route/api.php

use PgSql\Lob;
use App\Models\Sale;
use App\Models\User;
use App\Models\ApiKey;

use App\Models\RPMJob;
use App\Models\Package;
use App\Models\RPMTask;
use App\Models\RenoXSale;
use Illuminate\Support\Str;
use App\Models\RenoProgress;
use Illuminate\Http\Request;
use App\Events\SaleStatusUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use App\Models\DefectInspectionForm;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiskController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ApiKeyController;
use App\Http\Controllers\POItemController;
use App\Http\Controllers\QCFormController;
use App\Http\Controllers\RPMJobController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobTaskController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RPMTaskController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\PhaseJobController;
// use App\Http\Controllers\LarkAuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LarkEventController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RenoXSaleController;
use App\Http\Controllers\RPMTaskQCController;
use App\Http\Controllers\OTPRequestController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PMCategoryController;
use App\Http\Controllers\DiscountFeeController;
use App\Http\Controllers\KayaHeigFormController;
use App\Http\Controllers\RenoProgressController;
use App\Http\Controllers\ResourceItemController;
use App\Http\Controllers\KeyManagementController;
// use App\Http\Controllers\LarkSuiteAuthController;
use App\Http\Controllers\ProgressPhaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\InvestorInterestController;
use App\Http\Controllers\RegistrationFormController;
use App\Http\Controllers\UserItemPermissionController;
use App\Http\Controllers\DefectInspectionFormController;
use App\Http\Controllers\ProjectStatusHistoryController;

Route::get('/user', function (Request $request) {
    return new UserResource($request->user());
})->middleware('auth:sanctum');

Route::get('/invoices/public/view/{id}', [InvoiceController::class, 'showPublicInvoice'])->name('invoice.public.show');
Route::get('/payex/paymentIntent/invoice/{invoiceId}', [PaymentController::class, 'paymentIntent']);
Route::post('/payex/paymentIntent/invoice/{invoiceId}/payment/success', [PaymentController::class, 'paymentSuccess']);

Route::post('/sms-otp/request/{country_code}/{encryptedMobile}', [OTPRequestController::class, 'requestOtp'])->name('otp.request');
Route::post('/sms-otp/verify/login', [OTPRequestController::class, 'verifyLoginOtp'])->name('otp.verify.login');
Route::post('/sms-otp/verify/', [OTPRequestController::class, 'verifyOtp'])->name('otp.verify');


Route::get('/owner/check/list/user/{country_code}/{phone}', [UserController::class, 'verifyExistsPhoneUser']);

// PUBLIC PROPERTIES
Route::get('/public/properties', [PropertyController::class, 'getPublicProperties']);
Route::post('/owner/quotation-request-form/overview/submit', [RegistrationFormController::class, 'submitForm']);

// Confirm Order
Route::get('/orders/{id}/confirm', [OrderController::class, 'confirmOrder'])->name('orders.confirmOrder');
Route::get('/orders/{id}/release', [OrderController::class, 'releaseOrder'])->name('orders.releaseOrder');


Route::get('defect-inspection-forms/public/{hashedString}', [DefectInspectionFormController::class, 'publicShow']);

Route::post("/investor-interest-form", [InvestorInterestController::class, 'store']);
Route::post("/kaya-heig-form", [KayaHeigFormController::class, 'store']);

Route::get("/reno-progress/{id}/rpm/acknowledge", [RenoProgressController::class, 'acknowledgedByRPM']);

Route::get('/public/campaigns/{id}', [CampaignController::class, 'showPublic']);

Route::get('/public/bookings/validate', [BookingController::class, 'validateBooking']);

Route::post('/public/campaigns/{campaignSlug}/booking/payment/intent', [PaymentController::class, 'paymentIntentBooking']);
Route::post('/public/campaigns/{campaignSlug}/booking/payment/success/process', [PaymentController::class, 'paymentBookingProcess']);
Route::post('/public/campaigns/{campaignSlug}/booking/payment/success', [PaymentController::class, 'paymentSuccessBooking']);
Route::post('/public/campaigns/{campaignSlug}/booking/payment/error', [PaymentController::class, 'paymentErrorBooking']);

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('owner/staff/login', 'staffLoginToOwner');
    Route::post('operation/login', 'operationLogin');
    Route::post('credential/verify', 'isAuthenticated');
    Route::post('vendor/login', 'vendorLogin');

    Route::get('generate-owner-token', 'getOwnerToken');
    Route::get('obtain-login-token', 'ownerLoginWithToken');
});

// OAuth routes
// Route::get('/auth/larksuite', [LarkSuiteAuthController::class, 'redirectToLarkSuite']);
// Route::get('/auth/larksuite/callback', [LarkSuiteAuthController::class, 'handleLarkSuiteCallback']);
// Route::get('/auth/larksuite/debug', [LarkSuiteAuthController::class, 'debugSession']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json(new UserResource($request->user()));
    });

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');

    Route::get('/order/public/view/{id}', [OrderController::class, 'showOrderOverview']);
    Route::get('/owner/order/{id}', [OrderController::class, 'showOwnerOrder']);
    Route::get('/owner/orders', [OrderController::class, 'getOwnerOrders']);
    Route::put('/owner/orders/{id}/addon-packages/update', [OrderController::class, 'updateOwnerAddonPackages']);
    Route::get('/owner/orders/{id}/addon-packages/{package_id}/toggle', [OrderController::class, 'toggleOwnerAddonPackage']);
    Route::get('/owner/form/reno-registration-forms', [RegistrationFormController::class, 'retrieveRegistrationForms']);
    Route::get('/owner/form/reno-registration-forms/{id}', [RegistrationFormController::class, 'showRegistrationForm']);
    Route::get('/owner/reno/progresses', [RenoProgressController::class, 'ownerIndex']);
    Route::get('/owner/reno/progresses/{id}', [RenoProgressController::class, 'showOwnerRenoProgress']);
    Route::get('/owner/reno/progresses/{renoProgressId}/phase/{phase}/attachments', [ProgressPhaseController::class, 'retrieveRenoProgressPhaseAttachments']);
    Route::get('/owner/reno/progresses/{renoProgressId}/job/{jobId}/attachments', [PhaseJobController::class, 'retrieveJobAttachments']);

    // Change Invoice Link Status
    Route::put('/invoices/{invoiceId}/link/status/{status}', [InvoiceController::class, 'changeLinkStatus'])->name('invoice.status.change');
    Route::put('/invoices/{invoiceId}/paid', [InvoiceController::class, 'markAsPaid']);
    Route::post('/invoices/{invoiceId}/payment/save', [InvoiceController::class, 'savePaymentDetail']);
    Route::get('/invoices/{invoiceId}/payments/{paymentId}', [InvoiceController::class, 'getPaymentDetail']);

    Route::apiResource('/permissions', PermissionController::class);
    Route::apiResource('/user-item-permissions', UserItemPermissionController::class);
    Route::apiResource('/products', ProductController::class);
    Route::apiResource('/product/category', PMCategoryController::class);
    Route::apiResource('/packages', PackageController::class);
    Route::apiResource('/quotations', QuotationController::class);
    Route::apiResource('/contacts', ContactController::class);
    Route::apiResource('/properties', PropertyController::class);
    Route::apiResource('/orders', OrderController::class);
    Route::apiResource('/sales', SaleController::class);
    Route::apiResource('/reno-sales', RenoXSaleController::class);
    Route::apiResource('/discountFees', DiscountFeeController::class);
    Route::apiResource('/invoices', InvoiceController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/owner/quotation-request-form', RegistrationFormController::class);
    Route::apiResource("/investor-interest-forms", InvestorInterestController::class);
    Route::apiResource("/kaya-heig-forms", KayaHeigFormController::class);
    Route::apiResource('/reno-progress', RenoProgressController::class);
    Route::apiResource('/project-status-history', ProjectStatusHistoryController::class);
    Route::apiResource('/otp-requests', OTPRequestController::class);
    Route::apiResource('/purchase-orders', PurchaseOrderController::class);
    Route::apiResource('/inventory', InventoryController::class);
    Route::apiResource('/defect-inspection-forms', DefectInspectionFormController::class);
    Route::apiResource('/key-management', KeyManagementController::class);
    Route::apiResource('/campaigns', CampaignController::class);
    Route::apiResource('/bookings', BookingController::class);

    // API Key Management Routes
    Route::apiResource('/api-keys', ApiKeyController::class);
    Route::post('/api-keys/{id}/regenerate', [ApiKeyController::class, 'regenerate'])->name('api-keys.regenerate');

    Route::get('users/{id}/password/reset', [UserController::class, 'resetPassword']);
    Route::get('users/{id}/deactivate', [UserController::class, 'deactivateUser']);

    Route::get('/users/type/{type}', [UserController::class, 'getUsersWithType']);
    Route::get('/owner/quotation-request-form/{id}/status/approve', [RegistrationFormController::class, 'approveForm']);
    Route::get('/owner/quotation-request-form/{id}/status/reject', [RegistrationFormController::class, 'rejectForm']);

    Route::post('/properties/{id}/update', [PropertyController::class, 'updatePropertyWithFiles']);

    Route::get('/reno-progress/table/advance', [RenoProgressController::class, 'getAdvanceTable']);
    Route::get('/reno-progress/{id}/task/{taskId}/supply/toggle', [JobTaskController::class, 'toggleSupplyStatus']);
    Route::get('/reno-progress/{id}/task/{taskId}/install/toggle', [JobTaskController::class, 'toggleInstallStatus']);
    Route::get('/reno-progress/{id}/task/{taskId}/status/{status}', [JobTaskController::class, 'changeTaskStatus']);
    Route::post('/reno-progress/{id}/task/{taskId}/owner-comment/change', [JobTaskController::class, 'changeOwnerComment']);
    Route::post('/reno-progress/{id}/task/{taskId}/internal-comment/change', [JobTaskController::class, 'changeInternalComment']);
    Route::post('/reno-progress/{id}/task/{taskId}/documents/upload', [JobTaskController::class, 'uploadDocuments']);
    Route::post('/reno-progress/{id}/task/{taskId}/document/upload', [JobTaskController::class, 'uploadTaskImage']);
    Route::post('/reno-progress/{id}/task/{taskId}/documents/external/upload', [JobTaskController::class, 'uploadExternalDocuments']);
    Route::post('/reno-progress/{id}/task/{taskId}/document/external/upload', [JobTaskController::class, 'uploadTaskExternalImage']);
    Route::get('/reno-progress/{id}/task/{taskId}/documents/fetch', [JobTaskController::class, 'fetchTaskDocuments']);
    Route::get('/reno-progress/{id}/task/{taskId}/documents/{documentIndex}/remove', [JobTaskController::class, 'removeTaskDocument']);
    Route::get('/reno-progress/{id}/task/{taskId}/documents/external/{documentIndex}/remove', [JobTaskController::class, 'removeExternalTaskDocument']);
    Route::post('/reno-progress/{id}/task/{taskId}/comments', [JobTaskController::class, 'changeComments']);
    Route::get('/reno-progress/{id}/key-management', [KeyManagementController::class, 'getRenoKeyManagement']);

    Route::get('/rpm-task/{id}/status/{status}', [RPMTaskController::class, 'updateStatus']);
    Route::put('/rpm-task/{id}/comment/internal', [RPMTaskController::class, 'updateInternalComment']);
    Route::put('/rpm-task/{id}/comment/external', [RPMTaskController::class, 'updateExternalComment']);
    Route::post('/rpm-task/{id}/attachment/internal/upload', [RPMTaskController::class, 'uploadInternalAttachment']);
    Route::post('/rpm-task/{id}/attachment/external/upload', [RPMTaskController::class, 'uploadExternalAttachment']);
    Route::get('/rpm-task/{id}/attachment/internal/{index}/remove', [RPMTaskController::class, 'removeInternalAttachment']);
    Route::get('/rpm-task/{id}/attachment/external/{index}/remove', [RPMTaskController::class, 'removeExternalAttachment']);

    Route::get('/rpm-task-qc/{id}/status/{status}', [RPMTaskQCController::class, 'updateStatus']);
    Route::put('/rpm-task-qc/{id}/comment/internal', [RPMTaskQCController::class, 'updateComment']);
    Route::post('/rpm-task-qc/{id}/attachment/internal/upload', [RPMTaskQCController::class, 'uploadAttachment']);
    Route::get('/rpm-task-qc/{id}/attachment/internal/{index}/remove', [RPMTaskQCController::class, 'removeAttachment']);


    Route::get('/op/reno/progresses', [RenoProgressController::class, 'operationIndex']);

    Route::get('/op/properties', [PropertyController::class, 'getOperationProperties']);
    Route::get('/op/reno/progress/{id}/properties', [RenoProgressController::class, 'getProgressFormDetail']);
    Route::get('/op/reno/progress/{id}', [RenoProgressController::class, 'show']);

    Route::post('/op/reno/qc-form/submit', [QCFormController::class, 'submitForm']);
    Route::get('/op/reno/qc-forms', [QCFormController::class, 'index']);
    Route::get('/op/reno/qc-forms/{id}/fetch', [QCFormController::class, 'fetch']);

    Route::get('/op/reno/defect-inspection-form/{id}/submit', [DefectInspectionFormController::class, 'submitForm']);
    Route::get('/op/reno/defect-inspection-form/{id}', [DefectInspectionFormController::class, 'show']);
    Route::get('/op/reno/defect-inspection-forms', [DefectInspectionFormController::class, 'index']);
    Route::get('/op/reno/defect-inspection-forms/{id}/fetch', [DefectInspectionFormController::class, 'fetch']);

    Route::post('/op/reno/defect-inspection-forms/{id}/save', [DefectInspectionFormController::class, 'liveUpdateForm']);
    Route::post('/op/reno/defect-inspection-forms/{id}/attachment/remove', [DefectInspectionFormController::class, 'removeAttachment']);

    Route::post('/di-forms/addDIF', [DefectInspectionFormController::class, 'addDIF']);
    Route::get('/di-forms/{id}', [DefectInspectionFormController::class, 'show']);
    Route::get('/di-forms/{id}/generate', [DefectInspectionFormController::class, 'generateDIForm']);


    Route::post('/orders/{id}/internal-remark/update', [OrderController::class, 'updateInternalRemark']);
    Route::get('/orders/{id}/re-release', [OrderController::class, 'reReleaseOrder']);
    Route::get('/orders/{id}/void', [OrderController::class, 'voidOrder']);
    Route::get('/orders/{id}/toggle-is-be-powered', [OrderController::class, 'toggleIsBePowered']);

    Route::get('/sales/without-reno/{property_id}', [SaleController::class, 'getSaleWithoutReno']);

    Route::post('/reno-progress/{id}/contractual/overall/date', [RenoProgressController::class, 'changeContractualDate']);
    Route::post('/reno-progress/{id}/contractual/p1/date', [RenoProgressController::class, 'changeContractualP1Date']);
    Route::post('/reno-progress/{id}/contractual/p2/date', [RenoProgressController::class, 'changeContractualP2Date']);
    Route::post('/reno-progress/{id}/contractual/qc/date', [RenoProgressController::class, 'changeContractualQCDate']);
    Route::post('/reno-progress/{id}/contractual/pc/date', [RenoProgressController::class, 'changeContractualPCDate']);
    Route::post('/reno-progress/{id}/contractual/handover/date', [RenoProgressController::class, 'changeContractualHandoverDate']);

    Route::post('/reno-progress/{id}/contractor/overall/date', [RenoProgressController::class, 'changeContractorDate']);
    Route::post('/reno-progress/{id}/contractor/p1/date', [RenoProgressController::class, 'changeContractorP1Date']);
    Route::post('/reno-progress/{id}/contractor/p2/date', [RenoProgressController::class, 'changeContractorP2Date']);
    Route::post('/reno-progress/{id}/contractor/qc/date', [RenoProgressController::class, 'changeContractorQCDate']);
    Route::post('/reno-progress/{id}/contractor/pc/date', [RenoProgressController::class, 'changeContractorPCDate']);
    Route::post('/reno-progress/{id}/contractor/handover/date', [RenoProgressController::class, 'changeContractorHandoverDate']);
    Route::post('/reno-progress/{id}/general-permission', [RenoProgressController::class, 'changeGeneralPermission']);
    Route::get('/reno-progress/{id}/send-reno-to-lark', [RenoProgressController::class, 'sendRenoToLark']);

    Route::get('/reno-progress/{id}/task/{taskId}/visibility/toggle', [JobTaskController::class, 'toggleTaskVisibility']);

    Route::get('/reno-progress/{id}/convert/v3', [RenoProgressController::class, 'convertV2toV3']);
    Route::get('/reno-progress/{id}/old-ver', [RenoProgressController::class, 'showOldVersion']);
    Route::post('/reno-progress/{id}/date-management/change', [RenoProgressController::class, 'changeDateManagement']);
    Route::put('/reno-progress/{id}/status', [RenoProgressController::class, 'updateRenoProgressStatus']);
    Route::post('/reno-progress/generate', [RenoProgressController::class, 'createRenoProgress']);

    Route::get('/reno-progress/{id}/owner-handover/release', [RenoProgressController::class, 'releaseOwnerHandover']);
    Route::get('/reno-progress/{id}/owner-handover/submit', [RenoProgressController::class, 'submitOwnerHandover']);

    Route::get('/purchase-orders/table/advance', [PurchaseOrderController::class, 'getAdvanceTable']);
    Route::get('/purchase-orders/{id}/delivery/status/delivered', [POItemController::class, 'markAsDelivered']);
    Route::get('/purchase-orders/{id}/order/status/released', [PurchaseOrderController::class, 'releasePO']);
    Route::get('/purchase-orders/{id}/order/status/accepted', [PurchaseOrderController::class, 'acceptPO']);
    Route::get('/purchase-orders/{id}/order/status/rejected', [PurchaseOrderController::class, 'rejectPO']);
    Route::get('/purchase-orders/{id}/order/status/unreleased', [PurchaseOrderController::class, 'revertPO']);
    Route::get('/purchase-orders/sale/{saleId}', [PurchaseOrderController::class, 'getPurchaseOrderBySaleId']);
    // Route::post('/purchase-orders/invoice/create', [InvoiceController::class, 'storePOInvoice']);

    Route::post('/products/{id}/attachments/thumbnail/change', [ProductController::class, 'changeThumbnail']);
    Route::get('/products/{id}/attachments/photos/{photoIndex}/remove', [ProductController::class, 'removeProductPhoto']);
    Route::post('/products/{id}/attachments/photos/upload', [ProductController::class, 'uploadProductPhotos']);
    Route::get('/products/{id}/archive', [ProductController::class, 'archiveProduct']);
    Route::get('/products/{id}/restore', [ProductController::class, 'restoreProduct']);
    Route::get('/products/index/archived', [ProductController::class, 'indexArchived']);

    Route::get('packages/{id}/archive', [PackageController::class, 'archivePackage']);
    Route::get('packages/{id}/restore', [PackageController::class, 'restorePackage']);
    Route::get('packages/index/archived', [PackageController::class, 'indexArchived']);

    Route::get('quotations/{id}/archive', [QuotationController::class, 'archiveQuotation']);
    Route::get('quotations/{id}/restore', [QuotationController::class, 'restoreQuotation']);
    Route::get('quotations/index/archived', [QuotationController::class, 'indexArchived']);

    Route::get('defect-inspection-forms/{id}/completed', [DefectInspectionFormController::class, 'markAsCompleted']);
    Route::get('defect-inspection-forms/{id}/report-link/toggle', [DefectInspectionFormController::class, 'toggleDIReportLink']);

    Route::get('key-management/{keyManagementId}/{category}/add', [KeyManagementController::class, 'addCategoryItem']);
    Route::get('key-management/{keyManagementId}/{category}/add', [KeyManagementController::class, 'addCategoryItem']);
    Route::post('key-management/{keyManagementId}/{category}/change/{itemIndex}/name', [KeyManagementController::class, 'changeKeyManagementItemName']);
    Route::post('key-management/{keyManagementId}/{category}/change/{itemIndex}/remark', [KeyManagementController::class, 'changeKeyManagementItemRemark']);
    Route::post('key-management/{keyManagementId}/{category}/upload/{itemIndex}/photo', [KeyManagementController::class, 'uploadKeyManagementItemPhoto']);
    Route::post('key-management/{keyManagementId}/{category}/change/{itemIndex}/photo', [KeyManagementController::class, 'changeKeyManagementItemPhoto']);
    Route::get('key-management/{keyManagementId}/{category}/remove/{itemIndex}', [KeyManagementController::class, 'removeKeyManagementItem']);
    Route::post('key-management/{keyManagementId}/info/update', [KeyManagementController::class, 'updateKeyManagementInfo']);
    Route::post('key-management/{keyManagementId}/quantity/update', [KeyManagementController::class, 'updateKeyCategoryQuantity']);

    Route::get('campaigns/{campaignId}/bookings', [BookingController::class, 'getBookingByCampaign']);
    Route::post('campaigns/{campaignId}/update', [CampaignController::class, 'update']);

    Route::post('resource-items/add/user/permission', [ResourceItemController::class, 'createUserPermission']);
    Route::post('resource-items/{userId}/{itemId}/permission', [ResourceItemController::class, 'changeUserPermission']);
    Route::delete('resource-items/{userId}/{itemId}', [ResourceItemController::class, 'deleteUserPermission']);


    // TEST
    Route::get('/data', [MyController::class, 'getData']);
});

// API Key Management Routes
Route::middleware('auth.api_key')->get('/v1/user', function () {
    return response()->json(['user' => auth()->user()]);
});
Route::middleware('auth.api_key')->group(function () {
    // API Key Management Routes
    // Get owner order by uuid
    Route::get('/v1/owner/{uuid}/orders', [OrderController::class, 'showOwnerOrdersByUuid']);
    Route::get('/v1/owner/{uuid}/project-trackers', [RenoProgressController::class, 'showOwnerProjectTrackersByUuid']);
    Route::get('/v1/products', [ProductController::class, 'showByName']);
    Route::get('/v1/products/{id}', [ProductController::class, 'showById']);
    Route::get('/v1/owner/{uuid}/reno-progress', [RenoProgressController::class, 'showByOwnerUuid']);
    Route::get('/v1/owner/{uuid}/reno-progress/{id}', [RPMJobController::class, 'showByJobName']);

    Route::get('/v1/manager/project-trackers', [RenoProgressController::class, 'managerIndex']);
    Route::get('/v1/manager/project-trackers/{id}', [RenoProgressController::class, 'managerShowById']);

    // WIP
    Route::get('/v1/owners', [UserController::class, 'showOwners']);
    Route::get('/v1/owner', [UserController::class, 'showByParams']);
});

// Laark Event Routes
Route::post('/v1/lark/event', [LarkEventController::class, 'handleEvent']);

Route::get('/test/{id}', [TestController::class, 'test']);


Route::get('/disk', [DiskController::class, 'index']);

// Emergency route
// Route::get('/tmp/startSaleRenoProgress/saleId/{saleId}', function ($saleId) {
//     // Get sale by saleId
//     $sale = Sale::find($saleId);

//     if (!$sale) {
//         return response()->json(['error' => 'Sale not found'], 404);
//     }

//     event(new SaleStatusUpdated($sale));

//     return response()->json(['message' => 'Sale status updated successfully']);
// });

// Route::get('/tmp/changeSaleStatus/{saleId}', function ($saleId) {
//     // Get sale by saleId
//     $sale = Sale::find($saleId);

//     if (!$sale) {
//         return response()->json(['error' => 'Sale not found'], 404);
//     }

//     $sale->status = 'partial-paid';
//     $sale->save();

//     return response()->json(['message' => 'Sale status updated successfully']);
// });

Route::get('/tmp/update-reno-x-sale-info', function () {
    $sales = Sale::with('order')->get();

    // Group by property_id, block, floor, unit_no
    $grouped = $sales->groupBy(function ($sale) {
        $order = $sale->order;
        return $order->property_id . '|' . $order->block . '|' . $order->floor . '|' . $order->unit_no;
    });

    $tmpArr = [];

    foreach ($grouped as $groupKey => $groupSales) {
        $firstSale = $groupSales->first();
        $order = $firstSale->order;

        // Check if reno_sale_id already exists
        $existingRenoSaleId = $groupSales->pluck('reno_sale_id')->filter()->first();


        if ($existingRenoSaleId) {
            $foundedRenoSale = RenoXSale::find($existingRenoSaleId);

            $foundedRenoSale->user_id = $order->user_id;
            $foundedRenoSale->property_id = $order->property_id;
            $foundedRenoSale->unit_type = $order->unit_type;
            $foundedRenoSale->block = $order->block;
            $foundedRenoSale->floor = $order->floor;
            $foundedRenoSale->unit_no = $order->unit_no;
            $foundedRenoSale->save();

            $tmpArr[] = $foundedRenoSale;
        }
    }


    return response()->json(['Updated RenoXSales' => $tmpArr]);

    // DB::beginTransaction();

    // try {
    //     foreach ($grouped as $groupKey => $groupSales) {
    //         $firstSale = $groupSales->first();
    //         $order = $firstSale->order;

    //         // Check if reno_sale_id already exists
    //         $existingRenoSaleId = $groupSales->pluck('reno_sale_id')->filter()->first();

    //         if (!$existingRenoSaleId) {
    //             // Generate next reno_sale_no
    //             $lastReno = RenoXSale::withTrashed()->latest('id')->first();
    //             $lastNumber = $lastReno ? (int)substr($lastReno->reno_sale_no, 4) : 2500000;
    //             $newNumber = $lastNumber + 1;
    //             $formattedNo = 'RXS-' . $newNumber;

    //             $renoSale = RenoXSale::create([
    //                 'reno_sale_no' => $formattedNo,
    //             ]);

    //             $renoSaleId = $renoSale->id;
    //         } else {
    //             $renoSaleId = $existingRenoSaleId;
    //         }

    //         // Assign to each Sale
    //         foreach ($groupSales as $sale) {
    //             $sale->reno_sale_id = $renoSaleId;
    //             $sale->save();
    //         }
    //     }

    //     DB::commit();
    //     return "RenoXSales created and linked successfully.";
    // } catch (\Exception $e) {
    //     DB::rollBack();
    //     return response()->json(['error' => $e->getMessage()], 500);
    // }
});

// Route::get('/tmp/get-sales-with-no-reno/{property_id}', [SaleController::class, 'getSaleWithoutReno']);

// Route::get('/test/unauth/{renoId}/{saleId}', function ($renoId, $saleId) {

//     $renoProgress = \App\Models\RenoProgress::find($renoId);
//     $sale = \App\Models\Sale::find($saleId);

//     $metadata = [
//         'yard' => [
//             'q1' => ['value' => '', 'remark' => null],
//             'q2' => ['value' => '', 'remark' => null],
//             'q3' => ['value' => '', 'remark' => null],
//             'q4' => ['value' => '', 'remark' => null],
//             'q5' => ['value' => '', 'remark' => null],
//             'q6' => ['value' => '', 'remark' => null],
//         ],
//         'foyer' => [
//             'q1' => ['value' => '', 'remark' => null],
//             'q2' => ['value' => '', 'remark' => null],
//             'q3' => ['value' => '', 'remark' => null],
//             'q4' => ['value' => '', 'remark' => null],
//         ],
//         'living' => [
//             'q1' => ['value' => '', 'remark' => null],
//             'q2' => ['value' => '', 'remark' => null],
//             'q3' => ['value' => '', 'remark' => null],
//             'q4' => ['value' => '', 'remark' => null],
//             'q5' => ['value' => '', 'remark' => null],
//             'q6' => ['value' => '', 'remark' => null],
//             'q7' => ['value' => '', 'remark' => null],
//             'q8' => ['value' => '', 'remark' => null],
//             'q9' => ['value' => '', 'remark' => null],
//         ],
//         'balcony' => [
//             'q1' => ['value' => '', 'remark' => null],
//             'q2' => ['value' => '', 'remark' => null],
//             'q3' => ['value' => '', 'remark' => null],
//             'q4' => ['value' => '', 'remark' => null],
//         ],
//         'hallway' => [
//             'q1' => ['value' => '', 'remark' => null],
//             'q2' => ['value' => '', 'remark' => null],
//             'q3' => ['value' => '', 'remark' => null],
//             'q4' => ['value' => '', 'remark' => null],
//         ],
//         'kitchen' => [
//             'q1' => ['value' => '', 'remark' => null],
//             'q2' => ['value' => '', 'remark' => null],
//             'q3' => ['value' => '', 'remark' => null],
//             'q4' => ['value' => '', 'remark' => null],
//             'q5' => ['value' => '', 'remark' => null],
//             'q6' => ['value' => '', 'remark' => null],
//             'q7' => ['value' => '', 'remark' => null],
//             'q8' => ['value' => '', 'remark' => null],
//         ],
//         'bedrooms' => [],
//         'bathrooms' => [],
//     ];

//     // Generate bedroom entries dynamically
//     $bedroomTemplate = [
//         'q1' => ['value' => '', 'remark' => null],
//         'q2' => ['value' => '', 'remark' => null],
//         'q3' => ['value' => '', 'remark' => null],
//         'q4' => ['value' => '', 'remark' => null],
//         'q5' => ['value' => '', 'remark' => null],
//         'q6' => ['value' => '', 'remark' => null],
//         'q7' => ['value' => '', 'remark' => null],
//         'q8' => ['value' => '', 'remark' => null],
//         'q9' => ['value' => '', 'remark' => null],
//     ];

//     for ($i = 1; $i <= $sale->order->bedroom_count; $i++) {
//         $metadata['bedrooms']["bedroom{$i}"] = $bedroomTemplate;
//     }

//     // Generate bathroom entries dynamically
//     $bathroomTemplate = [
//         'q1' => ['value' => '', 'remark' => null],
//         'q2' => ['value' => '', 'remark' => null],
//         'q3' => ['value' => '', 'remark' => null],
//         'q4' => ['value' => '', 'remark' => null],
//         'q5' => ['value' => '', 'remark' => null],
//         'q6' => ['value' => '', 'remark' => null],
//         'q7' => ['value' => '', 'remark' => null],
//         'q8' => ['value' => '', 'remark' => null],
//         'q9' => ['value' => '', 'remark' => null],
//     ];

//     for ($i = 1; $i <= $sale->order->bathroom_count; $i++) {
//         $metadata['bathrooms']["bathroom{$i}"] = $bathroomTemplate;
//     }


//     // Generate hashed link for report
//     $randomString = Str::random(32); // 32-character random string
//     $base64String = base64_encode($randomString);
//     $base64UrlString = str_replace(['+', '/', '='], ['-', '_', ''], $base64String);

//     $form = DefectInspectionForm::create([
//         'reno_progress_id' => $renoProgress->id,
//         'property_name' => $sale->order->property_id,
//         'owner_email' => $sale->order->user->email,
//         'other_property_name' => null,
//         'block' => $sale->order->block,
//         'level' => $sale->order->floor,
//         'unit' => $sale->order->unit_no,
//         'status' => 'not_submitted',
//         'bedroom_count' => $sale->order->bedroom_count,
//         'bathroom_count' => $sale->order->bathroom_count,
//         'metadata' => json_encode($metadata),
//         'report_hash' => $base64UrlString
//     ]);
// });

// Route::get('/test/users/adduuid', function () {
//     $users = User::get();

//     foreach ($users as $user) {
//         $user->uuid = Str::uuid();
//         $user->save();
//     }

//     return response()->json(['message' => 'UUID added successfully', 'count' => $users->count()]);
// });


// Route::get('/setDiFormHashed', function () {
//     // Fetch all records where report_hash is null
//     $forms = DefectInspectionForm::get();

//     foreach ($forms as $form) {
//         // Combine id and reno_progress_id to create a unique base string
//         $baseString = $form->id . '-' . ($form->reno_progress_id ?? '0');

//         // Generate SHA-256 hash and take the first 32 characters
//         $hash = hash('sha256', $baseString);
//         $shortHash = substr($hash, 0, 12); // Truncate to 16 characters for brevity

//         // Update the record with the new report_hash
//         $form->report_hash = $shortHash;
//         $form->save();
//     }

//     return response()->json(['message' => 'Report hashes updated successfully', 'count' => $forms->count()]);
// });
