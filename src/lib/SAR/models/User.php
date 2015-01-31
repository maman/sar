<?php

/**
 * User Class for SAR Application
 *
 * this file contains the data access for User objects.
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
use SAR\models\Matkul as Matkul;

/**
 * User Class
 * @package SAR\models
 */
class User
{
    private $nip;
    private $name;
    private $role;
    private $matkul;
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

    public function getAllUser()
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                PEGAWAI
            WHERE
                ID_JABATAN_AKAD = 1'
        );
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if ($results > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get User
     *
     * Get user detail from provided ID/NIP
     * @param  string $id NIP of user
     * @return array
     */
    public function getUser($id)
    {
        $results = array();
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                PEGAWAI
            WHERE
                PEGAWAI.NIP = :nip
            AND
                PEGAWAI.TGLKELUAR IS null'
        );
        $query->bindParam(':nip', $id);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get Name from NIP
     * @param  string $nip
     * @return string
     */
    public function getUserName($nip)
    {
        $results = $this->getUser($nip);
        if ($results) {
            return $results[0]['NAMA'];
        } else {
            return 'Not Defined';
        }
    }

    /**
     * Get User Details
     *
     * Get all user details, including mata kuliah and user detail
     * from associated NIP.
     * @param  string $id
     * @return boolean
     */
    public function getUserDetails($nip)
    {
        /** define matkul private variable as Array */
        $this->matkul = array();

        $users = $this->getUser($nip);
        $matkul = new Matkul();
        $matkuls = $matkul->getMatkulByNIP($nip);
        foreach ($users as $user) {
            $this->nip = $user['NIP'];
            $this->name = $user['NAMA'];
            if ($user['ID_JABATAN_AKAD'] == 1) {
                if ($user['ID_JABATAN_STRUK'] == 1) {
                    $this->role = "kaprodi";
                } else {
                    $this->role = "dosen";
                }
            }
        }
        foreach ($matkuls as $matkul) {
            array_push($this->matkul, $matkul);
        }
        return true;
    }

    /**
     * Get User By MatkulID
     * @param  string $idMatkul
     * @return array
     */
    public function getUserFromMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT
                PEGAWAI.NAMA
            FROM
                PEGAWAI INNER JOIN PLOTTING_KOMPETENSI
            ON
                PEGAWAI.NIP = PLOTTING_KOMPETENSI.NIP
            WHERE
                PLOTTING_KOMPETENSI."KDMataKuliah" = :idMatkul'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get Kaprodi by Prodi
     * @param  string $idProdi
     * @return array
     */
    public function getKaprodi($idProdi)
    {
        $query = $this->core->db->prepare('
            SELECT
                NIP,
                NAMA
            FROM
                PEGAWAI
            WHERE
                ID_JABATAN_AKAD = 1
            AND
                ID_JABATAN_STRUK = 1
            AND
                IDPRODI = :idProdi
        ');
        $query->bindParam(':idProdi', $idProdi);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return array(
            'NIP' => $results[0]['NIP'],
            'NAMA' => $results[0]['NAMA']
        );
    }
}
