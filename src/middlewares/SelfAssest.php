<?php

/**
 * SAR Self Assesment Access Middleware
 *
 * This file contains the check routines used for check whether if an user
 * has access to a specific Self Assesment.
 *
 * DISCLAIMER: Only use this route middleware only to access /sar/ resources.
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
 * Slim accessmatkul Route Middleware
 */

use Functional as F;

$accessar = function ($route) use ($app) {
    $req = $app->request();
    $idMatkul = $route->getParams();
    $truth = array();
    foreach ($_SESSION['sar'] as $sar) {
        array_push($truth, F\contains($sar, $idMatkul['idMatkul']));
    }
    if (!F\contains($truth, true)) {
        // if ($_SESSION['role'] != 'kaprodi') {
        $app->flash('Tidak Diijinkan', 'Anda tidak diijinkan untuk mengakses Self Assesmen ini');
        $app->render('pages/_403.twig', array(
            'url' => $req->getUrl() . $req->getPath()
        ), 403);
        $app->log->error(
            "403: " . $req->getUrl() . $req->getPath() ." - User " . $_SESSION['username'] . " Not Authorized to accessing SAR with ID " . $idMatkul['idMatkul']
        );
        $app->stop();
            // $app->redirect('/', 403);
        // }
    }
};
