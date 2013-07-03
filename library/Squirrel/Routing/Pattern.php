<?php

namespace Squirrel\Routing;

use Squirrel\Routing\Exception\GenerationException;

/**
 * Helper for bidirectional pattern matching or compiling.
 *
 * @package Squirrel\Routing
 * @author Valérian Galliat
 */
class Pattern
{
    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var string[string]
     */
    protected $custom;

    /**
     * @var string
     */
    protected $compiled;

    /**
     * @var string[]
     */
    protected $params;

    /**
     * @param string $pattern Pattern to match.
     * @param array $custom Custom expressions for pattern variables.
     */
    public function __construct($pattern, array $custom = array())
    {
        $this->pattern = $pattern;
        $this->custom = $custom;
        $this->compile();
    }

    /**
     * Returns whether given string matches the current pattern.
     * 
     * @param string $string The string to test.
     * @param array $params Container for output parameters.
     * @return boolean
     */
    public function match($string, array & $params = null)
    {
        if (!preg_match($this->compiled, $string, $matches)) {
            return false;
        }

        $params = array();
        $count = count($this->params);

        for ($i = 0; $i < $count; $i++) {
            if (!isset($matches[$i + 1])) {
                continue;
            }

            $params[$this->params[$i]] = $matches[$i + 1];
        }

        return true;
    }

    /**
     * Generates a string from current pattern with given parameters.
     * 
     * @throws \InvalidArgumentException If a required parameter is not provided.
     * @param array $params
     * @return string
     */
    public function generate(array $params = array())
    {
        $string = $this->pattern;

        while (preg_match('/\([^()]+\)/', $string, $matches)) {
            $search = $matches[0];
            $replace = substr($matches[0], 1, -1);

            while (preg_match('/<[a-z0-9]+>/', $replace, $matches)) {
                $match = $matches[0];
                $param = substr($match, 1, -1);

                if (isset($params[$param])) {
                    $replace = str_replace($param, $params[$param], $replace);
                } else {
                    $replace = '';
                }
            }

            $string = str_replace($search, $replace, $string);
        }

        while (preg_match('/<[a-z0-9]+>/', $string, $matches)) {
            $match = $matches[0];
            $param = substr($match, 1, -1);

            if (!isset($params[$param])) {
                throw new \InvalidArgumentException(sprintf('The param "%s" is required for this route.', $param));
            }

            $string = str_replace($param, $params[$param], $string);
        }
    }

    /**
     * Compiles current pattern to a regular expression.
     */
    protected function compile()
    {
        $this->params = array();

        $compiled = str_replace('/', '\/', $this->pattern);
        $compiled = str_replace('(', '(?:', $compiled);
        $compiled = str_replace(')', ')?', $compiled);

        while (preg_match('/<([a-z0-9]+)>/', $compiled, $matches)) {
            $this->params[] = $matches[1];

            if (isset($this->custom[$matches[1]])) {
                $replace = '(' . $this->custom[$matches[1]] . ')';
            } else {
                $replace = '([a-z0-9]+)';
            }

            $compiled = str_replace($matches[0], $replace, $compiled);
        }

        $this->compiled = '/^' . $compiled . '$/';
    }
}
