#!/usr/bin/env php
<?php
/** @see Symfony\Component\ClassLoader\UniversalClassLoader */
require_once 'Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();

if (strpos('@php_bin@', '@php_bin') === 0) {
    $loader->registerNamespace('PHSA', __DIR__ . '/library');
    define('PHSA_CONFIG_FILE', __DIR__ . '/config/config.php');
} else {
    $loader->registerNamespace('PHSA', explode(PATH_SEPARATOR, get_include_path()));
    define('PHSA_CONFIG_FILE', '/etc/phsa/config.php');
}

$loader->registerNamespace('Symfony', explode(PATH_SEPARATOR, get_include_path()));
$loader->register();

$application = new PHSA\Application();
$application->run();
