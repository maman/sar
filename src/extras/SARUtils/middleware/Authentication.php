<?php

/**
 * SAR Authenticate Middleware
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

namespace SARUtils\middleware;

use Slim\Middleware;

class Authentication extends Middleware
{
    protected $settings = array(
        'login.url' => '/',
        'realm' => 'Protected Area',
    );

    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->settings, $config);
    }

    public function call()
    {
        $req = $this->app->request();
        $this->formAuth($req);
    }

    public function formAuth($req)
    {
        $app = $this->app;
        $config = $this->config;
        $this->app->hook('slim.before.dispatch', function () use ($app, $req, $config) {
            $secured_urls = isset($config['security.urls']) && is_array($config['security.urls']) ? $config['security.urls'] : array();
            foreach ($secured_urls as $url) {
                $patternAsRegex = $url['path'];
                if (substr($url['path'], -1) === '/') {
                    $patternAsRegex = $patternAsRegex . '?';
                }
                $patternAsRegex = '@^' . $patternAsRegex . '$@';
                if (preg_match($patternAsRegex, $req->getPathInfo())) {
                    if (!isset($_SESSION['nip']) && !isset($_SESSION['username']) && !isset($_SESSION['role']) && !isset($_SESSION['matkul'])) {
                        if ($req->getPath() !== $config['login.url']) {
                            $app->flash('error', 'Login Required');
                            $app->redirect('/?r=' . $req->getPath());
                            $app->redirect($config['login.url']);
                        }
                    }
                }
            }
        });
        $this->next->call();
    }
}
