<?php

/**
 * Silabus router for SAR Application
 *
 * this file contains route definition and logic for `/silabus` route.
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
// use SAR\models\User;

/** GET request on `/silabus` */
$app->get('/silabus', $authenticate($app), function () use ($app) {
    $username = $_SESSION['username'];
    $role     = $_SESSION['role'];
    $app->render('pages/_silabus.twig');
});
