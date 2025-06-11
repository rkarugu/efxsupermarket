<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\HqDashboardReportsController;
use App\Http\Controllers\ChairmanDashboardController;


Route::group(['prefix' => 'admin', 'middleware' => ['AdminLoggedIn', 'ip-blocker']], function () {
    Route::get('hq-dashboard-reports', [HqDashboardReportsController::class, 'index'])->name('hq-dashboard.index');
    Route::get('hq-dashboard-reports/order-taking-summary', [HqDashboardReportsController::class, 'orderTakingAndPosSummary'])->name('hq-dashboard.order-taking-summary');


    //Chairmans Dashboard
    Route::get('chairman-dashboard/index', [ChairmanDashboardController::class, 'index'])->name('chairman-dashboard.general.index');
    Route::get('chairman-dashboard/index/sales-report', [ChairmanDashboardController::class, 'indexSalesReport'])->name('chairman-dashboard.general.index.sales-report');
    Route::get('chairman-dashboard/fuel-entries/details/{id}', [ChairmanDashboardController::class, 'fuelEntryDetails'])->name('chairman-dashboard.general.index.fuel-entries');
 
});