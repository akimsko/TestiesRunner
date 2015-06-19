<?php

/**
 * Will walk backwards from $dir
 * and require $autoloadFile if found.
 *
 * Will give up if it reaches the filesystem root.
 *
 * @param string $autoloadFile File to be required.
 * @param string $dir          Initial directory to start search from.
 */
call_user_func(function ($autoloadFile, $dir) {
    while (!file_exists($dir . $autoloadFile)) {
        $dirUp = dirname($dir);
        if ($dirUp === $dir) {
            throw new \RuntimeException("Unable to locate autoloader.");
        }
        $dir = $dirUp;
    }
    require_once $dir . $autoloadFile;
}, '/vendor/autoload.php', __DIR__);
