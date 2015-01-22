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
use SAR\models\Silabus;
use SAR\models\Agenda;
use SAR\models\Task;
use SAR\models\Rps;
use SAR\models\Matkul;
use SAR\models\User;
use SAR\helpers\Utilities;
use SAR\externals\Plotting;

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
    private $idPlotting;
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
        $plotting = new Plotting();
        $currPlot = $plotting->getCurrentPlotting($idMatkul);
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
                    "Versi",
                    "ID_PLOTTING"
                FROM
                    Approval
                WHERE
                    "KDMataKuliah" = :idMatkul
                AND
                    "ID_PLOTTING" = :currPlot
                ORDER BY
                    "TglDisahkan" DESC
            )
            WHERE
                ROWNUM = 1'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->bindParam(':currPlot', $currPlot);
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
                $this->idPlotting = $result['ID_PLOTTING'];
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
                Approval
            ORDER BY
                "ID_Approval" DESC'
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
    public function getAllApprovalByMatkul($idMatkul, $tahun = null)
    {
        $plotting = new Plotting();
        if ($tahun === null) {
            $currPlot = $plotting->getCurrentPlotting($idMatkul);
        } else {
            $currPlot = $plotting->getCurrentPlotting($idMatkul, $tahun);
        }
        $currPlot = $plotting->getCurrentPlotting($idMatkul);
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
                "Versi",
                "ID_PLOTTING"
            FROM
                Approval
            WHERE
                "KDMataKuliah" = :idMatkul
            AND
                "ID_PLOTTING" = :currPlot
            ORDER BY
                "ID_Approval" ASC'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->bindParam(':currPlot', $currPlot);
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
        $plotting = new Plotting();
        $currPlot = $plotting->getCurrentPlotting($idMatkul);
        try {
            $insert = $this->core->db->prepare(
                'INSERT INTO
                    Approval
                (
                    "KDMataKuliah",
                    "NIP",
                    "TglMasuk",
                    "Versi",
                    "ID_PLOTTING"
                )
                VALUES
                (
                    :idMatkul,
                    :nip,
                    to_date(:tglMasuk, \'YYYY-MM-DD HH24:MI:SS\'),
                    :versi,
                    :currPlot
                )'
            );
            $insert->bindParam(':idMatkul', $idMatkul);
            $insert->bindParam(':nip', $nip);
            $insert->bindParam(':tglMasuk', $tglMasuk);
            $insert->bindParam(':versi', $versi);
            $insert->bindParam(':currPlot', $currPlot);
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
    public function approveMatkul($idApproval, $nip, $idMatkul, $review = '-')
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
            if ($this->core->container->has('solr')) {
                $solr = $this->core->solr;
                $matkul = new Matkul();
                $agenda = new Agenda();
                $task = new Task();
                $user = new User();
                $detail = $this->getApprovalByIdMatkul($idMatkul);
                $silabus = new Silabus($detail[0]['KDMataKuliah']);
                $kompetensis = $silabus->kompetensi;
                $pustakas = $silabus->pustaka;
                $matkulName = $matkul->getMatkulName($detail[0]['KDMataKuliah']);
                $agendaDetail = $agenda->getAgendaByMatkul($detail[0]['KDMataKuliah']);
                $taskDetail = $task->getDetailAktivitasByMatkul($detail[0]['KDMataKuliah']);
                $kompetensi = array();
                $pustaka = array();
                $subkompetensi = array();
                $materi = array();
                $task = array();
                foreach ($kompetensis as $item) {
                    $kompetensi[] = $item['NAMA_KOMPETENSI'];
                }
                foreach ($pustakas as $item) {
                    $pustaka[] = $item['JUDUL_PUSTAKA'] . '-' . $item['PENERBIT_PUSTAKA'] . '-' . $item['PENGARANG_PUSTAKA'];
                }
                foreach ($agendaDetail as $keyAgenda => $valAgenda) {
                    $subkompetensi[] = $agendaDetail[$keyAgenda]['TEXT_SUB_KOMPETENSI'];
                    $materi[] = $agendaDetail[$keyAgenda]['TEXT_MATERI_BELAJAR'];
                }
                foreach ($taskDetail as $keyTask => $valTask) {
                    foreach ($taskDetail[$keyTask]['AKTIVITAS'] as $keyAct => $valAct) {
                        $task[] = $taskDetail[$keyTask]['AKTIVITAS'][$keyAct]['TEXT_AKTIVITAS_AGENDA'];
                    }
                }
                $update = $solr->createUpdate();
                $doc = $update->createDocument();
                $doc->id = $detail[0]['KDMataKuliah'];
                $doc->namamatakuliah = $matkulName;
                $doc->tujuan = $silabus->tujuan;
                $doc->kompetensi = $kompetensi;
                $doc->pokokbahasan = $silabus->pokokBahasan;
                $doc->pustaka = $pustaka;
                $doc->subkompetensi = $subkompetensi;
                $doc->materi = $materi;
                $doc->task = $task;
                $doc->author = $user->getUserFromMatkul($detail[0]['KDMataKuliah'])[0]['NAMA'];
                $doc->acceptor = $user->getUserName($nip);
                $update->addDocument($doc);
                $update->addCommit();
                $solr->update($update);
            }
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
        $plotting = new Plotting();
        $currPlot = $plotting->getCurrentPlotting($idMatkul);
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
            AND
                "ID_PLOTTING" = :currPlot
        ');
        $query->bindParam(':idMatkul', $idMatkul);
        $query->bindParam(':versi', $versi);
        $query->bindParam(':currPlot', $currPlot);
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
        $plotting = new Plotting();
        $currPlot = $plotting->getCurrentPlotting($idMatkul);
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
                AND
                    "ID_PLOTTING" = :currPlot
            ');
            $query->bindParam(':idMatkul', $result['KDMataKuliah']);
            $query->bindParam(':currPlot', $currPlot);
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

    /**
     * Get All Approved Matakuliah Approval for
     * archiving purpose.
     * @return array
     */
    public function getAllApprovedMatkul()
    {
        $user = new User;
        $query = $this->core->db->prepare('
            SELECT
                APPROVAL."ID_Approval",
                APPROVAL."KDMataKuliah",
                APPROVAL.NIP,
                MATAKULIAH."NamaMK",
                TO_CHAR("TglMasuk", \'YYYY-MM-DD HH24:MI:SS\') as "TglMasuk",
                TO_CHAR("TglDisahkan", \'YYYY-MM-DD HH24:MI:SS\') as "TglDisahkan",
                APPROVAL."NIP_Pengesahan",
                MATAKULIAH."SemesterMK"
            FROM
                MATAKULIAH INNER JOIN APPROVAL
            ON
                MATAKULIAH."KDMataKuliah" = APPROVAL."KDMataKuliah"
            WHERE
                APPROVAL."Approval" = 2
        ');
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $itemKey => $itemVal) {
                $results[$itemKey]['namaSubmitter'] = $user->getUserName($results[$itemKey]['NIP']);
                $results[$itemKey]['namaApprover'] = $user->getUserName($results[$itemKey]['NIP_Pengesahan']);
            }
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Approved Approval count for provided Year
     * @param  date $date in Y format
     * @return int
     */
    public function getApprovedCount($date)
    {
        $query = $this->core->db->prepare('
            SELECT
                *
            FROM
                Approval
            WHERE
                "Approval" = 2
            AND
                TO_CHAR("TglMasuk", \'YYYY\') = :dateData
        ');
        $query->bindParam(':dateData', $date);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return count($results);
    }

    /**
     * Get Rejected Approval count for provided Year
     * @param  date $date in Y format
     * @return int
     */
    public function getRejectedCount($date)
    {
        $query = $this->core->db->prepare('
            SELECT
                *
            FROM
                Approval
            WHERE
                "Approval" = 1
            AND
                TO_CHAR("TglMasuk", \'YYYY\') = :dateData
        ');
        $query->bindParam(':dateData', $date);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        return count($results);
    }
}
