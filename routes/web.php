<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Middleware\CheckManagerRole;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Roles\RolesController;
use App\Http\Controllers\Setting\SettingController;
use App\Http\Controllers\Setting\GeneralSettingController;
use App\Http\Controllers\Setting\CurrencyConverterController;
use App\Http\Controllers\Setting\ProductTypesController;
use App\Http\Controllers\Setting\ProductionTeamDetailsController;
use App\Http\Controllers\Setting\InitialInspectionController;
use App\Http\Controllers\Setting\FinalInspectionController;
use App\Http\Controllers\Setting\SupplierController;
use App\Http\Controllers\Setting\ProcurementStdTimeController;
use App\Http\Controllers\Setting\StockHistoryController;
use App\Http\Controllers\ProductionManager\PurchaseOrderController;
use App\Http\Controllers\ProductionManager\ProductionManagerController;
use App\Http\Controllers\AssemblyManager\AssemblyManagerController;
use App\Http\Controllers\QualityManager\QualityManagerController;
use App\Http\Controllers\ProcurementManager\ProcurementManagerController;
use App\Http\Controllers\EstimantionManager\EstimationManagerController;
use App\Http\Controllers\ProductionSuperwisor\ProductionSuperwisorController;
use App\Http\Controllers\Operator\OperatorController;
use App\Http\Controllers\ProductionSuperwisor\OperatorTrackingController;
use App\Http\Controllers\Setting\DeletedProjectController;
use App\Http\Controllers\QualityManager\QualityActionController; // A Done: 23-01-2026

// Broadcasting Routes
Broadcast::routes();

// External API Routes
require __DIR__ . '/api.php';

// Root Route
Route::get('/', function () {
    return redirect()->route('login');
});

// Utility Routes
Route::get('/clear_cache', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('route:cache');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    Artisan::call('schedule:clear-cache');
    return 'All caches cleared successfully!';
});

Route::get('/phpinfo', function () {
    phpinfo();
});

// Test Routes
Route::get('/test-middleware', function () {
    $middleware = new \App\Http\Middleware\CheckManagerRole();
    return 'Middleware exists';
});

Route::middleware('test')->get('/alert', function () {
    return 'Middleware is working!';
});

// Pusher Authentication
Route::post('/pusher/auth', function (\Illuminate\Http\Request $request) {
    if (auth()->check()) {
        return Broadcast::auth($request);
    }
    return response()->json(['message' => 'Unauthorized'], 403);
});

// Authentication Routes
Route::namespace('App\Http\Controllers\Auth')->group(function () {
    Route::get('login', 'AuthController@login_form')->name('login');
    Route::post('login', 'AuthController@login')->name('AuthLogin');
    Route::post('authRedirectLogin', 'AuthController@authRedirectLogin')->name('AuthRedirectLogin'); // A Code: 22-12-2025
    Route::get('/logout', 'AuthController@logout')->name('Logout');
});

