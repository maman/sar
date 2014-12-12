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
use SAR\models\Kategori;
use SAR\models\Task;
use Functional as F;

/**
 * Agenda Class
 *
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
    public function getAgendaByMatkul($idMatkul, $mode = null)
    {
        $kategori = new Kategori();
        $results = $this->getDetailAgendaByMatkul($idMatkul);
        if ($results) {
            foreach ($results as $value => $agenda) {
                if (is_null($mode)) {
                    $results[$value]['UNIQUE_INDIKATOR'] = $kategori->getAgendaKategoriByAgendaId($results[$value]['ID_SUB_KOMPETENSI']);
                } else {
                    $results[$value]['UNIQUE_INDIKATOR'] = $kategori->getAgendaKategoriByAgendaIdVerbose($results[$value]['ID_SUB_KOMPETENSI']);
                    $results[$value]['MODE'] = $mode;
                }
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
     * Delete Agenda by Agenda ID
     * @param  string $idAgenda
     * @return boolean
     */
    public function deleteAgenda($idAgenda)
    {
        $this->deleteIndikatorByAgendaID($idAgenda);
        $this->deleteAktivitasByAgendaID($idAgenda);
        $this->deleteAsesmenByAgendaID($idAgenda);
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    AGENDA
                WHERE
                    ID_SUB_KOMPETENSI = :idAgenda'
            );
            $query->bindParam(':idAgenda', $idAgenda);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Get Indikator for the provided Agenda ID
     * @param  string $idAgenda
     * @param  string $mode [OPTIONAL]
     * @return mixed
     */
    public function getIndikatorByAgendaID($idAgenda)
    {
        $kategori = new Kategori();
        $query = $this->core->db->prepare(
            'SELECT
                ID_INDIKATOR,
                TEXT_INDIKATOR
            FROM
                INDIKATOR_AGENDA
            WHERE
                ID_SUB_KOMPETENSI = :idAgenda'
        );
        $query->bindParam(':idAgenda', $idAgenda);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $item => $indikator) {
                $results[$item]['INDIKATOR'] = $kategori->getAgendaKategoriByIndikatorId($results[$item]['ID_INDIKATOR']);
            }
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
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Delete Indikator by Indikator ID
     * @param  string $idIndikator
     * @return boolean
     */
    public function deleteIndikator($idIndikator)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    INDIKATOR_AGENDA
                WHERE
                    ID_INDIKATOR = :idIndikator'
            );
            $query->bindParam(':idIndikator', $idIndikator);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Delete Indikator by Agenda ID
     * @param  string $idAgenda
     * @return boolean
     */
    private function deleteIndikatorByAgendaID($idAgenda)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    INDIKATOR_AGENDA
                WHERE
                    ID_SUB_KOMPETENSI = :idAgenda'
            );
            $query->bindParam(':idAgenda', $idAgenda);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
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
                ID_SUB_KOMPETENSI = :idAgenda
            ORDER BY
                ID_AKTIVITAS_AGENDA'
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
     * Save new Aktivitas
     * @param  string  $idAgenda
     * @param  string  $txtAktivitas
     * @param  boolean $project
     * @param  boolean $task
     * @return boolean
     */
    public function saveAktivitas($idAgenda, $txtAktivitas, $project, $task)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    "AKTIVITAS_AGENDA"
                (
                    "ID_SUB_KOMPETENSI",
                    "TEXT_AKTIVITAS_AGENDA",
                    "PROJECT",
                    "TASK"
                )
                VALUES
                (
                    :idAgenda,
                    :txtAktivitas,
                    :project,
                    :task
                )'
            );
            $query->bindParam(':idAgenda', $idAgenda);
            $query->bindParam(':txtAktivitas', $txtAktivitas);
            $query->bindParam(':project', $project);
            $query->bindParam(':task', $task);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Delete aktivitas by Aktivitas ID
     * @param  string $idAktivitas
     * @return boolean
     */
    public function deleteAktivitas($idAktivitas)
    {
        $task = new Task();
        $task->deleteScopeByAktivitasId($idAktivitas);
        $task->deleteMetodeByAktivitasId($idAktivitas);
        $task->deleteKriteriaByAktivitasId($idAktivitas);
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    AKTIVITAS_AGENDA
                WHERE
                    ID_AKTIVITAS_AGENDA = :idAktivitas'
            );
            $query->bindParam(':idAktivitas', $idAktivitas);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Delete aktivitas by Agenda ID
     * @param  string $idAgenda
     * @return boolean
     */
    private function deleteAktivitasByAgendaID($idAgenda)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    AKTIVITAS_AGENDA
                WHERE
                    ID_SUB_KOMPETENSI = :idAgenda'
            );
            $query->bindParam(':idAgenda', $idAgenda);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
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

    /**
     * Save new Asesmen
     * @param  string  $idAgenda
     * @param  string  $txtAsesmen
     * @param  boolean $jenis
     * @return boolean
     */
    public function saveAsesmen($idAgenda, $txtAsesmen, $jenis)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    ASESMEN_AGENDA
                (
                    ID_SUB_KOMPETENSI,
                    NAMA_ASSESMENT_SUB_KOMPETENSI,
                    JENIS_ASSESMENT
                )
                VALUES
                (
                    :idAgenda,
                    :txtAsesmen,
                    :jenis
                )'
            );
            $query->bindParam(':idAgenda', $idAgenda);
            $query->bindParam(':txtAsesmen', $txtAsesmen);
            $query->bindParam(':jenis', $jenis);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete Asesmen by Asesmen ID
     * @param  string $idAsesmen
     * @return boolean
     */
    public function deleteAsesmen($idAsesmen)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    ASESMEN_AGENDA
                WHERE
                    ID_ASSESMENT_SUB_KOMPETENSI = :idAsesmen'
            );
            $query->bindParam(':idAsesmen', $idAsesmen);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Delete Asesmen by Agenda ID
     * @param  string $idAgenda
     * @return boolean
     */
    private function deleteAsesmenByAgendaID($idAgenda)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    ASESMEN_AGENDA
                WHERE
                    ID_SUB_KOMPETENSI = :idAgenda'
            );
            $query->bindParam(':idAgenda', $idAgenda);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }
}
