<?php

use App\Http\Controllers\CustomDeliveryShiftController;
use App\Http\Controllers\FuelEntryApprovalController;
use App\Http\Controllers\FuelEntryConfirmationController;
use App\Http\Controllers\FuelStatementController;
use App\Http\Controllers\FuelVerificationRecordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FuelReportsController;
use App\Http\Controllers\Admin\FuelLPOController;
use App\Http\Controllers\Admin\DeliveryScheduleController;
use App\Http\Controllers\Admin\DeviceTypeController;
use App\Http\Controllers\Admin\DeviceSimCardController;
use App\Http\Controllers\Admin\DeviceCenterController;
use App\Http\Controllers\Admin\DeviceRepairController;

Route::group(['prefix' => 'admin', 'middleware' => ['AdminLoggedIn', 'ip-blocker']], function () {
    Route::get('custom-delivery-shifts', [CustomDeliveryShiftController::class, 'showListingPage'])->name('custom-delivery-shifts.index');
    Route::get('custom-delivery-shifts/create', [CustomDeliveryShiftController::class, 'showCreatePage'])->name('custom-delivery-shifts.create');
    Route::get('custom-delivery-shifts/store', [CustomDeliveryShiftController::class, 'showCreatePage'])->name('custom-delivery-shifts.store');

    Route::get('fuel-reports/consumption-report', [FuelReportsController::class, 'consumptionIndex'])->name('fuel_consumption_reports.index');

    Route::get('fuel-reports/deliveries/{date}/{branch}', [FuelLPOController::class, 'confirmedEntriesDeliveries'])->name('fuel_lpos.deliveries');

    Route::get('fuel-entries/confirmation/overview', [FuelEntryConfirmationController::class, 'showOverviewPage'])->name('fuel-entry-confirmation.overview');
    Route::get('fuel-entries/confirmation/get-savings', [FuelEntryConfirmationController::class, 'getFuelSavings'])->name('fuel-entry-confirmation.savings');
    Route::get('fuel-entries/confirmation/summary', [FuelEntryConfirmationController::class, 'getSummary'])->name('fuel-entry-confirmation.summary');
    Route::get('fuel-entries/confirmation/verified', [FuelEntryConfirmationController::class, 'getVerifiedEntries'])->name('fuel-entry-confirmation.verified');

    Route::get('fuel-statements', [FuelStatementController::class, 'showListingPage'])->name('fuel-statements.listing');
    Route::get('fuel-statements/upload', [FuelStatementController::class, 'showUploadPage'])->name('fuel-statements.show-upload-page');
    Route::post('fuel-statements/upload', [FuelStatementController::class, 'upload'])->name('fuel-statements.upload');
    Route::post('fuel-statements/save', [FuelStatementController::class, 'save'])->name('fuel-statements.save');

    Route::get('fuel-verification', [FuelVerificationRecordController::class, 'showVerificationPage'])->name('fuel-verification.listing');
    Route::get('fuel-verification/get-records', [FuelVerificationRecordController::class, 'getVerificationRecords'])->name('fuel-verification.records');
    Route::get('fuel-verification/{id}/show', [FuelVerificationRecordController::class, 'showSingleRecordPage'])->name('fuel-verification.show');
    Route::post('fuel-verification/verify', [FuelVerificationRecordController::class, 'runVerification'])->name('fuel-verification.verify');
    Route::get('fuel-verification/summary', [FuelVerificationRecordController::class, 'getSummary'])->name('fuel-verification.summary');
    Route::get('fuel-verification/verified', [FuelVerificationRecordController::class, 'getVerifiedEntries'])->name('fuel-verification.verified');
    Route::get('fuel-verification/missing', [FuelVerificationRecordController::class, 'getMissingEntries'])->name('fuel-verification.missing');
    Route::get('fuel-verification/unknown', [FuelVerificationRecordController::class, 'getUnknownPayments'])->name('fuel-verification.unknown');
    Route::post('fuel-verification/unknown/resolve', [FuelVerificationRecordController::class, 'resolveUnknown'])->name('fuel-verification.unknown.resolve');
    Route::post('fuel-verification/unknown/reset', [FuelVerificationRecordController::class, 'resetUnknown'])->name('fuel-verification.unknown.reset');
    Route::get('fuel-verification/unfueled', [FuelVerificationRecordController::class, 'getUnfueledRoutes'])->name('fuel-verification.unfueled');
    Route::post('fuel-verification/unfueled/resolve', [FuelVerificationRecordController::class, 'resolveUnfueled'])->name('fuel-verification.unfueled.resolve');
    Route::post('fuel-verification/unfueled/reset', [FuelVerificationRecordController::class, 'resetUnfueled'])->name('fuel-verification.unfueled.reset');
    Route::get('fuel-verification/unutilized', [FuelVerificationRecordController::class, 'getUnUtilizedLpos'])->name('fuel-verification.unutilized');

    Route::get('fuel-approval', [FuelEntryApprovalController::class, 'showVerificationPage'])->name('fuel-approval.index');
    Route::get('fuel-approval/records', [FuelEntryApprovalController::class, 'getCompleteRecords'])->name('fuel-approval.records');
    Route::get('fuel-approval/show', [FuelEntryApprovalController::class, 'showSingleRecordPage'])->name('fuel-approval.records.show');
    Route::get('fuel-approval/summary', [FuelEntryApprovalController::class, 'getSummary'])->name('fuel-approval.summary');
    Route::get('fuel-approval/verified', [FuelEntryApprovalController::class, 'getVerifiedEntries'])->name('fuel-approval.verified');
    Route::get('fuel-approval/unknown', [FuelEntryApprovalController::class, 'getUnknownPayments'])->name('fuel-approval.unknown');
    Route::get('fuel-approval/unknown-pending', [FuelEntryApprovalController::class, 'getUnknownPending'])->name('fuel-approval.unknown-pending');
    Route::post('fuel-approval/verified/approve', [FuelEntryApprovalController::class, 'approveVerified'])->name('fuel-approval.approve-verified');

    Route::get('delivery-center/loading-list', [DeliveryScheduleController::class, 'getLoadingList'])->name('delivery-center.loading-list');
    Route::get('delivery-center/delivery-report', [DeliveryScheduleController::class, 'getDeliveryReport'])->name('delivery-center.delivery-report');
    Route::get('delivery-center/performance', [DeliveryScheduleController::class, 'getPerformanceReport'])->name('delivery-center.performance');
    Route::get('delivery-center/polylines', [DeliveryScheduleController::class, 'getMainTripPolyline'])->name('delivery-center.polylines');

    Route::get('device-type/delete/{id}', [DeviceTypeController::class,'delete'])->name('device-type.delete');
    Route::resource('device-type', DeviceTypeController::class);
    Route::get('device-sim-card/delete/{id}', [DeviceSimCardController::class,'delete'])->name('device-sim-card.delete');
    Route::resource('device-sim-card', DeviceSimCardController::class);
    Route::group(['prefix' => 'device-center', 'as' => 'device-center.'], function () {
        Route::get('/', [DeviceCenterController::class, 'index'])->name('index');
        Route::get('/{device}', [DeviceCenterController::class, 'show'])->name('show');
        
        Route::post('/store', [DeviceCenterController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [DeviceCenterController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [DeviceCenterController::class, 'update'])->name('update');
        
    });

    Route::get('/device/create', [DeviceCenterController::class, 'create'])->name('device-center.create');
    Route::get('/device/bulk-upload', [DeviceCenterController::class, 'bulk_upload'])->name('device-center.bulk-upload');
    Route::post('/device/bulk-upload', [DeviceCenterController::class, 'bulk_upload_process'])->name('device-center.bulk-upload');
    Route::post('/device/bulk-upload-store', [DeviceCenterController::class, 'bulk_upload_store'])->name('device-center.bulk-upload-store');

    Route::get('/device/bulk-allocate', [DeviceCenterController::class, 'bulk_allocate'])->name('device-center.bulk-allocate');
    Route::post('/device/bulk-allocate', [DeviceCenterController::class, 'bulk_allocate_process'])->name('device-center.bulk-allocate');
    Route::post('/device/bulk-allocate-store', [DeviceCenterController::class, 'bulk_allocate_store'])->name('device-center.bulk-allocate-store');
    
    Route::get('/get-device-holder/{id}',[DeviceCenterController::class, 'getDeviceHolder']);
    Route::resource('device-repair', DeviceRepairController::class);
});
