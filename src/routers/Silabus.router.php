<?php

/**
 * Silabus router for SAR Application
 *
 * this file contains route definition and logic for `/matakuliah/:idmatakuliah/silabus` route.
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
use SAR\models\Kategori;
use SAR\models\Rps;
use SAR\externals\Plotting;

/** GET request on `/matakuliah/:idMatkul/silabus` */
$app->get('/matakuliah/:idMatkul/silabus', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $plotting = new Plotting();
    $matkul = new Matkul();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    // $tahunMatkul = $details['TahunAjaranMK'];
    $new = false;
    if (is_null($silabus->silabusID)) {
        $new = true;
        $rps->startSilabus($idMatkul);
    }
    $startDate = $rps->silabusStart;
    $lastEditDate = $rps->silabusLastEdit;
    $isComplete = true;
    if ($silabus->pustaka === false || $silabus->kompetensi === false) {
        $isComplete = false;
    }
    if ($new) {
        $app->redirect('/matakuliah/' . $idMatkul . '/silabus/new');
    }
    $app->render('pages/_silabus.twig', array(
        'currPath' => $currPath,
        'isNew' => $new,
        'isComplete' => $isComplete,
        'startDate' => $startDate,
        'lastEditDate' => $lastEditDate,
        'namaMatkul' => $namaMatkul,
        'silabus' => array(
            'idSilabus' => $silabus->silabusID,
            'pokokBahasan' => $silabus->pokokBahasan,
            'tujuan' => $silabus->tujuan,
            'pustaka' => $silabus->pustaka,
            'kompetensi' => $silabus->kompetensi
        )
    ));
});

/** GET request on `/matakuliah/:idMatkul/silabus/bump` */
$app->get('/matakuliah/:idMatkul/silabus/bump', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $rps->bumpProgress($idMatkul, 'silabus');
    $app->redirect('/matakuliah/'. $idMatkul);
});

/** GET request on `/matakuliah/:idMatkul/silabus/submit` */
$app->get('/matakuliah/:idMatkul/silabus/submit', $authenticate($app), function ($idMatkul) use ($app) {
    $rps = new Rps();
    $rps->getRpsByIdMatkul($idMatkul);
    $rps->submitProgress($idMatkul, 'silabus');
    $rps->updateProgress($_SESSION['nip']);
    $app->redirect('/matakuliah/'. $idMatkul);
});

/** GET request on `/matakuliah/:idMatkul/silabus/new` */
$app->get('/matakuliah/:idMatkul/silabus/new', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $matkul = new Matkul();
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    if (!is_null($silabus->silabusID)) {
        $_SESSION['silabusID'] = $silabus->silabusID;
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/edit');
    } else {
        $rps = new Rps();
        $rps->getRpsByIdMatkul($idMatkul);
        if (is_null($rps->silabusStart)) {
            $rps->startSilabus($idMatkul);
        }
        $app->render('pages/_silabus-new.twig', array(
            'currPath' => $currPath,
            'namaMatkul' => $namaMatkul
        ));
    }
});

/** POST request on `/matakuliah/:idMatkul/silabus/new` */
$app->post('/matakuliah/:idMatkul/silabus/new', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $result = $silabus->saveOrEdit($idMatkul, '', $_POST['pokok-bahasan'], $_POST['tujuan']);
    if ($result) {
        $rps = new Rps();
        $rps->editSilabus($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/silabus/edit` */
$app->get('/matakuliah/:idMatkul/silabus/edit', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $matkul = new Matkul();
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    if (!empty($_SESSION['silabusID'])) {
        $silabusID = $_SESSION['silabusID'];
        unset($_SESSION['silabusID']);
    }
    if (is_null($silabus->silabusID)) {
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/new');
    } else {
        $app->render('pages/_silabus-new.twig', array(
            'currPath' => $currPath,
            'btnPath' => '/matakuliah/'. $idMatkul . '/silabus',
            'namaMatkul' => $namaMatkul,
            'silabus' => array(
                'idSilabus' => $silabus->silabusID,
                'pokokBahasan' => $silabus->pokokBahasan,
                'tujuan' => $silabus->tujuan,
                'pustaka' => $silabus->pustaka,
                'kompetensi' => $silabus->kompetensi
            )
        ));
    }
});

/** POST request on `/matakuliah/:idMatkul/silabus/edit` */
$app->post('/matakuliah/:idMatkul/silabus/edit', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $result = $silabus->saveOrEdit($idMatkul, $_POST['idSilabus'], $_POST['pokok-bahasan'], $_POST['tujuan']);
    if ($result) {
        $rps = new Rps();
        $rps->editSilabus($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/silabus/kompetensi` */
$app->get('/matakuliah/:idMatkul/silabus/kompetensi', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $kategori = new Kategori();
    $app->render('pages/_kompetensi.twig', array(
        'kategori' => $kategori->getAllKategori(),
        'currPath' => $currPath,
        'btnPath' => '/matakuliah/'. $idMatkul . '/silabus',
        'silabus' => array(
            'idSilabus' => $silabus->silabusID,
            'kompetensi' => $silabus->kompetensi
        )
    ));
});

/** POST request on `/matakuliah/:idMatkul/silabus/kompetensi` */
$app->post('/matakuliah/:idMatkul/silabus/kompetensi', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $result = $silabus->saveKompetensi($_POST['idSilabus'], $_POST['text'], $_POST['kategori']);
    if ($result) {
        $rps = new Rps();
        $rps->editSilabus($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/kompetensi');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/silabus/kompetensi/del/:idKompetensi` */
$app->get('/matakuliah/:idMatkul/silabus/kompetensi/del/:idKompetensi', $authenticate($app), $accessmatkul, function ($idMatkul, $idKompetensi) use ($app) {
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $result = $silabus->deleteKompetensi($idKompetensi);
    if ($result) {
        $rps = new Rps();
        $rps->editSilabus($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/kompetensi');
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/silabus/pustaka', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $currYear = date('Y');
    $app->render('pages/_pustaka.twig', array(
        'currPath' => $currPath,
        'silabus' => array(
            'idSilabus' => $silabus->silabusID,
            'pustaka' => $silabus->pustaka
        ),
        'currYear' => $currYear,
        'btnPath' => '/matakuliah/'. $idMatkul . '/silabus'
    ));
});

$app->post('/matakuliah/:idMatkul/silabus/pustaka', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $result = $silabus->saveKepustakaan($_POST['idSilabus'], $_POST['judul'], $_POST['tahun'], $_POST['penerbit'], $_POST['pengarang'], $_POST['edisi'], $_POST['tempat']);
    if ($result) {
        $rps = new Rps();
        $rps->editSilabus($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/pustaka');
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/silabus/pustaka/del/:idPustaka', $authenticate($app), $accessmatkul, function ($idMatkul, $idPustaka) use ($app) {
    $plotting = new Plotting();
    $currPlot = $plotting->getCurrentPlotting($idMatkul);
    $silabus = new Silabus($idMatkul, $currPlot);
    $result = $silabus->deleteKepustakaan($idPustaka);
    if ($result) {
        $rps = new Rps();
        $rps->editSilabus($idMatkul);
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/pustaka');
    } else {
        $app->stop();
    }
});
