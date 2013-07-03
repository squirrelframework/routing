<?php

namespace Squirrel\Routing\Loader;

/**
 * Interface for all routong loaders.
 *
 * @author Valérian Galliat
 */
interface LoaderInterface
{
    /**
     * Loads given file to get filled router.
     *
     * @throws InvalidArgumentException if the file cannot be loaded.
     * @param string $file
     * @return Routing\Router
     */
    public function load($file);
}
