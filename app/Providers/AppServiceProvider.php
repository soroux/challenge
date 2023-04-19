<?php

namespace App\Providers;

use App\Factory\Company\CompanyFactory;
use App\Factory\Company\CompanyFactoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(CompanyFactoryInterface::class,function ($app){
            return new CompanyFactory($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
