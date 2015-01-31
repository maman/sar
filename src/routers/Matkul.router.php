<?php

/**
 * Matkul router for SAR Application
 *
 * this file contains route definition and logic for `/matakuliah/:idmatakuliah` route.
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
use SAR\models\Rps;
use SAR\models\User;
use Functional as F;

$app->get('/matakuliah', $authenticate($app), function () use ($app) {
    $currPath = $app->request()->getPath();
    $rps = new Rps();
    $user = new User();
    $boss = $user->getKaprodi($_SESSION['prodi']);
    if (isset($_GET['filter'])) {
        if ($_GET['filter'] == 'active') {
            $results = F\select($_SESSION['matkul'], function ($item, $key, $col) {
                return $item['approved'] == 'never';
            });
        } elseif ($_GET['filter'] == 'wait') {
            $results = F\select($_SESSION['matkul'], function ($item, $key, $col) {
                return $item['approved'] == 'wait';
            });
        } elseif ($_GET['filter'] == 'approved') {
            $results = F\select($_SESSION['matkul'], function ($item, $key, $col) {
                return $item['approved'] == 'approved';
            });
        } elseif ($_GET['filter'] == 'none') {
            $results = $_SESSION['matkul'];
        }
        $app->render('pages/_matakuliah.twig', array(
            'filtered' => true,
            'selected' => $_GET['filter'],
            'currPath' => $currPath,
            'boss' => $boss,
            'results' => $results
        ));
    } else {
        $app->render('pages/_matakuliah.twig', array(
            'boss' => $boss,
            'currPath' => $currPath
        ));
    }
});

/** GET request on `/matakuliah/:idmatakuliah` */
$app->get('/matakuliah/:idMatkul', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $matkul = new Matkul();
    $rps = new Rps();
    $user = new User();
    $rps->getRpsByIdMatkul($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $boss = $user->getKaprodi($_SESSION['prodi']);
    // $tahunMatkul = $details['TahunAjaranMK'];
    $progress = $rps->getRpsProgress($idMatkul);
    $app->render('pages/_matakuliah.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'boss' => $boss,
        // 'tahunMatkul' => $tahunMatkul,
        'progress' => $progress,
        'currPath' => $currPath
    ));
});

$app->get('/matakuliah/:idMatkul/submit', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $rps = new Rps();
    $rps->submitRPS($idMatkul, $_SESSION['nip']);
    $rps->updateProgress($_SESSION['nip']);
    $app->redirect('/matakuliah?filter=wait');
});
