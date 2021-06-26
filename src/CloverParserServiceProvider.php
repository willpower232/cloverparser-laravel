<?php

namespace WillPower232\CloverParserLaravel;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class CloverParserServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $basePath = __DIR__ . '/../';
        $configPath = $basePath . 'config/clover-parser.php';

        //@codeCoverageIgnoreStart
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([
                $configPath => config_path('clover-parser.php'),
            ], 'config');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('clover-parser');
        }
        //@codeCoverageIgnoreEnd

        $this->mergeConfigFrom($configPath, 'clover-parser');

        $this->loadViewsFrom($basePath . 'resources/views', 'clover-parser');

        $this->publishes([
            $basePath . 'resources/views' => resource_path('views/vendor/clover-parser'),
        ], 'views');
    }
}
