<?php

namespace Themosis\Core\Http;

use Illuminate\Support\Facades\Facade;

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    protected $bootstrappers = [
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
        \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
        \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
        \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
        \Illuminate\Foundation\Bootstrap\BootProviders::class,
    ];
    
    /**
     * Initialize the kernel (bootstrap application base components).
     *
     * @param \Illuminate\Http\Request $request
     */
    public function init($request)
    {
        $this->app->instance('request', $request);
        Facade::clearResolvedInstance('request');
        $this->bootstrap();
    }
}
