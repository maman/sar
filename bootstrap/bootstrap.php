<?php

/**
 * Bootstrap Application
 */
require_once '../vendor/autoload.php';

use Pimple\Container;

$c = new Container();

/* Starting Session First */
session_cache_limiter(false);
session_start();

/* Load Settings */
$c['config'] = require 'config.php';

/* Setup Slim Add-Ons */
$logger = new Flynsarmy\SlimMonolog\Log\MonologWriter(array(
    'handlers' => array(
        new Monolog\Handler\StreamHandler($c['config']['app.log'])
    ),
));
$twigView = new \Slim\Views\Twig();

/* Setup Slim itself */
$app = new \Slim\Slim(array(
    'view' => $twigView,
    'mode' => $c['config']['app.environment'],
    'log.writer' => $logger,
    'templates.path' => $c['config']['path.templates'],
));

/* Setup Auth Middleware */
$authenticate = function ($app) {
    return function () use ($app) {
        if (!isset($_SESSION['username'])) {
            $_SESSION['urlredirect'] = $app->request()->getPathInfo();
            $app->flash('error', 'Login Required');
            $app->redirect('/login');
        }
    };
};

$app->hook('slim.before.dispatch', function() use ($app) {
    $user = null;
    if (isset($_SESSION['username'])) {
        $user = $_SESSION['username'];
    }
    $app->view()->setData('username', $user);
});

/* Setup App Env */
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

$app->configureMode('development', function () use ($app) {
    $debugbar = new \Slim\Middleware\DebugBar();
    $app->add($debugbar);
    $app->config(array(
        'log.enable' => false,
        'debug' => true
    ));
});

/* Load Database */
$app->container->singleton('db', function() use ($c) {
    $dsn = $c['config']['db.dsn'];
    $user = $c['config']['db.username'];
    $password = $c['config']['db.password'];
    return new \alfmel\OCI8\PDO($dsn, $user, $password);
});

/* Twig Options */
$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => $c['config']['path.templates.cache']
);

/* Load Routes */
foreach (glob($c['config']['path.routes'] . '*.router.php') as $router) {
    require $router;
}

/* Run the App */
$app->run();
