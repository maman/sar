<?php

/**
 * Main router for SAR Application
 *
 * this file contains route definition and logic for `/` route or the homepage.
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

use SAR\helpers\Utilities;
use SAR\models\Approval;
use SAR\models\Matkul;

/** GET request on `/` */
$app->get('/', function () use ($app) {
    $req = $app->request();
    $approval = new Approval();
    $util = new Utilities();
    $matkul = new Matkul();
    $matkulYear = $matkul->getMatkulYear();
    $range = $util->getRangeTahunAjaran();
    $results = $approval->getAllApproval();
    $matkulData = array();
    $chartData = array();
    foreach ($matkulYear as $yearKey => $yearVal) {
        $matkulCount = $matkul->getMatkulYearCount($yearVal['TahunAjaranMK']);
        array_push($matkulData, array(
            'year' => $yearVal['TahunAjaranMK'],
            'jumlah' => $matkulCount,
        ));
    }
    foreach ($range as $rangeKey => $rangeVal) {
        $approved = $approval->getApprovedCount($rangeVal);
        $rejected = $approval->getRejectedCount($rangeVal);
        array_push($chartData, array(
            'year' => $rangeVal,
            'approved' => $approved,
            'rejected' => $rejected
        ));
    }
    if (isset($_SESSION['username'])) {
        $matkulCount = count($_SESSION['matkul']);
        if (!isset($_SESSION['matkul']) || $matkulCount < 1) {
            $app->flash('errors', "Not Yet Plotted");
            $app->log->notice("NOT PLOTTED: " . $_SESSION['username'] . " from " . $req->getIp());
            $app->redirect('/login');
        }
    }
    $app->render('pages/_dashboard.twig', array(
        'chartData' => $chartData,
        'matkulData' => $matkulData
    ));
});
