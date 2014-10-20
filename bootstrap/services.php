<?php

$c = new Pimple\Container();

$c['config'] = require 'config.php';

$c['app'] = function ($c) {

    $logger = new Flynsarmy\SlimMonolog\Log\MonologWriter(array(
        'handlers' => array(
            new Monolog\Handler\StreamHandler($c['config']['app.error_log'])
        ),
    ));

    $app = new Slim\Slim(array(
        'view' => new Slim\Views\Twig(),
        'mode' => $c['config']['app.environment'],
        'log.writer' => $logger,
        'templates.path' => $c['config']['path.templates']
    ));

    // Only invoked if mode is "production"
    $app->configureMode('production', function () use ($app) {
        $app->config(array(
            'log.enable' => true,
            'debug' => false
        ));
    });

    // Only invoked if mode is "development"
    $app->configureMode('development', function () use ($app) {
        $app->config(array(
            'log.enable' => false,
            'debug' => true
        ));
    });

    return $app;
};

$c['db'] = function ($c) {
    $config = $c['config'];
    $db = new CrazyCodr\Pdo\Oci8($config['db.dsn'], $config['db.username'], $config['db.password']);
    return $db;
};

$c['cache'] = function ($c) {
    $config = $c['config'];
    $memcached = new Memcache;
    $instance = $memcached->connect($config['memcached.host'], $config['memcached.port']);
    return $instance;
};

return $c;
