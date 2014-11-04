<?php

/**
 * Matkul router for SAR Application
 *
 * this file contains route definition and logic for `/matakuliah/:idmatakuliah` route.
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
use SAR\models\User;
use SAR\models\Matkul;

/** GET request on `/matakuliah/:idmatakuliah` */
$app->get('/matakuliah/:id', $authenticate($app), function ($id) use ($app) {
    $matkul = new Matkul();
    $details = $matkul->getMatkulDetails($id)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $currPath = $app->request->getPath();
    $app->render('pages/_matakuliah.twig', array(
        'idMatkul' => $id,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'currPath' => $currPath
    ));
});
