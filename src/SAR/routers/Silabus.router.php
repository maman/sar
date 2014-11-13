<?php

/**
 * Silabus router for SAR Application
 *
 * this file contains route definition and logic for `/matakuliah/:idmatakuliah/silabus` route.
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
use SAR\models\Silabus;

/** GET request on `/matakuliah/:idMatkul/silabus` */
$app->get('/matakuliah/:idMatkul/silabus', $authenticate($app), function ($idMatkul) use ($app) {
    $silabus = new Silabus($idMatkul);
    var_dump($silabus);
    $app->render('base.twig');
});
