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
use SAR\models\Task;
use SAR\models\Rps;

/** GET request on `/matakuliah/:idMatkul/task` */
$app->get('/matakuliah/:idMatkul/task', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $task = new Task();
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $tasks = $task->getDetailAktivitasByMatkul($idMatkul);
    if (!is_array($tasks)) {
        $tasks = false;
    }
    if (is_null($rps->projectLastEdit)) {
        $lastEditDate = $rps->agendaLastEdit;
    } else {
        $lastEditDate = $rps->projectLastEdit;
    }
    $app->render('pages/_task.twig', array(
        'idMatkul' => $idMatkul,
        'lastEditDate' => $lastEditDate,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'tasks' => $tasks,
        'currPath' => $currPath
    ));
});

/** GET request on `/matakuliah/:idMatkul/task/bump` */
$app->get('/matakuliah/:idMatkul/task/bump', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $rps->bumpProgress($idMatkul, 'project');
    $app->redirect('/matakuliah/'. $idMatkul);
});

/** GET request on `/matakuliah/:idMatkul/task/submit` */
$app->get('/matakuliah/:idMatkul/task/submit', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $rps->submitProgress($idMatkul, 'project');
    $rps->updateProgress($_SESSION['nip']);
    $app->redirect('/matakuliah/'. $idMatkul);
});

/** GET request on `/matakuliah/:idMatkul/task/scope?id=:idAktivitas` */
$app->get('/matakuliah/:idMatkul/task/scope', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $task = new Task();
    $idAktivitas = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $collection = $task->getScopeByAktivitas($idAktivitas);
    $app->render('pages/_subtask.twig', array(
        'pageTitle' => 'Scope',
        'idAktivitas' => $idAktivitas,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'collection' => $collection,
        'currPath' => $currPath
    ));
});

/** POST request on `/matakuliah/:idMatkul/task/scope?id=:idAktivitas` */
$app->post('/matakuliah/:idMatkul/task/scope', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $task = new Task();
    $result = $task->saveScope($_POST['idAktivitas'], $_POST['text']);
    if ($result) {
        $rps->editProject($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/task/scope?id=' . $_POST['idAktivitas']);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/task/scope/del/:idScope?id=:idAktivitas` */
$app->get('/matakuliah/:idMatkul/task/scope/del/:idScope', $authenticate($app), function ($idMatkul, $idScope) use ($app) {
    $rps = new Rps();
    $task = new Task();
    $result = $task->deleteScopeById($idScope);
    $idAktivitas = $_GET['id'];
    if ($result) {
        $rps->editProject($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/task/scope?id=' . $idAktivitas);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/task/metode?id=:idAktivitas` */
$app->get('/matakuliah/:idMatkul/task/metode', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $task = new Task();
    $idAktivitas = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $collection = $task->getMetodeByAktivitas($idAktivitas);
    $app->render('pages/_subtask.twig', array(
        'pageTitle' => 'Metode',
        'idAktivitas' => $idAktivitas,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'collection' => $collection,
        'currPath' => $currPath
    ));
});

/** POST request on `/matakuliah/:idMatkul/task/metode?id=:idAktivitas` */
$app->post('/matakuliah/:idMatkul/task/metode', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $task = new Task();
    $result = $task->saveMetode($_POST['idAktivitas'], $_POST['text']);
    if ($result) {
        $rps->editProject($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/task/metode?id=' . $_POST['idAktivitas']);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/task/metode/del/:idMetode?id=:idAktivitas` */
$app->get('/matakuliah/:idMatkul/task/metode/del/:idMetode', $authenticate($app), function ($idMatkul, $idMetode) use ($app) {
    $rps = new Rps();
    $task = new Task();
    $result = $task->deleteMetodeById($idMetode);
    $idAktivitas = $_GET['id'];
    if ($result) {
        $rps->editProject($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/task/metode?id=' . $idAktivitas);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/task/kriteria?id=:idAktivitas` */
$app->get('/matakuliah/:idMatkul/task/kriteria', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $task = new Task();
    $idAktivitas = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $collection = $task->getKriteriaByAktivitas($idAktivitas);
    $app->render('pages/_subtask.twig', array(
        'pageTitle' => 'Kriteria',
        'idAktivitas' => $idAktivitas,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'collection' => $collection,
        'currPath' => $currPath
    ));
});

/** POST request on `/matakuliah/:idMatkul/task/kriteria?id=:idAktivitas` */
$app->post('/matakuliah/:idMatkul/task/kriteria', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $task = new Task();
    $result = $task->saveKriteria($_POST['idAktivitas'], $_POST['text']);
    if ($result) {
        $rps->editProject($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/task/kriteria?id=' . $_POST['idAktivitas']);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/task/kriteria/del/:idKriteria?id=:idAktivitas` */
$app->get('/matakuliah/:idMatkul/task/kriteria/del/:idKriteria', $authenticate($app), function ($idMatkul, $idKriteria) use ($app) {
    $rps = new Rps();
    $task = new Task();
    $result = $task->deleteScopeById($idKriteria);
    $idAktivitas = $_GET['id'];
    if ($result) {
        $rps->editProject($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/task/kriteria?id=' . $idAktivitas);
    } else {
        $app->stop();
    }
});
