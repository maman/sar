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
use SAR\models\Rps;
use SAR\models\Prodi;

/** POST request on `/login` */
$app->post('/login', function () use ($app) {
    $req = $app->request();
    $username = $req->post('username');
    $password = $req->post('password');
    $auth = new Login();
    $rps = new Rps();
    if ($username != "" && $password != "") {
        if ($auth->authenticate($username, $password)) {
            $_SESSION['nip'] = $auth->nip;
            $_SESSION['username'] = $auth->username;
            $_SESSION['role'] = $auth->role;
            $_SESSION['prodi'] = $auth->prodi;
            $rps->updateProgress($auth->nip);
            $matkulCount = count($_SESSION['matkul']);
            $sarCount = count($_SESSION['sar']);
            $app->log->notice($sarCount);
            if ($matkulCount < 1 && $sarCount < 1) {
                $app->flash('errors', "Pengguna belum dikenakan mata kuliah. Silahkan menghubungi kepala program studi untuk melakukan plotting ulang");
                $app->log->notice("NOT PLOTTED: " . $_SESSION['username'] . " from " . $req->getIp());
                unset($_SESSION['nip']);
                unset($_SESSION['username']);
                unset($_SESSION['role']);
                unset($_SESSION['matkul']);
                unset($_SESSION['sar']);
                unset($_SESSION['prodi']);
                $app->redirect('/');
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
            $app->flash('errors', "Autentifikasi Pengguna Gagal");
            $app->flash('username', $username);
            $app->log->notice("LOGIN ATTEMPT: " . $username . ":" . $password . " from " . $req->getIp());
            $app->redirect('/');
        }
    } else {
        $app->flash('errors', "Username atau Password tidak boleh kosong");
        $app->redirect('/');
    }
});

/** GET request on `/logout` */
$app->get('/logout', $authenticate($app), function () use ($app) {
    $req = $app->request();
    unset($_SESSION['nip']);
    unset($_SESSION['username']);
    unset($_SESSION['role']);
    unset($_SESSION['matkul']);
    unset($_SESSION['sar']);
    unset($_SESSION['prodi']);
    session_destroy();
    $app->view()->setData(array(
        'nip' => null,
        'username' => null,
        'role' => null,
        'matkuls' => null,
        'sar' => null,
        'prodi' => null,
    ));
    $app->response->header('Last-Modified', gmdate("D, d M Y H:i:s")) . ' GMT';
    $app->response->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    $app->response->header('Cache-Control', 'post-check=0, pre-check=0, false');
    $app->response->header('Pragma', 'no-cache');
    $app->response->header('Expires', '0');
    $app->redirect('/');
});
