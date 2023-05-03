<?php


namespace Codilar\Witch;


class Router
{

    /**
     * @var array
     */
    protected $routes = [];

    protected $noRoute = null;

    protected $data = [];

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function getData($key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * @param $route
     * @param $method
     * @param $resolver
     * @param $layout
     * @return $this
     */
    public function addRoute($route, $method, $resolver, $layout)
    {
        $data = [
            'route' => $route,
            'method' => $method,
            'resolver' => $resolver,
            'layout' => $layout
        ];
        if ($route === '404') {
            $this->noRoute = $data;
        } else {
            $this->routes[] = $data;
        }

        return $this;
    }

    public function route()
    {
        $requestRoute = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        foreach ($this->routes as $route) {
            $routeRegex = str_replace('/', '\/', $route['route']);
            $isRouteMatched = preg_match('/^' . $routeRegex . '$/', $requestRoute, $matches);
            if ($isRouteMatched && $route['method'] === $requestMethod) {
                $this->resolve($route);
                return;
            }
        }
        if ($this->noRoute) {
            $this->resolve($this->noRoute);
        }
    }

    protected function resolve($route)
    {
        $resolver = $route['resolver'];
        if (is_string($resolver)) {
            $renderer = \Closure::bind(function ($resolver, $layout) {
                ob_start();
                include $resolver;
                $resolver = ob_get_clean();
                ob_start();
                include $layout;
                return ob_get_clean();
            }, $this, self::class);
            echo call_user_func($renderer, $resolver, $route['layout']);
        } else if (is_callable($resolver)) {
            $resolver($this);
        }
    }


}
