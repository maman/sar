<?php
$basedir = __DIR__ . '/../';

return array(

    // Application Environment
    'app.environment'      => 'development',
    'app.log'        => $basedir . 'logs/app-' . date('Y-m-d') . '.log',

    // PHP Configs
    'php.error_reporting'  => E_ALL,
    'php.display_errors'   => true,
    'php.log_errors'       => true,
    'php.error_log'        => $basedir . 'logs/php_errors.log',
    'php.date.timezone'    => 'Asia/Jakarta',

    // DB Configs
    'db.dsn'               => 'oci://10.10.10.11:1521/orcl',
    'db.username'          => 'sar',
    'db.password'          => 'Blink182',

    // Application Paths
    'path.routes'          => $basedir . 'src/SAR/routers/',
    'path.templates'       => $basedir . 'src/SAR/templates/',
    'path.templates.cache' => $basedir . 'src/SAR/templates/cache'
);
