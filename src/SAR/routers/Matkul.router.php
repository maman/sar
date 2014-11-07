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
use SAR\models\Matkul;

/** GET request on `/matakuliah/:idmatakuliah` */
$app->get('/matakuliah/:idMatkul', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $app->render('pages/_matakuliah.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'currPath' => $currPath
    ));
});

/** GET request on `/matakuliah/:idMatkul/silabus` */
$app->get('/matakuliah/:idMatkul/silabus', $authenticate($app), function ($idMatkul) use ($app) {
    $app->render('pages/_silabus.twig');
});

/** GET request on `//matakuliah/:idMatkul/rpp` */
$app->get('/matakuliah/:idMatkul/rpp', $authenticate($app), function ($idMatkul) use ($app) {
    $app->render('pages/_silabus.twig');
});
