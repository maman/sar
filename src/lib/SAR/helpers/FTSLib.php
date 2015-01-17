<?php

/**
 * SAR FTSLib Library
 *
 * This file contains the fulltextsearch related function for SAR application.
 * this library uses Solarium, which in turns uses Apache Solr to create indexes.
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

use Solarium\Client;

class FTSLib
{
    protected $settings = array(
        'endpoint' => array(
            'localhost' => array(
                'host' => '127.0.0.1',
                'port' => '8983',
                'path' => '/solr/'
            )
        )
    );

    protected $client;

    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->settings, $config);
        $this->client = new Client($this->config);
    }

    /**
     * Search documents
     * @param  string $query Query to perform on Solr
     */
    public function search(string $query)
    {

    }

    public function update()
    {

    }
}
