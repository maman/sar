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
use SAR\externals\Plotting;
use Functional as F;

/** GET request on `/matakuliah/:idMatkul/agenda` */
$app->get('/matakuliah/:idMatkul/agenda', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $kategori = new Kategori();
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    // $tahunMatkul = $details['TahunAjaranMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    if (!$agendas) {
        $rps->startAgenda($idMatkul);
        $rps->editAgenda($idMatkul);
    }
    $startDate = $rps->agendaStart;
    $lastEditDate = $rps->agendaLastEdit;
    $isComplete = false;
    if ($agendas) {
        $completionArray = array();
        foreach ($agendas as $item) {
            array_push($completionArray, F\truthy($item));
        }
        $isComplete = F\truthy($completionArray);
    }
    $app->render('pages/_agenda.twig', array(
        'idMatkul' => $idMatkul,
        'startDate' => $startDate,
        'lastEditDate' => $lastEditDate,
        'isComplete' => $isComplete,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        // 'tahunMatkul' => $tahunMatkul,
        'agendas' => $agendas,
        'currPath' => $currPath
    ));
});

/** GET request on `/matakuliah/:idMatkul/agenda/bump` */
$app->get('/matakuliah/:idMatkul/agenda/bump', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $rps->bumpProgress($idMatkul, 'agenda');
    $rps->updateProgress($_SESSION['nip']);
    $app->redirect('/matakuliah/'. $idMatkul);
});

$app->get('/matakuliah/:idMatkul/agenda/submit', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $rps->submitProgress($idMatkul, 'agenda');
    $rps->updateProgress($_SESSION['nip']);
    $app->redirect('/matakuliah/'. $idMatkul);
});

/** GET request on `/matakuliah/:idMatkul/agenda/new` */
$app->get('/matakuliah/:idMatkul/agenda/new', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $kategori = new Kategori();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $app->render('pages/_agenda-new.twig', array(
        'isEdit' => false,
        'idSilabus' => $silabus->silabusID,
        'kompetensi' => $kategori->getAllKompetensiByMatkul($idMatkul),
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'currPath' => $currPath,
        'btnPath' => '/matakuliah/'. $idMatkul . '/agenda',
        'isBack' => true
    ));
});

