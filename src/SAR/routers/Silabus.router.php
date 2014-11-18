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

/** GET request on `/matakuliah/:idMatkul/silabus` */
$app->get('/matakuliah/:idMatkul/silabus', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $matkul = new Matkul();
    $silabus = new Silabus($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $new = true;
    if (!is_null($silabus->silabusID)) {
        $new = false;
    }
    $app->render('pages/_silabus.twig', array(
        'currPath' => $currPath,
        'isNew' => $new,
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

/** GET request on `/matakuliah/:idMatkul/silabus/new` */
$app->get('/matakuliah/:idMatkul/silabus/new', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $matkul = new Matkul();
    $silabus = new Silabus($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    if (!is_null($silabus->silabusID)) {
        $_SESSION['silabusID'] = $silabus->silabusID;
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/edit');
    } else {
        $app->render('pages/_silabus-new.twig', array(
            'currPath' => $currPath,
            'namaMatkul' => $namaMatkul
        ));
    }
});

/** POST request on `/matakuliah/:idMatkul/silabus/new` */
$app->post('/matakuliah/:idMatkul/silabus/new', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $silabus = new Silabus($idMatkul);
    $result = $silabus->saveOrEdit($idMatkul, '', $_POST['pokok-bahasan'], $_POST['tujuan']);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/silabus/edit` */
$app->get('/matakuliah/:idMatkul/silabus/edit', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $matkul = new Matkul();
    $silabus = new Silabus($idMatkul);
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
    $silabus = new Silabus($idMatkul);
    $result = $silabus->saveOrEdit($idMatkul, $_POST['idSilabus'], $_POST['pokok-bahasan'], $_POST['tujuan']);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/silabus/kompetensi` */
$app->get('/matakuliah/:idMatkul/silabus/kompetensi', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $silabus = new Silabus($idMatkul);
    $kategori = new Kategori();
    $app->render('pages/_kompetensi.twig', array(
        'kategori' => $kategori->getAllKategori(),
        'currPath' => $currPath,
        'silabus' => array(
            'idSilabus' => $silabus->silabusID,
            'kompetensi' => $silabus->kompetensi
        )
    ));
});

/** POST request on `/matakuliah/:idMatkul/silabus/kompetensi` */
$app->post('/matakuliah/:idMatkul/silabus/kompetensi', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $silabus = new Silabus($idMatkul);
    $result = $silabus->saveKompetensi($_POST['idSilabus'], $_POST['text'], $_POST['kategori']);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/kompetensi');
    } else {
        $app->stop();
    }
});

/** GET request on `/matakuliah/:idMatkul/silabus/kompetensi/del/:idKompetensi` */
$app->get('/matakuliah/:idMatkul/silabus/kompetensi/del/:idKompetensi', $authenticate($app), $accessmatkul, function ($idMatkul, $idKompetensi) use ($app) {
    $silabus = new Silabus($idMatkul);
    $result = $silabus->deleteKompetensi($idKompetensi);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/kompetensi');
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/silabus/pustaka', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $silabus = new Silabus($idMatkul);
    $app->render('pages/_pustaka.twig', array(
        'currPath' => $currPath,
        'silabus' => array(
            'idSilabus' => $silabus->silabusID,
            'pustaka' => $silabus->pustaka
        )
    ));
});

$app->post('/matakuliah/:idMatkul/silabus/pustaka', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $silabus = new Silabus($idMatkul);
    $result = $silabus->saveKepustakaan($_POST['idSilabus'], $_POST['judul'], $_POST['tahun'], $_POST['penerbit'], $_POST['pengarang']);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/pustaka');
    } else {
        $app->stop();
    }
});

$app->get('/matakuliah/:idMatkul/silabus/pustaka/del/:idPustaka', $authenticate($app), $accessmatkul, function ($idMatkul, $idPustaka) use ($app) {
    $silabus = new Silabus($idMatkul);
    $result = $silabus->deleteKepustakaan($idPustaka);
    if ($result) {
        $app->redirect('/matakuliah/'. $idMatkul .'/silabus/pustaka');
    } else {
        $app->stop();
    }
});