// Add this route for SSE
Route::get('/timer-stream/{projectId}/{productId}/{orderQty}', function ($projectId, $productId, $orderQty) {
    return response()->stream(function () use ($projectId, $productId, $orderQty) {
        while (true) {
            $timerStates = app(TimerController::class)->getTimerState(new Request([
                'projectId' => $projectId,
                'productId' => $productId,
                'orderQty' => $orderQty
            ]));

            echo "data: " . json_encode($timerStates->getData()) . "\n\n";
            ob_flush();
            flush();
            sleep(2); // Update every 2 seconds
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
});

// Admin Routes
Route::namespace('App\Http\Controllers\Admin')->prefix('admin')->middleware('\App\Http\Middleware\CheckManagerRole:Admin')->group(function () {
    Route::get('dashboard', 'AdminController@dashboard')->name('AdminDashboard');
    Route::get('settings', 'AdminController@settings_form')->name('AdminSettingsForm');

    Route::get('hours_settings', 'AdminController@hours_settings')->name('AdminHoursSettings');
    Route::post('hours_settings/store', 'AdminController@admin_hours_store')->name('admin.hrs.store');
    Route::put('hours_settings/update/{id}', 'AdminController@admin_hours_update')->name('admin.hrs.update');
    Route::delete('hours_settings/delete/{id}', 'AdminController@admin_hours_destroy')->name('admin.hrs.destroy');

    Route::post('settings_done', 'AdminController@settings')->name('AdminSettings');

    // admin user button redirect 
    Route::get('/admin/impersonate/{id}', 'AdminController@generateImpersonationLink')->name('admin.impersonate');
    Route::get('/admin/impersonate/perform/{token}', 'AdminController@performImpersonation')->name('admin.performImpersonation');
    Route::post('/admin/impersonate/logout', 'AdminController@autoLogout')->name('admin.impersonate.logout');
    Route::get('/admin/stop-impersonation', 'AdminController@stopImpersonation')->name('admin.stopImpersonation');  
});

Route::middleware(['\App\Http\Middleware\CheckManagerRole:Admin'])->group(function () {
    // Admin User Management
    Route::get('/admin/users', [UserController::class, 'index'])->name('AdminUsersIndex');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/store', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/edit/{id}', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/update/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/delete/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Admin Role Management
    Route::get('/admin/roles', [RolesController::class, 'roles'])->name('AdminRoles');
    Route::post('/roles/store', [RolesController::class, 'store'])->name('roles.store');
    Route::put('/roles/update/{id}', [RolesController::class, 'update'])->name('roles.update');
    Route::delete('/roles/delete/{id}', [RolesController::class, 'destroy'])->name('roles.destroy');


    Route::get('/settings', [SettingsController::class, 'index'])->name('SettingsPage');
    Route::put('/settings/{id}', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/setting', [SettingController::class, 'index'])->name('setting');

    Route::get('/settings', [GeneralSettingController::class, 'index'])->name('SettingsPage');
    Route::put('/settings/{id}', [GeneralSettingController::class, 'update'])->name('settings.update');

    Route::get('/currency-converter', [CurrencyConverterController::class, 'index'])->name('currency');
    Route::put('/currency-converter/update', [CurrencyConverterController::class, 'update'])->name('currency.update');

    Route::get('production-team-details', [ProductionTeamDetailsController::class, 'create'])->name('production.team.details');
    Route::post('production-team-details', [ProductionTeamDetailsController::class, 'store'])->name('production.team.store');
    Route::get('/production-team-details/edit/{id}', [ProductionTeamDetailsController::class, 'edit'])->name('production.team.edit');
    Route::post('production-team-details/update/{id}', [ProductionTeamDetailsController::class, 'update'])->name('production.team.update');
    Route::delete('production-team-details/delete/{id}', [ProductionTeamDetailsController::class, 'destroy'])->name('production.team.destroy');

    Route::get('/product-types', [ProductTypesController::class, 'index'])->name('product-types');
    Route::post('/product-types/store', [ProductTypesController::class, 'store'])->name('product-types.store');
    Route::post('/product-types/update', [ProductTypesController::class, 'update'])->name('product-types.update');
    Route::get('/initial-inspection', [InitialInspectionController::class, 'index'])->name('initial.inspection');
    Route::post('/initial-inspection/update', [InitialInspectionController::class, 'update'])->name('initial.inspection.update');

    Route::get('/final-inspection', [FinalInspectionController::class, 'index'])->name('final.inspection');
    Route::put('/final-inspection/update', [FinalInspectionController::class, 'update'])->name('final-inspection.update');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.list');
    Route::put('/supplier/update', [SupplierController::class, 'update'])->name('supplier.update');

    Route::post('/supplier/store', [SupplierController::class, 'store'])->name('supplier.store');
    Route::delete('/supplier/destroy', [SupplierController::class, 'destroy'])->name('supplier.destroy');

    Route::get('/settings/procurement_std_time', [ProcurementStdTimeController::class, 'index'])->name('procurement.std.time');
    Route::put('/procurement/update', [ProcurementStdTimeController::class, 'update'])->name('procurement.update');
    Route::post('/procurement/store', [ProcurementStdTimeController::class, 'store'])->name('procurement.store');
    Route::delete('/procurement/delete/{id}', [ProcurementStdTimeController::class, 'destroy'])->name('procurement.destroy');
    Route::get('/settings/stock_history', [StockHistoryController::class, 'index'])->name('stock.history');

    Route::get('/settings/deleted_projects', [DeletedProjectController::class, 'index'])->name('deleted.projects.list'); // A Code: 31-12-2025
});

// Estimation Manager Routes
Route::namespace('App\Http\Controllers\EstimantionManager')
    ->middleware('\App\Http\Middleware\CheckManagerRole:Estimation Manager')
    ->group(function () {
        Route::get('estimation_manager/dashboard', 'EstimationManagerController@dashboard')->name('EstimationManagerDashboard');
        Route::get('estimation_manager/inbox', 'EstimationManagerController@inbox')->name('EstimationManagerInbox');
        Route::post('estimation_manager/upload_bom_drawing', 'EstimationManagerController@upload_bom_drawing')->name('EstimationManagerUploadBomDrawing');
        Route::post('estimation_manager/upload_bom', 'EstimationManagerController@uploadBom')->name('EstimationManagerUploadBom');
        Route::post('/update-approval-status', [EstimationManagerController::class, 'updateApprovalStatus'])->name('update.approval.status');
        Route::post('/estimation-manager/update-remarks', [EstimationManagerController::class, 'updateRemarks'])->name('EstimationManagerUpdateRemarks');
        Route::get('/estimation-manager/get-remarks', [EstimationManagerController::class, 'getRemarks'])->name('EstimationManagerGetRemarks');
        Route::post('/estimation-manager/approve-pdf', [EstimationManagerController::class, 'approvePDF'])->name('approvepdf');
        Route::post('/estimation-manager/reject-pdf', [EstimationManagerController::class, 'rejectPDF'])->name('rejectpdf');
        Route::post('/upload-drawing-estimation', [EstimationManagerController::class, 'uploadDrawingEstimation'])->name('upload.drawing.estimation');
    });

// Assembly Manager Routes

Route::namespace('App\Http\Controllers\AssemblyManager')
    ->middleware('\App\Http\Middleware\CheckManagerRole:Assembly Manager')
    ->group(function () {
        Route::get('assembly_manager/dashboard', 'AssemblyManagerController@dashboard')->name('AssemblyManagerDashboard');
        Route::get('/assembly-manager/inbox', [AssemblyManagerController::class, 'inbox'])->name('AssemblyManagerInbox');
        Route::get('/purchase-order/view/{id}', [AssemblyManagerController::class, 'view'])->name('purchase_order.view');
        Route::post('/purchase-order/approve/{id}', [AssemblyManagerController::class, 'approve'])->name('purchase_order.approve');
        Route::post('/purchase-order/reject/{id}', [AssemblyManagerController::class, 'reject'])->name('purchase_order.reject');
    });

// Quality Manager Routes

Route::namespace('App\Http\Controllers\QualityManager')
    ->middleware('\App\Http\Middleware\CheckManagerRole:Quality Engineer')
    ->group(function () {
        Route::get('quality_manager/dashboard', 'QualityManagerController@dashboard')->name('QualityManagerDashboard');  

        Route::get('quality_manager/quality/create_form', 'QualityManagerController@quality_create_form')->name('QualityManagerQualityCreate');
        Route::get('quality_manager/quality/final_inspection_form', 'QualityManagerController@final_inspection_form')->name('QualityManagerFinalinspectionCreate');
        Route::get('quality_manager/inbox', 'QualityManagerController@inbox')->name('QualityManagerInbox');
        Route::post('/quality-manager-initial-inspection', [QualityManagerController::class, 'store'])->name('QualityManagerInitialInspection');
        Route::post('/upload-initial-report', [QualityManagerController::class, 'uploadInitialReport'])->name('upload.initial.report');
        Route::post('/quality-manager-final-inspection', [QualityManagerController::class, 'store_finalinspection'])->name('QualityManagerFinalInspection');
        Route::post('quality_manager/upload_report_doc', 'QualityManagerController@upload_report_doc')->name('QualityManagerUploadReportDoc');
        Route::get('/get-project-name', [QualityManagerController::class, 'getProjectName']);
        Route::post('/upload-inspection-images', [QualityManagerController::class, 'uploadInspectionImages'])->name('upload.inspection.images');
        Route::post('/delete-inspection-image', [QualityManagerController::class, 'deleteImage'])->name('delete.inspection.image');
        Route::post('/upload-final-report', [QualityManagerController::class, 'uploadFinalReport'])->name('upload.final.report');
        Route::post('/upload-test-report', [QualityManagerController::class, 'uploadTestReport'])->name('upload.test.report');

        // A Code: 23-01-2026 Start
        Route::get('quality_manager/quality_action', [QualityActionController::class, 'qualityAction'])->name('qualityAction');
        Route::post('/quality-manager-action-save', [QualityActionController::class, 'qualityActionSave'])->name('QualityManagerActionSave');        
        // A Code: 23-01-2026 End
    });

Route::middleware('auth')->get('quality_manager/quality', [QualityManagerController::class, 'quality'])->name('QUALITY');
Route::middleware('auth')->post('quality_manager/quality/show_initial_item_list', [QualityManagerController::class, 'showInitialItemList'])
    ->name('showInitialItemList');

// Procurement Manager Routes
Route::namespace('App\Http\Controllers\ProcurementManager')
    ->middleware('\App\Http\Middleware\CheckManagerRole:Procurement Specialist')
    ->group(function () {
        Route::get('procurement_manager/dashboard', 'ProcurementManagerController@dashboard')->name('ProcurementManagerDashboard');
        Route::get('procurement_manager/inbox', 'ProcurementManagerController@inbox')->name('ProcurementManagerInbox');
        Route::get('/procurement-manager/reupload-po/{id}', [ProcurementManagerController::class, 'reuploadPo'])->name('procurement_manager.reupload_po');
        Route::post('/procurement-manager/reupload-po/{id}', [ProcurementManagerController::class, 'storeReuploadPo'])->name('procurement_manager.reupload_po.store');
        Route::post('/update-procurement-check', 'ProcurementManagerController@updateCheckStatus')->name('UpdateProcurementCheckStatus');
        Route::post('supplierOrdersChart', 'ProcurementManagerController@ajaxSupplierOrdersChart');
        Route::post('totalOrdersChart', 'ProcurementManagerController@ajaxTotalOrdersChart');
        Route::post('articleOrdersChart', 'ProcurementManagerController@ajaxArticleOrdersChart');
        Route::post('totalArticlesChart', 'ProcurementManagerController@ajaxTotalArticlesChart');
        Route::post('/get-product-bom', 'ProcurementManagerController@getProductBOM')->name('GetProductBOM');
        Route::post('/save-stock-bom-po', 'ProcurementManagerController@saveStockBOMPo')->name('SaveStockBOMPo');
        Route::post('/get-saved-stock-bom', 'ProcurementManagerController@getSavedStockBOMPo')->name('GetSavedStockBOMPo');
        Route::post('/save-procurement-remark', 'ProcurementManagerController@saveProcurementRemark')->name('SaveProcurementRemark');
        Route::post('/save-procurement-drawing-remark', 'ProcurementManagerController@saveProcurementDrawingRemark')->name('SaveProcurementDrawingRemark');
        Route::get('/get-procurement-remark/{id}', 'ProcurementManagerController@getProcurementRemark')->name('GetProcurementRemark');
        Route::post('/place-po-status', 'ProcurementManagerController@placePOStatus')->name('placePOStatus');
    });

// Production Manager Routes (Consolidated)
Route::namespace('App\Http\Controllers\ProductionManager')->group(function () {
    Route::middleware(['auth'])->group(function () {
        // Product Engineer Project Routes
        Route::get('product_engineer/project/index', 'ProductionManagerController@index')->name('ProductionManagerProjectIndex');
        Route::post('product_engineer/project/index', 'ProductionManagerController@index')->name('ProductionManagerProjectIndexFilter');
        Route::post('product_engineer/project/export-csv', 'ProductionManagerController@exportCSV')->name('project.export.csv');

        Route::get('qr_code/{project_no}/{full_article_number}/{qty_index}', [ProductionManagerController::class, 'showProductDetails'])->name('showProductDetails');
        Route::get('/projects/{projectId}/qr-codes/{articleNumber}', [ProductionManagerController::class, 'getProjectQrCodes'])->name('getProjectQrCodes');
        Route::get('/projects/{projectId}/qr-codes/{articleNumber}/download-all', [ProductionManagerController::class, 'downloadAllQrCodes'])->name('downloadAllQrCodes');
        Route::get('/production-manager/project/{projectId}/download-all-project-qr-codes', [ProductionManagerController::class, 'downloadAllProjectQrCodes'])->name('downloadAllProjectQrCodes');
        Route::get('/get-project-documents', 'ProductionManagerController@getProjectDocuments')->name('getProjectDocuments');
        Route::get('/get-project-documents-for-inbox', 'ProductionManagerController@getProjectDocumentsForInbox')->name('getProjectDocumentsForInbox');
        Route::get('/get-project-bom', 'ProductionManagerController@getProjectsBOM')->name('getProjectsBOM');
        Route::get('product_engineer/check_project_status/{id}', 'ProductionManagerController@check_project_status')->name('CheckProjectStatus');
        Route::get('/download-qr/{projectId}/{projectName}', 'ProductionManagerController@generateAndDownloadQRCode')->name('download.qr');
        Route::post('get_quotation_items', 'ProductionManagerController@get_quotation_items')->name('GetQuotationItems');
        Route::post('getBOM', 'ProductionManagerController@getBOM')->name('getBOM');
        Route::post('getBOMForCheckStatus', 'ProductionManagerController@getBOMForCheckStatus')->name('getBOMForCheckStatus');
        Route::get('/production-manager/get-wip-photos', 'ProductionManagerController@getWIPPhotos')->name('getWIPPhotos');
        Route::get('/projects/{projectId}/execution/partial-order-pl/list', [ProductionManagerController::class, 'getProjectExecutionPartialOrderPLList'])->name('getProjectExecutionPartialOrderPLList');
        Route::get('/projects/{projectId}/execution/partial-order-pl/{articleNumber}/{qtyNo}/docs', [ProductionManagerController::class, 'getPartialOrderPLDocs'])->name('getPartialOrderPLDocs');

        // Project Execution Images
        Route::get('/production-manager/projects/{projectId}/execution/images/list', [ProductionManagerController::class, 'getProjectExecutionImageList'])->name('getProjectExecutionImageList');
        Route::get('/production-manager/projects/{projectId}/execution/work-orders/list', [ProductionManagerController::class, 'getProjectExecutionWorkOrdersList'])->name('getProjectExecutionWorkOrdersList');
        Route::get('/production-manager/projects/{projectId}/quality/list', [ProductionManagerController::class, 'getQualityList'])->name('getQualityList');
        Route::get('/production-manager/project/{projectId}/execution/images/{articleNumber}/{qtyNo}', [ProductionManagerController::class, 'getProjectExecutionImages'])->name('getProjectExecutionImages');
        Route::post('/production-manager/projects/upload-execution-image', [ProductionManagerController::class, 'uploadProjectExecutionImage'])->name('uploadProjectExecutionImage');
        Route::post('/production-manager/projects/delete-execution-image', [ProductionManagerController::class, 'deleteProjectExecutionImage'])->name('deleteProjectExecutionImage');

        // Project Execution MRF, PL, Work Orders
        Route::get('/production-manager/projects/{projectId}/execution/mrf/list', [ProductionManagerController::class, 'getProjectExecutionMRFList'])->name('getProjectExecutionMRFList');
        Route::get('/production-manager/projects/{projectId}/execution/docs/{type}/{articleNumber?}', [ProductionManagerController::class, 'getProjectExecutionDocs'])->name('getProjectExecutionDocs');
        Route::post('/production-manager/projects/upload-execution-doc', [ProductionManagerController::class, 'uploadProjectExecutionDoc'])->name('uploadProjectExecutionDoc');
        Route::post('/production-manager/projects/delete-execution-doc', [ProductionManagerController::class, 'deleteProjectExecutionDoc'])->name('deleteProjectExecutionDoc');
        Route::get('/production-manager/projects/{id}', [ProductionManagerController::class, 'getProjectById'])->name('getProjectById');

        // Quality Documents
        Route::get('/production-manager/get-quality-docs/{projectId}/{type}/{articleNumber?}/{qtyNo?}', [ProductionManagerController::class, 'getQualityDocs'])->name('getQualityDocs');
        Route::post('/production-manager/upload-quality-doc', 'ProductionManagerController@uploadQualityDoc')->name('uploadQualityDoc');
        Route::post('/production-manager/delete-quality-doc', 'ProductionManagerController@deleteQualityDoc')->name('deleteQualityDoc');

        Route::get('/production-manager/project-drawings', 'ProductionManagerController@getProjectsDrawings')->name('getProjectsDrawings');
        Route::get('/download-drawing/{projectId}/{articleNumber}/{type}', [ProductionManagerController::class, 'downloadDrawing'])->name('downloadDrawing');
        Route::get('/production-manager/get-incoming-inspection-pos', [App\Http\Controllers\ProductionManager\ProductionManagerController::class, 'getIncomingInspectionPOs'])->name('getIncomingInspectionPOs');
        Route::get('/production-manager/get-incoming-inspection-docs', [App\Http\Controllers\ProductionManager\ProductionManagerController::class, 'getIncomingInspectionDocs'])->name('getIncomingInspectionDocs');
        Route::post('/production-manager/delete-incoming-inspection-doc', [App\Http\Controllers\ProductionManager\ProductionManagerController::class, 'deleteIncomingInspectionDoc'])->name('deleteIncomingInspectionDoc');

        // Nameplate images
        Route::get('/get-nameplate-list/{projectId}', [ProductionManagerController::class, 'getNamePlateList'])->name('getNamePlateList');
        Route::get('/get-nameplate-images/{projectId}/{articleNumber}/{qtyNo}', [ProductionManagerController::class, 'getNamePlateImages'])->name('getNamePlateImages');
    });
    // Production Manager Controller - Restricted to Production Engineer
    Route::middleware('\App\Http\Middleware\CheckManagerRole:Production Engineer')->group(function () {
        Route::get('product_engineer/dashboard', 'ProductionManagerController@dashboard')->name('ProductionManagerDashboard');
        Route::get('product_engineer/project/create_form/{project_name}', 'ProductionManagerController@project_create_form')->name('ProductionManagerProjectCreate');
        Route::post('product_engineer/project/create', 'ProductionManagerController@project_create')->name('ProductionManagerProjectCreateDo');
        Route::get('product_engineer/project/edit_form/{id}', 'ProductionManagerController@project_edit_form')->name('ProductionManagerProjectEdit');
        Route::post('product_engineer/project/update', 'ProductionManagerController@project_update')->name('ProductionManagerProjectUpdate');                
        // A Code: 26-12-2025
        Route::post('product_engineer/delete/{id}', 'ProductionManagerController@project_delete')->name('ProductionManagerProjectDelete');
        
        Route::get('product_engineer/inbox', 'ProductionManagerController@inbox')->name('ProductionManagerInbox');
        Route::get('/purchase-order-engineer/view/{id}', [ProductionManagerController::class, 'view'])->name('purchase_order_engineer.view');
        Route::post('/purchase-order-engineer/approve/{id}', [ProductionManagerController::class, 'approve'])->name('purchase_order.approve');
        Route::post('/purchase-order-engineer/reject/{id}', [ProductionManagerController::class, 'reject'])->name('purchase_order.reject');
        Route::post('/production-engineer/reject-order', [ProductionManagerController::class, 'rejectOrder'])->name('ProductionManagerRejectOrder');
        Route::get('/production-engineer/rejection-details', [ProductionManagerController::class, 'getRejectionDetails'])->name('getRejectionDetails');
        Route::get('/production-engineer/cancellation-details', [ProductionManagerController::class, 'getCancellationDetails'])->name('getCancellationDetails');
        Route::post('upload_nameplate_img', 'ProductionManagerController@upload_nameplate_img')->name('ProductionManagerUploadNameplateImg');


        Route::namespace('App\Http\Controllers\QRCode')->group(function () {
            Route::get('/download-qr-code/{id}', 'QrCodeController@downloadQRCode')->name('download.qrCode');
            Route::get('/qrcode/{text}', 'QRCodeController@download')->where('text', '.*')->name('qrcode.download');
        });
    });

    // Restricted to authenticated users only (any role)
    Route::middleware('auth')->get('procurement_specialist/purchase_order', 'PurchaseOrderController@index')->name('PurchaseOrder');

    Route::middleware('auth')->post('/update-received-date', 'PurchaseOrderController@updateReceivedDate')->name('update.received.date');

    Route::middleware('auth')->post('procurement_specialist/purchase_order', 'PurchaseOrderController@index')->name('PurchaseOrderFilter');
    Route::middleware('auth')->post('procurement_specialist/purchase_order/export-csv', 'PurchaseOrderController@exportCSV')->name('po.export.csv');
    Route::middleware('auth')->get('product_engineer/product-tracking', 'ProductsTrackingController@index')->name('product_tracking');

    Route::middleware('auth')->post('product_engineer/product-tracking', 'ProductsTrackingController@index')->name('ProductTrackingFilter');
    Route::middleware('auth')->post('product_engineer/product-tracking/export-csv', 'ProductsTrackingController@exportCSV')->name('ProductTracking.export.csv');

    // Purchase Order Controller - Restricted to Procurement Specialist
    Route::middleware('\App\Http\Middleware\CheckManagerRole:Procurement Specialist')->group(function () {
        Route::get('/production-manager/add-po/{productId?}', [PurchaseOrderController::class, 'addPo'])->name('addPO');
        Route::get('/production-manager/add-po/stock/{productId}', [PurchaseOrderController::class, 'addPo'])->name('addPOFromStock');
        Route::post('/purchase-order/store', [PurchaseOrderController::class, 'store'])->name('purchase_order.store');
        Route::get('/purchase-order/edit/{id}', [PurchaseOrderController::class, 'edit'])->name('editPO');
        Route::put('/purchase-order/{id}', [PurchaseOrderController::class, 'update'])->name('updatePO');
        Route::get('/purchase-order/{id}', [PurchaseOrderController::class, 'getPurchaseOrder'])->name('purchase.order.details');
        Route::get('/purchase-order/children/{parentId}', [PurchaseOrderController::class, 'getPurchaseOrderChildren']);
        Route::get('/api/get-project-name/{project_number}', [PurchaseOrderController::class, 'getProjectName']);
        Route::post('/update-purchase-order', [PurchaseOrderController::class, 'updatePurchaseOrder']);
        Route::post('/upload', [PurchaseOrderController::class, 'upload'])->name('upload');
    });
});

// Designer Engineer Routes
Route::namespace('App\Http\Controllers\DesignerEngineer')->group(function () {
    Route::get('designer-engineer/dashboard', 'DesignerEngineerController@dashboard')->name('DesignerEngineerDashboard');
});

Route::get('/production-superwisor/mark-materials-ready', [ProductionSuperwisorController::class, 'markMaterialsReady'])->name('mark.materials.ready');

// Production Supervisor Routes
Route::namespace('App\Http\Controllers\ProductionSuperwisor')
    ->middleware('\App\Http\Middleware\CheckManagerRole:Production Superwisor')
    ->group(function () {
        Route::get('production-superwisor/dashboard', 'ProductionSuperwisorController@dashboard')->name('ProductionSuperwisorDashboard');
        Route::get('production-superwisor/inbox', 'ProductionSuperwisorController@inbox')->name('ProductionSuperwisorInbox');
        Route::post('production-superwisor/assign_task', 'ProductionSuperwisorController@assign_task')->name('superwisorAssignTaskToOperator');
        Route::post('production-superwisor/assign_task_operators', 'ProductionSuperwisorController@assign_multiple_operators')->name('superwisorAssignTaskToMultipleOperators');
        Route::get('/mrf-excel-download', [ProductionSuperwisorController::class, 'mrf_excel_download'])->name('MRFExcelDownload');
        Route::post('/upload-mrf-file', [ProductionSuperwisorController::class, 'uploadMRFFile'])->name('upload.mrf.file');
        Route::post('/production-superwisor/send-mrf-email', [ProductionSuperwisorController::class, 'sendMRFEmail'])->name('send.mrf.email');
        Route::post('production-superwisor/show_operator_list', 'ProductionSuperwisorController@showOperatorList')->name('showOperatorList');
        Route::post('/update-approval-status', [ProductionSuperwisorController::class, 'updateApprovalStatus'])->name('update.approval.status');
        Route::post('production-superwisor/uploadPlDoc', 'ProductionSuperwisorController@uploadPlDoc')->name('ProductionSuperwisorUploadPlDoc');
        Route::post('/confirm-assembly-process', 'ProductionSuperwisorController@assemblyProcessConfirm')->name('ConfirmAssemblyProcess');
        Route::post('/assembly_process_confirm_product_wise/{id}', 'ProductionSuperwisorController@assemblyProcessConfirmProductWise')->name('ConfirmAssemblyProcessProductWise');
        Route::post('/approve-pdf', [ProductionSuperwisorController::class, 'approvePDF'])->name('approve.pdf');
        Route::post('/reject-pdf', [ProductionSuperwisorController::class, 'rejectPDF'])->name('reject.pdf');
        Route::post('production-superwisor/upload-partial-pl', [ProductionSuperwisorController::class, 'uploadPartialPlDoc'])->name('ProductionSuperwisorUploadPartialPlDoc');
        Route::middleware('auth')->post('pl/view_products', 'ProductionSuperwisorController@view_project_details')->name('viewProjectDetails');
        Route::get('production-superwisor/operator_tracking', 'OperatorTrackingController@index')->name('OperatorTracking');
        Route::post('production-superwisor/operator_tracking', 'OperatorTrackingController@index')->name('OperatorTrackingFilter');
        Route::post('production-superwisor/operator_tracking/export-csv', 'OperatorTrackingController@exportCSV')->name('OperatorTracking.export.csv');
        Route::post('/update-production-check', 'ProductionSuperwisorController@updateCheckStatus')->name('UpdateProductionCheckStatus');
        Route::get('/mrf-excel-download-inspected', [ProductionSuperwisorController::class, 'mrf_excel_download_inspected'])
            ->name('MRFExcelDownloadInspected');

        Route::get('/mrf-excel-download-not-inspected', [ProductionSuperwisorController::class, 'mrf_excel_download_not_inspected'])
            ->name('MRFExcelDownloadNotInspected');
        Route::post('/send-mrf-email-inspected', [ProductionSuperwisorController::class, 'send_mrf_email_inspected'])
            ->name('SendMRFEmailInspected');

        Route::post('/send-mrf-email-not-inspected', [ProductionSupervisorController::class, 'send_mrf_email_not_inspected'])
            ->name('SendMRFEmailNotInspected');

        Route::post('/send-mrf-email-from-stock', [ProductionSuperwisorController::class, 'send_mrf_email_from_stock'])
            ->name('SendMRFEmailFromStock');
    });

Route::namespace('App\Http\Controllers\ProductionSuperwisor')
    ->middleware('\App\Http\Middleware\CheckManagerRole:Admin,Production Superwisor')
    ->group(function () {
        Route::get('production-superwisor/operator_tracking', 'OperatorTrackingController@index')->name('OperatorTracking');
        Route::post('production-superwisor/operator_tracking', 'OperatorTrackingController@index')->name('OperatorTrackingFilter');
        Route::post('production-superwisor/operator_tracking/export-csv', 'OperatorTrackingController@exportCSV')->name('OperatorTracking.export.csv');
    });

Route::namespace('App\Http\Controllers\Operator')->group(function () {
    Route::get('operator/qr_page/{product_id}', 'OperatorController@qr_page')
        ->middleware('auth')
        ->name('QRPage');

    // Put all operator routes inside a function
    $operatorRoutes = function () {
        //
         Route::get('operator/product_type_process_qr_code/{product_id}/{project_type_name}/{seq_qty}', 'OperatorController@product_type_process_qr_code')->name('OperatorProductTypeProcessQRCode');
        //
        Route::get('operator/dashboard', 'OperatorController@dashboard')->name('OperatorDashboard');
        Route::post('/operator/update-process-status', [OperatorController::class, 'updateProcessStatus'])->name('operator.updateProcessStatus');
        Route::get('operator/product_type/{product_id}/{redirect}', 'OperatorController@product_type')->name('OperatorProductType');
        Route::get('operator/product_type_process/{product_id}/{project_type_name}/{seq_qty}', 'OperatorController@product_type_process')->name('OperatorProductTypeProcess');
        Route::get('operator/timer_start_stop/{process_id}', 'OperatorController@start_stop_time')->name('OperatorStartStopTimer');
        Route::post('operator/update_process_status', 'OperatorController@update_process_status')->name('OperatorUpdateProcessStatus');
        Route::post('operator/start_timer', 'OperatorController@startTimer')->name('OperatorstartTimer');
        Route::post('operator/pause_timer', 'OperatorController@pauseTimer')->name('OperatorpauseTimer');
        Route::post('operator/stop_timer', 'OperatorController@stopTimer')->name('OperatorstopTimer');
        Route::post('operator/reset_timer', 'OperatorController@resetTimer')->name('OperatorresetTimer');
        Route::post('get-timer-state', 'OperatorController@getTimerState')->name('getTimerState');
        Route::post('/operator/save-edited-pdf', 'OperatorController@saveEditedPDF')->name('save.edited.pdf');
        Route::post('/operator/save-captured-photo', 'OperatorController@savecapturedphoto')->name('save.captured.photo');
        Route::post('/operator/get-process-photos', 'OperatorController@getprocessphotos')->name('get.process.photos');
        Route::post('/operator/get-po-numbers', 'OperatorController@getPoNumbers');
    };

    // Apply middleware for BOTH Wilo Operator and 3rd Party Operator
    Route::middleware('\App\Http\Middleware\CheckManagerRole:Wilo Operator,3rd Party Operator')
        ->group($operatorRoutes);
});


// User Routes
Route::namespace('App\Http\Controllers\User')
    ->group(function () {
        Route::get('clear_table', 'UserController@truncate_table')->name('TruncateTable');
    });

Route::namespace('App\Http\Controllers\StockMaster')->group(function () {
    Route::middleware('auth')->get('Stock/stock_master', 'StockMasterController@index')->name('Stock');
    Route::middleware('auth')->post('Stock/stock_master', 'StockMasterController@index')->name('StockFilter');
    Route::middleware('auth')->post('Stock/export-csv', 'StockMasterController@exportCSV')->name('stock.export.csv');
    Route::middleware('auth')->post('Stock/show_hide_qty', 'StockMasterController@viewHideQty')->name('showHideQty');
    Route::middleware('auth')->post('Stock/show_qty_in_order', 'StockMasterController@viewQtyInOrder')->name('showQtyInOrder');
    Route::middleware('\App\Http\Middleware\CheckManagerRole:Admin')->group(function () {
        Route::post('Stock/store', 'StockMasterController@store')->name('StockMaster.Stock.store');
        Route::post('Stock/import', 'StockMasterController@import')->name('StockMaster.Stock.import'); // New route
        Route::put('Stock/update/{id}', 'StockMasterController@update')->name('StockMaster.Stock.update');
        Route::delete('Stock/delete/{id}', 'StockMasterController@destroy')->name('StockMaster.Stock.destroy');
    });
});
