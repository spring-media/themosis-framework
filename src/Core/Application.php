<?php

namespace Themosis\Core;

use Closure;
use Composer\Autoload\ClassLoader;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Contracts\Foundation\CachesRoutes;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Log\LogServiceProvider;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Themosis\Route\RouteServiceProvider;

class Application extends \Illuminate\Foundation\Application implements
    ApplicationContract,
    CachesConfiguration,
    CachesRoutes,
    HttpKernelInterface
{
    /**
     * Themosis framework version.
     */
    public const THEMOSIS_VERSION = '12.0.0';

    /**
     * Application textdomain.
     */
    public const TEXTDOMAIN = 'themosis';

    public function __construct($basePath = null)
    {
        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->registerBaseBindings();
        $this->registerBaseServiceProviders();
        $this->registerCoreContainerAliases();
    }

    /**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders()
    {
        $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this));
        $this->register(new RouteServiceProvider($this));
    }

    /**
     * Register the core class aliases in the container.
     */
    public function registerCoreContainerAliases()
    {
        $list = [
            'action' => [
                \Themosis\Hook\ActionBuilder::class,
            ],
            'ajax' => [
                \Themosis\Ajax\Ajax::class,
            ],
            'app' => [
                Application::class,
                \Illuminate\Contracts\Container\Container::class,
                \Illuminate\Contracts\Foundation\Application::class,
                \Psr\Container\ContainerInterface::class,
            ],
            'asset' => [
                \Themosis\Asset\Factory::class,
            ],
            'auth' => [
                \Illuminate\Auth\AuthManager::class,
                \Illuminate\Contracts\Auth\Factory::class,
            ],
            'auth.driver' => [
                \Illuminate\Contracts\Auth\Guard::class,
            ],
            'auth.password' => [
                \Illuminate\Auth\Passwords\PasswordBrokerManager::class,
                \Illuminate\Contracts\Auth\PasswordBrokerFactory::class,
            ],
            'auth.password.broker' => [
                \Illuminate\Auth\Passwords\PasswordBroker::class,
                \Illuminate\Contracts\Auth\PasswordBroker::class,
            ],
            'blade.compiler' => [
                \Illuminate\View\Compilers\BladeCompiler::class,
            ],
            'cache' => [
                \Illuminate\Cache\CacheManager::class,
                \Illuminate\Contracts\Cache\Factory::class,
            ],
            'cache.store' => [
                \Illuminate\Cache\Repository::class,
                \Illuminate\Contracts\Cache\Repository::class,
            ],
            'config' => [
                \Illuminate\Config\Repository::class,
                \Illuminate\Contracts\Config\Repository::class,
            ],
            'cookie' => [
                \Illuminate\Cookie\CookieJar::class,
                \Illuminate\Contracts\Cookie\Factory::class,
                \Illuminate\Contracts\Cookie\QueueingFactory::class,
            ],
            'db' => [
                \Illuminate\Database\ConnectionResolverInterface::class,
                \Illuminate\Database\DatabaseManager::class,
            ],
            'db.connection' => [
                \Illuminate\Database\Connection::class,
                \Illuminate\Database\ConnectionInterface::class,
            ],
            'encrypter' => [
                \Illuminate\Encryption\Encrypter::class,
                \Illuminate\Contracts\Encryption\Encrypter::class,
            ],
            'events' => [
                \Illuminate\Events\Dispatcher::class,
                \Illuminate\Contracts\Events\Dispatcher::class,
            ],
            'files' => [
                \Illuminate\Filesystem\Filesystem::class,
            ],
            'filesystem' => [
                \Illuminate\Filesystem\FilesystemManager::class,
                \Illuminate\Contracts\Filesystem\Factory::class,
            ],
            'filesystem.disk' => [
                \Illuminate\Contracts\Filesystem\Filesystem::class,
            ],
            'filesystem.cloud' => [
                \Illuminate\Contracts\Filesystem\Cloud::class,
            ],
            'filter' => [
                \Themosis\Hook\FilterBuilder::class,
            ],
            'form' => [
                \Themosis\Forms\FormFactory::class,
            ],
            'hash' => [
                \Illuminate\Hashing\HashManager::class,
            ],
            'hash.driver' => [
                \Illuminate\Contracts\Hashing\Hasher::class,
            ],
            'html' => [
                \Themosis\Html\HtmlBuilder::class,
            ],
            'log' => [
                \Illuminate\Log\LogManager::class,
                \Psr\Log\LoggerInterface::class,
            ],
            'mail.manager' => [
                \Illuminate\Mail\MailManager::class,
                \Illuminate\Contracts\Mail\Factory::class
            ],
            'mailer' => [
                \Illuminate\Mail\Mailer::class,
                \Illuminate\Contracts\Mail\Mailer::class,
                \Illuminate\Contracts\Mail\MailQueue::class,
            ],
            'metabox' => [
                \Themosis\Metabox\Factory::class,
            ],
            'posttype' => [
                \Themosis\PostType\Factory::class,
            ],
            'queue' => [
                \Illuminate\Queue\QueueManager::class,
                \Illuminate\Contracts\Queue\Factory::class,
                \Illuminate\Contracts\Queue\Monitor::class,
            ],
            'queue.connection' => [
                \Illuminate\Contracts\Queue\Queue::class,
            ],
            'queue.failer' => [
                \Illuminate\Queue\Failed\FailedJobProviderInterface::class,
            ],
            'redirect' => [
                \Illuminate\Routing\Redirector::class,
            ],
            'redis' => [
                \Illuminate\Redis\RedisManager::class,
                \Illuminate\Contracts\Redis\Factory::class,
            ],
            'request' => [
                \Illuminate\Http\Request::class,
                \Symfony\Component\HttpFoundation\Request::class,
            ],
            'router' => [
                \Themosis\Route\Router::class,
                \Illuminate\Routing\Router::class,
                \Illuminate\Contracts\Routing\Registrar::class,
                \Illuminate\Contracts\Routing\BindingRegistrar::class,
            ],
            'session' => [
                \Illuminate\Session\SessionManager::class,
            ],
            'session.store' => [
                \Illuminate\Session\Store::class,
                \Illuminate\Contracts\Session\Session::class,
            ],
            'taxonomy' => [
                \Themosis\Taxonomy\Factory::class,
            ],
            'taxonomy.field' => [
                \Themosis\Taxonomy\TaxonomyFieldFactory::class,
            ],
            'translator' => [
                \Illuminate\Translation\Translator::class,
                \Illuminate\Contracts\Translation\Translator::class,
            ],
            'twig' => [
                \Twig_Environment::class,
            ],
            'url' => [
                \Illuminate\Routing\UrlGenerator::class,
                \Illuminate\Contracts\Routing\UrlGenerator::class,
            ],
            'validator' => [
                \Illuminate\Validation\Factory::class,
                \Illuminate\Contracts\Validation\Factory::class,
            ],
            'view' => [
                \Illuminate\View\Factory::class,
                \Illuminate\Contracts\View\Factory::class,
            ],
        ];

        foreach ($list as $key => $aliases) {
            foreach ($aliases as $alias) {
                $this->alias($key, $alias);
            }
        }
    }

    /**
     * Bind all of the application paths in the container.
     */
    protected function bindPathsInContainer()
    {
        // Core
        $this->instance('path', $this->path());
        // Base
        $this->instance('path.base', $this->basePath());
        // Content
        $this->instance('path.content', $this->contentPath());
        // Mu-plugins
        $this->instance('path.muplugins', $this->mupluginsPath());
        // Plugins
        $this->instance('path.plugins', $this->pluginsPath());
        // Themes
        $this->instance('path.themes', $this->themesPath());
        // Application
        $this->instance('path.application', $this->applicationPath());
        // Resources
        $this->instance('path.resources', $this->resourcePath());
        // Languages
        $this->instance('path.lang', $this->langPath());
        // Web root
        $this->instance('path.web', $this->webPath());
        // Root
        $this->instance('path.root', $this->rootPath());
        // Config
        $this->instance('path.config', $this->configPath());
        // Public
        $this->instance('path.public', $this->webPath());
        // Storage
        $this->instance('path.storage', $this->storagePath());
        // Database
        $this->instance('path.database', $this->databasePath());
        // Bootstrap
        $this->instance('path.bootstrap', $this->bootstrapPath());
        // WordPress
        $this->instance('path.wp', $this->wordpressPath());
    }

    /**
     * Get the WordPress "content" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function contentPath($path = '')
    {
        return WP_CONTENT_DIR . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the WordPress "mu-plugins" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function mupluginsPath($path = '')
    {
        return $this->contentPath('mu-plugins') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the WordPress "plugins" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function pluginsPath($path = '')
    {
        return $this->contentPath('plugins') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the WordPress "themes" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function themesPath($path = '')
    {
        return $this->contentPath('themes') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the application directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function applicationPath($path = '')
    {
        return $this->basePath('app') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path to the resources "languages" directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function langPath($path = '')
    {
        return $this->resourcePath('languages') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path of the web server root.
     *
     * @param string $path
     *
     * @return string
     */
    public function webPath($path = '')
    {
        return $this->basePath(THEMOSIS_PUBLIC_DIR) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the root path of the project.
     *
     * @param string $path
     *
     * @return string
     */
    public function rootPath($path = '')
    {
        if (defined('THEMOSIS_ROOT')) {
            return THEMOSIS_ROOT . ($path ? DIRECTORY_SEPARATOR . $path : $path);
        }

        return $this->webPath($path);
    }

    /**
     * Get the storage directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function storagePath($path = '')
    {
        if (defined('THEMOSIS_ROOT')) {
            return $this->rootPath('storage') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
        }

        return $this->contentPath('storage') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the database directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function databasePath($path = '')
    {
        return $this->rootPath('database') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the bootstrap directory path.
     *
     * @param string $path
     *
     * @return string
     */
    public function bootstrapPath($path = '')
    {
        return $this->rootPath('bootstrap') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the WordPress directory path.
     *
     * @param string $path
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return string
     */
    public function wordpressPath($path = '')
    {
        return $this->webPath(env('WP_DIR', 'cms')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Determine if the application is currently down for maintenance.
     *
     * @throws \Illuminate\Container\EntryNotFoundException
     *
     * @return bool
     */
    public function isDownForMaintenance()
    {
        $filePath = $this->wordpressPath('.maintenance');

        if (function_exists('wp_installing') && ! file_exists($filePath)) {
            return \wp_installing();
        }

        return file_exists($filePath);
    }

    /**
     * Bootstrap a Themosis like plugin.
     *
     * @param string $filePath
     * @param string $configPath
     *
     * @return PluginManager
     */
    public function loadPlugin(string $filePath, string $configPath)
    {
        $plugin = (new PluginManager($this, $filePath, new ClassLoader()))->load($configPath);

        $this->instance('wp.plugin.' . $plugin->getHeader('plugin_id'), $plugin);

        return $plugin;
    }

    /**
     * Register the framework core "plugin" and auto-load
     * any found mu-plugins after the framework.
     *
     * @param string $pluginsPath
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Exception
     */
    public function loadPlugins(string $pluginsPath)
    {
        $directories = Collection::make((new Filesystem())->directories($this->mupluginsPath()))
                                 ->map(function ($directory) {
                                     return ltrim(substr($directory, strrpos($directory, DS)), '\/');
                                 })->toArray();

        (new PluginsRepository($this, new Filesystem(), $pluginsPath, $this->getCachedPluginsPath()))
            ->load($directories);
    }

    /**
     * Register a plugin and load it.
     *
     * @param string $path Plugin relative path (pluginDirName/pluginMainFile).
     */
    public function registerPlugin(string $path)
    {
        require $this->mupluginsPath($path);
    }

    /**
     * Return cached plugins manifest file path.
     *
     * @return string
     */
    public function getCachedPluginsPath()
    {
        return $this->bootstrapPath('cache/plugins.php');
    }

    /**
     * Register a list of hookable instances.
     *
     * @param string $config
     */
    public function registerConfiguredHooks(string $config = '')
    {
        if (empty($config)) {
            $config = 'app.hooks';
        }

        $hooks = Collection::make($this->config[$config]);

        (new HooksRepository($this))->load($hooks->all());
    }

    /**
     * Create and register a hook instance.
     *
     * @param string $hook
     */
    public function registerHook(string $hook)
    {
        // Build a "Hookable" instance.
        // Hookable instances must extend the "Hookable" class.
        $instance = new $hook($this);
        $hooks = (array) $instance->hook;

        if (! method_exists($instance, 'register')) {
            return;
        }

        if (! empty($hooks)) {
            $this['action']->add($hooks, [$instance, 'register'], $instance->priority, $instance->acceptedArgs);
        } else {
            $instance->register();
        }
    }

    /**
     * Load current active theme.
     *
     * @param string $dirPath    The theme directory path.
     * @param string $configPath The theme relative configuration folder path.
     *
     * @return ThemeManager
     */
    public function loadTheme(string $dirPath, string $configPath)
    {
        $theme = (new ThemeManager($this, $dirPath, new ClassLoader()))
            ->load($dirPath . '/' . trim($configPath, '\/'));

        $this->instance('wp.theme', $theme);

        return $theme;
    }

    /**
     * Load configuration files based on given path.
     *
     * @param Repository $config
     * @param string     $path   The configuration files folder path.
     *
     * @return Application
     */
    public function loadConfigurationFiles(Repository $config, $path = '')
    {
        $files = $this->getConfigurationFiles($path);

        foreach ($files as $key => $path) {
            $config->set($key, require $path);
        }

        return $this;
    }

    /**
     * Get all configuration files.
     *
     * @param mixed $path
     *
     * @return array
     */
    protected function getConfigurationFiles($path)
    {
        $files = [];

        foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
            $directory = $this->getNestedDirectory($file, $path);
            $files[$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get configuration file nesting path.
     *
     * @param SplFileInfo $file
     * @param string      $path
     *
     * @return string
     */
    protected function getNestedDirectory(SplFileInfo $file, $path)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($path, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }

    /**
     * Handle incoming request and return a response.
     * Abstract the implementation from the user for easy
     * theme integration.
     *
     * @param string                                    $kernel  Application kernel class name.
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this
     */
    public function manage(string $kernel, $request)
    {
        $kernel = $this->make($kernel);

        $response = $kernel->handle($request);
        $response->sendHeaders();
        $response->sendContent();

        if (function_exists('shutdown_action_hook')) {
            shutdown_action_hook();
        }

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif (function_exists('litespeed_finish_request')) {
            litespeed_finish_request();
        } elseif (! in_array(PHP_SAPI, ['cli', 'phpdbg'], true)) {
            Response::closeOutputBuffers(0, true);
        }

        $kernel->terminate($request, $response);

        return $this;
    }

    /**
     * Handle WordPress administration incoming request.
     * Only send response headers.
     *
     * @param string                                    $kernel
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return $this;
     */
    public function manageAdmin(string $kernel, $request)
    {
        if (! $this->isWordPressAdmin() && ! $this->has('action')) {
            return $this;
        }

        $this['action']->add('admin_init', $this->dispatchToAdmin($kernel, $request));

        return $this;
    }

    /**
     * Manage WordPress Admin Init.
     * Handle incoming request and return a response.
     *
     * @param string $kernel
     * @param $request
     *
     * @return Closure
     */
    protected function dispatchToAdmin(string $kernel, $request)
    {
        return function () use ($kernel, $request) {
            $kernel = $this->make($kernel);

            /** @var Response $response */
            $response = $kernel->handle($request);

            if (500 <= $response->getStatusCode()) {
                // In case of an internal server error, we stop the process
                // and send full response back to the user.
                $response->send();
            } else {
                // HTTP OK - Send only the response headers.s
                $response->sendHeaders();
            }
        };
    }

    /**
     * Determine if we currently inside the WordPress administration.
     *
     * @return bool
     */
    public function isWordPressAdmin(): bool
    {
        if (isset($GLOBALS['current_screen']) && is_a($GLOBALS['current_screen'], 'WP_Screen')) {
            return $GLOBALS['current_screen']->in_admin();
        } elseif (defined('WP_ADMIN')) {
            return WP_ADMIN;
        }

        return false;
    }

    /**
     * Return a Javascript Global variable.
     */
    public function outputJavascriptGlobal(string $name, array $data): string
    {
        $output = "<script type=\"text/javascript\">\n\r";
        $output .= "/* <![CDATA[ */\n\r";
        $output .= "var {$name} = {\n\r";

        if (! empty($data) && is_array($data)) {
            foreach ($data as $key => $value) {
                $output .= $key . ': ' . json_encode($value) . ",\n\r";
            }
        }

        $output .= "};\n\r";
        $output .= "/* ]]> */\n\r";
        $output .= '</script>';

        return $output;
    }
}
