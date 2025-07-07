<?php

namespace Themosis\Tests\Mocks;

class ProviderMock extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        // Do nothing
    }

    public function isDeferred(): true
    {
        return true;
    }

    public function provides(): array
    {
        return ['foo.provides1', 'foo.provides2'];
    }

    public function when(): array
    {
        return [];
    }
}
