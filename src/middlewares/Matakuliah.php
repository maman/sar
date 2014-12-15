<?php

/**
 * SAR Matakuliah Access Middleware
 *
 * This file contains the check routines used for check whether if an user
 * has access to a specific matakuliah.
 *
 * DISCLAIMER: Only use this route middleware only to access /matakuliah/ resources.
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
 * Slim accessmatkul Middleware
 */

$accessmatkul = function ($route) use ($app) {
    $req = $app->request();
    $idMatkul = $route->getParams();
    if (!array_search($idMatkul['idMatkul'], $_SESSION['matkul'])) {
        if ($_SESSION['role'] != 'kaprodi') {
            $app->log->error(
                "403: " . $req->getUrl() . $req->getPath()." - SessionDump: [" . var_export($_SESSION, true) . "]"
            );
            $app->redirect('/', 403);
        }
    }
};
