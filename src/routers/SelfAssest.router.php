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
use SAR\models\SelfAssest;

$app->get('/sar/:idMatkul', $authenticate($app), $accessar, function ($idMatkul) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $sarDetails = $sar->getSARByAgenda($agendas[0]['ID_SUB_KOMPETENSI']);
    $app->render('pages/_self-assest.twig', array(
        'currPath' => $currPath,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'agendas' => $agendas,
        'sarDetails' => $sarDetails,
        'currList' => true
    ));
});

$app->get('/sar/:idMatkul/agenda', $authenticate($app), $accessar, function ($idMatkul) use ($app) {
    // $currPath = $app->request->getPath();
    // $matkul = new Matkul();
    // $agenda = new Agenda();
    // $sar = new SelfAssest();
    // $details = $matkul->getMatkulDetails($idMatkul)[0];
    // $namaMatkul = $details['NamaMK'];
    // $agendas = $agenda->getAgendaByMatkul($idMatkul);
    // $sarDetails = $sar->getSARByAgenda($agendas[0]['ID_SUB_KOMPETENSI']);
    // $app->render('pages/_self-assest.twig', array(
    //     'currPath' => $currPath,
    //     'idMatkul' => $idMatkul,
    //     'namaMatkul' => $namaMatkul,
    //     'agendas' => $agendas,
    //     'sars' => $sarDetails,
    //     'currList' => true
    // ));
    $app->redirect('/sar/' . $idMatkul);
});

$app->get('/sar/:idMatkul/agenda/:idAgenda', $authenticate($app), $accessar, function ($idMatkul, $idAgenda) use ($app) {
    $currPath = $app->request->getPath();
    $matkul = new Matkul();
    $agenda = new Agenda();
    $sar = new SelfAssest();
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $sarDetails = $sar->getSARByAgenda($idAgenda);
    $app->render('pages/_self-assest.twig', array(
        'currPath' => $currPath,
        'idAgenda' => $idAgenda,
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'agendas' => $agendas,
        'sars' => $sarDetails,
        'currAgenda' => $idAgenda
    ));
});
