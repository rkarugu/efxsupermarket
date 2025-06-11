<?php

namespace App\Providers;

use App\Interfaces\MpesaPaymentInterface;
use App\Interfaces\SmsService;
use App\Services\AirTouchSmsService;
use App\Services\DarajaDisbursementService;
use App\Services\InfoSkySmsService;
use App\Services\PesaFlowMpesaPaymentService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

use App\Model\WaStockMove;
use App\Observers\StockMoveObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        Activity::saving(function (Activity $activity) {
            $activity->properties = $activity->properties->put('ip', request()->ip());
            $activity->properties = $activity->properties->put('user_agent', request()->header('User-Agent'));
        });

        WaStockMove::observe(StockMoveObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $mpesaPaymentsProvider = config('app.mpesa_payments_provider');
        switch ($mpesaPaymentsProvider) {
            case 'daraja':
                $this->app->singleton(MpesaPaymentInterface::class, DarajaDisbursementService::class);
                break;
            case 'pesaflow':
                $this->app->singleton(MpesaPaymentInterface::class, PesaFlowMpesaPaymentService::class);
                break;
            default:
                break;
        }

        $smsProvider = config('app.sms_provider');
        switch ($smsProvider) {
            case 'infosky':
                $this->app->singleton(SmsService::class, InfoSkySmsService::class);
                break;
            default:
                $this->app->singleton(SmsService::class, AirTouchSmsService::class);
                break;
        }
    }
}
