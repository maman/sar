<?php

/**
 * Bootstrap Application
 */
require_once '../vendor/autoload.php';

$c = require 'services.php';

/* Set sane runtime environment */
$config = $c['config'];
error_reporting($config['php.error_reporting']);
ini_set('display_errors', $config['php.display_errors']);
ini_set('log_errors', $config['php.log_errors']);
ini_set('error_log', $config['php.error_log']);
date_default_timezone_set($config['php.date.timezone']);
session_start();
$cache = $c['cache'];
$db = $c['db'];
$app = $c['app'];

/* Load Additional Libraries */
foreach (glob($config['path.libs'] . '*php') as $file) {
    require_once $file;
}

/* Load Models */
foreach (glob($config['path.models'] . '*php') as $file) {
    require_once $file;
}

/* Load Controllers */
foreach (glob($config['path.controllers'] . '*php') as $file) {
    require_once $file;
}

/* Load Routes */
require_once '../src/routes.php';

/* Run boy, Run */
$app->run();
