<?php

/**
 * Approval router for SAR Application
 *
 * this file contains route definition and logic for `/approval` & `/review` route.
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
use SAR\models\Rps;
use SAR\models\Silabus;
use SAR\models\Agenda;
use SAR\models\Task;
use SAR\models\Approval;
use SAR\helpers\Utilities;
use SAR\externals\Plotting;
use Functional as F;

$app->get('/approval', $authenticate($app), $kaprodi, function () use ($app) {
    $currPath = $app->request()->getPath();
    $approval = new Approval();
    $user = new User();
    $matkul = new Matkul();
    $rps = new Rps();
    $results = $approval->getAllApproval();
    $flash = $app->view()->getData('flash');
    if ($results) {
        foreach ($results as $num => $result) {
            $rps->getRpsByIdMatkul($results[$num]['KDMataKuliah']);
            // $results[$num]['Versi'] = $rps->versi;
            $results[$num]['NamaDosen'] = $user->getUserName($results[$num]['NIP']);
            $results[$num]['NamaMatkul'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['NamaMK'];
            $results[$num]['Semester'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['SemesterMK'];
        }
        if (isset($flash['msg'])) {
            $msg = $flash['msg'];
            $app->view->appendData(array(
                'msg' => $msg
            ));
        }
        if (isset($_GET['filter'])) {
            if ($_GET['filter'] == 'pending') {
                $results = F\select($results, function ($item, $key, $col) {
                    return $item['Approval'] == '0';
                });
            } elseif ($_GET['filter'] == 'approved') {
                $results = F\select($results, function ($item, $key, $col) {
                    return $item['Approval'] == '2';
                });
            } elseif ($_GET['filter'] == 'none') {
                $results = $results;
            }
            $app->render('pages/_approval.twig', array(
                'filtered' => true,
                'selected' => $_GET['filter'],
                'currPath' => $currPath,
                'results' => $results
            ));
        } else {
            $app->render('pages/_approval.twig', array(
                'currPath' => $currPath,
                'results' => $results
            ));
        }
    } else {
        $app->render('pages/_approval.twig', array(
            'currPath' => $currPath,
            'results' => $results
        ));
    }
});

$app->get('/approval/:idApproval/approve', $authenticate($app), $kaprodi, function ($idApproval) use ($app) {
    $rps = new Rps();
    $approval = new Approval();
    $matkul = new Matkul();
    $result = $rps->approve($_GET['id']);
    $matkulName = $matkul->getMatkulName($_GET['id']);
    $result = true;
    if ($result) {
        $approval->approveMatkul($idApproval, $_SESSION['nip'], $_GET['id']);
        $rps->updateProgress($_SESSION['nip']);
        $app->flash('msg', 'Request untuk mata kuliah ' . $matkulName . ' berhasil diapprove');
        $app->redirect('/approval?filter=pending');
    } else {
        $app->stop();
    }
});

$app->post('/approval/:idApproval/reject', $authenticate($app), $kaprodi, function ($idApproval) use ($app) {
    $rps = new Rps();
    $rps->getRpsByIdMatkul($_POST['idMatkul']);
    $approval = new Approval();
    $matkul = new Matkul();
    $result = $approval->rejectMatkul($idApproval, $_SESSION['nip'], $_POST['review']);
    $matkulName = $matkul->getMatkulName($_POST['idMatkul']);
    $result = true;
    if ($result) {
        $rps->resetAndBump($_POST['idMatkul']);
        $rps->updateProgress($_SESSION['nip']);
        $app->flash('msg', 'Request untuk mata kuliah ' . $matkulName . ' direject');
        $app->redirect('/approval?filter=pending');
    } else {
        $app->stop();
    }
});

$app->get('/review', $kaprodi, function () use ($app) {
    $app->redirect('/approval?filter=pending');
});

$app->get('/review/:idMatkul(/:year)', function ($idMatkul, $year = null) use ($app) {
    $req = $app->request();
    $currPath = $app->request()->getPath();
    $user = new User();
    $matkul = new Matkul();
    $rps = new Rps();
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul, $year);
    $silabus = new Silabus($idMatkul, $currPlot);
    $agenda = new Agenda();
    $task = new Task();
    $rps->getRpsByIdMatkul($idMatkul, $year);
    $matkulDetails = $matkul->getMatkulDetails($idMatkul);
    if ($matkulDetails) {
        $details = $matkulDetails[0];
    } else {
        $app->render('pages/_500.twig', array(
            'url' => $req->getUrl() . $req->getPath()
        ), 500);
        $app->stop();
    }
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul, $year);
    $tasks = $task->getDetailAktivitasByMatkul($idMatkul, $year);
    if (isset($_GET['id'])) {
        $idApproval = $_GET['id'];
        $app->view->appendData(array(
            'idApproval' => $idApproval
        ));
    }
    if (isset($_GET['nip'])) {
        $currNip = $_GET['nip'];
        $app->view->appendData(array(
            'currNip' => $currNip
        ));
    }
    $app->render('pages/_review.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'silabus' => array(
            'idSilabus' => $silabus->silabusID,
            'pokokBahasan' => $silabus->pokokBahasan,
            'tujuan' => $silabus->tujuan,
            'pustaka' => $silabus->pustaka,
            'kompetensi' => $silabus->kompetensi
        ),
        'agendas' => $agendas,
        'tasks' => $tasks,
        'currPath' => $currPath
    ));
});
