<?php
namespace SAR\models;

use \Slim\Slim;

/**
* Login
*/
class Login
{
    private $nip;
    private $username;
    private $role;
    private $matkul;
    private $core;

    function __construct()
    {
        $this->core = Slim::getInstance();
    }

    public function __get($prop) {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
    }

    public function __set($prop, $val) {
        if (property_exists($this, $prop)) {
            $this->$prop = $val;
        }
        return $this;
    }

    public function authenticate($username, $password)
    {
        $query = $this->core->db->prepare(
            "SELECT PEGAWAI.NIP,
                PEGAWAI.TGLKELUAR,
                PEGAWAI.PASS_STAFF,
                PEGAWAI.NAMA,
                PEGAWAI.ID_JABATAN_AKAD,
                PEGAWAI.ID_JABATAN_STRUK,
                PEGAWAI.IDPRODI
            FROM PEGAWAI
            WHERE PEGAWAI.NIP = :nip AND PEGAWAI.PASS_STAFF = :pass"
        );
        $query->bindParam(':nip', $username);
        $query->bindParam(':pass', $password);
        $query->execute();
        $results = $query->fetchAll(\alfmel\OCI8\PDO::FETCH_ASSOC);
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
            }
            $this->nip = $result['NIP'];
            $this->username = $result['NAMA'];
            return true;
            }
        }
    }

    public function getMatkul($username) {
        $results = array();
        $query = $this->core->db->prepare(
            "SELECT PLOTTING.ID_MATAKULIAH
            FROM PLOTTING
            WHERE PLOTTING.NIP = :nip
            AND PLOTTING.STATUS IS NOT NULL"
        );
        $query->bindParam(':nip', $username);
        $query->execute();
        $results = $query->fetchAll(\alfmel\OCI8\PDO::FETCH_ASSOC);
        return $results;
    }
}
