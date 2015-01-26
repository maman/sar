<?php

/**
 * SAR Exporter Library
 *
 * This file contains the Export - related function for SAR application which
 * implemented as Abstract Class. this library exist to enable multi-export plugin
 * feature for SAR application.
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

abstract class Exporter
{
    abstract protected function export($idMatkul, $page = null, $year = null);
}
