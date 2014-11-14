<?php

/**
 * Kategori Class for SAR Application
 *
 * this file contains the data access for Kategori objects.
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
 * Kategori Class
 * @package SAR\models
 */
class Kategori
{
    private $nama;
    private $kode;
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
}
