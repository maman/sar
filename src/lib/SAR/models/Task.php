<?php

/**
 * Task Class for SAR Application
 *
 * this file contains the data access for Task objects.
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
use SAR\models\Agenda;

/**
 * Task Class
 *
 * @package SAR\models
 */
class Task
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
     * Get All Scope for provided $idAktivitas
     * @param  string $idAktivitas
     * @return mixed
     */
    public function getScopeByAktivitas($idAktivitas)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                SCOPE
            WHERE
                SCOPE.ID_AKTIVITAS_AGENDA = :idAktivitas
            ORDER BY
                SCOPE.ID_SCOPE ASC'
        );
        $query->bindParam(':idAktivitas', $idAktivitas);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get All Metode for provided $idAktivitas
     * @param  string $idAktivitas
     * @return mixed
     */
    public function getMetodeByAktivitas($idAktivitas)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                METODE
            WHERE
                METODE.ID_AKTIVITAS_AGENDA = :idAktivitas
            ORDER BY
                METODE.ID_METODE ASC'
        );
        $query->bindParam(':idAktivitas', $idAktivitas);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get All Kriteria for provided $idAktivitas
     * @param  string $idAktivitas
     * @return mixed
     */
    public function getKriteriaByAktivitas($idAktivitas)
    {
        $query = $this->core->db->prepare(
            'SELECT
                *
            FROM
                KRITERIA
            WHERE
                KRITERIA.ID_AKTIVITAS_AGENDA = :idAktivitas
            ORDER BY
                KRITERIA.ID_KRITERIA ASC'
        );
        $query->bindParam(':idAktivitas', $idAktivitas);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Aktivitas Detail for the provided $idMatkul
     * @param  string $idMatkul
     * @return mixed
     */
    public function getDetailAktivitasByMatkul($idMatkul)
    {
        $agenda = new Agenda();
        $results = $agenda->getAgendaByMatkul($idMatkul);
        if ($results) {
            foreach ($results as $key => $value) {
                unset($results[$key]['TEXT_MATERI_BELAJAR']);
                unset($results[$key]['BOBOT']);
                unset($results[$key]['UNIQUE_INDIKATOR']);
                unset($results[$key]['INDIKATOR']);
                unset($results[$key]['ASESMEN']);
                if ($results[$key]['AKTIVITAS']) {
                    foreach ($results[$key]['AKTIVITAS'] as $keyAct => $valueAct) {
                        if ($results[$key]['AKTIVITAS'][$keyAct]['TASK'] == '0' && $results[$key]['AKTIVITAS'][$keyAct]['PROJECT'] == '0') {
                            unset($results[$key]['AKTIVITAS'][$keyAct]);
                        } else {
                            $results[$key]['AKTIVITAS'][$keyAct]['SCOPE'] = $this->getScopeByAktivitas($results[$key]['AKTIVITAS'][$keyAct]['ID_AKTIVITAS_AGENDA']);
                            $results[$key]['AKTIVITAS'][$keyAct]['METODE'] = $this->getMetodeByAktivitas($results[$key]['AKTIVITAS'][$keyAct]['ID_AKTIVITAS_AGENDA']);
                            $results[$key]['AKTIVITAS'][$keyAct]['KRITERIA'] = $this->getKriteriaByAktivitas($results[$key]['AKTIVITAS'][$keyAct]['ID_AKTIVITAS_AGENDA']);
                        }
                    }
                    return $results;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function saveScope($idAktivitas, $txtScope)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    SCOPE
                (
                    ID_AKTIVITAS_AGENDA,
                    TEXT_SCOPE
                )
                VALUES
                (
                    :idAktivitas,
                    :txtScope
                )'
            );
            $query->bindParam(':idAktivitas', $idAktivitas);
            $query->bindParam(':txtScope', $txtScope);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteScopeById($idScope)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    SCOPE
                WHERE
                    ID_SCOPE = :idScope'
            );
            $query->bindParam(':idScope', $idScope);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    public function deleteScopeByAktivitasId($idAktivitas)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    SCOPE
                WHERE
                    ID_AKTIVITAS_AGENDA = :idAktivitas'
            );
            $query->bindParam(':idAktivitas', $idAktivitas);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    public function saveMetode($idAktivitas, $txtMetode)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    METODE
                (
                    ID_AKTIVITAS_AGENDA,
                    TEXT_METODE
                )
                VALUES
                (
                    :idAktivitas,
                    :txtMetode
                )'
            );
            $query->bindParam(':idAktivitas', $idAktivitas);
            $query->bindParam(':txtMetode', $txtMetode);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteMetodeById($idMetode)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    METODE
                WHERE
                    ID_METODE = :idMetode'
            );
            $query->bindParam(':idMetode', $idMetode);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    public function deleteMetodeByAktivitasId($idAktivitas)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    METODE
                WHERE
                    ID_AKTIVITAS_AGENDA = :idAktivitas'
            );
            $query->bindParam(':idAktivitas', $idAktivitas);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    public function saveKriteria($idAktivitas, $txtKriteria)
    {
        try {
            $query = $this->core->db->prepare(
                'INSERT INTO
                    KRITERIA
                (
                    ID_AKTIVITAS_AGENDA,
                    TEXT_KRITERIA
                )
                VALUES
                (
                    :idAktivitas,
                    :txtKriteria
                )'
            );
            $query->bindParam(':idAktivitas', $idAktivitas);
            $query->bindParam(':txtKriteria', $txtKriteria);
            $query->execute();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteKriteriaById($idKriteria)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    KRITERIA
                WHERE
                    ID_KRITERIA = :idKriteria'
            );
            $query->bindParam(':idKriteria', $idKriteria);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }

    public function deleteKriteriaByAktivitasId($idAktivitas)
    {
        try {
            $query = $this->core->db->prepare(
                'DELETE FROM
                    KRITERIA
                WHERE
                    ID_AKTIVITAS_AGENDA = :idAktivitas'
            );
            $query->bindParam(':idAktivitas', $idAktivitas);
            $query->execute();
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }
}
