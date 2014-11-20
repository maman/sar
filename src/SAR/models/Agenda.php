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
use SAR\models\Silabus;

/**
 * Agenda Class
 *
 * To initialize, provide a `idMatkul` variable.
 * @package SAR\models
 */
class Agenda
{
    private $agendaID;
    private $pertemuan;
    private $subKompetensi;
    private $materi;
    private $bobot;
    private $core;

    function __construct($idMatkul)
    {
        $this->core = Slim::getInstance();
        $silabus = new Silabus($idMatkul);
        $silabusId = $silabus->silabusID;
        $agenda = $this->getAgendaBySilabus($silabusId);
        if ($agenda) {
            foreach ($agenda as $result) {
                $this->agendaID = $result['ID_SUB_KOMPETENSI'];
                $this->subKompetensi = $result['TEXT_SUB_KOMPETENSI'];
                $this->materi = $result['TEXT_MATERI_BELAJAR'];
                $this->pertemuan = $result['RANGE_PERTEMUAN'];
                $this->bobot = $result['BOBOT'];
                $this->indikator = $this->getIndikatorByAgendaID($result['ID_SUB_KOMPETENSI']);
            }
        }
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

    /**
     * Get Agenda for the provided Silabus ID
     * @param  string $idSilabus
     * @return mixed
     */
    private function getAgendaBySilabus($idSilabus)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                AGENDA
            WHERE
                ID_SILABUS = :idSilabus'
        );
        $query->bindParam(':idSilabus', $idSilabus);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    private function getIndikatorByAgendaID($idAgenda)
    {
        # TODO -> return indikator array or false.
    }

    private function getAktivitasByAgendaID($idAgenda)
    {
        # TODO -> return aktivitas array or false.
    }
}
