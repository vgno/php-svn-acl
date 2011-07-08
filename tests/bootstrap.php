<?php
spl_autoload_register(
    function($className) {
        if (!class_exists($className, false) && !interface_exists($className, false)) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

            foreach (array(__DIR__ . '/../library', __DIR__) as $path) {
                $fullPath = realpath($path . '/' .  $file);

                if ($fullPath !== false) {
                    require $fullPath;
                }
            }

            return false;
        }

        // If we end up here the class already exists, or the require statement above has been
        // executed
        return true;
    }
);
