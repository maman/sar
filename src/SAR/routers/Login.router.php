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
use SAR\models\Matkul;
use SAR\models\Rps;

/** GET request on `/login` */
$app->get('/login', function () use ($app) {
    $errors = '';
    $urlRedirect = '/';
    $user_val = $user_error = $pass_error = '';
    $flash = $app->view()->getData('flash');
    if ($app->request->get('r') && $app->request->get('r') != '/logout' && $app->request->get('r') != '/login') {
        $urlRedirect = $app->request->get('r');
        $_SESSION['urlRedirect'] = $urlRedirect;
    }
    if (isset($_SESSION['username']) & !isset($flash['errors'])) {
        $app->redirect('/');
    } else {
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
    $user = new Matkul();
    $rps = new Rps();
    if ($username != "" && $password != "") {
        if ($auth->authenticate($username, $password)) {
            $_SESSION['nip'] = $auth->nip;
            $_SESSION['username'] = $auth->username;
            $_SESSION['role'] = $auth->role;
            $matkul = $user->getMatkulByNIP($username);
            if (count($matkul) > 1) {
                foreach($matkul as $key => $matkul_loop) {
                    if (!$rps->getRpsProgress($matkul_loop['KDMataKuliah'])) {
                        $rps->createRpsForMatkul($matkul_loop['KDMataKuliah']);
                    }
                    $matkul[$key]['percentage'] = $rps->getRpsProgress($matkul_loop['KDMataKuliah'])['percentage'];
                }
                $_SESSION['matkul'] = $matkul;
            } else if (count($matkul) == 1) {
                if (!$rps->getRpsProgress($matkul[0]['KDMataKuliah'])) {
                    $rps->createRpsForMatkul($matkul[0]['KDMataKuliah']);
                }
                $matkul[0]['percentage'] = $rps->getRpsProgress($matkul[0]['KDMataKuliah'])['percentage'];
                $_SESSION['matkul'] = $matkul[0];
            }
            if (isset($_SESSION['urlRedirect'])) {
                $tmp = $_SESSION['urlRedirect'];
                unset($_SESSION['urlRedirect']);
                $app->redirect($tmp);
            } else {
                $app->log->info("LOGIN SUCCESS: " . $username . ":" . sha1($password) . " from " . $req->getIp());
                $app->redirect('/');
            }
        } else {
            $app->flash('errors', "Authentication Error");
            $_SESSION['username'] = $req->post('username');
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
    $app->view()->setData(array(
        'nip' => null,
        'username' => null,
        'role' => null,
        'matkuls' => null
    ));
    $app->redirect('/');
});
