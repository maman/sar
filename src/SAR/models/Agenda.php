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

    /**
     * Get Indikator for the provided Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    private function getIndikatorByAgendaID($idAgenda)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                INDIKATOR_AGENDA
            WHERE
                ID_SUB_KOMPETENSI = :idAgenda'
        );
        $query->bindParam(':idAgenda', $idAgenda);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Aktivitas for the provided Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    private function getAktivitasByAgendaID($idAgenda)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                AKTIVITAS_AGENDA
            WHERE
                ID_SUB_KOMPETENSI = :idAgenda'
        );
        $query->bindParam(':idAgenda', $idAgenda);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Asesmen for the provided Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    private function getAsesmenByAgendaID($idAgenda)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                ASESMEN_AGENDA
            WHERE
                ID_SUB_KOMPETENSI = :idAgenda'
        );
        $query->bindParam(':idAgenda', $idAgenda);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    public function getAgendaByMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT
                AGENDA.ID_SUB_KOMPETENSI,
                AGENDA.TEXT_SUB_KOMPETENSI,
                AGENDA.TEXT_MATERI_BELAJAR,
                AGENDA.RANGE_PERTEMUAN,
                AGENDA.BOBOT
            FROM
                MATAKULIAH INNER JOIN SILABUS
            ON
                MATAKULIAH."KDMataKuliah" = SILABUS.ID_MATAKULIAH
                INNER JOIN AGENDA
            ON
                SILABUS.ID_SILABUS = AGENDA.ID_SILABUS
            WHERE
                MATAKULIAH."KDMataKuliah" = :idMatkul'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $value => $agenda) {
                $results[$value]['INDIKATOR'] = $this->getIndikatorByAgendaID($results[$value]['ID_SUB_KOMPETENSI']);
                $results[$value]['AKTIVITAS'] = $this->getAktivitasByAgendaID($results[$value]['ID_SUB_KOMPETENSI']);
                $results[$value]['ASESMEN'] = $this->getAsesmenByAgendaID($results[$value]['ID_SUB_KOMPETENSI']);
            }
            return $results;
        } else {
            return false;
        }
    }
}
