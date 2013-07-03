<?php

namespace Squirrel\Routing\Loader;

use Squirrel\Routing\Router;
use Squirrel\Routing\Route;

/**
 * JSON routing file loader.
 *
 * @author Valérian Galliat
 */
class JsonLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $routes = json_decode(file_get_contents($file), true);

        if (!is_array($routes))
        {
            throw new \InvalidArgumentException(sprintf('Unable to load given file "%s".', $file));
        }

        $router = new Router;
        $count = count($routes);

        for ($i = 0; $i < $count; $i++)
        {
            if (!is_array($routes[$i]))
            {
                throw new \InvalidArgumentException(sprintf('Error in file "%s" at offset %s, route must be an array.', $file, $i));
            }

            if (!isset($routes[$i]['path']))
            {
                throw new \InvalidArgumentException(sprintf('Error in file "%s" at offset %s, route must have a path.', $file, $i));
            }

            if (isset($routes[$i]['custom']))
            {
                if (!is_array($routes[$i]['custom']))
                {
                    throw new \InvalidArgumentException(sprintf('Error in file "%s" at offset %s, custom patterns must be an array.', $file, $i));
                }

                $route = new Route($routes[$i]['path'], $routes[$i]['custom']);
            }
            else
            {
                $route = new Route($routes[$i]['path']);
            }

            if (isset($routes[$i]['defaults']))
            {
                if (!is_array($routes[$i]['defaults']))
                {
                    throw new \InvalidArgumentException(sprintf('Error in file "%s" at offset %s, defaults values must be an array.', $file, $i));
                }

                $route->setDefaults($routes[$i]['defaults']);
            }

            $router->add($route);
        }

        return $router;
    }
}
