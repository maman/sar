<?php

/**
 * Login router for SAR Application
 *
 * this file contains route definition and logic for `/login` and `/logout` route.
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
use SAR\models\Login;

/** GET request on `/login` */
$app->get('/login', function () use ($app) {
    $errors = '';
    $urlRedirect = '/';
    $user_val = $user_error = $pass_error = '';
    $flash = $app->view()->getData('flash');
    if ($app->request->get('r') && $app->request->get('r') != '/logout' && $app->request->get('r') != '/login') {
        $_SESSION['urlRedirect'] = $app->request->get('r');
    }
    if (isset($_SESSION['username'])) {
        $app->redirect('/');
    } else {
        if (isset($_SESSION['urlRedirect'])) {
            $urlRedirect = $_SESSION['urlRedirect'];
        }
        if (isset($flash['errors'])) {
            $errors = $flash['errors'];
        }
        $app->render('pages/_login.twig', array(
            'errors' => $errors,
            'urlRedirect' => $urlRedirect
        ));
    }
});

/** POST request on `/login` */
$app->post('/login', function () use ($app) {
    $req = $app->request();
    $username = $req->post('username');
    $password = $req->post('password');
    $auth = new Login();
    if ($username != "" && $password != "") {
        if ($auth->authenticate($username, $password)) {
            $_SESSION['nip'] = $auth->nip;
            $_SESSION['username'] = $auth->username;
            $_SESSION['role'] = $auth->role;
            $matkul = $auth->getMatkul($username);
            if (count($matkul) >= 1) {
                $_SESSION['matkul'] = $matkul;
                if (isset($_SESSION['urlRedirect'])) {
                    $tmp = $_SESSION['urlRedirect'];
                    unset($_SESSION['urlRedirect']);
                    $app->redirect($tmp);
                } else {
                    $app->log->info("LOGIN SUCCESS: " . $username . ":" . sha1($password) . " from " . $req->getIp());
                    $app->redirect('/');
                }
            } else {
                $app->flash('errors', "Not Yet Plotted");
                $app->log->notice("NOT PLOTTED: " . $username . ":" . $password . " from " . $req->getIp());
                $app->redirect('/login');
            }
        } else {
            $app->flash('errors', "Authentication Error");
            $app->log->notice("LOGIN ATTEMPT: " . $username . ":" . $password . " from " . $req->getIp());
            $app->redirect('/login');
        }
    }
    $app->flash('errors', "Username or Password cannot be empty");
    $app->redirect('/login');
});

/** GET request on `/logout` */
$app->get('/logout', $authenticate($app), function () use ($app) {
    $req = $app->request();
    unset($_SESSION['nip']);
    unset($_SESSION['username']);
    unset($_SESSION['role']);
    unset($_SESSION['matkul']);
    $app->view()->setData('username', null);
    $app->redirect('/');
});
