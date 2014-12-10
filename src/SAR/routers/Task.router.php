<?php

/**
 * Task router for SAR Application
 *
 * this file contains route definition and logic for `/matakuliah/:idmatakuliah/task` route.
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
use SAR\models\Silabus;
use SAR\models\Agenda;
use SAR\models\Kategori;
use SAR\models\Task;
use SAR\models\Rps;

$app->get('/matakuliah/:idMatkul/task', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $task = new Task();
    $kategori = new Kategori();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $tasks = $task->getDetailAktivitasByMatkul($idMatkul);
    $app->render('pages/_task.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'tasks' => $tasks,
        'currPath' => $currPath
    ));
});
