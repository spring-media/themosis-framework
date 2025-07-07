<?php

namespace Themosis\Tests\Core;

use PHPUnit\Framework\TestCase;
use Themosis\Core\Application;
use Themosis\Core\HooksRepository;
use Themosis\Hook\ActionBuilder;
use Themosis\Hook\Hookable;

class HooksRepositoryTest extends TestCase
{
    protected Application $app;

    public function setUp(): void
    {
        $this->app = new Application();
    }

    public function testHookablesClassesAreRegistered()
    {
        $app = $this->getMockBuilder(\Themosis\Core\Application::class)
            ->disableOriginalConstructor()
            ->getMock();

        $app->expects($this->exactly(2))
            ->method('registerHook');

        (new HooksRepository($app))->load([
            'Some\Namespace\Hookable',
            MyActions::class,
        ]);
    }

    public function testCorrectAcceptedArgsCount()
    {
        $action = $this->getMockBuilder(ActionBuilder::class)
                       ->setConstructorArgs([$this->app])
                       ->onlyMethods(['addAction'])
                       ->getMock();

        $action->expects($this->once())
               ->method('addAction')
               ->with('someHook', [new MyActions($this->app), 'register'], 10, 4);

        $this->app->offsetSet('action', $action);
        $this->app->registerHook(MyActions::class);
    }
}

class MyActions extends Hookable
{
    public $hook = 'someHook';
    public $priority = 10;
    public $acceptedArgs = 4;

    public function register($one, $two, $three, $four)
    {
    }
}
