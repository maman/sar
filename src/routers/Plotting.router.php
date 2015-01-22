<?php

/**
 * Arsip router for SAR Application
 *
 * this file contains route definition and logic for `/arsip` route.
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
use SAR\models\Prodi;
use SAR\models\Plotting;
use Functional as F;

$app->get('/plotting', $kaprodi, function () use ($app) {
    $groupPlots = array();
    $currDosen = array();
    $currPath = $app->request->getPath();
    $prodi = new Prodi();
    $plotting = new Plotting();
    $user = new User();
    $matkul = new Matkul();
    $idProdi = $_SESSION['prodi'];
    $namaProdi = $prodi->getProdiDetails($idProdi)['NamaProdi'];
    $plottings = $plotting->getAllPlotting($idProdi);
    $currDosen = $plotting->getUnplottedDosen($idProdi);
    if ($currDosen) {
        foreach ($currDosen as $keyDosen => $valDosen) {
            $currDosen[$keyDosen]['Nama'] = $user->getUserName($currDosen[$keyDosen]['NIP']);
        }
    }
    if ($plottings) {
        foreach ($plottings as $plotKey => $plotVal) {
            $plottings[$plotKey]['Nama'] = $user->getUserName($plottings[$plotKey]['NIP']);
            $plottings[$plotKey]['Matakuliah'] = $matkul->getMatkulName($plottings[$plotKey]['KDMataKuliah']);
        }
        $groupPlots = F\group($plottings, function ($plot) {
            return $plot['Nama'];
        });
    }
    $app->render('pages/_plotting.twig', array(
        'currPath' => $currPath,
        'namaProdi' => $namaProdi,
        'plottings' => $groupPlots,
        'dosens' => $currDosen
    ));
});

$app->get('/plotting/new', $kaprodi, function () use ($app) {
    $nip = $_GET['id'];
    $idProdi = $_SESSION['prodi'];
    $plotting = new Plotting();
    $user = new User();
    $matkul = new Matkul();
    $currMatkul = array();
    $currPath = $app->request->getPath();
    $currMatkul = $plotting->getUnplottedMatkul($idProdi);
    if ($currMatkul) {
        foreach ($currMatkul as $keyMatkul => $valMatkul) {
            $currMatkul[$keyMatkul]['Nama'] = $matkul->getMatkulName($currMatkul[$keyMatkul]['KDMataKuliah'], false);
        }
    }
    $currDosen = $user->getUserName($nip);
    $app->render('pages/_plotting-new.twig', array(
        'currPath' => $currPath,
        'currMatkul' => $currMatkul,
        'currDosen' => $currDosen,
        'currNip' => $_GET['id'],
        'nip' => $nip,
        'isNew' => true
    ));
});

$app->post('/plotting/new', $kaprodi, function () use ($app) {
    $plotting = new Plotting();
    $result = $plotting->savePlotting($_POST['nip'], $_POST['matkul']);
    if ($result) {
        $app->redirect('/plotting/edit?id=' . $_POST['nip']);
    } else {
        $app->stop();
    }
});

$app->get('/plotting/edit', $kaprodi, function () use ($app) {
    $nip = $_GET['id'];
    $idProdi = $_SESSION['prodi'];
    $plotting = new Plotting();
    $user = new User();
    $matkul = new Matkul();
    $currMatkul = array();
    $collection = array();
    $currPath = $app->request->getPath();
    $currMatkul = $plotting->getUnplottedMatkul($idProdi);
    if ($currMatkul) {
        foreach ($currMatkul as $keyMatkul => $valMatkul) {
            $currMatkul[$keyMatkul]['Nama'] = $matkul->getMatkulName($currMatkul[$keyMatkul]['KDMataKuliah']);
        }
    }
    $collection = $plotting->getDosenMatkul($nip, $idProdi);
    if ($collection) {
        foreach ($collection as $keyCol => $valCol) {
            $collection[$keyCol]['Nama'] = $matkul->getMatkulName($collection[$keyCol]['KDMataKuliah']);
        }
    }
    $currDosen = $user->getUserName($nip);
    $app->render('pages/_plotting-new.twig', array(
        'currPath' => $currPath,
        'currMatkul' => $currMatkul,
        'currDosen' => $currDosen,
        'collection' => $collection,
        'currNip' => $nip
    ));
});

$app->get('/plotting/del', $kaprodi, function () use ($app) {
    $id = $_GET['id'];
    $plotting = new Plotting();
    $result = $plotting->deletePlotting($id);
    if ($result) {
        $app->redirect('/plotting/edit?id=' . $_GET['nip']);
    } else {
        $app->stop();
    }
});
