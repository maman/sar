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

    public function __construct($idMatkul)
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
                SILABUS.ID_SILABUS = :idSilabus
            ORDER BY
                PUSTAKA.ID_PUSTAKA ASC'
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
     * Get Kompetensi for the provided Silabus ID
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
                SILABUS.ID_SILABUS = :idSilabus
            ORDER BY
                KOMPETENSI.ID_KOMPETENSI ASC'
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

    /**
     * Get Last inserted Kompetensi ID
     * @return string
     */
    private function getLastKompetensiId()
    {
        $query = $this->core->db->prepare(
            'SELECT
                ID_KOMPETENSI
            FROM
                KOMPETENSI
            ORDER BY
                ID_KOMPETENSI DESC'
        );
        $query->execute();
        $results = $query->fetchColumn();
        return $results;
    }

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
     * Save Kompetensi
     * @param  string $idSilabus
     * @param  string $text
     * @param  array  $kategori
     * @return boolean
     */
    public function saveKompetensi($idSilabus, $text, $kategori)
    {
        $nextInsertId = null;
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    KOMPETENSI
                (
                    ID_SILABUS,
                    NAMA_KOMPETENSI
                )
                VALUES
                (
                    :idSilabus,
                    :text
                )'
            );
            $query->bindParam(':idSilabus', $idSilabus);
            $query->bindParam(':text', $text);
            $query->execute();
            $nextInsertId = $this->getLastKompetensiId();
            foreach ($kategori as $item) {
                try {
                    $query2 = $this->core->db->prepare(
                        'INSERT INTO
                            SILABUS_KATEGORI_KOMPETENSI
                        (
                            ID_KOMPETENSI,
                            ID_KATEGORI_KOMPETENSI
                        )
                        VALUES
                        (
                            :idKompetensi,
                            :idKategori
                        )'
                    );
                    $query2->bindParam(':idKompetensi', $nextInsertId);
                    $query2->bindParam(':idKategori', $item);
                    $query2->execute();
                } catch (PDOException $e) {
                    return false;
                }
            }
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Delete Kompetensi
     * @param  string $idKompetensi
     * @return boolean
     */
    public function deleteKompetensi($idKompetensi)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    KOMPETENSI
                WHERE
                    ID_KOMPETENSI = :idKompetensi'
            );
            $query->bindParam(':idKompetensi', $idKompetensi);
            $query->execute();
            try {
                $query2 = $this->core->db->prepare(
                    'DELETE FROM
                        SILABUS_KATEGORI_KOMPETENSI
                    WHERE
                        ID_KOMPETENSI = :idKompetensi'
                );
                $query2->bindParam(':idKompetensi', $idKompetensi);
                $query2->execute();
            } catch (PDOException $e) {
                return false;
            }
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Save Kepustakaan
     * @param  string $idSilabus
     * @param  string $judul
     * @param  string $tahunTerbit
     * @param  string $penerbit
     * @param  string $pengarang
     * @return boolean
     */
    public function saveKepustakaan($idSilabus, $judul, $tahunTerbit, $penerbit, $pengarang)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    PUSTAKA
                (
                    ID_SILABUS,
                    PENGARANG_PUSTAKA,
                    JUDUL_PUSTAKA,
                    PENERBIT_PUSTAKA,
                    TAHUN_TERBIT_PUSTAKA
                )
                VALUES
                (
                    :idSilabus,
                    :pengarang,
                    :judul,
                    :penerbit,
                    :tahunTerbit
                )'
            );
            $query->bindParam(':idSilabus', $idSilabus);
            $query->bindParam(':judul', $judul);
            $query->bindParam(':tahunTerbit', $tahunTerbit);
            $query->bindParam(':penerbit', $penerbit);
            $query->bindParam(':pengarang', $pengarang);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    /**
     * Delete Kepustakaan by provided ID
     * @param  string $idPustaka
     * @return boolean
     */
    public function deleteKepustakaan($idPustaka)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    PUSTAKA
                WHERE
                    ID_PUSTAKA = :idPustaka'
            );
            $query->bindParam(':idPustaka', $idPustaka);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }
}
