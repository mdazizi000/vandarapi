<?php

namespace MohmdAzizi\VandarApi;

use App\Classes\Vandar\Vandar;
use Illuminate\Support\ServiceProvider;

class VandarServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes(
            [
                __DIR__.'/../config/vandar.php'=>config_path('vandar.php')
            ]
        );
    }

    public function register()
    {
        $this->app->singleton(Vandar::class,function (){
            return new Vandar();
        });
     }
}