<?php

namespace Themosis\Tests\Mocks;

use Illuminate\Routing\Controller;

class ControllerMock extends Controller
{
    public function index(): string
    {
        return 'Controller index action';
    }
}