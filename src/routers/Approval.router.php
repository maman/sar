<?php

/**
 * Approval router for SAR Application
 *
 * this file contains route definition and logic for `/approval` & `/review` route.
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
use SAR\models\Rps;
use SAR\models\Silabus;
use SAR\models\Agenda;
use SAR\models\Task;
use SAR\models\Approval;
use Functional as F;

$app->get('/approval', $authenticate($app), $kaprodi, function () use ($app) {
  $currPath = $app->request()->getPath();
  $approval = new Approval();
  $user = new User();
  $matkul = new Matkul();
  $rps = new Rps();
  $results = $approval->getAllApproval();
  foreach ($results as $num => $result) {
    $rps->getRpsByIdMatkul($results[$num]['KDMataKuliah']);
    $results[$num]['Versi'] = $rps->versi;
    $results[$num]['NamaDosen'] = $user->getUser($results[$num]['NIP'])[0]['NAMA'];
    $results[$num]['NamaMatkul'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['NamaMK'];
    $results[$num]['Semester'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['SemesterMK'];
    $results[$num]['Tahun'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['TahunAjaranMK'];
  }
  if (isset($_GET['filter'])) {
      if ($_GET['filter'] == 'pending') {
          $results = F\select($results, function($item, $key, $col) {
              return $item['Approval'] == '0';
          });
      } elseif ($_GET['filter'] == 'approved') {
          $results = F\select($results, function($item, $key, $col) {
              return $item['Approval'] == '2';
          });
      } elseif ($_GET['filter'] == 'none') {
          $results = $results;
      }
      $app->render('pages/_approval.twig', array(
          'filtered' => true,
          'selected' => $_GET['filter'],
          'currPath' => $currPath,
          'results' => $results
      ));
  } else {
    $app->render('pages/_approval.twig', array(
      'currPath' => $currPath,
      'results' => $results
    ));
  }
});

$app->get('/approval/:idApproval/approve', $authenticate($app), $kaprodi, function () use ($app) {

});

$app->get('/review', $kaprodi, function () use ($app) {
    $app->redirect('/approval');
});

$app->get('/review/:idMatkul', $authenticate($app), $kaprodi, function ($idMatkul) use ($app) {
    $currPath = $app->request()->getPath();
    $matkul = new Matkul();
    $rps = new Rps();
    $silabus = new Silabus($idMatkul);
    $agenda = new Agenda();
    $task = new Task();
    $rps->getRpsByIdMatkul($idMatkul);
    $details = $matkul->getMatkulDetails($idMatkul)[0];
    $namaMatkul = $details['NamaMK'];
    $semesterMatkul = $details['SemesterMK'];
    $tahunMatkul = $details['TahunAjaranMK'];
    $agendas = $agenda->getAgendaByMatkul($idMatkul);
    $tasks = $task->getDetailAktivitasByMatkul($idMatkul);
    $app->render('pages/_review.twig', array(
        'idMatkul' => $idMatkul,
        'namaMatkul' => $namaMatkul,
        'semesterMatkul' => $semesterMatkul,
        'tahunMatkul' => $tahunMatkul,
        'silabus' => array(
            'idSilabus' => $silabus->silabusID,
            'pokokBahasan' => $silabus->pokokBahasan,
            'tujuan' => $silabus->tujuan,
            'pustaka' => $silabus->pustaka,
            'kompetensi' => $silabus->kompetensi
        ),
        'agendas' => $agendas,
        'tasks' => $tasks,
        'currPath' => $currPath
    ));
});
