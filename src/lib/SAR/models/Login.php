<?php

/**
 * Login Class for SAR Application
 *
 * this file contains the data access for User login process.
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
 * Login Class
 * @package SAR\models
 */
class Login
{
    private $nip;
    private $username;
    private $role;
    private $matkul;
    private $prodi;
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
     * Authenticate Users
     *
     * Authenticate users based on NIP as username, and password.
     * @param  string $username
     * @param  string $password
     * @return boolean
     */
    public function authenticate($username, $password)
    {
        $query = $this->core->db->prepare(
            'SELECT
                PEGAWAI.NIP,
                PEGAWAI.TGLKELUAR,
                PEGAWAI.PASS_STAFF,
                PEGAWAI.NAMA,
                PEGAWAI.ID_JABATAN_AKAD,
                PEGAWAI.ID_JABATAN_STRUK,
                PEGAWAI.IDPRODI
            FROM
                PEGAWAI
            WHERE
                PEGAWAI.NIP = :nip
                AND PEGAWAI.PASS_STAFF = :pass'
        );
        $query->bindParam(':nip', $username);
        $query->bindParam(':pass', $password);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 1) {
            return false;
        } else {
            foreach ($results as $result) {
                if ($result['ID_JABATAN_AKAD'] == 1) {
                    if ($result['ID_JABATAN_STRUK'] == 1) {
                        $this->role = "kaprodi";
                    } else {
                        $this->role = "dosen";
                    }
                } else {
                    return false;
                }
                $this->nip = $result['NIP'];
                $this->username = $result['NAMA'];
                $this->prodi = $result['IDPRODI'];
                return true;
            }
        }
    }
}
