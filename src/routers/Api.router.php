<?php

/**
 * API router for SAR Application
 *
 * this file contains route definition and logic for `/api` route.
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

use SAR\helpers\Utilities;
use SAR\helpers\PDFRender;
use SAR\models\Matkul;
use SAR\models\Agenda;
use SAR\models\Rps;
use Functional as F;

/** `/api` Group */
$app->group('/api/v1', function () use ($app) {
    $app->group('/update', function () use ($app) {
        $app->get('/progress/:nip', function ($nip) use ($app) {
            $rps = new Rps();
            $sarTemp = $_SESSION['sar'];
            $matkulTemp = $_SESSION['matkul'];
            $nextSar = $rps->updateSarProgress($nip);
            $nextMatkul = $rps->updateMatkulProgress($nip);
            if ($sarTemp !== $nextSar) {
                $_SESSION['sar'] = $nextSar;
                $app->response()->header('Content-Type', 'application/json');
                if (count($nextSar) > count($sarTemp)) {
                    echo json_encode(array(
                        'status' => 'new',
                        'data' => Utilities::arrayDiff($nextSar, $sarTemp)
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'delete',
                        'data' => Utilities::arrayDiff($sarTemp, $nextSar)
                    ));
                }
            } else {
                if ($matkulTemp !== $nextMatkul) {
                    $_SESSION['matkul'] = $nextMatkul;
                    $app->response()->header('Content-Type', 'application/json');
                    if (count($nextMatkul) > count($matkulTemp)) {
                        echo json_encode(array(
                            'status' => 'new',
                            'data' => Utilities::arrayDiff($nextMatkul, $matkulTemp)
                        ));
                    } else {
                        echo json_encode(array(
                            'status' => 'delete',
                            'data' => Utilities::arrayDiff($matkulTemp, $nextMatkul)
                        ));
                    }
                } else {
                    $app->response->setStatus(304);
                }
            }
        });
    });
    /** '/agenda' API Endpoint Group */
    $app->group('/agenda', function () use ($app) {
        /** '/checkPercentage' */
        $app->get('/checkPercentage/:idMatkul', function ($idMatkul) use ($app) {
            $arg = '';
            $agenda = new Agenda();
            $percentage = $agenda->getAgendaPercentage($idMatkul);
            if (!$percentage) {
                $totalNow = 0;
            } else {
                $totalNow = end($percentage)['total'];
            }
            if (isset($_GET['bobot'])) {
                $arg = $_GET['bobot'];
            }
            $totalFuture = $totalNow + $arg;
            if ($totalFuture <= 100) {
                $app->response()->header('Content-Type', 'application/json');
                echo json_encode($percentage);
            } else {
                $app->response()->header('Content-Type', 'application/json');
                echo json_encode('Percentage too High');
                $app->response->setStatus(404);
            }
        });
    });
    /** '/generate' API Endpoint Group */
    $app->group('/generate', function () use ($app) {
        $app->get('/pdf/:idMatkul(/:year)', function ($idMatkul, $year = null) use ($app) {
            $renderer = new PDFRender();
            $matkul = new Matkul();
            $matkuls = $matkul->getMatkulDetails($idMatkul);
            $pdf = $renderer->renderPDFByMatkul($idMatkul, $year);
            if ($year === null) {
                $title = 'RPS Mata Kuliah ' . $matkuls[0]['NamaMK'] . ' ' . date('Y');
            } else {
                $title = 'RPS Mata Kuliah ' . $matkuls[0]['NamaMK'] . ' ' . $year;
            }
            $app->response->headers->set('Content-Type', 'application/pdf');
            $app->response->headers->set('Content-Transfer-Encoding', 'binary');
            if (isset($_GET['print'])) {
                $pdf->output($title . '.pdf', 'I');
            } else {
                $pdf->output($title . '.pdf', 'D');
            }
        });
        $app->get('/pdf', function () use ($app) {

        });
    });
    /** '/proxy?callback' API for JSONP Bridging */
    $app->get('/proxy', function () use ($app) {
        $callback = $_SERVER['QUERY_STRING'];
        if (!empty($callback)) {
            $jsonp_response = file_get_contents($callback);
            $app->response()->header('Content-Type', 'application/json');
            $app->response()->body($jsonp_response);
        } else {
            echo json_encode('Provide callback URL');
            $app->response->setStatus(400);
        }
    });
});
