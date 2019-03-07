<?php

/**
 * Autoloader for Kinikit and Netistrar
 */
spl_autoload_register(function ($class) {
    $class = str_replace("Kinikit\\Core\\", "Kinikit\\", $class);
    $file = DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists(__DIR__ . "/$file")) {
        require __DIR__ . "/$file";
        return true;
    } else
        return false;
});
