<?php

/**
 * Bootstrap Application
 */
require_once '../vendor/autoload.php';
require_once 'config.php';

/* Setup Slim Add-Ons */
$logger = new Flynsarmy\SlimMonolog\Log\MonologWriter(array(
    'handlers' => array(
        new Monolog\Handler\StreamHandler('../logs/app_errors' . date('Y-m-d') . '.log')
    ),
));
$twigView = new \Slim\Views\Twig();

/* Setup Slim itself */
$app = new \Slim\Slim(array(
    'view' => $twigView,
    'mode' => 'development',
    'log.writer' => $logger,
    'templates.path' => '../src/SAR/templates',
));

$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

/* Twig Options */
$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => '../src/SAR/templates/cache'
);

/* Load Routes */
foreach (glob('../src/SAR/routers/*.router.php') as $router) {
    require $router;
}

/* Run the App */
$app->run();
