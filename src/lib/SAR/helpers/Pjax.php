<?php

/**
 * SAR Pjax Library
 *
 * This file contains the PJAX related function for SAR application which
 * implemented as Slim Middleware. this library are depends on `jquery`
 * and `jquery-pjax` library.
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

namespace SAR\helpers;

use Slim\Middleware;

class Pjax extends Middleware
{

    public function call()
    {
        $app = $this->app;
        $req = $app->request();
        $res = $app->response();
        $app->hook('slim.after', function () use ($app, $req, $res) {
            $this->responseMarker($req, $res);
            $this->redirectRewriter($req, $res);
        });
        $app->view->appendData(array(
            'isPjax' => true
        ));
        $this->next->call();
    }

    public function detectRequest(\Slim\Http\Request $req)
    {
        if ($req->headers->get('X-PJAX') && ($req->params('_pjax'))) {
            return true;
        } else {
            return false;
        }
    }

    public function cleanUrl(string $Url)
    {
        $url = preg_replace("/\?_pjax=[^&]+&?/", "?", $url);
        $url = preg_replace("/_pjax=[^&]+&?/", "", $url);
        $url = preg_replace("/[\?&]$/", "", $url);
        return $url;
    }

    public function pjaxify(\Slim\Http\Request $req, string $url)
    {
        if (!$req->params('_pjax')) {
            return $url;
        }
        $pjaxContainer = urlencode($req->params('_pjax'));
        if (strpos($url, '_pjax=') !== false) {
            return $url;
        }
        if (strpos($url, '?') === false) {
            return $url . '?_pjax=' . $pjaxContainer;
        }
        return $url . '&_pjax=' . $pjaxContainer;
    }

    public function responseMarker(\Slim\Http\Request $req, \Slim\Http\Response $res)
    {
        if ($this->detectRequest($req) && !$res->headers->get('x-pjax-url')) {
            $res->headers->set('X-PJAX-URL', $this->cleanUrl($req->getResourceUri()));
        }
    }

    public function redirectRewriter(\Slim\Http\Request $req, \Slim\Http\Response $res)
    {
        if ($res->isRedirection() && $this->detectRequest($req)) {
            $location = $res->headers->get('Location');
            $location = $this->pjaxify($req, $location);
            $res->headers->set('Location', $location);
        }
    }
}
