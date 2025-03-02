<?php

//  route/api.php

use PgSql\Lob;
use App\Models\Sale;
use App\Models\Package;
use Illuminate\Http\Request;

use App\Events\SaleStatusUpdated;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiskController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\POItemController;
use App\Http\Controllers\QCFormController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobTaskController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PhaseJobController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\OTPRequestController;
use App\Http\Controllers\PMCategoryController;
use App\Http\Controllers\DiscountFeeController;
use App\Http\Controllers\RenoProgressController;
use App\Http\Controllers\KeyManagementController;
use App\Http\Controllers\ProgressPhaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\RegistrationFormController;
use App\Http\Controllers\DefectInspectionFormController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ResourceItemController;
use App\Http\Controllers\UserItemPermissionController;

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
Route::post('/owner/reno-registration-form/overview/submit', [RegistrationFormController::class, 'submitForm']);

// Confirm Order
Route::get('/orders/{id}/confirm', [OrderController::class, 'confirmOrder'])->name('orders.confirmOrder');
Route::get('/orders/{id}/release', [OrderController::class, 'releaseOrder'])->name('orders.releaseOrder');

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('owner/staff/login', 'staffLoginToOwner');
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
    Route::apiResource('/discountFees', DiscountFeeController::class);
    Route::apiResource('/invoices', InvoiceController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/owner/reno-registration-form', RegistrationFormController::class);
    Route::apiResource('/reno-progress', RenoProgressController::class);
    Route::apiResource('/purchase-orders', PurchaseOrderController::class);
    Route::apiResource('/inventory', InventoryController::class);
    Route::apiResource('/defect-inspection-forms', DefectInspectionFormController::class);
    Route::apiResource('/key-management', KeyManagementController::class);

    Route::get('users/{id}/password/reset', [UserController::class, 'resetPassword']);
    Route::get('users/{id}/deactivate', [UserController::class, 'deactivateUser']);

    Route::get('/users/type/{type}', [UserController::class, 'getUsersWithType']);
    Route::get('/owner/reno-registration-form/{id}/status/approve', [RegistrationFormController::class, 'approveForm']);
    Route::get('/owner/reno-registration-form/{id}/status/reject', [RegistrationFormController::class, 'rejectForm']);

    Route::get('/reno-progress/table/advance', [RenoProgressController::class, 'getAdvanceTable']);
    Route::get('/reno-progress/{id}/task/{taskId}/supply/toggle', [JobTaskController::class, 'toggleSupplyStatus']);
    Route::get('/reno-progress/{id}/task/{taskId}/install/toggle', [JobTaskController::class, 'toggleInstallStatus']);
    Route::get('/reno-progress/{id}/task/{taskId}/status/{status}', [JobTaskController::class, 'changeTaskStatus']);
    Route::post('/reno-progress/{id}/task/{taskId}/owner-comment/change', [JobTaskController::class, 'changeOwnerComment']);
    Route::post('/reno-progress/{id}/task/{taskId}/internal-comment/change', [JobTaskController::class, 'changeInternalComment']);
    Route::post('/reno-progress/{id}/task/{taskId}/documents/upload', [JobTaskController::class, 'uploadDocuments']);
    Route::post('/reno-progress/{id}/task/{taskId}/document/upload', [JobTaskController::class, 'uploadTaskImage']);
    Route::get('/reno-progress/{id}/task/{taskId}/documents/fetch', [JobTaskController::class, 'fetchTaskDocuments']);
    Route::get('/reno-progress/{id}/task/{taskId}/documents/{documentIndex}/remove', [JobTaskController::class, 'removeTaskDocument']);
    Route::post('/reno-progress/{id}/task/{taskId}/comments', [JobTaskController::class, 'changeComments']);


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

    Route::get('/reno-progress/{id}/task/{taskId}/visibility/toggle', [JobTaskController::class, 'toggleTaskVisibility']);

    Route::get('/purchase-orders/{id}/delivery/status/delivered', [POItemController::class, 'markAsDelivered']);

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

    Route::get('key-management/{keyManagementId}/{category}/add', [KeyManagementController::class, 'addCategoryItem']);
    Route::post('key-management/{keyManagementId}/{category}/change/{itemIndex}/name', [KeyManagementController::class, 'changeKeyManagementItemName']);
    Route::post('key-management/{keyManagementId}/{category}/change/{itemIndex}/remark', [KeyManagementController::class, 'changeKeyManagementItemRemark']);
    Route::post('key-management/{keyManagementId}/{category}/upload/{itemIndex}/photo', [KeyManagementController::class, 'uploadKeyManagementItemPhoto']);
    Route::post('key-management/{keyManagementId}/{category}/change/{itemIndex}/photo', [KeyManagementController::class, 'changeKeyManagementItemPhoto']);
    Route::get('key-management/{keyManagementId}/{category}/remove/{itemIndex}', [KeyManagementController::class, 'removeKeyManagementItem']);
    Route::post('key-management/{keyManagementId}/info/update', [KeyManagementController::class, 'updateKeyManagementInfo']);

    Route::post('resource-items/add/user/permission', [ResourceItemController::class, 'createUserPermission']);
    Route::post('resource-items/{userId}/{itemId}/permission', [ResourceItemController::class, 'changeUserPermission']);
    Route::delete('resource-items/{userId}/{itemId}', [ResourceItemController::class, 'deleteUserPermission']);


    // TEST
    Route::get('/data', [MyController::class, 'getData']);
    Route::get('/test', function () {

        $body = [
            "msg_type" => "interactive",
            "card" => [
                "elements" => [
                    [
                        "tag" => "div",
                        "text" => [
                            "content" => "An owner has submitted a Reno Registration Form to RenoXpert",
                            "tag" => "plain_text"
                        ]
                    ],
                    [
                        "tag" => "column_set",
                        "flex_mode" => "none",
                        "background_style" => "default",
                        "columns" => [
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**Form No :**",
                                            "tag" => "lark_md"
                                        ]
                                    ],
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "[form_no]",
                                            "tag" => "plain_text"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**Owner Name :**",
                                            "tag" => "lark_md"
                                        ]
                                    ],
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "[owner_name]",
                                            "tag" => "plain_text"
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        "action" => [],
                        "horizontal_spacing" => "default"
                    ],
                    [
                        "tag" => "column_set",
                        "flex_mode" => "none",
                        "background_style" => "default",
                        "columns" => [
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**Property :**",
                                            "tag" => "lark_md"
                                        ]
                                    ],
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "[property_name]",
                                            "tag" => "plain_text"
                                        ]
                                    ]
                                ]
                            ],
                            [
                                "tag" => "column",
                                "width" => "weighted",
                                "weight" => 1,
                                "vertical_align" => "top",
                                "elements" => [
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "**Phone No :**",
                                            "tag" => "lark_md"
                                        ]
                                    ],
                                    [
                                        "tag" => "div",
                                        "text" => [
                                            "content" => "[phone_no]",
                                            "tag" => "plain_text"
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    [
                        "tag" => "hr"
                    ],
                    [
                        "tag" => "action",
                        "actions" => [
                            [
                                "tag" => "button",
                                "text" => [
                                    "tag" => "plain_text",
                                    "content" => "View Detail"
                                ],
                                "type" => "primary",
                                "multi_url" => [
                                    "url" => "https://renoxpert.my/registration-forms/[form_id]",
                                    "pc_url" => "",
                                    "android_url" => "",
                                    "ios_url" => ""
                                ]
                            ]
                        ]
                    ]
                ],
                "header" => [
                    "template" => "blue",
                    "title" => [
                        "content" => "New Reno Registration Form",
                        "tag" => "plain_text"
                    ]
                ]
            ]
        ];

        $bodyJson = json_encode($body, JSON_PRETTY_PRINT);

        Log::info($bodyJson);
    });

    // DEV TOOLS
    Route::get('/dev/database/refresh', function () {
        Artisan::call('migrate:refresh --seed');

        return 'Database migrated and seeded successfully!';
    });

    Route::get('/dev/storage/clear', function () {

        // Clear S3 data
        $files = Storage::disk('s3')->allFiles();

        foreach ($files as $file) {
            Storage::disk('s3')->delete($file);
        }

        return 'Storage cleared successfully!';
    });
});


