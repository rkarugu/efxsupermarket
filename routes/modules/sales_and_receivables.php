<?php

use App\Http\Controllers\Admin\Incentives\IncentiveSettingsController;
use App\Http\Controllers\Admin\PerformanceController;
use App\Http\Controllers\Admin\TransactionMispostFixController;
use App\Http\Controllers\PaymentReconciliationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TransactionHistoryController;
use App\Http\Controllers\Admin\BankStatementMispostFixController;
use App\Http\Controllers\Admin\ItemMarginsReportController;

use App\Http\Controllers\Admin\SmallPacksContoller;
use App\Http\Controllers\BankingApprovalController;
use App\Http\Controllers\Admin\GroupRepresentativeController;
use App\Http\Controllers\DebtorsReportController;
use App\Http\Controllers\GlReconciliationController;
use App\Http\Controllers\PosBankingController;
use App\Http\Controllers\PoscashBankingReportController;
use App\Http\Controllers\WaCustomerController;
use App\Http\Controllers\Admin\RunEodController;

Route::group(['middleware' => ['AdminLoggedIn', 'ip-blocker']], function () {
    Route::group(['prefix' => 'eod-routine'], function () {
        Route::get('/', [RunEodController::class, 'index'])->name('eod-routine.index');
        Route::get('/run-eod-routine', [RunEodController::class, 'runRoutine'])->name('eod-routine.run-routine');
        Route::get('/run-eod-routine/fetch-return-summary', [RunEodController::class, 'fetchReturnsSummary'])->name('eod-routine.fetch-return-summary');
        Route::post('/run-eod-routine/verify-returns', [RunEodController::class, 'verifyReturns'])->name('eod-routine.verify-returns');
        Route::get('/run-eod-routine/fetch-splits', [RunEodController::class, 'fetchSplits'])->name('eod-routine.fetch-splits');
        Route::post('/run-eod-routine/verify-splits', [RunEodController::class, 'verifySplits'])->name('eod-routine.verify-splits');
        Route::get('/run-eod-routine/fetch-binless-items', [RunEodController::class, 'fetchBinlessItems'])->name('eod-routine.fetch-binlessItems');
        Route::post('/run-eod-routine/verify-binless-tems', [RunEodController::class, 'verifyBinlessItems'])->name('eod-routine.verify-binless-items');
        Route::get('/run-eod-routine/fetch-sales-vs-stocks', [RunEodController::class, 'fetchSalesVsStocks'])->name('eod-routine.fetch-sales-vs-stocks');
        Route::post('/run-eod-routine/verify-stocks-vs-sales', [RunEodController::class, 'verifySalesVsStocks'])->name('eod-routine.verify-sales-vs-stocks');
        Route::get('/run-eod-routine/fetch-cash-at-hand', [RunEodController::class, 'fetchCashAtHand'])->name('eod-routine.fetch-cash-at-hand');
        Route::post('/run-eod-routine/verify-cash-at-hand', [RunEodController::class, 'verifyCashAtHand'])->name('eod-routine.verify-cash-at-hand');
        Route::post('/run-eod-routine/balance-transactions', [RunEodController::class, 'balanceTransactions'])->name('eod-routine.balance-transactions');


        Route::get('/run-eod-routine/fetch-number-series', [RunEodController::class, 'fetchNumberSeries'])->name('eod-routine.fetch-number-series');
        // Route::post('/run-eod-routine/verify-number-series', [RunEodController::class, 'verifyNumberSeries'])->name('eod-routine.verify-number-series');
        Route::post('/run-eod-routine/close-day', [RunEodController::class, 'closeDay'])->name('eod-routine.close-day');

    });
    Route::group(['prefix' => 'reconciliation'], function () {
        Route::group(['prefix' => 'payments'], function () {
            Route::get('overview', [PaymentReconciliationController::class, 'showOverviewPage'])->name('payment-reconciliation.overview');
            Route::get('overview/debtors-balance', [PaymentReconciliationController::class, 'getDebtorsBalance'])->name('payment-reconciliation.overview.debtors-balance');
            Route::get('overview/summary', [PaymentReconciliationController::class, 'getSummary'])->name('payment-reconciliation.overview.summary');
            Route::get('overview/salesVsReceipts', [PaymentReconciliationController::class, 'getSalesVsReceipts'])->name('payment-reconciliation.overview.salesVsReceipts');
            Route::get('overview/recon-issues', [PaymentReconciliationController::class, 'getReconIssues'])->name('payment-reconciliation.overview.getReconIssues');
            Route::get('overview/recon-resolutions', [PaymentReconciliationController::class, 'getReconResolutions'])->name('payment-reconciliation.overview.getReconResolutions');
        });
    });

    Route::get('transaction-history', [TransactionHistoryController::class, 'index'])->name('transaction-history');
    Route::post('transaction-history', [TransactionHistoryController::class, 'fetch'])->name('transactio-history');
    Route::group(['prefix' =>  'admin'], function () {
        Route::get('salesman-performance-report', [PerformanceController::class, 'salesmanPerformance'])->name('salesman-performance-report');
        Route::get('driver-performance-report', [PerformanceController::class, 'driverPerformance'])->name('driver-performance-report');

        Route::get('salesman-performance-report/salesman-shifts-details/{routeId}/{start}/{end}', [PerformanceController::class, 'salesmanShiftdetails'])->name('salesman-performance-shift-details');
        Route::get('driver-performance-report/driver-shifts-details/{userId}/{start}/{end}', [PerformanceController::class, 'driverShiftdetails'])->name('driver-performance-shift-details');

        Route::get('salesman-performance-report/salesman-route-tonnage', [PerformanceController::class, 'routeTonnageDetails'])->name('salesman-performance-route-tonnage-details');
        Route::get('salesman-performance-report/met-customers', [PerformanceController::class, 'metUnmetSummary'])->name('salesman-performance-route-met-unmet-summary');
        Route::get('salesman-performance-report/met-customers-details/{routeId}/{date}', [PerformanceController::class, 'metUnmetSummaryDetails'])->name('salesman-performance-route-met-unmet-summary.details');

        Route::get('driver-performance-report/store-dispatch-details/{userId}/{start}/{end}', [PerformanceController::class, 'diverDispatchDetails'])->name('driver-performance.driver-dispatch-details');
        Route::get('driver-performance-report/store-dispatch-details/late-dispatches/{scheduleId}', [PerformanceController::class, 'getDeliveryScheduleLateDispatches'])->name('driver-performance.driver-dispatch-details.late');




        Route::resource('incentive-settings', IncentiveSettingsController::class);

        //margin reports
        Route::get('items-margin-report', [ItemMarginsReportController::class, 'index'])->name('item-margins-report.index');
    });

    Route::group(['prefix' => 'transaction-mispost'], function () {
        Route::get('/', [TransactionMispostFixController::class, 'index'])->name('transaction-mispost.index');
        Route::get('/create', [TransactionMispostFixController::class, 'create'])->name('transaction-mispost.create');
        Route::post('/fetch-transaction', [TransactionMispostFixController::class, 'fetch_transaction'])->name('transaction-mispost.fetch_transaction');
        Route::post('/store', [TransactionMispostFixController::class, 'store'])->name('transaction-mispost.store');
        Route::post('/store-single', [TransactionMispostFixController::class, 'store_single'])->name('transaction-mispost.store-single');
    });

    Route::group(['prefix' => 'bank-statement-mispost'], function () {
        Route::get('/', [BankStatementMispostFixController::class, 'index'])->name('bank-statement-mispost.index');
        Route::get('/create', [BankStatementMispostFixController::class, 'create'])->name('bank-statement-mispost.create');
        Route::post('/fetch-statement', [BankStatementMispostFixController::class, 'fetch_statement'])->name('bank-statement-mispost.fetch_statement');
        Route::post('/store', [BankStatementMispostFixController::class, 'store'])->name('bank-statement-mispost.store');
    });

    Route::group(['prefix' => 'small-packs'], function () {
        Route::get('store-loading-sheets', [SmallPacksContoller::class, 'store_loading_sheets'])->name('small-packs.store-loading-sheets');
        Route::get('loading-sheets/{dispatch}', [SmallPacksContoller::class, 'loading_sheets'])->name('small-packs.loading-sheets');
        Route::get('view-loading-sheets/{dispatch}/{bin}', [SmallPacksContoller::class, 'view_loading_sheets'])->name('small-packs.view-loading-sheets');
        Route::post('process-dispatch/', [SmallPacksContoller::class, 'process_dispatch'])->name('small-packs.process-dispatch');

        Route::get('dispatched', [SmallPacksContoller::class, 'dispatched'])->name('small-packs.dispatched');
        Route::get('dispatched/{id}', [SmallPacksContoller::class, 'dispatched_view'])->name('small-packs.dispatched-view');
    });

    Route::group(['prefix' => 'banking'], function () {
        Route::get('route/overview', [BankingApprovalController::class, 'showRouteOverviewPage'])->name('route-banking-approval.overview');
        Route::get('route/overview/records', [BankingApprovalController::class, 'getRecords']);
        Route::get('route/details', [BankingApprovalController::class, 'showRouteDetailsPage']);
        Route::get('route/details/verified', [BankingApprovalController::class, 'getVerifiedReceipts']);
        Route::get('route/details/unverified', [BankingApprovalController::class, 'getUnVerifiedReceipts']);
        Route::post('route/details/unverified/suspend', [BankingApprovalController::class, 'suspendUnverified']);
        Route::get('route/details/others', [BankingApprovalController::class, 'getOtherReceivables']);
        Route::get('route/details/fraud', [BankingApprovalController::class, 'getFraudTransactions']);
        Route::post('route/details/approve', [BankingApprovalController::class, 'approveBanking']);
        Route::group(['prefix'=>'route'], function(){
            Route::get('records/sales', [BankingApprovalController::class, 'getSales']);
            Route::get('records/returns', [BankingApprovalController::class, 'getReturns']);
            Route::get('records/eazzy', [BankingApprovalController::class, 'getEazzy']);
            Route::get('records/eb-main', [BankingApprovalController::class, 'getEbMain']);
            Route::get('records/vooma', [BankingApprovalController::class, 'getVooma']);
            Route::get('records/kcb-main', [BankingApprovalController::class, 'getKcbMain']);
            Route::get('records/mpesa', [BankingApprovalController::class, 'getMpesa']);
            Route::get('bank-summary', [BankingApprovalController::class, 'getBankingSummary']);
            Route::get('route-sale-banking-overview/print', [BankingApprovalController::class, 'printRouteSaleBankingOverview']);



        });

        Route::group(['prefix' => 'pos'], function () {
            Route::get('daily-overview', [PosBankingController::class, 'showDailyOverviewPage'])->name('pos-banking.daily-overview');
            Route::get('daily-overview/details', [PosBankingController::class, 'showOverviewPage'])->name('pos-banking.overview');
            Route::get('daily-records', [PosBankingController::class, 'getDailyRecords']);
            Route::get('records', [PosBankingController::class, 'getRecords']);
            Route::get('bank-summary', [PosBankingController::class, 'getBankingSummary']);
            Route::post('records/verify', [PosBankingController::class, 'runVerification']);
            Route::get('records/cdms', [PosBankingController::class, 'getCdms']);
            Route::get('records/drops', [PosBankingController::class, 'getDrops']);
            Route::post('records/cdms/search', [PosBankingController::class, 'searchCdmDeposit']);
            Route::post('records/cdms/allocate', [PosBankingController::class, 'allocateCdmDeposit']);
            Route::get('records/unknown', [PosBankingController::class, 'getUnknown']);
            Route::get('records/sales', [PosBankingController::class, 'getSales']);
            Route::get('records/returns', [PosBankingController::class, 'getReturns']);
            Route::get('records/unverified', [PosBankingController::class, 'getUnverified']);
            Route::get('records/cash-banking', [PosBankingController::class, 'getCashBankingRecords']);
            Route::post('records/cash-banking/search', [PosBankingController::class, 'searchCbDeposit']);
            Route::post('records/cash-banking/allocate', [PosBankingController::class, 'allocateCbDeposit']);
            Route::get('records/eazzy', [PosBankingController::class, 'getEazzy']);
            Route::get('records/eb-main', [PosBankingController::class, 'getEbMain']);
            Route::get('records/vooma', [PosBankingController::class, 'getVooma']);
            Route::get('records/kcb-main', [PosBankingController::class, 'getKcbMain']);
            Route::get('records/mpesa', [PosBankingController::class, 'getMpesa']);
            Route::get('records/manual-allocations', [PosBankingController::class, 'getManualAllocations']);
            Route::get('cash-sale-banking-overview/print', [PosBankingController::class, 'printCashSaleBankingOverview']);
            Route::get('records/short-banking', [PosBankingController::class, 'getShortBankingRecords']);
            Route::post('records/short-banking/allocate', [PosBankingController::class, 'allocateShortBanking'])->name('allocate-pos-short-banking');
            Route::get('opening-balance', [PosBankingController::class, 'getOpeningBalance']);
            Route::get('short-bankings/details', [PosBankingController::class, 'shortBankingDetails'])->name('short-bankings-details');
            Route::get('daily-records/balances', [PosBankingController::class, 'getDailyRecordsBalances']);
            Route::get('daily-short-bankings-breakdown', [PosBankingController::class, 'shortBankingDetailsBreakdown']);
            Route::get('fetch-short-banking-comment-record', [PosBankingController::class, 'getShortBankingComment']);
            Route::post('edit-short-banking-comment', [PosBankingController::class, 'editShortBankingComment'])->name('edit-short-banking-comment');

            Route::post('complete-verification', [PosBankingController::class, 'completeVerification']);
            Route::post('approve-and-close', [PosBankingController::class, 'approveAndCloseBanking']);


        });

        Route::group(['prefix' => 'gl-recon'], function () {
            Route::get('overview', [GlReconciliationController::class, 'showOverviewPage'])->name('gl-recon.overview');
            Route::get('overview/records', [GlReconciliationController::class, 'getRecords']);
            Route::get('opening-balances', [GlReconciliationController::class, 'getOpeningBalances']);
            Route::post('opening-balances/update', [GlReconciliationController::class, 'updateOpeningBalances']);
        });
    });

    Route::group(['prefix' => 'group-representative'], function () {
        Route::get('/', [GroupRepresentativeController::class, 'index'])->name('group-rep.index');
        Route::get('/view/{id}', [GroupRepresentativeController::class, 'show'])->name('group-rep.view');
        Route::post('add-route', [GroupRepresentativeController::class, 'add_route'])->name('group-rep.add-route');
        Route::post('ressign-route', [GroupRepresentativeController::class, 'ressign_route'])->name('ressign-route');
        Route::post('ressign-all-routes', [GroupRepresentativeController::class, 'ressign_all_routes'])->name('ressign-all-routes');
        
    });

    Route::group(['prefix' => 'cashier-management'], function () {
        Route::get('cash-banking-report', [PoscashBankingReportController::class, 'showReportPage'])->name('cashier-management.cash-banking-report');
        Route::get('cash-banking-report/generate', [PoscashBankingReportController::class, 'generateReport'])->name('cashier-management.cash-banking-report.generate');
        Route::get('cash-banking-report/generate-chief-cashier', [PoscashBankingReportController::class, 'generateChiefCashierReport']);


        /*drop cash from Cashier Side*/
        Route::post('/drop/send-otp', [App\View\Components\DropComponent::class, 'sendOtp'])->name('drop.sendOtp');
        Route::post('/drop/verify-otp', [App\View\Components\DropComponent::class, 'verifyOtp'])->name('drop.verifyOtp');
        Route::post('/drop/dropcash', [App\View\Components\DropComponent::class, 'dropcash'])->name('drop.dropcash');
        Route::post('/drop/call_cashier', [App\View\Components\DropComponent::class, 'callCashier'])->name('drop.call-cashier');
    });

    Route::get('customer-accounts/{customer}/settle-from-fraud', [WaCustomerController::class, 'showFraudPostingPage'])->name('maintain-customers.settle-from-fraud');
    Route::post('customer-accounts/settle-from-fraud', [WaCustomerController::class, 'settleAccountFromFraud']);

    Route::get('sales-and-receivables/reports/debtors-report', [DebtorsReportController::class, 'index'])->name('debtors-report.index');
    Route::get('sales-and-receivables/reports/debtors-report/generate', [DebtorsReportController::class, 'generate'])->name('debtors-report.generate');


});
