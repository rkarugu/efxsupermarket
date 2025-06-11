<?php

use App\Http\Controllers\Admin\FuelLPOController;
use App\Http\Controllers\Admin\FuelStationController;
use App\Http\Controllers\Admin\FuelSuppliersController;
use App\Http\Controllers\Admin\NewFuelEntryController;
use App\Http\Controllers\Admin\VehicleCentreContoller;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => ['AdminLoggedIn', 'ip-blocker']], function () {
    Route::resource('fuel-stations', FuelStationController::class);
    Route::resource('fuel-entries', NewFuelEntryController::class);
    Route::resource('fuel-suppliers', FuelSuppliersController::class);

    Route::get('fuel-lpos', [FuelLPOController::class, 'index'])->name('fuel-lpos.index');
    Route::get('fuel-lpos/create', [FuelLPOController::class, 'create'])->name('fuel-lpos.create');
    Route::get('fuel-lpos/store', [FuelLPOController::class, 'store'])->name('fuel-lpos.store');
    Route::get('fuel-purchase-orders/pending', [FuelLPOController::class, 'showPending'])->name('fuel-lpos.pending');
    Route::get('fuel-purchase-orders/pending/details/{id}', [FuelLPOController::class, 'pendingFuelEntriesDetails'])->name('fuel-lpos.pending.details');
    Route::get('fuel-purchase-orders/approve/{id}', [FuelLPOController::class, 'approveLpo'])->name('fuel-lpos.approveLpo');
    Route::get('fuel-purchase-orders/confirmed', [FuelLPOController::class, 'confirmedEntries'])->name('fuel-lpos.confirmed');
    Route::get('fuel-purchase-orders/expired', [FuelLPOController::class, 'expiredEntries'])->name('fuel-lpos.expired');
    Route::post('fuel-purchase-orders/reactivate', [FuelLPOController::class, 'reactivateExpiredLpo'])->name('reactivate-expired-lpos');
    Route::get('fuel-purchase-orders/approved/details/{id}', [FuelLPOController::class, 'confirmFuelEntriesDetails'])->name('fuel-lpos.approved.details');
    Route::get('fuel-purchase-orders/confirm/{id}', [FuelLPOController::class, 'confirmLpo'])->name('fuel-lpos.confirmLpo');
    Route::post('fuel-purchase-orders/confirm-all', [FuelLPOController::class, 'confirmSelected'])->name('fuel-lpos.confirm-selected');
    Route::get('fuel-purchase-orders/processed', [FuelLPOController::class, 'processedEntries'])->name('fuel-lpos.processed');

    Route::get('fuel-lpos/expire/{id}', [FuelLPOController::class, 'expireLpo'])->name('fuel-lpos.expire');

    Route::group(['prefix'=> 'vehicles/vehicle-center'], function(){
        Route::get('/{vehicle}', [VehicleCentreContoller::class, 'show'])->name('vehicle-center.show');
    });
    

});