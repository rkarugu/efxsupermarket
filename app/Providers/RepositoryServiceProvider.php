<?php

namespace App\Providers;


use App\Interfaces\GLTagInterface;
use App\Interfaces\ProjectInterface;
use App\Interfaces\Finance\GeneralLedgerInterface;
use App\Interfaces\Finance\ChartOfAccountsInterface;
use App\Interfaces\Inventory\ApprovalItemInterface;
use App\Interfaces\LocationStoreInterface;
use App\Interfaces\Finance\BankReconciliationInterface;

use App\Repositories\GLTagRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\Finance\GeneralLedgerRepository;
use App\Repositories\Finance\ChartOfAccountsRepository;
use App\Repositories\Inventory\ApprovalItemRepository;
use App\Repositories\LocationStoreRepository;
use App\Repositories\Finance\BankReconciliationRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(GLTagInterface::class, GLTagRepository::class);
        $this->app->bind(ProjectInterface::class, ProjectRepository::class);
        $this->app->bind(GeneralLedgerInterface::class, GeneralLedgerRepository::class);
        $this->app->bind(ChartOfAccountsInterface::class, ChartOfAccountsRepository::class);
        $this->app->bind(ApprovalItemInterface::class, ApprovalItemRepository::class);
        $this->app->bind(LocationStoreInterface::class, LocationStoreRepository::class);
        $this->app->bind(BankReconciliationInterface::class, BankReconciliationRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
