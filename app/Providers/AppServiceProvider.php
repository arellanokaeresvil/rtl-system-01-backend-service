<?php

namespace App\Providers;

use App\Services\Egg\EggService;
use App\Services\Auth\AuthService;
use App\Services\Bird\BirdService;
use App\Services\Feed\FeedService;
use App\Services\Batch\BatchService;
use App\Services\Utils\ResponseService;
use Illuminate\Support\ServiceProvider;
use App\Services\Egg\EggServiceInterface;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Bird\BirdServiceInterface;
use App\Services\Feed\FeedServiceInterface;
use App\Services\Batch\BatchServiceInterface;
use App\Services\Utils\ResponseServiceInterface;
use App\Repository\Mortality\MortalityRepository;
use App\Repository\Mortality\MortalityRepositoryInterface;
use App\Services\Sale\SaleServiceInterface;
use App\Services\Sale\SaleService;
use App\Services\Expense\ExpenseServiceInterface;
use App\Services\Expense\ExpenseService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(ResponseServiceInterface::class, ResponseService::class);
        $this->app->bind(BatchServiceInterface::class, BatchService::class);
        $this->app->bind(FeedServiceInterface::class, FeedService::class);
        $this->app->bind(MortalityRepositoryInterface::class, MortalityRepository::class);
        $this->app->bind(BirdServiceInterface::class, BirdService::class);
        $this->app->bind(EggServiceInterface::class, EggService::class);
        $this->app->bind(SaleServiceInterface::class, SaleService::class);
        $this->app->bind(ExpenseServiceInterface::class, ExpenseService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
