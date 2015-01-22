<?php

/**
 * Dokumen router for SAR Application
 *
 * this file contains route definition and logic for `/dokumen` route.
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

$app->get('/dokumen/:idMatkul', function ($idMatkul) use ($app) {
    $app->render('pages/_document.twig', array(
        'idMatkul' => $idMatkul
    ));
});

$app->get('/dokumen/:idMatkul/:year', function ($idMatkul, $year) use ($app) {
    $app->render('pages/_document.twig', array(
        'idMatkul' => $idMatkul,
        'year' => $year
    ));
});
