<?php

/**
 * SAR Authenticate Middleware ---- MOVED TO SARUtils\extra\Authentication;
 *
 * This file contains the authentication middleware for Slim Framework.
 * this middleware ensures that the client has logged in and the required
 * previlleges to access the requested resources.
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
 * @link https://github.com/maman/sar
 */

/**
 * Slim authenticate Route Middleware
 */
$authenticate = function ($app) {
    return function () use ($app) {
        // $currPath = $app->request()->getPath();
        // if (!isset($_SESSION['username'])) {
        //     $app->flash('error', 'Login Required');
        //     $app->redirect('/?r=' . $currPath);
        // }
    };
};
