<?php

/**
 * Approval Class for SAR Application
 *
 * this file contains the data access for Approval objects.
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
use SAR\models\Rps;

/**
* Approval Class
* @package SAR\models
*/
class Approval
{
    private $idMatkul;
    private $idApproval;
    private $nip;
    private $tglMasuk;
    private $tglPeriksa;
    private $tglDisahkan;
    private $nipPeriksa;
    private $nipSahkan;
    private $notePeriksa;
    private $noteSahkan;
    private $kodeApproval;
    private $versi;
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

    private function getTodayDate()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Get Approval associated with provided Matakuliah ID
     * @param  string $idMatkul
     * @return mixed
     */
    public function getApprovalByIdMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
            (
                SELECT
                    "ID_Approval",
                    "KDMataKuliah",
                    "NIP",
                    TO_CHAR("TglMasuk", \'YYYY-MM-DD HH24:MI:SS\') as "TglMasuk",
                    TO_CHAR("TglPeriksa", \'YYYY-MM-DD HH24:MI:SS\') as "TglPeriksa",
                    TO_CHAR("TglDisahkan", \'YYYY-MM-DD HH24:MI:SS\') as "TglDisahkan",
                    "NIP_Periksa",
                    "NIP_Pengesahan",
                    "NotePeriksa",
                    "NotePengesahan",
                    "Approval",
                    "Versi"
                FROM
                    Approval
                WHERE
                    "KDMataKuliah" = :idMatkul
                ORDER BY
                    "ID_Approval" DESC
            )
            WHERE
                ROWNUM = 1'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->idMatkul = $result['KDMataKuliah'];
                $this->idApproval = $result['ID_Approval'];
                $this->nip = $result['NIP'];
                $this->tglMasuk = $result['TglMasuk'];
                $this->tglPeriksa = $result['TglPeriksa'];
                $this->tglDisahkan = $result['TglDisahkan'];
                $this->nipPeriksa = $result['NIP_Periksa'];
                $this->nipSahkan = $result['NIP_Pengesahan'];
                $this->notePeriksa = $result['NotePeriksa'];
                $this->noteSahkan = $result['NotePengesahan'];
                $this->kodeApproval = $result['Approval'];
                $this->versi = $result['Versi'];
            }
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get All content of Approval table
     * @return mixed
     */
    public function getAllApproval()
    {
        $query = $this->core->db->prepare(
            'SELECT
                "ID_Approval",
                "KDMataKuliah",
                "NIP",
                TO_CHAR("TglMasuk", \'YYYY-MM-DD HH24:MI:SS\') as "TglMasuk",
                TO_CHAR("TglPeriksa", \'YYYY-MM-DD HH24:MI:SS\') as "TglPeriksa",
                TO_CHAR("TglDisahkan", \'YYYY-MM-DD HH24:MI:SS\') as "TglDisahkan",
                "NIP_Periksa",
                "NIP_Pengesahan",
                "NotePeriksa",
                "NotePengesahan",
                "Approval",
                "Versi"
            FROM
                Approval'
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
     * Get Approval History associated with provided Matakuliah ID
     * @param  string $idMatkul
     * @return mixed
     */
    public function getAllApprovalByMatkul($idMatkul)
    {
        $query = $this->core->db->prepare(
            'SELECT
                "ID_Approval",
                "KDMataKuliah",
                "NIP",
                TO_CHAR("TglMasuk", \'YYYY-MM-DD HH24:MI:SS\') as "TglMasuk",
                TO_CHAR("TglPeriksa", \'YYYY-MM-DD HH24:MI:SS\') as "TglPeriksa",
                TO_CHAR("TglDisahkan", \'YYYY-MM-DD HH24:MI:SS\') as "TglDisahkan",
                "NIP_Periksa",
                "NIP_Pengesahan",
                "NotePeriksa",
                "NotePengesahan",
                "Approval",
                "Versi"
            FROM
                Approval
            WHERE
                "KDMataKuliah" = :idMatkul
            ORDER BY
                "ID_Approval" ASC'
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
     * Create new Approval Entry
     * @param  string $idMatkul
     * @param  string $nip
     * @return boolean
     */
    public function createApprovalForMatkul($idMatkul, $nip, $versi)
    {
        $tglMasuk = $this->getTodayDate();
        try {
            $insert = $this->core->db->prepare(
                'INSERT INTO
                    Approval
                (
                    "KDMataKuliah",
                    "NIP",
                    "TglMasuk",
                    "Versi"
                )
                VALUES
                (
                    :idMatkul,
                    :nip,
                    to_date(:tglMasuk, \'YYYY-MM-DD HH24:MI:SS\'),
                    :versi
                )'
            );
            $insert->bindParam(':idMatkul', $idMatkul);
            $insert->bindParam(':nip', $nip);
            $insert->bindParam(':tglMasuk', $tglMasuk);
            $insert->bindParam(':versi', $versi);
            $insert->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Perform Reject on provided $idApproval
     * @param  string $idApproval
     * @param  string $nip
     * @param  string $review [optional - if not provided, then status is ditolak(0)]
     * @return boolean
     */
    public function rejectMatkul($idApproval, $nip, $review = '-')
    {
        $rps = new Rps();
        $tglReview = $this->getTodayDate();
        $rps->resetAndBump($this->idMatkul);
        try {
            $query = $this->core->db->prepare('
                UPDATE
                    Approval
                SET
                    "TglPeriksa" = to_date(:tglReview, \'YYYY-MM-DD HH24:MI:SS\'),
                    "NotePeriksa" = :review,
                    "NIP_Periksa" = :nip,
                    "Approval" = 1
                WHERE
                    "ID_Approval" = :idApproval
            ');
            $query->bindParam(':tglReview', $tglReview);
            $query->bindParam(':review', $review);
            $query->bindParam(':nip', $nip);
            $query->bindParam(':idApproval', $idApproval);
            $query->execute();
            $this->tglPeriksa = $tglReview;
            $this->notePeriksa = $review;
            $this->nipPeriksa = $nip;
            $this->kodeApproval = '1';
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Perform Approval on provided $idApproval
     * @param  string $idApproval
     * @param  string $nip
     * @param  string $review
     * @return boolean
     */
    public function approveMatkul($idApproval, $nip, $review = '-')
    {
        $rps = new Rps();
        $tglApprove = $this->getTodayDate();
        try {
            $query = $this->core->db->prepare('
                UPDATE
                    Approval
                SET
                    "TglDisahkan" = to_date(:tglApprove, \'YYYY-MM-DD HH24:MI:SS\'),
                    "NotePengesahan" = :review,
                    "NIP_Pengesahan" = :nip,
                    "Approval" = 2
                WHERE
                    "ID_Approval" = :idApproval
            ');
            $query->bindParam(':tglApprove', $tglApprove);
            $query->bindParam(':review', $review);
            $query->bindParam(':nip', $nip);
            $query->bindParam(':idApproval', $idApproval);
            $query->execute();
            $this->tglDisahkan = $tglApprove;
            $this->noteSahkan = $review;
            $this->nipSahkan = $nip;
            $this->kodeApproval = '2';
            $rps->approve($this->idMatkul);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get Rejection Detail
     * @param  string $idMatkul
     * @param  string $versi
     * @return mixed
     */
    public function getRejectDetail($idMatkul, $versi)
    {
        $versi = $versi - 1;
        $query = $this->core->db->prepare('
            SELECT
                TO_CHAR("TglPeriksa", \'YYYY-MM-DD HH24:MI:SS\') as "TglPeriksa",
                "NIP_Periksa",
                "NotePeriksa"
            FROM
                Approval
            WHERE
                "KDMataKuliah" = :idMatkul
            AND
                "Versi" = :versi
        ');
        $query->bindParam(':idMatkul', $idMatkul);
        $query->bindParam(':versi', $versi);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Approval Detail
     * @param  string $idMatkul
     * @return mixed
     */
    public function getApprovalDetail($idMatkul)
    {
        $result = $this->getApprovalByIdMatkul($idMatkul);
        if ($result) {
            $query = $this->core->db->prepare('
                SELECT
                    TO_CHAR("TglPeriksa", \'YYYY-MM-DD HH24:MI:SS\') as "TglPeriksa",
                    "NIP_Periksa",
                    "NotePeriksa"
                FROM
                    Approval
                WHERE
                    "KDMataKuliah" = :idMatkul
            ');
            $query->bindParam(':idMatkul', $result['KDMataKuliah']);
            $query->execute();
            $results = $query->fetchAll(OCI8::FETCH_ASSOC);
            if (count($results) > 0) {
                return $results;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
