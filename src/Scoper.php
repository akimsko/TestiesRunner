<?php
/**
 * This file is part of the TestiesRunner project.
 */

namespace TestiesRunner;

/**
 * Class Scoper
 *
 * @author Bo Thinggaard <akimsko@gmail.com>
 */
class Scoper
{
    /**
     * scope.
     *
     * @param string      $test
     * @param string|null $config
     *
     * @return int
     */
    public static function scope($test, $config = null)
    {
        if ($config) {
            require $config;
        }

        require $test;

        return run();
    }
}
