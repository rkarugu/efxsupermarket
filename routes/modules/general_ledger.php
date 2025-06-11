<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\GlReconciliationController;

Route::group(['prefix' => 'admin', 'middleware' => ['AdminLoggedIn', 'ip-blocker']], function () {
    Route::get('gl-reconciliation/overview', [GlReconciliationController::class, 'overview'])->name('gl-reconciliation.overview');
    Route::get('gl-reconciliation/verification', [GlReconciliationController::class, 'list'])->name('gl-reconciliation.list');
    Route::get('gl-reconciliation/closed', [GlReconciliationController::class, 'closed_recon'])->name('gl-reconciliation.closed');
    Route::get('gl-reconciliation/create', [GlReconciliationController::class, 'create'])->name('gl-reconciliation.create');
    Route::post('gl-reconciliation/store', [GlReconciliationController::class, 'store'])->name('gl-reconciliation.store');
    Route::get('gl-reconciliation/view/{id}', [GlReconciliationController::class, 'view'])->name('gl-reconciliation.view');
    Route::get('gl-reconciliation/edit/{id}', [GlReconciliationController::class, 'edit'])->name('gl-reconciliation.edit');
    Route::post('gl-reconciliation/update/{id}', [GlReconciliationController::class, 'update'])->name('gl-reconciliation.update');
    Route::get('gl-reconciliation/payment/datatable/{id}', [GlReconciliationController::class, 'payment_datatable'])->name('gl-reconciliation.payment-table');
    Route::get('gl-reconciliation/begin-balance/datatable/{id}', [GlReconciliationController::class, 'begin_balance_datatable'])->name('gl-reconciliation.begin_balance-table');
    Route::get('gl-reconciliation/voucher/datatable/{id}', [GlReconciliationController::class, 'voucher_datatable'])->name('gl-reconciliation.voucher-table');
    Route::get('gl-reconciliation/matched/datatable/{id}', [GlReconciliationController::class, 'matched_datatable'])->name('gl-reconciliation.matched-table');
    Route::get('gl-reconciliation/get-bank-balance',[GlReconciliationController::class, 'get_bank_balance'])->name('gl-reconciliation.get_bank_balance');
    Route::get('gl-reconciliation/re-verify/{id}',[GlReconciliationController::class, 're_verify'])->name('gl-reconciliation.re-verify');
    Route::post('gl-reconciliation/close/{id}',[GlReconciliationController::class, 'close_recon'])->name('gl-reconciliation.close-recon');

});