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

use SAR\models\Approval;
use SAR\models\Rps;
use Functional as F;

$app->get('/arsip', function () use ($app) {
    $currPath = $app->request()->getPath();
    $approval = new Approval();
    $results = $approval->getAllApprovedMatkul(false);
    if (isset($_SESSION['nip'])) {
        if (isset($_GET['current'])) {
            if ($results) {
                $filtered = F\select($results, function ($item, $key, $val) {
                    return $item['NIP'] == $_SESSION['nip'];
                });
                $results = $filtered;
            }
            $app->render('pages/_arsip.twig', array(
                'currPath' => $currPath,
                'results' => $results,
                'isCurrent' => 'true'
            ));
        } else {
            $app->render('pages/_arsip.twig', array(
                'currPath' => $currPath,
                'results' => $results
            ));
        }
    } else {
        if (isset($_GET['current'])) {
            $app->render('pages/_404.twig');
        } else {
            $app->render('pages/_arsip.twig', array(
                'currPath' => $currPath,
                'results' => $results
            ));
        }
    }
});
