<?php

namespace App\Providers;

use App\Repositories\Eloquents\CustomerRepository;
use App\Repositories\Eloquents\ProductRepository;
use App\Repositories\Interfaces\ICustomerRepository;
use App\Repositories\Interfaces\IProductRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ICustomerRepository::class, CustomerRepository::class);
        $this->app->singleton(IProductRepository::class, ProductRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
