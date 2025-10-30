<?php

namespace Themosis\Route;

use Illuminate\Http\Request;

class CompiledRouteCollection extends \Illuminate\Routing\CompiledRouteCollection
{
    public function match(Request $request) {
        $routes = $this->get($request->getMethod());
        $route = $this->matchAgainstRoutes($routes, $request);

        return is_null($route)
            ? parent::match($request)
            : $this->handleMatchedRoute($request, $route);
    }
}
