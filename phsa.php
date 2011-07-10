#!/usr/bin/env php
<?php
/** @see Symfony\Component\ClassLoader\UniversalClassLoader */
require_once __DIR__ . '/library/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$loader->registerNamespaces(array(
    'Symfony' => __DIR__ . '/library',
    'PHSA'    => __DIR__ . '/library',
));
$loader->register();

define('PHSA_CONFIG_FILE', __DIR__ . '/config/config.php');

$application = new PHSA\Application();
$application->run();
