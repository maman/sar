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
                PEGAWAI.NIP = :nip'
        );
        $query->bindParam(':nip', $id);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get Mata Kuliah
     *
     * Get Mata Kuliah associated with id
     * @param  string $id
     * @return array
     */
    public function getMatkul($id)
    {
        $results = array();
        $query = $this->core->db->prepare(
            'SELECT
                PLOTTING."KDMataKuliah",
                MATAKULIAH."NamaMK"
            FROM
                PLOTTING INNER JOIN MATAKULIAH
            ON
                PLOTTING."KDMataKuliah" = MATAKULIAH."KDMataKuliah"
            WHERE
                PLOTTING.NIP = :nip
                AND PLOTTING.STATUS IS NOT NULL'
        );
        $query->bindParam(':nip', $id);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return $results;
    }

    /**
     * Get User Details
     *
     * Get all user details, including mata kuliah and user detail
     * from associated NIP.
     * @param  string $id
     * @return boolean
     */
    public function getUserDetails($id)
    {
        /** define matkul private variable as Array */
        $this->matkul = array();

        $users = $this->getUser($id);
        $matkuls = $this->getMatkul($id);
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
}
