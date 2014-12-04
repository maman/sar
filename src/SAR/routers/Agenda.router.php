<?php

/**
 * Agenda router for SAR Application
 *
 * this file contains route definition and logic for `/matakuliah/:idmatakuliah/agenda` route.
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
use SAR\models\Rps;

/** GET request on `/matakuliah/:idMatkul/agenda` */
$app->get('/matakuliah/:idMatkul/agenda', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $kategori = new Kategori();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $app->render('pages/_agenda.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'agendas' => $agendas,
        'currPath' => $currPath
    ));
});

$app->get('/matakuliah/:idMatkul/agenda/new', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $silabus = new Silabus($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $app->render('pages/_agenda-new.twig', array(
        'idSilabus' => $silabus->silabusID,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'currPath' => $currPath
    ));
});

$app->post('/matakuliah/:idMatkul/agenda/new',  $authenticate($app), function ($idMatkul) use ($app) {
    $agenda = new Agenda();
    $result = $agenda->saveOrEdit($_POST['idSilabus'], '', $_POST['rangePertemuan'], $_POST['bobot'], $_POST['textSubKompetensi'], $_POST['textMateriBelajar']);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda');
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/agenda/edit', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $idAgenda = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $detailAgenda = $agenda->getDetailAgenda($idAgenda);
    $app->render('pages/_agenda-new.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'idAgenda' => $idAgenda,
        'idSilabus' => $detailAgenda[0]['ID_SILABUS'],
        'rangePertemuan' => $detailAgenda[0]['RANGE_PERTEMUAN'],
        'bobot' => $detailAgenda[0]['BOBOT'],
        'txtSubKompetensi' => $detailAgenda[0]['TEXT_SUB_KOMPETENSI'],
        'txtMateriBelajar' => $detailAgenda[0]['TEXT_MATERI_BELAJAR'],
        'currPath' => $currPath
    ));
});

$app->post('/matakuliah/:idMatkul/agenda/edit',  $authenticate($app), function ($idMatkul) use ($app) {
    $agenda = new Agenda();
    $result = $agenda->saveOrEdit($_POST['idSilabus'], $_POST['idAgenda'], $_POST['rangePertemuan'], $_POST['bobot'], $_POST['textSubKompetensi'], $_POST['textMateriBelajar']);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda');
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/agenda/del/:idAgenda', $authenticate($app), function ($idMatkul, $idAgenda) use ($app) {
    $agenda = new Agenda();
    $result = $agenda->deleteAgenda($idAgenda);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda');
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/agenda/indikator', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $kategori = new Kategori();
    $idAgenda = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $indikator = $agenda->getIndikatorByAgendaID($idAgenda);
    $allKategori = $kategori->getAllKategoriIndikator();
    $app->render('pages/_indikator.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'idAgenda' => $idAgenda,
        'indikator' => $indikator,
        'allKategori' => $allKategori,
        'currPath' => $currPath
    ));
});

$app->post('/matakuliah/:idMatkul/agenda/indikator', $authenticate($app), function ($idMatkul) use ($app) {
    $agenda = new Agenda();
    $result = $agenda->saveIndikator($_POST['idAgenda'], $_POST['textIndikator'], $_POST['indikator']);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/indikator?id=' . $_POST['idAgenda']);
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/agenda/indikator/del/:idIndikator', $authenticate($app), function ($idMatkul, $idIndikator) use ($app) {
    $agenda = new Agenda();
    $result = $agenda->deleteIndikator($idIndikator);
    $idAgenda = $_GET['id'];
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/indikator?id=' . $idAgenda);
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/agenda/aktivitas', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $kategori = new Kategori();
    $idAgenda = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $aktivitas = $agenda->getAktivitasByAgendaID($idAgenda);
    $allKategori = $kategori->getAllKategoriIndikator();
    $app->render('pages/_aktivitas.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'idAgenda' => $idAgenda,
        'aktivitas' => $aktivitas,
        'currPath' => $currPath
    ));
});

$app->post('/matakuliah/:idMatkul/agenda/aktivitas', $authenticate($app), function ($idMatkul) use ($app) {
    var_dump($_POST);
    $task = '0';
    $project = '0';
    if(isset($_POST['chkTask'])) {
        $task = '1';
    }
    if(isset($_POST['chkProject'])) {
        $project = '1';
    }
    $agenda = new Agenda();
    // echo('$result = $agenda->saveAktivitas(' . $_POST['idAgenda'] . ', ' . $_POST['textAktivitas'] . ', ' . $project . ', ' . $task . ')');
    $result = $agenda->saveAktivitas($_POST['idAgenda'], $_POST['textAktivitas'], $project, $task);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/aktivitas?id=' . $_POST['idAgenda']);
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/agenda/aktivitas/del/:idAktivitas', $authenticate($app), function ($idMatkul, $idAktivitas) use ($app) {
    $agenda = new Agenda();
    $result = $agenda->deleteAktivitas($idAktivitas);
    $idAgenda = $_GET['id'];
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/aktivitas?id=' . $idAgenda);
    } else {
        $app->stop();
    }
});
