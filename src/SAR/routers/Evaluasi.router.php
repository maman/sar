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
use SAR\models\Agenda;
use SAR\models\Kategori;

$app->get('/matakuliah/:idMatkul/evaluasi', $authenticate($app), $accessmatkul, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $kategori = new Kategori();
    $agenda = new Agenda();
    $allKategori = $kategori->getAllKategoriIndikator();
    $groupKategori = $kategori->groupKategoriIndikator();
    $countKategori = count($allKategori);
    $agendas = $agenda->getAgendaByMatkul($idMatkul, 'verbose');
    $app->render('pages/_evaluasi.twig', array(
        'currPath' => $currPath,
        'groupKategori' => $groupKategori,
        'countKategori' => $countKategori,
        'agendas' => $agendas
    ));
});
