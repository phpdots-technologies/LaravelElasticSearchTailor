<?php

namespace PHPDots\ElasticSearchTailor;

use Illuminate\Support\ServiceProvider;
use Elasticsearch\ClientBuilder;

class ElasticSearchTailorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'elasticConfig.php' => config_path('elasticConfig.php'),
        ]);
    }
}
