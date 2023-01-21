<?php

namespace Themosis\Core\Http;

use Illuminate\Support\Facades\Facade;

class Kernel extends \Illuminate\Foundation\Http\Kernel
{
    

    
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
