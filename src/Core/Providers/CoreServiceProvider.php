<?php

namespace Themosis\Core\Providers;

use Illuminate\Foundation\Providers\FormRequestServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\AggregateServiceProvider;
use Illuminate\Support\Facades\URL;

class CoreServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array
     */
    protected $providers = [
        FormRequestServiceProvider::class,
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        parent::register();

        $this->registerRequestValidate();
        $this->registerRequestSignatureValidation();
    }

    /**
     * Register the "validate" macro on the request.
     */
    public function registerRequestValidate(): void
    {
        Request::macro('validate', function (array $rules, ...$params) {
            return validator()->validate($this->all(), $rules, ...$params);
        });
    }

    /**
     * Register the "hasValidSignature" macro on the request.
     */
    public function registerRequestSignatureValidation(): void
    {
        Request::macro('hasValidSignature', function ($absolute = true) {
            return URL::hasValidSignature($this, $absolute);
        });
    }

    /**
     * Publish core assets.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../../dist' => web_path('dist'),
            ], 'themosis');

            $this->publishes([
                __DIR__.'/../Exceptions/views' => resource_path('views/errors/'),
            ], 'themosis-errors');

            $this->publishes([
                __DIR__.'/../Dropins/files' => content_path(),
            ], 'themosis-dropins');

            $this->publishes([
                __DIR__.'/../Dropins/files/object-cache.php' => content_path('object-cache.php'),
            ], 'themosis-dropins-objectcache');
        }
    }
}
