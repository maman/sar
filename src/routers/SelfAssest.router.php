<?php

/**
 * Self Assest router for SAR Application
 *
 * this file contains route definition and logic for `/sar` route.
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
use SAR\models\Agenda;
use SAR\models\User;
use SAR\models\Plotting;
use SAR\models\SelfAssest;

$app->get('/sar', function () use ($app) {
    $app->render('pages/_self-assest-main.twig');
});

$app->get('/sar/:idMatkul', $accessar, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $sarDetails = array();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $user = new User();
    $plotting = new Plotting();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $sarDetails = $sar->getSARByAgenda($agendas[0]['ID_SUB_KOMPETENSI']);
    // var_dump($sar->getSARByAgenda($agendas[0]['ID_SUB_KOMPETENSI']));
    $dosenName = $user->getUserName($plotting->getDosenByMatkul($idMatkul, $_SESSION['prodi']));
    $app->render('pages/_self-assest.twig', array(
        'currPath' => $currPath,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'agendas' => $agendas,
        'sarDetails' => $sarDetails,
        'penanggungJawab' => $dosenName,
        'currList' => true
    ));
});

$app->get('/sar/:idMatkul/agenda', $accessar, function ($idMatkul) use ($app) {
    $app->flash('noHighlight', true);
    $app->redirect('/sar/' . $idMatkul);
});

$app->get('/sar/:idMatkul/agenda/:idAgenda', $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $user = new User();
    $plotting = new Plotting();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $sarDetails = $sar->getSARByAgenda($idAgenda);
    $dosenName = $user->getUserName($plotting->getDosenByMatkul($idMatkul, $_SESSION['prodi']));
    $app->render('pages/_self-assest.twig', array(
        'currPath' => $currPath,
        'idAgenda' => $idAgenda,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'agendas' => $agendas,
        'sarDetails' => $sarDetails,
        'penanggungJawab' => $dosenName,
        'currAgenda' => $idAgenda
    ));
});

$app->get('/sar/:idMatkul/agenda/:idAgenda/new', $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $user = new User();
    $plotting = new Plotting();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $sarDetails = $sar->getSARByAgenda($idAgenda);
    $dosenName = $user->getUserName($plotting->getDosenByMatkul($idMatkul, $_SESSION['prodi']));
    $app->render('pages/_self-assest.twig', array(
        'currPath' => $currPath,
        'idAgenda' => $idAgenda,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'agendas' => $agendas,
        'penanggungJawab' => $dosenName,
        'currAgenda' => $idAgenda,
        'isNew' => true
    ));
});

$app->post('/sar/:idMatkul/agenda/:idAgenda/new', $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $sar = new SelfAssest();
    $result = $sar->saveOrEdit('', $_POST['idAgenda'], $_POST['nama'], date("Y-m-d", strtotime($_POST['tanggal'])), $_POST['review'], $_POST['hambatan'], $_POST['persentase']);
    if ($result) {
        $app->redirect('/sar/' . $idMatkul . '/agenda/' . $idAgenda);
    } else {
        $app->stop();
    }
});

$app->get('/sar/:idMatkul/agenda/:idAgenda/edit', $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $user = new User();
    $plotting = new Plotting();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $sarDetails = $sar->getSARByAgenda($idAgenda);
    $dosenName = $user->getUserName($plotting->getDosenByMatkul($idMatkul, $_SESSION['prodi']));
    $app->render('pages/_self-assest.twig', array(
        'currPath' => $currPath,
        'idAgenda' => $idAgenda,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'agendas' => $agendas,
        'penanggungJawab' => $dosenName,
        'currAgenda' => $idAgenda,
        'sarDetails' => $sarDetails,
        'isNew' => true
    ));
});

$app->post('/sar/:idMatkul/agenda/:idAgenda/edit', $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $sar = new SelfAssest();
    $result = $sar->saveOrEdit($_POST['idSAR'], $_POST['idAgenda'], $_POST['nama'], date("Y-m-d", strtotime($_POST['tanggal'])), $_POST['review'], $_POST['hambatan'], $_POST['persentase']);
    if ($result) {
        $app->redirect('/sar/' . $idMatkul . '/agenda/' . $idAgenda);
    } else {
        $app->stop();
    }
});

$app->get('/sar/:idMatkul/agenda/:idAgenda/del/:idSAR', $accessar, function ($idMatkul, $idAgenda, $idSAR) use ($app) {
    $sar = new SelfAssest();
    $result = $sar->deleteSAR($idSAR);
    if ($result) {
        $app->redirect('/sar/' . $idMatkul . '/agenda/' . $idAgenda);
    } else {
        $app->stop();
    }
});
