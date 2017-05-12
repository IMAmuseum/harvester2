<?php

namespace Imamuseum\Harvester2;

use Illuminate\Support\ServiceProvider;

class HarvesterServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/harvester.php' => config_path('harvester.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../config/document_store.php' => config_path('document_store.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            'Imamuseum\Harvester2\Console\Commands\Harvest',
            'Imamuseum\Harvester2\Console\Commands\CreateIndex',
        ]);
    }
}
