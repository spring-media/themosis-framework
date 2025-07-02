<?php

namespace Themosis\Core\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    /**
     * Initialize the kernel (bootstrap application base components).
     *
     * @param  Request  $request
     */
    public function init(Request $request): void
    {
        $this->app->instance('request', $request);
        Facade::clearResolvedInstance('request');
        $this->bootstrap();
    }
}
