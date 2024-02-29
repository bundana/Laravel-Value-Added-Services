<?php
namespace Bundana\Services;

use Illuminate\Support\ServiceProvider;

class BundanaServiceProvider extends ServiceProvider{
 public function boot(){

    // Publish the config file when the user runs "php artisan vendor:publish"
    $this->publishes([__DIR__.'/config.php' => config_path('bundana-config.php'),], 'config');

     // Merging configurations
     $this->mergeConfigFrom(__DIR__.'/config.php', 'bundana');

 }

 public function register(){}
}
