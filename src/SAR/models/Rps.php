<?php

/**
 * RPS Class for SAR Application
 *
 * this file contains the data access for RPS objects.
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
use SAR\models\Matkul;

/**
 * Rps Class
 * @package SAR\models
 */
class Rps
{
    private $silabus;
    private $agenda;
    private $project;
    private $idMatkul;
    private $approval;
    private $silabusStart;
    private $agendaStart;
    private $projectStart;
    private $silabusLastEdit;
    private $agendaLastEdit;
    private $projectLastEdit;
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

    /**
     * getRpsByIdMatkul
     *
     * get RPS detail for the provided idMatkul
     * @param  string $idMatkul
     * @return boolean
     */
    public function getRpsByIdMatkul($idMatkul)
    {
        $results = false;
        $query = $this->core->db->prepare(
            'SELECT
                "Silabus",
                "Agenda",
                "Project",
                "Approval",
                "KDMataKuliah",
                TO_CHAR("Silabus_StartDate", \'YYYY-MM-DD HH24:MI:SS\') as "Silabus_StartDate",
                TO_CHAR("Agenda_StartDate", \'YYYY-MM-DD HH24:MI:SS\') as "Agenda_StartDate",
                TO_CHAR("Silabus_LastEdit", \'YYYY-MM-DD HH24:MI:SS\') as "Silabus_LastEdit",
                TO_CHAR("Agenda_LastEdit", \'YYYY-MM-DD HH24:MI:SS\') as "Agenda_LastEdit",
                TO_CHAR("Project_LastEdit", \'YYYY-MM-DD HH24:MI:SS\') as "Project_LastEdit",
                "Versi"
            FROM
                RPS
            WHERE
                "KDMataKuliah" = :idMatkul'
        );
        $query->bindParam(':idMatkul', $idMatkul);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            foreach ($results as $result) {
                $this->silabus         = $result['Silabus'];
                $this->agenda          = $result['Agenda'];
                $this->project         = $result['Project'];
                $this->approval        = $result['Approval'];
                $this->idMatkul        = $result['KDMataKuliah'];
                $this->silabusStart    = $result['Silabus_StartDate'];
                $this->agendaStart     = $result['Agenda_StartDate'];
                $this->silabusLastEdit = $result['Silabus_LastEdit'];
                $this->agendaLastEdit  = $result['Agenda_LastEdit'];
                $this->projectLastEdit = $result['Project_LastEdit'];
                $this->versi           = $result['Versi'];
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create RPS
     *
     * Create RPS for the provided idMatkul
     * @param  string $idMatkul
     * @return boolean
     */
    public function createRpsForMatkul($idMatkul)
    {
        try {
            $insert = $this->core->db->prepare(
                'INSERT INTO
                    RPS
                ("KDMataKuliah")
                    VALUES
                (:idMatkul)'
            );
            $insert->bindParam(':idMatkul', $idMatkul);
            $insert->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Get RPS Progress for the provided ID Matkul
     * @param  string $idMatkul
     * @return mixed
     */
    public function getRpsProgress($idMatkul)
    {
        $progress = array(
            'percentage' => 0,
            'approved' => 'never',
            'RPSDetails' => array(
                'silabus' => 'never',
                'agenda' => 'never',
                'project' => 'never',
            )
        );
        $percentage = 0;
        if ($this->getRpsByIdMatkul($idMatkul)) {
            if (!($this->silabus) == '0') {
                switch($this->silabus) {
                    case '1':
                        $progress['RPSDetails']['silabus'] = 'work';
                        break;
                    case '2':
                        $progress['RPSDetails']['silabus'] = 'finish';
                        $percentage += 20;
                        break;
                }
            }
            if (!($this->agenda) == '0') {
                switch($this->agenda) {
                    case '1':
                        $progress['RPSDetails']['agenda'] = 'work';
                        break;
                    case '2':
                        $progress['RPSDetails']['agenda'] = 'finish';
                        $percentage += 20;
                        break;
                }
            }
            if (!($this->project) == '0') {
                switch($this->project) {
                    case '1':
                        $progress['RPSDetails']['project'] = 'work';
                        break;
                    case '2':
                        $progress['RPSDetails']['project'] = 'finish';
                        $percentage += 20;
                        break;
                }
            }
            if (!($this->approval) == '0') {
                switch($this->approval) {
                    case '1':
                        $progress['approved'] = 'wait';
                        $percentage += 20;
                        break;
                    case '2':
                        $progress['approved'] = 'approved';
                        $percentage += 40;
                        break;
                }
            }
            $progress['percentage'] = $percentage;
        } else {
            return false;
        }
        return $progress;
    }

    /**
     * Start working on Silabus
     * @param  string $idMatkul
     * @return boolean
     */
    public function startSilabus($idMatkul)
    {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Silabus_StartDate" = to_date(:currDate, \'YYYY-MM-DD HH24:MI:SS\')
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':currDate', $currDate);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->silabusStart = $currDate;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Last edit on Silabus
     * @param  string $idMatkul
     * @return boolean
     */
    public function editSilabus($idMatkul)
    {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Silabus_LastEdit" = to_date(:currDate, \'YYYY-MM-DD HH24:MI:SS\')
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':currDate', $currDate);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->silabusLastEdit = $currDate;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Start working on Agenda
     * @param  string $idMatkul
     * @return boolean
     */
    public function startAgenda($idMatkul)
    {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Agenda_StartDate" = to_date(:currDate, \'YYYY-MM-DD HH24:MI:SS\')
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':currDate', $currDate);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->agendaStart = $currDate;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Last Edit on Agenda
     * @param  string $idMatkul
     * @return boolean
     */
    public function editAgenda($idMatkul)
    {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Agenda_LastEdit" = to_date(:currDate, \'YYYY-MM-DD HH24:MI:SS\')
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':currDate', $currDate);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->agendaLastEdit = $currDate;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Start working on Project
     * @param  string $idMatkul
     * @return boolean
     */
    public function startProject($idMatkul)
    {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Project_StartDate" = to_date(:currDate, \'YYYY-MM-DD HH24:MI:SS\')
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':currDate', $currDate);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->projectStart = $currDate;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Last Edit on Project
     * @param  string $idMatkul
     * @return boolean
     */
    public function editProject($idMatkul)
    {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Project_LastEdit" = to_date(:currDate, \'YYYY-MM-DD HH24:MI:SS\')
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':currDate', $currDate);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->projectLastEdit = $currDate;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Bump Progress
     * @param  string $idMatkul
     * @param  string $field - {'silabus', 'agenda', 'project'}
     * @return boolean
     */
    public function bumpProgress($idMatkul, $field)
    {
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "' . ucfirst($field) . '" = 1
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->$field = '1';
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Submit Progress
     * @param  string $idMatkul
     * @param  string $field - {'silabus', 'agenda', 'project'}
     * @return boolean
     */
    public function submitProgress($idMatkul, $field)
    {
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "' . ucfirst($field) . '" = 2
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->$field = '2';
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Submit RPS
     * @param  string $idMatkul
     * @return boolean
     */
    public function submitRPS($idMatkul, $nip)
    {
        $approval = new Approval();
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Approval" = 1
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->approval = '1';
            $approval->createApprovalForMatkul($idMatkul, $nip);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Change Approval on RPS to True for the provided $idMatkul
     * @param  string $idMatkul
     * @return boolean
     */
    public function approve($idMatkul)
    {
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Approval" = 2
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->approval = '2';
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Reset progress and Bump Version
     * @param  string $idMatkul
     * @return boolean
     */
    public function resetAndBump($idMatkul)
    {
        $versi = $this->versi + 1;
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Silabus" = 0,
                    "Agenda" = 0,
                    "Project" = 0,
                    "Approval" = 0,
                    "Versi" = :versi
                WHERE
                    "KDMataKuliah" = :idMatkul'
            );
            $query->bindParam(':versi', $versi);
            $query->bindParam(':idMatkul', $idMatkul);
            $query->execute();
            $this->silabus = '0';
            $this->agenda = '0';
            $this->project = '0';
            $this->approval = '0';
            $this->versi = $versi;
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Update User Progress
     * @param  string $username
     */
    public function updateProgress($username)
    {
        $user = new Matkul();
        $matkul = $user->getMatkulByNIP($username);
        if (count($matkul) > 1) {
            foreach ($matkul as $key => $matkul_loop) {
                if (!$this->getRpsProgress($matkul_loop['KDMataKuliah'])) {
                    $this->createRpsForMatkul($matkul_loop['KDMataKuliah']);
                }
                $matkul[$key]['percentage'] = $this->getRpsProgress($matkul_loop['KDMataKuliah'])['percentage'];
                $matkul[$key]['approved'] = $this->getRpsProgress($matkul_loop['KDMataKuliah'])['approved'];
                $matkul[$key]['RPSDetails'] = $this->getRpsProgress($matkul_loop['KDMataKuliah'])['RPSDetails'];
            }
            $_SESSION['matkul'] = $matkul;
        } elseif (count($matkul) == 1) {
            if (!$this->getRpsProgress($matkul[0]['KDMataKuliah'])) {
                $this->createRpsForMatkul($matkul[0]['KDMataKuliah']);
            }
            $matkul[0]['percentage'] = $this->getRpsProgress($matkul[0]['KDMataKuliah'])['percentage'];
            $matkul[0]['approved'] = $this->getRpsProgress($matkul[0]['KDMataKuliah'])['approved'];
            $matkul[0]['RPSDetails'] = $this->getRpsProgress($matkul[0]['KDMataKuliah'])['RPSDetails'];
            $_SESSION['matkul'] = $matkul[0];
        }
    }
}