/** POST request on `/matakuliah/:idMatkul/agenda/new` */
$app->post('/matakuliah/:idMatkul/agenda/new', $authenticate($app), function ($idMatkul) use ($app) {
    $agenda = new Agenda();
    $rps = new Rps();
    $result = $agenda->saveOrEdit($_POST['idSilabus'], '', $_POST['rangePertemuan'], $_POST['bobot'], $_POST['kompetensi'], $_POST['textSubKompetensi'], $_POST['textMateriBelajar']);
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/edit?id=:idAgenda` */
$app->get('/matakuliah/:idMatkul/agenda/edit', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $kategori = new Kategori();
    $idAgenda = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $detailAgenda = $agenda->getDetailAgenda($idAgenda);
    $app->render('pages/_agenda-new.twig', array(
        'isEdit' => true,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'idAgenda' => $idAgenda,
        'idSilabus' => $detailAgenda[0]['ID_SILABUS'],
        'rangePertemuan' => $detailAgenda[0]['RANGE_PERTEMUAN'],
        'bobot' => $detailAgenda[0]['BOBOT'],
        'kompetensi' => $kategori->getAssociatedKompetensiVerbose($idMatkul, $idAgenda),
        'txtSubKompetensi' => $detailAgenda[0]['TEXT_SUB_KOMPETENSI'],
        'txtMateriBelajar' => $detailAgenda[0]['TEXT_MATERI_BELAJAR'],
        'currPath' => $currPath,
        'btnPath' => '/matakuliah/'. $idMatkul . '/agenda',
        'isBack' => true
    ));
});

/** POST request on `/matakuliah/:idMatkul/agenda/edit?id=:idAgenda` */
$app->post('/matakuliah/:idMatkul/agenda/edit', $authenticate($app), function ($idMatkul) use ($app) {
    $agenda = new Agenda();
    $rps = new Rps();
    $result = $agenda->saveOrEdit($_POST['idSilabus'], $_POST['idAgenda'], $_POST['rangePertemuan'], $_POST['bobot'], $_POST['kompetensi'], $_POST['textSubKompetensi'], $_POST['textMateriBelajar']);
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/del/:idAgenda` */
$app->get('/matakuliah/:idMatkul/agenda/del/:idAgenda', $authenticate($app), function ($idMatkul, $idAgenda) use ($app) {
    $agenda = new Agenda();
    $rps = new Rps();
    $result = $agenda->deleteAgenda($idAgenda);
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/indikator?id=:idAgenda` */
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
        'btnPath' => '/matakuliah/'. $idMatkul . '/agenda',
        'currPath' => $currPath
    ));
});

/** POST request on `/matakuliah/:idMatkul/agenda/indikator?id=:idAgenda` */
$app->post('/matakuliah/:idMatkul/agenda/indikator', $authenticate($app), function ($idMatkul) use ($app) {
    $agenda = new Agenda();
    $rps = new Rps();
    $result = $agenda->saveIndikator($_POST['idAgenda'], $_POST['textIndikator'], $_POST['indikator']);
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/indikator?id=' . $_POST['idAgenda']);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/indikator/del/:idIndikator?id=:idAgenda` */
$app->get('/matakuliah/:idMatkul/agenda/indikator/del/:idIndikator', $authenticate($app), function ($idMatkul, $idIndikator) use ($app) {
    $agenda = new Agenda();
    $rps = new Rps();
    $result = $agenda->deleteIndikator($idIndikator);
    $idAgenda = $_GET['id'];
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/indikator?id=' . $idAgenda);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/aktivitas?id=:idAgenda` */
$app->get('/matakuliah/:idMatkul/agenda/aktivitas', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $kategori = new Kategori();
    $idAgenda = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $aktivitas = $agenda->getAktivitasByAgendaID($idAgenda);
    $app->render('pages/_aktivitas.twig', array(
        'idMatkul' => $idMatkul,
        'btnPath' => '/matakuliah/'. $idMatkul . '/agenda',
        'namaMatkul' => $namaMatkul,
        'idAgenda' => $idAgenda,
        'aktivitas' => $aktivitas,
        'currPath' => $currPath
    ));
});

/** POST request on `/matakuliah/:idMatkul/agenda/aktivitas?id=:idAgenda` */
$app->post('/matakuliah/:idMatkul/agenda/aktivitas', $authenticate($app), function ($idMatkul) use ($app) {
    $task = '0';
    $project = '0';
    if (isset($_POST['chkTask'])) {
        $task = '1';
    }
    if (isset($_POST['chkProject'])) {
        $project = '1';
    }
    $rps = new Rps();
    $agenda = new Agenda();
    $result = $agenda->saveAktivitas($_POST['idAgenda'], $_POST['textAktivitas'], $project, $task);
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/aktivitas?id=' . $_POST['idAgenda']);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/aktivitas/del/:idAktivitas?id=:idAgenda` */
$app->get('/matakuliah/:idMatkul/agenda/aktivitas/del/:idAktivitas', $authenticate($app), function ($idMatkul, $idAktivitas) use ($app) {
    $rps = new Rps();
    $agenda = new Agenda();
    $result = $agenda->deleteAktivitas($idAktivitas);
    $idAgenda = $_GET['id'];
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/aktivitas?id=' . $idAgenda);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/aktivitas?id=:idAgenda` */
$app->get('/matakuliah/:idMatkul/agenda/asesmen', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $kategori = new Kategori();
    $idAgenda = $_GET['id'];
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $asesmen = $agenda->getAsesmenByAgendaID($idAgenda);
    $app->render('pages/_asesmen.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'idAgenda' => $idAgenda,
        'asesmen' => $asesmen,
        'btnPath' => '/matakuliah/'. $idMatkul . '/agenda',
        'currPath' => $currPath
    ));
});

/** POST request on `/matakuliah/:idMatkul/agenda/aktivitas?id=:idAgenda` */
$app->post('/matakuliah/:idMatkul/agenda/asesmen', $authenticate($app), function ($idMatkul) use ($app) {
    $jenis = '0';
    if (isset($_POST['jenisAsesmen'])) {
        $jenis = '1';
    }
    $rps = new Rps();
    $agenda = new Agenda();
    $result = $agenda->saveAsesmen($_POST['idAgenda'], $_POST['textAsesmen'], $jenis);
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/asesmen?id=' . $_POST['idAgenda']);
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/agenda/aktivitas/del/:idAktivitas?id=:idAgenda` */
$app->get('/matakuliah/:idMatkul/agenda/asesmen/del/:idAsesmen', $authenticate($app), function ($idMatkul, $idAsesmen) use ($app) {
    $rps = new Rps();
    $agenda = new Agenda();
    $result = $agenda->deleteAsesmen($idAsesmen);
    $idAgenda = $_GET['id'];
    if ($result) {
        $rps->editAgenda($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/agenda/asesmen?id=' . $idAgenda);
    } else {
        $app->stop();
    }
});
