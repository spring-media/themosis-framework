<?php

namespace Themosis\Tests\Mocks;


use Illuminate\Support\ServiceProvider;
use Themosis\Tests\Application;

class BasicServiceProviderMock extends ServiceProvider
{
    use Application;

    public function __construct()
    {
        parent::__construct($this->getApplication());
    }

    public function register()
    {
    }

    public function boot()
    {
    }
}
