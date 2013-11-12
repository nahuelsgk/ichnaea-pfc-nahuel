<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';

//@TODO: Test this added into a service 
$loader->add('Ichnaea\\Amqp\\Model', __DIR__.'/../vendor/Miguel/src');
$loader->add('Ichnaea\\Amqp', __DIR__.'/../vendor/Miguel/src');

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
