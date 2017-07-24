<?php

require_once('../vendor/autoload.php');

use Silex\Provider\ServiceControllerServiceProvider;

$app = new \Silex\Application();

$app->register(new ServiceControllerServiceProvider);
$app->register(new Sneek\Providers\ControllerProvider('src/Controllers', 'Test', 'src'));


$app->get('/', 'Test\Controllers\ExampleController:index');
