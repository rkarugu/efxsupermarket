<?php

use App\Http\Controllers\Admin\GrnController;
use App\Http\Controllers\Admin\SupplierBillController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['AdminLoggedIn', 'ip-blocker']], function () {
    // GRNs
    Route::put('grns/send-documents/{grn_number}', [GrnController::class, 'sendDocuments'])->name('grns.send-documents');
    Route::put('grns/receive-documents/{grn_number}', [GrnController::class, 'receiveDocuments'])->name('grns.receive-documents');

    Route::resource('supplier-bills', SupplierBillController::class);
});
