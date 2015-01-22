<?php

/**
 * Utilities Class for SAR Application
 *
 * this file contains the helpers, utility, and misc function for SAR Application.
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @package SAR\helpers
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 */
namespace SAR\helpers;

use Functional as F;

/**
* Helpers Class
*
* @package SAR\models
*/
class Utilities
{
    public function getRangeTahunAjaran()
    {
        $tahun = array();
        $tahun['start'] = date('Y', strtotime('-1 year'));
        $tahun['current'] = date('Y');
        $tahun['end'] = date('Y', strtotime('+1 year'));
        return $tahun;
    }

    public function getTahunAjaran()
    {
        $tahun = date('Y');
        return $tahun;
    }

    public static function arrayDiff(array $a1, array $a2)
    {
        $result = array();
        foreach ($a1 as $item1) {
            $duplicate = array();
            foreach ($a2 as $item2) {
                if ($item1 === $item2) {
                    $duplicate[] = true;
                } else {
                    $duplicate[] = false;
                }
            }
            if (F\false($duplicate)) {
                $result[] = $item1;
            }
        }
        return $result;
    }
}
