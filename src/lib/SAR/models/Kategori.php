<?php

/**
 * Kategori Class for SAR Application
 *
 * this file contains the data access for Kompetensi & Kategori objects.
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
use Functional as F;

/**
 * Kategori Class
 * @package SAR\models
 */
class Kategori
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

    public function getAllKompetensiByMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT
                KOMPETENSI.ID_KOMPETENSI,
                KOMPETENSI.NAMA_KOMPETENSI
            FROM
                SILABUS INNER JOIN KOMPETENSI
            ON
                SILABUS.ID_SILABUS = KOMPETENSI.ID_SILABUS
            WHERE
                SILABUS.ID_MATAKULIAH = :idMatkul'
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
     * Get Associated Kompetensi ID from Agenda
     * @param  string $idAgenda
     * @return mixed
     */
    public function getAssociatedKompetensi($idAgenda)
    {
        $query = $this->core->db->prepare(
            'SELECT
                KOMPETENSI.ID_KOMPETENSI,
                KOMPETENSI.NAMA_KOMPETENSI
            FROM
                KOMPETENSI INNER JOIN KOMPETENSI_AGENDA
            ON
                KOMPETENSI.ID_KOMPETENSI = KOMPETENSI_AGENDA.ID_KOMPETENSI
            WHERE
                "ID_SUB_KOMPETENSI" = :idAgenda'
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
     * Get Associated Kompetensi from Agenda & Matkul, Verbosely
     * @param  string $idMatkul
     * @param  string $idAgenda
     * @return mixed
     */
    public function getAssociatedKompetensiVerbose($idMatkul, $idAgenda)
    {
        $allKompetensi = $this->getAllKompetensiByMatkul($idMatkul);
        $currentKompetensi = $this->getAssociatedKompetensi($idAgenda);
        if ($currentKompetensi) {
            foreach ($allKompetensi as $keyAll => $valAll) {
                foreach ($currentKompetensi as $keyCurr => $valCurr) {
                    if ($valCurr['ID_KOMPETENSI'] == $valAll['ID_KOMPETENSI']) {
                        $allKompetensi[$keyAll]['SELECTED'] = true;
                        break;
                    } else {
                        $allKompetensi[$keyAll]['SELECTED'] = false;
                    }
                }
            }
            return $allKompetensi;
        } else {
            return false;
        }
    }

    /**
     * Get All Kategori
     * @return array
     */
    public function getAllKategori()
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                KATEGORI_KOMPETENSI'
        );
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get Kategori for the provided Silabus ID
     * @param  string $idSilabus
     * @return mixed
     */
    public function getKategoriById($idSilabus)
    {
        $query = $this->core->db->prepare(
            'SELECT
                KATEGORI_KOMPETENSI.ID_KATEGORI_KOMPETENSI,
                KATEGORI_KOMPETENSI.NAMA_KATEGORI_KOMPETENSI
            FROM
                KATEGORI_KOMPETENSI INNER JOIN SILABUS_KATEGORI_KOMPETENSI
            ON
                KATEGORI_KOMPETENSI.ID_KATEGORI_KOMPETENSI = SILABUS_KATEGORI_KOMPETENSI.ID_KATEGORI_KOMPETENSI
                INNER JOIN KOMPETENSI
            ON
                KOMPETENSI.ID_KOMPETENSI = SILABUS_KATEGORI_KOMPETENSI.ID_KOMPETENSI
            WHERE
                KOMPETENSI.ID_SILABUS = :idSilabus'
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

    /**
     * Get Kategori for the provided Kompetensi ID
     * @param  string $idKompetensi
     * @return mixed
     */
    public function getKategoriByKompetensiID($idKompetensi)
    {
        $query = $this->core->db->prepare(
            'SELECT
                KATEGORI_KOMPETENSI.ID_KATEGORI_KOMPETENSI,
                KATEGORI_KOMPETENSI.NAMA_KATEGORI_KOMPETENSI
            FROM
                KATEGORI_KOMPETENSI INNER JOIN SILABUS_KATEGORI_KOMPETENSI
            ON
                KATEGORI_KOMPETENSI.ID_KATEGORI_KOMPETENSI = SILABUS_KATEGORI_KOMPETENSI.ID_KATEGORI_KOMPETENSI
            WHERE
                SILABUS_KATEGORI_KOMPETENSI.ID_KOMPETENSI = :idKompetensi'
        );
        $query->bindParam(':idKompetensi', $idKompetensi);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get all Kategori Indikators
     * @return mixed
     */
    public function getAllKategoriIndikator()
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
     * Get Unique Agenda Kategori from provided Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    public function getAgendaKategoriByAgendaId($idAgenda)
    {
        $query = $this->core->db->prepare(
            'SELECT DISTINCT
                KATEGORI_INDIKATOR_AGENDA.ID_KETERANGAN,
                KATEGORI_INDIKATOR_AGENDA.NAMA,
                KATEGORI_INDIKATOR_AGENDA.KATEGORI
            FROM
                AGENDA INNER JOIN INDIKATOR_AGENDA
            ON
                AGENDA.ID_SUB_KOMPETENSI = INDIKATOR_AGENDA.ID_SUB_KOMPETENSI
                INNER JOIN KATEGORI_INDIKATOR_AGENDA
            ON
                INDIKATOR_AGENDA.ID_KETERANGAN = KATEGORI_INDIKATOR_AGENDA.ID_KETERANGAN
            WHERE
                INDIKATOR_AGENDA.ID_SUB_KOMPETENSI = :idAgenda'
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

    public function getAgendaKategoriByAgendaIdVerbose($idAgenda)
    {
        $allKategori = $this->groupKategoriIndikator();
        $currentKategori = $this->getAgendaKategoriByAgendaId($idAgenda);
        if ($currentKategori) {
            foreach ($allKategori as $keyKat => $valKat) {
                foreach ($valKat as $key => $value) {
                    if (is_array($value)) {
                        foreach ($currentKategori as $num => $val) {
                            if ($val['ID_KETERANGAN'] == $value['ID_KETERANGAN']) {
                                $allKategori[$keyKat][$key]['SELECTED'] = true;
                                break;
                            } else {
                                $allKategori[$keyKat][$key]['SELECTED'] = false;
                            }
                        }
                    }
                }
            }
            return $allKategori;
        } else {
            return false;
        }
    }

    /**
     * Get Agenda Kategori from provided Indikator ID
     * @param  string $idIndikator
     * @return mixed
     */
    public function getAgendaKategoriByIndikatorId($idIndikator)
    {
        $query = $this->core->db->prepare(
            'SELECT
                KATEGORI_INDIKATOR_AGENDA.ID_KETERANGAN,
                KATEGORI_INDIKATOR_AGENDA.NAMA,
                KATEGORI_INDIKATOR_AGENDA.KATEGORI
            FROM
                KATEGORI_INDIKATOR_AGENDA INNER JOIN INDIKATOR_AGENDA
            ON
                KATEGORI_INDIKATOR_AGENDA.ID_KETERANGAN = INDIKATOR_AGENDA.ID_KETERANGAN
            WHERE
                INDIKATOR_AGENDA.ID_INDIKATOR = :idIndikator
            ORDER BY
                INDIKATOR_AGENDA.ID_INDIKATOR ASC'
        );
        $query->bindParam(':idIndikator', $idIndikator);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Group Indikator Kategori By Indikator
     * @return array
     */
    public function groupKategoriIndikator()
    {
        $indikator = $this->getAllKategoriIndikator();
        $results = F\group($indikator, function ($kategori) {
            return $kategori['KATEGORI'];
        });
        foreach ($results as $key => $value) {
            $results[$key]['LENGTH'] = count($value);
        }
        return $results;
    }
}
