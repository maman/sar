<?php

/**
 * Config File
 *
 * This file contains the base application configuration.
 * Edit this file as needed.
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

/**
 * Define the base directory of the Application. this should be pointed to the
 * project root directory, relative to the current directory.
 * @var string
 */
$basedir = __DIR__ . '/../';

return array(

    /** Application Settings */
    'app.environment'      => 'development',
    'app.log'              => $basedir . 'logs/app-' . date('Y-m-d') . '.log',
    'app.cookie.lifetime'  => '20 minutes',
    'app.cookie.path'      => '/',
    'app.cookie.domain'    => null,
    'app.cookie.secure'    => false,
    'app.cookie.httponly'  => false,
    'app.cookie.secretkey' => '0f3f86a8f06ea64ae3f388c65c35a53d',

    /** PHP Additional Settings */
    'php.error_reporting'  => E_ALL,
    'php.display_errors'   => true,
    'php.log_errors'       => true,
    'php.error_log'        => $basedir . 'logs/php_errors.log',
    'php.date.timezone'    => 'Asia/Jakarta',

    /** Database Settings */
    'db.dsn'               => 'oci://10.10.10.11:1521/orcl',
    'db.username'          => 'sar',
    'db.password'          => 'Blink182',

    /** Application Paths */
    'path.routes'          => $basedir . 'src/SAR/routers/',
    'path.templates'       => $basedir . 'src/SAR/templates/',
    'path.templates.cache' => $basedir . 'src/SAR/templates/cache'
);
