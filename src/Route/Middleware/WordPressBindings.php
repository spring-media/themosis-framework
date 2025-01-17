<?php

namespace Themosis\Route\Middleware;

use Illuminate\Contracts\Routing\Registrar;

class WordPressBindings
{
    /**
     * @var Registrar
     */
    protected $router;

    /**
     * Create a new WordPressBindings substitutor.
     */
    public function __construct(Registrar $router)
    {
        $this->router = $router;
    }

    /**
     * Handle an incoming request.
     *
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $route = $request->route();

        if ($route->hasCondition()) {
            $this->router->addWordPressBindings($route);
        }

        return $next($request);
    }
}
