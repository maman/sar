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
use Functional as F;

/**
 * Agenda Class
 *
 * To initialize, provide a `idMatkul` variable.
 * @package SAR\models
 */
class Agenda
{
    private $core;

    public function __construct()
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
     * Divide Assesmen to `tes` & `nontes`
     * @param  string $idAgenda
     * @return array
     */
    private function divideAssesmen($idAgenda)
    {
        $asesmen = $this->getAsesmenByAgendaID($idAgenda);
        if ($asesmen) {
            $returns = F\partition($asesmen, function ($task) {
                return $task['JENIS_ASSESMENT'] == '0';
            });
            $result = array(
                'tes' => $returns[1],
                'nontes' => $returns[0]
            );
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Get Detail Agenda from provided $idMatkul
     * @param  string $idMatkul
     * @return mixed
     */
    private function getDetailAgendaByMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT
                AGENDA.ID_SUB_KOMPETENSI,
                AGENDA.ID_SILABUS,
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
                MATAKULIAH."KDMataKuliah" = :idMatkul
            ORDER BY
                AGENDA.ID_SUB_KOMPETENSI ASC'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get All Agenda object for the provided $idMatkul
     * @param  string $idMatkul
     * @return mixed
     */
    public function getAgendaByMatkul($idMatkul)
    {
        $results = $this->getDetailAgendaByMatkul($idMatkul);
        if ($results) {
            foreach ($results as $value => $agenda) {
                $results[$value]['INDIKATOR'] = $this->getIndikatorByAgendaID($results[$value]['ID_SUB_KOMPETENSI']);
                $results[$value]['AKTIVITAS'] = $this->getAktivitasByAgendaID($results[$value]['ID_SUB_KOMPETENSI']);
                $results[$value]['ASESMEN'] = $this->divideAssesmen($results[$value]['ID_SUB_KOMPETENSI']);
            }
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Detail Agenda from the provided $idAgenda
     * @param  string $idAgenda
     * @return mixed
     */
    public function getDetailAgenda($idAgenda)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                AGENDA
            WHERE
                AGENDA."ID_SUB_KOMPETENSI" = :idAgenda'
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
     * Save or Edit Agenda entries.
     * @param  string $idSilabus
     * @param  string $idAgenda
     * @param  string $rangePertemuan
     * @param  string $bobot
     * @param  string $txtSubKompetensi
     * @param  string $txtMateriBelajar
     * @return boolean
     */
    public function saveOrEdit($idSilabus, $idAgenda, $rangePertemuan, $bobot, $txtSubKompetensi, $txtMateriBelajar)
    {
        if ($idAgenda == '') {
            try {
                $query = $this->core->db->prepare(
                    'INSERT INTO
                        AGENDA
                    (
                        ID_SILABUS,
                        TEXT_SUB_KOMPETENSI,
                        TEXT_MATERI_BELAJAR,
                        RANGE_PERTEMUAN,
                        BOBOT
                    )
                    VALUES
                    (
                        :idSilabus,
                        :txtSubKompetensi,
                        :txtMateriBelajar,
                        :rangePertemuan,
                        :bobot
                    )'
                );
                $query->bindParam(':idSilabus', $idSilabus);
                $query->bindParam(':txtSubKompetensi', $txtSubKompetensi);
                $query->bindParam(':txtMateriBelajar', $txtMateriBelajar);
                $query->bindParam(':rangePertemuan', $rangePertemuan);
                $query->bindParam(':bobot', $bobot);
                $query->execute();
                return true;
            } catch (PDOExcetion $e) {
                return false;
            }
        } else {
            try {
                $query = $this->core->db->prepare(
                    'UPDATE
                        AGENDA
                    SET
                        TEXT_SUB_KOMPETENSI = :txtSubKompetensi,
                        TEXT_MATERI_BELAJAR = :txtMateriBelajar,
                        RANGE_PERTEMUAN = :rangePertemuan,
                        BOBOT = :bobot
                    WHERE
                        ID_SUB_KOMPETENSI = :idAgenda'
                );
                $query->bindParam(':idAgenda', $idAgenda);
                $query->bindParam(':txtSubKompetensi', $txtSubKompetensi);
                $query->bindParam(':txtMateriBelajar', $txtMateriBelajar);
                $query->bindParam(':rangePertemuan', $rangePertemuan);
                $query->bindParam(':bobot', $bobot);
                $query->execute();
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

    /**
     * Get all Kategori Indikators
     * @return mixed
     */
    public function getKategoriIndikator()
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                KATEGORI_INDIKATOR_AGENDA'
        );
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Indikator for the provided Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    public function getIndikatorByAgendaID($idAgenda)
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
     * Save or Edit Indikator
     * @param  string $idAgenda
     * @param  string $idIndikator
     * @param  string $txtIndikator
     * @param  string $idKeterangan
     * @return boolean
     */
    public function saveIndikator($idAgenda, $txtIndikator, $idKeterangan)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    INDIKATOR_AGENDA
                (
                    TEXT_INDIKATOR,
                    ID_KETERANGAN,
                    ID_SUB_KOMPETENSI
                )
                VALUES
                (
                    :txtIndikator,
                    :idKeterangan,
                    :idAgenda
                )'
            );
            $query->bindParam(':txtIndikator', $txtIndikator);
            $query->bindParam(':idKeterangan', $idKeterangan);
            $query->bindParam(':idAgenda', $idAgenda);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get Aktivitas for the provided Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    public function getAktivitasByAgendaID($idAgenda)
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
    public function getAsesmenByAgendaID($idAgenda)
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
}
