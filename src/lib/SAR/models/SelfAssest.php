<?php

/**
 * SelfAssest Class for SAR Application
 *
 * this file contains the data access for SelfAssest objects.
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
use SAR\models\Matkul;
use SAR\models\Agenda;
use SAR\externals\Plotting;

/**
 * SelfAssest Class
 * @package SAR\models
 */
class SelfAssest
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
     * Get All SAR by Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    public function getSARByAgenda($idAgenda)
    {
        $result = $this->getAllSAR();
        if ($result) {
            $results = F\select($result, function ($item, $key, $col) use ($idAgenda) {
                return $item['ID_SUB_KOMPETENSI'] == $idAgenda;
            });
            if (count($results) == 1) {
                $val = reset($results);
                return $val;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get All SAR
     * @return mixed
     */
    public function getAllSAR()
    {
        $query = $this->core->db->prepare(
            'SELECT
                ID_SAR,
                ID_SUB_KOMPETENSI,
                NAMA_SAR,
                TO_CHAR("TGL_PELAKSANA", \'YYYY-MM-DD HH24:MI:SS\') as "TGL_PELAKSANA",
                REVIEW,
                HAMBATAN,
                PERSENTASE,
                AKTIVITAS
            FROM
                SAR'
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
     * Get All SAR Associated with the provided ID Matakuliah
     * @param  string $idMatkul
     * @return mixed
     */
    public function getSARByMatkul($idMatkul, $year = null)
    {
        $agenda = new Agenda();
        $arraySar = array();
        $result = $agenda->getAgendaByMatkul($idMatkul, $year, null);
        foreach ($result as $resultKey => $resultValue) {
            array_push($arraySar, $this->getAllSARByAgenda($result[$resultKey]['ID_SUB_KOMPETENSI']));
        }
        return $arraySar;
    }

    /**
     * Save or Edit SAR Entry
     * @param  string $idSAR
     * @param  string $idAgenda
     * @param  string $nama
     * @param  string $tgl
     * @param  string $review
     * @param  string $hambatan
     * @param  string $persentase
     * @return boolean
     */
    public function saveOrEdit($idSAR, $idAgenda, $nama, $tgl, $aktivitas, $review, $hambatan, $persentase)
    {
        $currAct = join(',', $aktivitas);
        if ($idSAR == '') {
            try {
                $query = $this->core->db->prepare(
                    'INSERT INTO
                        SAR
                    (
                        ID_SUB_KOMPETENSI,
                        NAMA_SAR,
                        TGL_PELAKSANA,
                        REVIEW,
                        HAMBATAN,
                        PERSENTASE,
                        AKTIVITAS
                    )
                    VALUES
                    (
                        :idAgenda,
                        :nama,
                        to_date(:tgl, \'YYYY-MM-DD HH24:MI:SS\'),
                        :review,
                        :hambatan,
                        :persentase,
                        :currAct
                    )'
                );
                $query->bindParam(':idAgenda', $idAgenda);
                $query->bindParam(':nama', $nama);
                $query->bindParam(':tgl', $tgl);
                $query->bindParam(':review', $review);
                $query->bindParam(':hambatan', $hambatan);
                $query->bindParam(':persentase', $persentase);
                $query->bindParam(':currAct', $currAct);
                $query->execute();
                return true;
            } catch (PDOException $e) {
                return false;
            }
        } else {
            try {
                $query = $this->core->db->prepare(
                    'UPDATE
                        SAR
                    SET
                        NAMA_SAR = :nama,
                        TGL_PELAKSANA = to_date(:tgl, \'YYYY-MM-DD HH24:MI:SS\'),
                        REVIEW = :review,
                        HAMBATAN = :hambatan,
                        PERSENTASE = :persentase,
                        AKTIVITAS = :currAct
                    WHERE
                        ID_SAR = :idSAR'
                );
                $query->bindParam(':idSAR', $idSAR);
                $query->bindParam(':nama', $nama);
                $query->bindParam(':tgl', $tgl);
                $query->bindParam(':review', $review);
                $query->bindParam(':hambatan', $hambatan);
                $query->bindParam(':persentase', $persentase);
                $query->bindParam(':currAct', $currAct);
                $query->execute();
                return true;
            } catch (PDOException $e) {
                return false;
            }
        }
    }

    /**
     * Delete SAR Entry
     * @param  string $idSAR
     * @return boolean
     */
    public function deleteSAR($idSAR)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    SAR
                WHERE
                    ID_SAR = :idSAR'
            );
            $query->bindParam(':idSAR', $idSAR);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
}