Route::get('/disk', [DiskController::class, 'index']);

// Emergency route
Route::get('/tmp/startSaleRenoProgress/saleId/{saleId}', function ($saleId) {
    // Get sale by saleId
    $sale = Sale::find($saleId);

    if (!$sale) {
        return response()->json(['error' => 'Sale not found'], 404);
    }

    event(new SaleStatusUpdated($sale));

    return response()->json(['message' => 'Sale status updated successfully']);
});


Route::get('/tmp/progress/generate', function () {
    $sale = Sale::find(1);

    $sale->status = 'partial-paid';

    $sale->save();
});

Route::get('/test/unauth', function () {

    $body = [
        "msg_type" => "interactive",
        "card" => [
            "elements" => [
                [
                    "tag" => "div",
                    "text" => [
                        "content" => "An owner has submitted a Reno Registration Form to RenoXpert",
                        "tag" => "plain_text"
                    ]
                ],
                [
                    "tag" => "column_set",
                    "flex_mode" => "none",
                    "background_style" => "default",
                    "columns" => [
                        [
                            "tag" => "column",
                            "width" => "weighted",
                            "weight" => 1,
                            "vertical_align" => "top",
                            "elements" => [
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "**Form No :**",
                                        "tag" => "lark_md"
                                    ]
                                ],
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "[form_no]",
                                        "tag" => "plain_text"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "tag" => "column",
                            "width" => "weighted",
                            "weight" => 1,
                            "vertical_align" => "top",
                            "elements" => [
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "**Owner Name :**",
                                        "tag" => "lark_md"
                                    ]
                                ],
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "[owner_name]",
                                        "tag" => "plain_text"
                                    ]
                                ]
                            ]
                        ]
                    ],
                    "action" => [],
                    "horizontal_spacing" => "default"
                ],
                [
                    "tag" => "column_set",
                    "flex_mode" => "none",
                    "background_style" => "default",
                    "columns" => [
                        [
                            "tag" => "column",
                            "width" => "weighted",
                            "weight" => 1,
                            "vertical_align" => "top",
                            "elements" => [
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "**Property :**",
                                        "tag" => "lark_md"
                                    ]
                                ],
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "[property_name]",
                                        "tag" => "plain_text"
                                    ]
                                ]
                            ]
                        ],
                        [
                            "tag" => "column",
                            "width" => "weighted",
                            "weight" => 1,
                            "vertical_align" => "top",
                            "elements" => [
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "**Phone No :**",
                                        "tag" => "lark_md"
                                    ]
                                ],
                                [
                                    "tag" => "div",
                                    "text" => [
                                        "content" => "[phone_no]",
                                        "tag" => "plain_text"
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    "tag" => "hr"
                ],
                [
                    "tag" => "action",
                    "actions" => [
                        [
                            "tag" => "button",
                            "text" => [
                                "tag" => "plain_text",
                                "content" => "View Detail"
                            ],
                            "type" => "primary",
                            "multi_url" => [
                                "url" => "https://renoxpert.my/registration-forms/[form_id]",
                                "pc_url" => "",
                                "android_url" => "",
                                "ios_url" => ""
                            ]
                        ]
                    ]
                ]
            ],
            "header" => [
                "template" => "blue",
                "title" => [
                    "content" => "New Reno Registration Form",
                    "tag" => "plain_text"
                ]
            ]
        ]
    ];

    $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    Log::info($bodyJson);

    return $bodyJson;
});
