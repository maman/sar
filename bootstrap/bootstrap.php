<?php

/**
 * Main bootstrap code
 *
 * This file runs the `real` bootstrapping code that referenced in index.php file.
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 * @license GNU General Public License v2
 * @link https://github.com/maman/sar
 */
require_once '../vendor/autoload.php';

use Pimple\Container;

$c = new Container();

/* Starting Session First */
session_cache_limiter(false);
session_start();

/* Load Settings */
$c['config'] = require 'config.php';
$config = $c['config'];

/* Sensible PHP Settings */
error_reporting($config['php.error_reporting']);
ini_set('display_errors', $config['php.display_errors']);
ini_set('log_errors', $config['php.log_errors']);
ini_set('error_log', $config['php.error_log']);
date_default_timezone_set($config['php.date.timezone']);

/* Setup Slim Add-Ons */
$logger = new Flynsarmy\SlimMonolog\Log\MonologWriter(array(
    'handlers' => array(
        new Monolog\Handler\StreamHandler($config['app.log'])
    ),
));
$twigView = new \Slim\Views\Twig();

/* Setup Slim itself */
$app = new \Slim\Slim(array(
    'view' => $twigView,
    'mode' => $c['config']['app.environment'],
    'log.writer' => $logger,
    'templates.path' => $config['path.templates'],
    'cookies.encrypt' => true,
    'cookies.lifetime' => $config['app.cookie.lifetime'],
    'cookies.path' => $config['app.cookie.path'],
    'cookies.domain' => $config['app.cookie.domain'],
    'cookies.secure' => $config['app.cookie.secure'],
    'cookies.httponly' => $config['app.cookie.httponly'],
    'cookies.secret_key' => $config['app.cookie.secretkey'],
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
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

$app->hook('slim.before.dispatch', function () use ($app) {
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
    $app->hook('slim.before', function () use ($app) {
        $debug = true;
        $app->view()->appendData(array(
            'debug' => $debug
        ));
    });
});

/* Load Database */
$app->container->singleton('db', function() use ($config) {
    $dsn = $config['db.dsn'];
    $user = $config['db.username'];
    $password = $config['db.password'];
    return new \alfmel\OCI8\PDO($dsn, $user, $password);
});

/* Twig Options */
$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => $config['path.templates.cache']
);

/* Load Routes */
foreach (glob($config['path.routes'] . '*.router.php') as $router) {
    require $router;
}

/* Run the App */
$app->run();
