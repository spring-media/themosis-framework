<?php

namespace Themosis\Core\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Themosis\Core\Console\CustomerTableCommand;
use Themosis\Core\Console\DownCommand;
use Themosis\Core\Console\DropinClearCommand;
use Themosis\Core\Console\FormMakeCommand;
use Themosis\Core\Console\HookMakeCommand;
use Themosis\Core\Console\PasswordResetTableCommand;
use Themosis\Core\Console\PluginInstallCommand;
use Themosis\Core\Console\PublishFuturePostCommand;
use Themosis\Core\Console\SaltsGenerateCommand;
use Themosis\Core\Console\ThemeInstallCommand;
use Themosis\Core\Console\UpCommand;
use Themosis\Core\Console\WidgetMakeCommand;

class ArtisanServiceProvider extends \Illuminate\Foundation\Providers\ArtisanServiceProvider implements DeferrableProvider
{
    /**
     * Commands to register.
     *
     * @var array
     */
    protected $commands = [
        'Down' => DownCommand::class,
        'DropinClear' => DropinClearCommand::class,
        'PublishFuturePost' => PublishFuturePostCommand::class,
        'SaltsGenerate' => SaltsGenerateCommand::class,
        'Up' => UpCommand::class,
    ];

    /**
     * Development commands to register.
     *
     * @var array
     */
    protected $devCommands = [
        'CustomerTable' => CustomerTableCommand::class,
        'FormMake' => FormMakeCommand::class,
        'HookMake' => HookMakeCommand::class,
        'PasswordResetTable' => PasswordResetTableCommand::class,
        'PluginInstall' => PluginInstallCommand::class,
        'ThemeInstall' => ThemeInstallCommand::class,
        'WidgetMake' => WidgetMakeCommand::class,
    ];

    /**
     * Register the customer:table command.
     */
    protected function registerCustomerTableCommand(): void
    {
        $this->app->singleton(CustomerTableCommand::class, function ($app) {
            return new CustomerTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the dropin:clear command.
     */
    protected function registerDropinClearCommand(): void
    {
        $this->app->singleton(DropinClearCommand::class, function ($app) {
            return new DropinClearCommand($app['files']);
        });
    }

    /**
     * Register the make:form command.
     */
    protected function registerFormMakeCommand(): void
    {
        $this->app->singleton(FormMakeCommand::class, function ($app) {
            return new FormMakeCommand($app['files']);
        });
    }

    /**
     * Register the make:hook command.
     */
    protected function registerHookMakeCommand(): void
    {
        $this->app->singleton(HookMakeCommand::class, function ($app) {
            return new HookMakeCommand($app['files']);
        });
    }

    /**
     * Register the password:table command.
     */
    protected function registerPasswordResetTableCommand(): void
    {
        $this->app->singleton(PasswordResetTableCommand::class, function ($app) {
            return new PasswordResetTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the plugin:install command.
     */
    protected function registerPluginInstallCommand(): void
    {
        $this->app->singleton(PluginInstallCommand::class, function ($app) {
            return new PluginInstallCommand($app['files'], new \ZipArchive());
        });
    }

    /**
     * Register the theme:install command.
     */
    protected function registerThemeInstallCommand(): void
    {
        $this->app->singleton(ThemeInstallCommand::class, function ($app) {
            return new ThemeInstallCommand($app['files'], new \ZipArchive());
        });
    }

    /**
     * Register the make:widget command.
     */
    protected function registerWidgetMakeCommand(): void
    {
        $this->app->singleton(WidgetMakeCommand::class, function ($app) {
            return new WidgetMakeCommand($app['files']);
        });
    }
}
