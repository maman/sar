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

/** GET request on `/` */
$app->get('/', function () use ($app) {
    $req = $app->request();
    if (isset($_SESSION['username'])) {
        if (!isset($_SESSION['matkul'])) {
            $app->flash('errors', "Not Yet Plotted");
            $app->log->notice("NOT PLOTTED: " . $_SESSION['username'] . " from " . $req->getIp());
            $app->redirect('/login');
        }
    }
    $app->render('pages/_dashboard.twig');
});
