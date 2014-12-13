<?php

/**
 * Approval router for SAR Application
 *
 * this file contains route definition and logic for `/approval` route.
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
use SAR\models\Approval;

$app->get('/approval', $authenticate($app), $kaprodi, function () use ($app) {
  $currPath = $app->request()->getPath();
  $approval = new Approval();
  $rps = new Rps();
  $user = new User();
  $matkul = new Matkul();
  $results = $approval->getAllApproval();
  foreach ($results as $num => $result) {
    $rps->getRpsByIdMatkul($results[$num]['KDMataKuliah']);
    $results[$num]['Versi'] = $rps->versi;
    $results[$num]['NamaDosen'] = $user->getUser($results[$num]['NIP'])[0]['NAMA'];
    $results[$num]['NamaMatkul'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['NamaMK'];
    $results[$num]['Semester'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['SemesterMK'];
    $results[$num]['Tahun'] = $matkul->getMatkulDetails($results[$num]['KDMataKuliah'])[0]['TahunAjaranMK'];
  }
  $app->render('pages/_approval.twig', array(
    'currPath' => $currPath,
    'results' => $results
  ));
});
