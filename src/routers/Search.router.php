<?php

/**
 * Search router for SAR Application
 *
 * this file contains route definition and logic for `/search` route.
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

$app->get('/search', function () use ($app) {
    if ($app->container->has('solr')) {
        $solr = $app->solr;
        $query = $solr->createSelect();
        $query->setQuery($_GET['q']);
        $resultSet = $solr->select($query);
        $highlight = $resultSet->getHighlighting();
        echo 'Found: ' . $resultSet->getNumFound();
        foreach ($resultSet as $document) {
            echo '<hr/><table>';
            // the documents are also iterable, to get all fields
            foreach ($document as $field => $value) {
                // this converts multivalue fields to a comma-separated string
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
                echo '<tr><th>' . $field . '</th><td>' . $value . '</td></tr>';
            }
            var_dump($document->namamatakuliah);
        }
    } else {
        $app->error();
    }
});
