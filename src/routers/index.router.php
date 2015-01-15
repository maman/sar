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
    if (isset($_SESSION['nip']) && isset($_SESSION['username']) && isset($_SESSION['role']) && isset($_SESSION['matkul'])) {
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
        $app->render('pages/_dashboard.twig', array(
            'chartData' => $chartData,
            'matkulData' => $matkulData
        ));
    } else {
        $errors = '';
        $urlRedirect = '/';
        $user_val = $user_error = $pass_error = '';
        $flash = $app->view()->getData('flash');
        if ($app->request->get('r') && $app->request->get('r') != '/logout' && $app->request->get('r') != '/login') {
            $urlRedirect = $app->request->get('r');
            $_SESSION['urlRedirect'] = $urlRedirect;
        }
        if (isset($flash['errors'])) {
            $errors = $flash['errors'];
        }
        $app->render('pages/_login.twig', array(
            'errors' => $errors,
            'urlRedirect' => $urlRedirect,
            'isLogin' => true
        ));
    }
});
