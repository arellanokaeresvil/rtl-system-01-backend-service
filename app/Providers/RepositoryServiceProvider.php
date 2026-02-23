<?php

namespace App\Providers;

use App\Repository\Egg\EggRepository;

use App\Repository\Base\BaseRepository;
use App\Repository\Feed\FeedRepository;
use App\Repository\User\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Repository\Batch\BatchRepository;
use App\Repository\Sale\EggSaleRepository;
use App\Repository\Sale\BirdSaleRepository;
use App\Repository\Feed\FeedUsageRepository;
use App\Repository\Culling\CullingRepository;
use App\Repository\Expense\ExpenseRepository;
use App\Repository\Egg\EggRepositoryInterface;
use App\Repository\Base\BaseRepositoryInterface;
use App\Repository\Feed\FeedRepositoryInterface;
use App\Repository\User\UserRepositoryInterface;
use App\Repository\Mortality\MortalityRepository;
use App\Repository\Batch\BatchRepositoryInterface;
use App\Repository\Sale\EggSaleRepositoryInterface;
use App\Repository\Sale\BirdSaleRepositoryInterface;
use App\Repository\Feed\FeedUsageRepositoryInterface;
use App\Repository\Culling\CullingRepositoryInterface;
use App\Repository\Expense\ExpenseRepositoryInterface;
use App\Repository\Mortality\MortalityRepositoryInterface;
use App\Repository\ExpenseCategory\ExpenseCategoryRepository;
use App\Repository\ExpenseCategory\ExpenseCategoryRepositoryInterface;


class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BatchRepositoryInterface::class, BatchRepository::class);
        $this->app->bind(BaseRepositoryInterface::class, BaseRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(EggRepositoryInterface::class, EggRepository::class);
        $this->app->bind(FeedRepositoryInterface::class, FeedRepository::class);
        $this->app->bind(FeedUsageRepositoryInterface::class, FeedUsageRepository::class);
        $this->app->bind(MortalityRepositoryInterface::class, MortalityRepository::class);
        $this->app->bind(CullingRepositoryInterface::class, CullingRepository::class);
        $this->app->bind(ExpenseCategoryRepositoryInterface::class, ExpenseCategoryRepository::class);
        $this->app->bind(ExpenseRepositoryInterface::class, ExpenseRepository::class);
        $this->app->bind(EggSaleRepositoryInterface::class, EggSaleRepository::class);
        $this->app->bind(BirdSaleRepositoryInterface::class, BirdSaleRepository::class);
    }
    


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}