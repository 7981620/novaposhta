<?php

namespace Agenta\AgentaNovaPoshta;

use Agenta\AgentaNovaPoshta\Console\Commands\NovaPoshta\ImportCitiesCommand;
use Agenta\AgentaNovaPoshta\Console\Commands\NovaPoshta\ImportRegionsCommand;
use Agenta\AgentaNovaPoshta\Console\Commands\NovaPoshta\UpdateWarehousesCommand;
use Agenta\AgentaNovaPoshta\Livewire\NovaPoshtaWarehouse;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AgentaNovaPoshtaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */

        Livewire::component('novaposhta-select-warehouse', NovaPoshtaWarehouse::class);


        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'agentanovaposhta');
         $this->loadViewsFrom(__DIR__.'/../resources/views', 'agentanovaposhta');
         $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('agentanovaposhta.php'),
            ], 'config');

            // Publishing the views.
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/agentanovaposhta'),
            ], 'views-agentanovaposhta');

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/agentanovaposhta'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/agentanovaposhta'),
            ], 'lang');*/

            // Registering package commands.
             $this->commands([
                 ImportCitiesCommand::class,
                 ImportRegionsCommand::class,
                 UpdateWarehousesCommand::class
             ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'agentanovaposhta');

        // Register the main class to use with the facade
        $this->app->singleton('agentanovaposhta', function () {
            return new AgentaNovaPoshta;
        });
    }
}
