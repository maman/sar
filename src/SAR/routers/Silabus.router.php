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
use SAR\models\Silabus;
use SAR\models\Kategori;

/** GET request on `/matakuliah/:idMatkul/silabus` */
$app->get('/matakuliah/:idMatkul/silabus', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $kategori = new Kategori();
    $app->render('pages/_silabus.twig', array(
        'currPath' => $currPath
    ));
});

$app->get('/matakuliah/:idMatkul/silabus/new', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $kategori = new Kategori();
    $app->render('pages/_silabus-new.twig', array(
        'currPath' => $currPath
    ));
});

$app->get('/matakuliah/:idMatkul/kompetensi', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $kategori = new Kategori();
    $app->render('pages/_kompetensi.twig', array(
        'kategori' => $kategori->getAllKategori(),
        'currPath' => $currPath
    ));
});

$app->get('/matakuliah/:idMatkul/pustaka', $authenticate($app), function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $kategori = new Kategori();
    $app->render('pages/_pustaka.twig', array(
        'kategori' => $kategori->getAllKategori(),
        'currPath' => $currPath
    ));
});

/** POST request on `/matakuliah/:idMatkul/silabus` */
// $app->post('/matakuliah/:idMatkul/silabus', function ($idMatkul) use ($app) {
//     $silabus = new Silabus();
//     if (!empty($_POST)) {
//         $pokokBahasan = $_POST['pokok-bahasan'];
//         $tujuan = $_POST['tujuan'];
//         $kompetensi = $_POST['kompetensi'];
//         // echo('$silabus->save('.$pokokBahasan.', '.$idMatkul.', '.$tujuan.')<br>');
//         foreach ($kompetensi as $key => $item) {
//             // echo('$kompetensi->save(idsilabus'.$kompetensi[$key]['text'].')<br>');
//         }
//         $app->response()->header('Content-Type', 'application/json');
//         // echo(json_encode($_POST));
//     } else {
//         echo 'ERROR';
//     }
// });

// $app->get('/matakuliah/:idMatkul/silabus/edit', $authenticate($app), function ($idMatkul) use ($app) {
//     $currPath = $app->request()->getPath();
//     $silabus = new Silabus();
//     $result = $silabus->init($idMatkul);
//     $app->render('pages/_silabus.twig', array(
//         'warning' => true,
//         'tujuan' => $silabus->tujuan,
//         'kompetensi' => $silabus->kompetensi,
//         'pokokBahasan' => $silabus->pokokBahasan,
//         'pustaka' => $silabus->pustaka,
//         'currPath' => $currPath
//     ));
// });

// $app->post('/matakuliah/:idMatkul/silabus/edit', function ($idMatkul) use ($app) {
//     $app->response()->header('Content-Type', 'application/json');
//     echo json_encode($_POST);
// });
