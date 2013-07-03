<?php

namespace Squirrel\Routing;

use Squirrel\Routing\Route;

/**
 * Provides basic routing functionalities.
 *
 * @package Squirrel\Routing
 * @author Valérian Galliat
 */
class Router
{
    /**
     * @var Route[]
     */
    protected $routes;

    /**
     * Adds given route into routes array.
     *
     * @param string $name The route name.
     * @param Route $route The route itself.
     * @return Router
     */
    public function add($name, Route $route)
    {
        if (!isset($this->routes)) {
            $this->routes = array();
        }

        $this->routes[$name] = $route;
        return $this;
    }

    /**
     * Tries to match given string to all contained routes.
     * 
     * @param string $string The string to test.
     * @param array $params Container for output parameters.
     * @return boolean
     */
    public function match($string, array & $params = null)
    {
        foreach ($this->routes as $name => $route) {
            if ($route->match($string, $params)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generates a string for given route pattern with given arguments.
     *
     * @throws \InvalidArgumentException If the route does not exists.
     * @throws \InvalidArgumentException If a required parameter is not provided.
     * @param string $name
     * @param array $params
     * @return string
     */
    public function generate($name, array $params = array())
    {
        if (!isset($this->routes[$name])) {
            throw new \InvalidArgumentException(sprintf('Given route "%s" does not exists.', $name));
        }

        return $this->routes[$name]->generate($params);
    }
}
