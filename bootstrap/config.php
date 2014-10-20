<?php

use SAR\lib\Config;

$basedir = __DIR__ . '/../';

/* App Config */
Config::write('app.environment', 'development');
Config::write('app.error_log', $basedir . 'logs/app_errors' . date('Y-m-d') . '.log');

/* Database Config */
Config::write('db.dsn', 'oci://10.10.10.11:1521/orcl');
Config::write('db.username', 'sar');
Config::write('db.password', 'Blink182');

/* Memcached Config */
Config::write('memcached.host', '127.0.0.1');
Config::write('memcached.port', '11211');
