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
     * getRpsByIdMatkul
     *
     * get RPS detail for the provided idMatkul
     * @param  string $idMatkul
     * @return boolean
     */
    public function getRpsByIdMatkul($idMatkul) {
        $results = false;
        $query = $this->core->db->prepare(
            'SELECT
                "Silabus",
                "Agenda",
                "Project",
                "KDMataKuliah",
                "KDApproval",
                TO_CHAR("Silabus_StartDate", \'YYYY-MM-DD HH24:MI:SS\') as "Silabus_StartDate",
                TO_CHAR("Agenda_StartDate", \'YYYY-MM-DD HH24:MI:SS\') as "Agenda_StartDate",
                TO_CHAR("Project_StartDate", \'YYYY-MM-DD HH24:MI:SS\') as "Project_StartDate",
                TO_CHAR("Silabus_LastEdit", \'YYYY-MM-DD HH24:MI:SS\') as "Silabus_LastEdit",
                TO_CHAR("Agenda_LastEdit", \'YYYY-MM-DD HH24:MI:SS\') as "Agenda_LastEdit",
                TO_CHAR("Project_LastEdit", \'YYYY-MM-DD HH24:MI:SS\') as "Project_LastEdit"
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
                $this->silabus = $result['Silabus'];
                $this->agenda = $result['Agenda'];
                $this->project = $result['Project'];
                $this->idMatkul = $result['KDMataKuliah'];
                $this->approval = $result['KDApproval'];
                $this->silabusStart = $result['Silabus_StartDate'];
                $this->agendaStart = $result['Agenda_StartDate'];
                $this->projectStart = $result['Project_StartDate'];
                $this->silabusLastEdit = $result['Silabus_LastEdit'];
                $this->agendaLastEdit = $result['Agenda_LastEdit'];
                $this->projectLastEdit = $result['Project_LastEdit'];
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
    public function createRpsForMatkul($idMatkul) {
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
    }

    /**
     * Get RPS Progress for the provided ID Matkul
     * @param  string $idMatkul
     * @return mixed
     */
    public function getRpsProgress($idMatkul) {
        $progress = array(
            'percentage' => 0,
            'approved' => false,
            'RPSDetails' => array(
                'silabus' => false,
                'agenda' => false,
                'project' => false,
            )
        );
        $percentage = 0;
        if ($this->getRpsByIdMatkul($idMatkul)) {
            if (!($this->silabus) == '0') {
                $progress['RPSDetails']['silabus'] = true;
                $percentage += 20;
            }
            if (!($this->agenda) == '0') {
                $progress['RPSDetails']['agenda'] = true;
                $percentage += 20;
            }
            if (!($this->project) == '0') {
                $progress['RPSDetails']['project'] = true;
                $percentage += 20;
            }
            if (!($this->approval) == '0') {
                $progress['approved'] = true;
                $percentage += 20;
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
    public function startSilabus($idMatkul) {
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
    public function editSilabus($idMatkul) {
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
    public function startAgenda($idMatkul) {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Agenda_StartDate" = :currDate
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
    public function editAgenda($idMatkul) {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Agenda_LastEdit" = :currDate
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
    public function startProject($idMatkul) {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Project_StartDate" = :currDate
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
    public function editProject($idMatkul) {
        $currDate = date('Y-m-d H:i:s');
        try {
            $query = $this->core->db->prepare(
                'UPDATE
                    RPS
                SET
                    "Project_LastEdit" = :currDate
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
}
