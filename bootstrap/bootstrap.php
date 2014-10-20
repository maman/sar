<?php

/**
 * Bootstrap Application
 */
require_once '../vendor/autoload.php';
require_once 'config.php';

/* Setup Add-Ons */
$logger = new Flynsarmy\SlimMonolog\Log\MonologWriter(array(
    'handlers' => array(
        new Monolog\Handler\StreamHandler('../logs/app_errors' . date('Y-m-d') . '.log')
    ),
));
$twigView = new \Slim\Views\Twig();

/* Setup Slim */
$app = new \Slim\Slim(array(
    'view' => $twigView,
    'mode' => 'development',
    'log.writer' => $logger,
    'templates.path' => '../SAR/src/templates',
));

/* Invoked if in production */
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

/* Invoked if in development */
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

/* Load Routes */
foreach (glob('../src/SAR/routers/*.router.php') as $router) {
    require $router;
}

/* Run the App */
$app->run();
