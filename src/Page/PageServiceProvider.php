<?php

namespace Themosis\Page;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Factory;

class PageServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        /** @var Factory $view */
        $view = $this->app['view'];
        $view->addLocation(__DIR__.'/views');

        $this->app->bind('page', function ($app) {
            return new PageFactory($app['action'], $app['filter'], $app['view'], $app['validator']);
        });
    }

    /**
     * Return list of registered bindings.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['page'];
    }
}
