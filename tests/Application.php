<?php

namespace Themosis\Tests;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemServiceProvider;
use Illuminate\Routing\CallableDispatcher;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\TranslationServiceProvider;
use Illuminate\Translation\Translator;
use Themosis\Core\Application as CoreApplication;
use Illuminate\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use Illuminate\Routing\Contracts\ControllerDispatcher as ControllerDispatcherContract;

trait Application
{
    /**
     * @var \Themosis\Core\Application
     */
    protected $application;

    /**
     * Return a core application instance.
     *
     * @return CoreApplication
     */
    public function getApplication(): CoreApplication
    {
        if (! is_null($this->application)) {
            return $this->application;
        }

        $this->application = new CoreApplication();
        $this->application->register(new TranslationServiceProvider($this->application));
        $this->application->register(new FilesystemServiceProvider($this->application));

        $this->application->bind('config', function () {
            $config = new Repository();
            $config->set('app.locale', 'en_US');

            return $config;
        });

       $this->application->bind('translator', function () {
            $loader = new FileLoader(new Filesystem(), '');

            return new Translator($loader, 'en_US');
        });

        $this->application->bind(CallableDispatcherContract::class, fn ($app) => new CallableDispatcher($app));
        $this->application->bind(ControllerDispatcherContract::class, fn ($app) => new ControllerDispatcher($app));

        return $this->application;
    }
}
