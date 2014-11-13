<?php

/**
 * Agenda Class for SAR Application
 *
 * this file contains the data access for Agenda objects.
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @package SAR\models
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 */
namespace SAR\models;
use Slim\Slim;
use alfmel\OCI8\PDO as OCI8;

/**
 * Agenda Class
 * @package SAR\models
 */
class Agenda
{
    private $pertemuan;
    private $subKompetensi;
    private $materi;
    private $indikator;
    private $aktivitas;
    private $evaluasi;
    private $core;

    function __construct()
    {
        $this->core = Slim::getInstance();
    }

    public function __get($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
    }

    public function __set($prop, $val)
    {
        if (property_exists($this, $prop)) {
            $this->$prop = $val;
        }
        return $this;
    }

    public function getAgendaByMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            ''
        );
    }
}
