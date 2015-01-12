<?php

/**
 * Error router for SAR Application
 *
 * this file contains route definition and logic for HTTP Error 500 and 404.
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

/** HTTP 404 */
$app->notFound(function () use ($app) {
    $req = $app->request();
    $app->log->error("404: " . $req->getUrl() . $req->getPath());
    $app->render('pages/_404.twig', array(
        'url' => $req->getUrl() . $req->getPath()
    ));
});

/** HTTP 500 */
$app->error(function (\Exception $e) use ($app) {
    if ($c['config']['app.environment'] === 'development') {
        echo($e->getMessage());
        debug_print_backtrace();
    } else {
        $req = $app->request();
        $app->log->error("500: " . $req->getUrl() . $req->getPath() . " - StackTrace: [" . $e->getMessage . "] - SessionDump: [" . printf($_SESSION) . "]");
        $app->render('pages/_500.twig', array(
            'url' => $req->getUrl() . $req->getPath()
        ));
    }
});
