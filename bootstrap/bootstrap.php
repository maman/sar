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
require_once __DIR__ . '/../vendor/autoload.php';

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
    'cookies.encrypt' => $config['app.cookie.encrypt'],
    'cookies.lifetime' => $config['app.cookie.lifetime'],
    'cookies.path' => $config['app.cookie.path'],
    'cookies.domain' => $config['app.cookie.domain'],
    'cookies.secure' => $config['app.cookie.secure'],
    'cookies.httponly' => $config['app.cookie.httponly'],
    'cookies.secret_key' => $config['app.cookie.secretkey'],
    'cookies.cipher' => MCRYPT_RIJNDAEL_256,
    'cookies.cipher_mode' => MCRYPT_MODE_CBC
));

$app->hook('slim.before.dispatch', function () use ($app) {
    $c['config'] = require 'config.php';
    $config = $c['config'];
    $baseUrl = $config['app.base.url'];
    $nip = null;
    $user = null;
    $role = null;
    $prodi = null;
    $matkul = null;
    $sar = null;
    $prevLink = null;
    $pathInfo = array();
    $currPath = $app->request->getPathInfo();
    $breadcrumb = explode('/', $currPath);
    foreach ($breadcrumb as $link) {
        if ($prevLink) {
            $pathInfo[$link] = $baseUrl . '/' . $prevLink . '/' . $link;
            $prevLink .= '/';
        } else {
            $pathInfo[$link] = $baseUrl . '/' . $link;
        }
        $prevLink .= $link;
    }
    if (isset($_SESSION['nip'])) {
        $nip = $_SESSION['nip'];
    }
    if (isset($_SESSION['username'])) {
        $user = $_SESSION['username'];
    }
    if (isset($_SESSION['role'])) {
        $role = $_SESSION['role'];
    }
    if (isset($_SESSION['prodi'])) {
        $prodi = $_SESSION['prodi'];
    }
    if (isset($_SESSION['matkul'])) {
        $matkul = $_SESSION['matkul'];
    }
    if (isset($_SESSION['sar'])) {
        $sar = $_SESSION['sar'];
    }
    $app->view()->appendData(array(
        'baseUrl' => $baseUrl,
        'nip' => $nip,
        'username' => $user,
        'role' => $role,
        'prodi' => $prodi,
        'matkuls' => $matkul,
        'sars' => $sar,
        'breadcrumbs' => $pathInfo
    ));
});

$app->setName('SAR');

/* PJAX */
if ($config['app.pjax']) {
    $app->add(new \SAR\helpers\Pjax());
}

/* CSRF Guard */
$app->add(new \Slim\Extras\Middleware\CsrfGuard());

/* STRONG Auth */
$app->add(new \SAR\helpers\Authentication(array(
    'login.url' => $config['login.url'],
    'security.urls' => $config['security.urls']
)));

/* Setup App Env */
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => true,
        'debug' => false
    ));
});

$app->configureMode('development', function () use ($app) {
    $debugbar = new \Slim\Middleware\DebugBar();
    $whoops = new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware();
    $app->add($debugbar);
    $app->add($whoops);
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
$app->container->singleton('db', function () use ($config) {
    $dsn = $config['db.dsn'];
    $user = $config['db.username'];
    $password = $config['db.password'];
    return new \alfmel\OCI8\PDO($dsn, $user, $password);
});

/* use APC, if Enabled */
if ($config['app.caching']) {
    $app->container->singleton('stash', function () use ($app, $config) {
        $timeout = $config['app.caching.timeout'];
        $options = array(
            'ttl' => $timeout
        );
        $driver = new \Stash\Driver\Apc();
        $driver->setOptions($options);
        return new \Stash\Pool($driver);
    });
}

/* Load Solr, if Enabled. */
if ($config['solr.enabled']) {
    $app->container->singleton('solr', function () use ($config) {
        $host = $config['solr.host'];
        $port = $config['solr.port'];
        $basePath = $config['solr.basePath'];
        $core = $config['solr.core'];
        return new \Solarium\Client(array(
            'endpoint' => array(
                'localhost' => array(
                    'host' => $host,
                    'port' => $port,
                    'path' => $basePath,
                    'core' => $core
                )
            )
        ));
    });
    $app->hook('slim.before', function () use ($app, $config) {
        $app->view()->appendData(array(
            'fts' => true
        ));
    });
}

/* Enable WebSocket, if Enabled. */
if ($config['ws.enabled']) {
    $app->hook('slim.before', function () use ($app, $config) {
        $app->view()->appendData(array(
            'ws' => true,
            'wsHost' => $config['ws.host']
        ));
    });
}

/* Twig Options */
$view = $app->view();
$view->parserOptions = array(
    'debug' => true,
    'cache' => $config['path.templates.cache']
);

/* Load Route Middlewares */
foreach (glob($config['path.middlewares'] . '*.php') as $middleware) {
    require $middleware;
}

/* Load Routes */
foreach (glob($config['path.routes'] . '*.router.php') as $router) {
    require $router;
}

/* Run the App */
$app->run();
