<?php

namespace Squirrel\Routing;

use Squirrel\Routing\Exception\GenerationException;

/**
 * Represents a single route to match parameters.
 *
 * @package Squirrel\Routing
 * @author Valérian Galliat
 */
class Route
{
    /**
     * @param string $pattern Pattern to match.
     * @param array $custom Custom expressions for pattern variables.
     */
    public function __construct(Pattern $pattern, array $defaults = array())
    {
        $this->pattern = $pattern;
        $this->defaults = $defaults;
    }

    /**
     * Proxy for the pattern match function.
     * 
     * @param string $string The string to test.
     * @param array $params Container for output parameters.
     * @return boolean
     */
    public function match($string, array & $params = null)
    {
        $match = $this->pattern->match($string, $params);
        $params = array_merge($this->defaults, $params);
        return $match;
    }

    /**
     * Proxy for the pattern generate function with default values override.
     *
     * @throws \InvalidArgumentException If a required parameter is not provided.
     * @param array $params
     * @return string
     */
    public function generate(array $params = array())
    {
        foreach ($params as $param => $value) {
            if (isset($this->defaults[$param]) && $value === $this->defaults[$param]) {
                unset($params[$param]);
            }
        }

        return $this->pattern->generate($params);
    }
}
