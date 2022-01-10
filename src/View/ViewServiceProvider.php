<?php

namespace Themosis\View;

use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Illuminate\View\ViewServiceProvider as IlluminateViewServiceProvider;
use Themosis\View\Engines\Twig;
use Themosis\View\Extensions\WordPress;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class ViewServiceProvider extends IlluminateViewServiceProvider
{
    /**
     * Register view bindings.
     */
    public function register()
    {
        parent::register();
        $this->registerTwigLoader();
        $this->registerTwigEnvironment();
        $this->registerTwigEngine();

        $this->registerBladeMacros();
    }

    /**
     * Register Blade View Macros
     *
     * @return void
     */
    public function registerBladeMacros()
    {
        $app = $this->app;

        /**
         * Get the compiled path of the view
         *
         * @return string
         */
        View::macro('getCompiled', function () {
            /** @var string $file path to file */
            $file = $this->getPath();

            /** @var \Illuminate\Contracts\View\Engine $engine */
            $engine = $this->getEngine();

            return ($engine instanceof CompilerEngine)
                ? $engine->getCompiler()->getCompiledPath($file)
                : $file;
        });

        /**
         * Creates a loader for the view to be called later
         *
         * @return string
         */
        View::macro('makeLoader', function () use ($app) {
            $view = $this->getName();
            $path = $this->getPath();
            $id = md5($this->getCompiled());
            $compiled_path = $app['config']['view.compiled'];

            $content = "<?= \\view('{$view}', \$data ?? get_defined_vars())->render(); ?>"
                . "\n<?php /**PATH {$path} ENDPATH**/ ?>";

            if (!file_exists($loader = "{$compiled_path}/{$id}-loader.php")) {
                file_put_contents($loader, $content);
            }

            return $loader;
        });
    }

    /**
     * Register Themosis view finder.
     */
    public function registerViewFinder()
    {
        $this->app->singleton('view.finder', function ($app) {
            return new FileViewFinder($app['files'], $app['config']['view.paths']);
        });
    }

    /**
     * Register Twig Loader.
     */
    public function registerTwigLoader()
    {
        $this->app->singleton('twig.loader', function ($app) {
            return new FilesystemLoader($app['view.finder']->getPaths());
        });
    }

    /**
     * Register Twig Environment.
     */
    public function registerTwigEnvironment()
    {
        $this->app->singleton('twig', function ($app) {
            $twig = new Environment(
                $app['twig.loader'],
                [
                    'auto_reload' => true,
                    'cache' => $app['config']['view.twig']
                ]
            );

            // Add Twig Debug Extension
            $twig->addExtension(new DebugExtension());

            // Enable debug.
            if ($app['config']['app.debug']) {
                $twig->enableDebug();
            }

            // Add WordPress helpers extension.
            $twig->addExtension(new WordPress());

            return $twig;
        });
    }

    /**
     * Register the Twig engine implementation.
     */
    public function registerTwigEngine()
    {
        /** @var Factory $factory */
        $factory = $this->app['view'];
        $factory->addExtension('twig', 'twig', function () {
            return new Twig($this->app['twig'], $this->app['view.finder']);
        });
    }
}
