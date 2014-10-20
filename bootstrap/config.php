<?php

$basedir = __DIR__ . '/../';

return array(

    'app.environment'     => 'development',
    'app.error_log'       => $basedir . 'logs/app_errors' . date('Y-m-d') . '.log',

    'php.error_reporting' => E_ALL,
    'php.display_errors'  => true,
    'php.log_errors'      => true,
    'php.error_log'       => $basedir . 'logs/php_errors.log',
    'php.date.timezone'   => 'Asia/Jakarta',

    'db.dsn'              => 'oci://10.10.10.11:1521/orcl',
    'db.username'         => 'sar',
    'db.password'         => 'Blink182',

    'memcached.host'      => '127.0.0.1',
    'memcached.port'      => '11211',

    'path.libs'           => $basedir . 'src/lib',
    'path.models'         => $basedir . 'src/models',
    'path.controllers'    => $basedir . 'src/controllers',
    'path.templates'      => $basedir . 'src/views'
);
