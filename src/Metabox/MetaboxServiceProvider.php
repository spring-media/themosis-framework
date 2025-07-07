<?php

namespace Themosis\Metabox;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use League\Fractal\Manager;
use League\Fractal\Serializer\ArraySerializer;
use Themosis\Metabox\Resources\MetaboxResource;
use Themosis\Metabox\Resources\Transformers\MetaboxTransformer;

class MetaboxServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->registerMetabox();
        $this->registerMetaboxInterface();
    }

    /**
     * Register the metabox factory.
     */
    public function registerMetabox(): void
    {
        $this->app->bind('metabox', function ($app) {
            $resource = new MetaboxResource(
                $app->bound('league.fractal') ? $app['league.fractal'] : new Manager(),
                new ArraySerializer(),
                new MetaboxTransformer(),
            );

            return new Factory($app, $app['action'], $app['filter'], $resource);
        });
    }

    /**
     * Register the metabox manager interface.
     */
    public function registerMetaboxInterface(): void
    {
        $this->app->bind('Themosis\Metabox\Contracts\MetaboxManagerInterface', 'Themosis\Metabox\Manager');
    }

    /**
     * Return list of registered bindings.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['metabox'];
    }
}
