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
use SAR\lib\Helpers;
use SAR\models\Kategori;

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
        if ($silabus) {
            foreach ($silabus as $result) {
                $this->silabusID = $result['ID_SILABUS'];
                // $this->mediaBelajarSoft = $result['MEDIA_BELAJAR_SOFTWARE'];
                // $this->mediaBelajarHard = $result['MEDIA_BELAJAR_HARDWARE'];
                // $this->assesmentTes = $result['ASSESMENT_TES'];
                // $this->assesmentNonTes = $result['ASSESMENT_NONTES'];
                $this->pokokBahasan = $result['POKOK_BAHASAN'];
                $this->tujuan = $result['TUJUAN'];
                $this->pustaka = $this->getPustakaBySilabusID($result['ID_SILABUS']);
                $this->kompetensi = $this->combineKompetensiKategori($result['ID_SILABUS']);
            }
            return true;
        } else {
            return false;
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
                PUSTAKA.ID_PUSTAKA,
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
     * Combine Kompetensi and Kategori for the provided Silabus ID
     * @param  string $idSilabus
     * @return mixed
     */
    private function combineKompetensiKategori($idSilabus)
    {
        $kategori = new Kategori();
        $allKategori = $kategori->getAllKategori();
        $kompetensi = $this->getKompetensiBySilabusID($idSilabus);
        if ($kompetensi) {
            foreach ($kompetensi as $key => $item) {
                $currentKategori = $kategori->getKategoriByKompetensiID($item['ID_KOMPETENSI']);
                if ($currentKategori) {
                    foreach ($allKategori as $id => $value) {
                        foreach ($currentKategori as $num => $val) {
                            if ($currentKategori[$num]['ID_KATEGORI_KOMPETENSI'] == $allKategori[$id]['ID_KATEGORI_KOMPETENSI']) {
                                $allKategori[$id]['SELECTED'] = true;
                                break;
                            } else {
                                $allKategori[$id]['SELECTED'] = false;
                                // break;
                            }
                        }
                    }
                    $kompetensi[$key]['KATEGORI_KOMPETENSI'] = $allKategori;
                } else {
                    foreach ($allKategori as $id => $value) {
                        $allKategori[$id]['SELECTED'] = false;
                    }
                    $kompetensi[$key]['KATEGORI_KOMPETENSI'] = $allKategori;
                }
            }
            return $kompetensi;
        } else {
            return false;
        }
    }

    // public function init($idMatkul) {
    //     $silabus = $this->getSilabusByMatkul($idMatkul);
    //     if (count($silabus) == 1) {
    //         foreach ($silabus as $result) {
    //             $this->silabusID = $result['ID_SILABUS'];
    //             // $this->mediaBelajarSoft = $result['MEDIA_BELAJAR_SOFTWARE'];
    //             // $this->mediaBelajarHard = $result['MEDIA_BELAJAR_HARDWARE'];
    //             // $this->assesmentTes = $result['ASSESMENT_TES'];
    //             // $this->assesmentNonTes = $result['ASSESMENT_NONTES'];
    //             $this->pokokBahasan = $result['POKOK_BAHASAN'];
    //             $this->tujuan = $result['TUJUAN'];
    //             $this->pustaka = $this->getPustakaBySilabusID($result['ID_SILABUS']);
    //             $this->kompetensi = $this->combineKompetensiKategori($result['ID_SILABUS']);
    //         }
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    /**
     * Save or Edit Silabus Entries
     * @param  string $pokokBahasan     Pokok Bahasan
     * @param  string $idMatkul         Mata Kuliah ID
     * @param  string $tujuan           Tujuan
     * @return boolean
     */
    public function saveOrEdit($idMatkul, $idSilabus, $pokokBahasan, $tujuan)
    {
        if ($idSilabus == '') {
            try {
                $query = $this->core->db->prepare(
                    'INSERT INTO
                        SILABUS
                    (
                        POKOK_BAHASAN,
                        ID_MATAKULIAH,
                        TUJUAN
                    )
                    VALUES
                    (
                        :pokokBahasan,
                        :idMatkul,
                        :tujuan
                    )'
                );
                $query->bindParam(':pokokBahasan', $pokokBahasan);
                $query->bindParam(':idMatkul', $idMatkul);
                $query->bindParam(':tujuan', $tujuan);
                $query->execute();
                return true;
            } catch (PDOException $e) {
                return false;
            }
        } else {
            try {
                $query = $this->core->db->prepare(
                    'UPDATE
                        SILABUS
                    SET
                        POKOK_BAHASAN = :pokokBahasan,
                        TUJUAN = :tujuan
                    WHERE
                        ID_SILABUS = :idSilabus'
                );
                $query->bindParam(':pokokBahasan', $pokokBahasan);
                $query->bindParam(':idSilabus', $idSilabus);
                $query->bindParam(':tujuan', $tujuan);
                $query->execute();
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
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
    public function edit($silabusID, $pokokBahasan, $mediaBelajarSoft, $mediaBelajarHard, $assesmentTes, $assesmentNonTes, $idMatkul, $tujuan)
    {
        # TODO
    }
}
