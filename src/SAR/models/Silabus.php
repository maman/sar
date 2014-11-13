<?php

/**
 * Agenda Class for SAR Application
 *
 * this file contains the data access for Silabus objects.
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
 * Silabus Class
 *
 * To initialize, provide a `idMatkul` variable.
 * @package SAR\models
 */
class Silabus
{
    private $silabusID;
    private $semester;
    private $tujuan;
    private $kompetensi;
    private $pokokBahasan;
    private $pustaka;
    private $mediaBelajarSoft;
    private $mediaBelajarHard;
    private $assesmentTes;
    private $assesmentNonTes;
    private $core;

    function __construct($idMatkul)
    {
        $this->core = Slim::getInstance();
        $silabus = $this->getSilabusByMatkul($idMatkul);
        if (count($silabus) == 1) {
            foreach ($silabus as $result) {
                $this->silabusID = $result['ID_SILABUS'];
                $this->mediaBelajarSoft = $result['MEDIA_BELAJAR_SOFTWARE'];
                $this->mediaBelajarHard = $result['MEDIA_BELAJAR_HARDWARE'];
                $this->assesmentTes = $result['ASSESMENT_TES'];
                $this->assesmentNonTes = $result['ASSESMENT_NONTES'];
                $this->pokokBahasan = $result['POKOK_BAHASAN'];
                $this->tujuan = $result['TUJUAN'];
                $this->pustaka = $this->getPustakaBySilabusID($result['ID_SILABUS']);
                $this->kompetensi = $this->combineKompetensiKategori($result['ID_SILABUS']);
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
     * Get Kategori for the provided Silabus ID
     * @return mixed
     */
    public function getKategori()
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
        $query->bindParam(':idSilabus', $this->silabusID);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Silabus for the provided Matkul ID
     * @param  string $idMatkul
     * @return mixed
     */
    private function getSilabusByMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                SILABUS
            WHERE
                ID_MATAKULIAH = :idMatkul'
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
     * Get Pustaka from the provided Silabus ID
     * @param  string $idSilabus
     * @return mixed
     */
    private function getPustakaBySilabusID($idSilabus)
    {
        $query = $this->core->db->prepare(
            'SELECT
                PUSTAKA.JUDUL_PUSTAKA,
                PUSTAKA.PENERBIT_PUSTAKA,
                PUSTAKA.PENGARANG_PUSTAKA,
                PUSTAKA.TAHUN_TERBIT_PUSTAKA
            FROM
                PUSTAKA INNER JOIN SILABUS
            ON
                SILABUS.ID_SILABUS = PUSTAKA.ID_SILABUS
            WHERE
                SILABUS.ID_SILABUS = :idSilabus'
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
     * Get Kompetensi for the provided Matkul ID
     * @param  string $idSilabus
     * @return mixed
     */
    private function getKompetensiBySilabusID($idSilabus)
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
                SILABUS.ID_SILABUS = :idSilabus'
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
    private function getKategoriByKompetensiID($idKompetensi)
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
     * Combine Kompetensi and Kategori for the provided Silabus ID
     * @param  string $idSilabus
     * @return array
     */
    private function combineKompetensiKategori($idSilabus)
    {
        $kompetensi = $this->getKompetensiBySilabusID($idSilabus);
        foreach ($kompetensi as $key => $item) {
            $kompetensi[$key]['KATEGORI_KOMPETENSI'] = $this->getKategoriByKompetensiID($item['ID_KOMPETENSI']);
        }
        return $kompetensi;
    }

    /**
     * Save or Edit Silabus Entries
     * @param  string $silabusID        Silabus ID
     * @param  string $pokokBahasan     Pokok Bahasan
     * @param  string $mediaBelajarSoft Media Belajar Software
     * @param  string $mediaBelajarHard Media Belajar Hardware
     * @param  string $assesmentTes     Assesment Tes
     * @param  string $assesmentNonTes  Assesment Non Tes
     * @param  string $idMatkul         Mata Kuliah ID
     * @param  string $tujuan           Tujuan
     * @return boolean
     */
    public function saveOrEdit($silabusID, $pokokBahasan, $mediaBelajarSoft, $mediaBelajarHard, $assesmentTes, $assesmentNonTes, $idMatkul, $tujuan)
    {
        # TODO
    }
}
