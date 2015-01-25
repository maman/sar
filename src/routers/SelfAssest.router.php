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
use SAR\externals\Nilai;
use SAR\externals\Plotting as extPlot;

$app->get('/sar/all', $kaprodi, function () use ($app) {
    $app->render('pages/_self-assest-all.twig');
});

$app->get('/sar/:idMatkul', function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $agendaTemp = array();
    $nilai = array();
    $chartData = array();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $user = new User();
    $plotting = new Plotting();
    $nilai = new Nilai();
    $extPlot = new extPlot();
    $range = $extPlot->getAllPlottingYearByMatkul($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    if (!isset($_GET['year'])) {
        $agendas = $agenda->getAgendaByMatkul($idMatkul);
        $total = $nilai->getTotalPercentageByMatkul($idMatkul);
    } else {
        $agendas = $agenda->getAgendaByMatkul($idMatkul, $_GET['year'], null);
        $total = $nilai->getTotalPercentageByMatkul($idMatkul, $_GET['year']);
        $app->view->appendData(array(
            'isByYear' => true
        ));
    }
    if ($agendas) {
        foreach ($agendas as $keyAgenda => $valAgenda) {
            array_push($agendaTemp, array(
                'ID_SUB_KOMPETENSI' => $agendas[$keyAgenda]['ID_SUB_KOMPETENSI'],
                'NAMA_SAR' => $sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['NAMA_SAR'],
                'PERSENTASE_SAR' => ($sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['PERSENTASE'] == '0' || $sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['PERSENTASE'] == '' ? '0': $sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['PERSENTASE'])
            ));
        }
    }
    foreach ($agendaTemp as $tempKey => $tempVal) {
        $agendaTemp[$tempKey]['PERSENTASE_NILAI'] = $nilai->getPercentageByAgenda($agendaTemp[$tempKey]['ID_SUB_KOMPETENSI']);
        $agendaTemp[$tempKey]['SUM'] = $agendaTemp[$tempKey]['PERSENTASE_NILAI'] + $agendaTemp[$tempKey]['PERSENTASE_SAR'];
    }
    foreach ($range as $rangeKey => $rangeVal) {
        $num = $nilai->getTotalPercentageByMatkul($idMatkul, $range[$rangeKey]['TAHUNAJARAN']);
        array_push($chartData, array(
            'year' => $range[$rangeKey]['TAHUNAJARAN'],
            'nilai' => $num
        ));
    }
    $dosenName = $user->getUserName($plotting->getDosenByMatkul($idMatkul, $_SESSION['prodi']));
    $app->render('pages/_self-assest-main.twig', array(
        'currPath' => $currPath,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'agendas' => $agendaTemp,
        'total' => $total,
        'chartData' => $chartData,
        'penanggungJawab' => $dosenName,
        'currList' => true
    ));
});

$app->get('/sar/:idMatkul/details(/:year)', $accessar, function ($idMatkul, $year = null) use ($app) {
    // $app->render('pages/_self-assest-main.twig');
    $currPath = $app->request->getPath();
    $sarDetails = array();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $user = new User();
    $plotting = new Plotting();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    if ($year === null) {
        $agendas = $agenda->getAgendaByMatkul($idMatkul);
    } else {
        $agendas = $agenda->getAgendaByMatkul($idMatkul, $year, null);
    }
    $sarDetails = $sar->getSARByAgenda($agendas[0]['ID_SUB_KOMPETENSI']);
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

$app->get('/sar/:idMatkul/details/agenda', $accessar, function ($idMatkul) use ($app) {
    $app->flash('noHighlight', true);
    $app->redirect('/sar/' . $idMatkul . '/details');
});

$app->get('/sar/:idMatkul/details/agenda/:idAgenda', $accessar, function ($idMatkul, $idAgenda) use ($app) {
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

$app->get('/sar/:idMatkul/details/agenda/:idAgenda/new', $accessar, function ($idMatkul, $idAgenda) use ($app) {
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

$app->post('/sar/:idMatkul/details/agenda/:idAgenda/new', $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $sar = new SelfAssest();
    $result = $sar->saveOrEdit('', $_POST['idAgenda'], $_POST['nama'], date("Y-m-d", strtotime($_POST['tanggal'])), $_POST['review'], $_POST['hambatan'], $_POST['persentase']);
    if ($result) {
        $app->redirect('/sar/' . $idMatkul . '/details/agenda/' . $idAgenda);
    } else {
        $app->stop();
    }
});

$app->get('/sar/:idMatkul/details/agenda/:idAgenda/edit', $accessar, function ($idMatkul, $idAgenda) use ($app) {
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

$app->post('/sar/:idMatkul/details/agenda/:idAgenda/edit', $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $sar = new SelfAssest();
    $result = $sar->saveOrEdit($_POST['idSAR'], $_POST['idAgenda'], $_POST['nama'], date("Y-m-d", strtotime($_POST['tanggal'])), $_POST['review'], $_POST['hambatan'], $_POST['persentase']);
    if ($result) {
        $app->redirect('/sar/' . $idMatkul . '/details/agenda/' . $idAgenda);
    } else {
        $app->stop();
    }
});

$app->get('/sar/:idMatkul/details/agenda/:idAgenda/del/:idSAR', $accessar, function ($idMatkul, $idAgenda, $idSAR) use ($app) {
    $sar = new SelfAssest();
    $result = $sar->deleteSAR($idSAR);
    if ($result) {
        $app->redirect('/sar/' . $idMatkul . '/details/agenda/' . $idAgenda);
    } else {
        $app->stop();
    }
});
