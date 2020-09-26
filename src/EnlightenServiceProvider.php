<?php

namespace Styde\Enlighten;

use Illuminate\Config\Repository as Config;
use Illuminate\Support\ServiceProvider;

class EnlightenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->addDatabaseConnection($this->app->config);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->mergeConfigFrom(__DIR__.'/../config/enlighten.php', 'enlighten');

        if ($this->app->environment('local', 'testing')) {
            $this->loadroutesFrom(__DIR__ . '/../routes/web.php');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'enlighten');

        $this->loadViewComponentsAs('enlighten', [
            'headline' => 'enlighten::components.headline',
            'html-response' => 'enlighten::components.html-response',
            'json-response' => 'enlighten::components.json-response',
            'key-value' => 'enlighten::components.key-value',
            'sub-title' => 'enlighten::components.sub-title',
        ]);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../dist' => public_path('enlighten'),
                __DIR__.'/../resources/views' => resource_path('views/vendor/enlighten'),
            ], 'enlighten');
        }
    }

    protected function addDatabaseConnection(Config $config)
    {
        $connection = $config->get('database.connections.'.$config->get('database.default'));

        if ($connection['driver'] !== 'sqlite') {
            $connection['database'] = $connection['database'].'_enlighten';
        }

        $config->set('database.connections.enlighten', $connection);
    }

    public function register()
    {
        $this->app->singleton(ExampleGenerator::class, function () {
            $config = $this->app->config->get('enlighten');

            return new ExampleGenerator(
                new TestInspector($config['tests']),
                new RequestInspector(new RouteInspector),
                new ResponseInspector($config['response'])
            );
        });
    }
}
