<?php

/**
 * Arsip router for SAR Application
 *
 * this file contains route definition and logic for `/arsip` route.
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
 */

use SAR\models\Approval;
use SAR\models\Rps;

$app->get('/arsip', function () use ($app) {
    $currPath = $app->request()->getPath();
    $approval = new Approval();
    if (isset($_SESSION['nip'])) {
        $results = $approval->getAllApprovedMatkul(true);
    } else {
        $results = $approval->getAllApprovedMatkul(false);
    }
    $app->render('pages/_arsip.twig', array(
        'currPath' => $currPath,
        'results' => $results
    ));
});
