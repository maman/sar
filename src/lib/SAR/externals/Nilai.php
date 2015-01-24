<?php

/**
 * Nilai Class for SAR Application
 *
 * this file contains the data access for Nilai objects.
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
namespace SAR\externals;

use Slim\Slim;
use alfmel\OCI8\PDO as OCI8;
use Functional as F;
use SAR\models\Agenda;
use SAR\models\SelfAssest;

/**
 * Nilai Class
 * @package SAR\models
 */
class Nilai
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
     * Get All Jawaban referenced by ID Soal
     * @param  string $idSoal
     * @return mixed
     */
    private function getAllJawabanBySoal($idSoal)
    {
        $query = $this->core->db->prepare('
            SELECT
                POINJAWABAN
            FROM
                JAWABAN
            WHERE
                IDSOAL = :idSoal
        ');
        $query->bindParam(':idSoal', $idSoal);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Poin soal from ID Soal
     * @param  string $idSoal
     * @return string
     */
    private function getSoalValue($idSoal)
    {
        $query = $this->core->db->prepare('
            SELECT
                POINSOAL
            FROM
                SOAL
            WHERE
                IDSOAL = :idSoal
        ');
        $query->bindParam(':idSoal', $idSoal);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) == 1) {
            return $results[0]['POINSOAL'];
        } else {
            return false;
        }
    }

    /**
     * Get All Soal by Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    private function getAllSoalByAgenda($idAgenda)
    {
        $query = $this->core->db->prepare('
            SELECT
                IDSOAL
            FROM
                SOAL
            WHERE
                ID_SUB_KOMPETENSI = :idAgenda
        ');
        $query->bindParam(':idAgenda', $idAgenda);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get All Poin Soal by Agenda ID
     * @param  string $idAgenda
     * @return mixed
     */
    private function getAllPoinSoalByAgenda($idAgenda)
    {
        $query = $this->core->db->prepare('
            SELECT
                POINSOAL
            FROM
                SOAL
            WHERE
                ID_SUB_KOMPETENSI = :idAgenda
        ');
        $query->bindParam(':idAgenda', $idAgenda);
        $query->execute();
        $results = $query->fetchAll(OCI8::FETCH_ASSOC);
        if (count($results) > 0) {
            return $results;
        } else {
            return false;
        }
    }

    /**
     * Get Average Jawaban for provided ID Soal
     * @param  string $idSoal
     * @return float
     */
    private function getAvgJawabanBySoal($idSoal)
    {
        $avgJawaban = 0;
        $jawaban = $this->getAllJawabanBySoal($idSoal);
        if ($jawaban) {
            $avgJawaban = F\average($jawaban);
            if ($avgJawaban > 0) {
                return $avgJawaban;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Get Sum Poin Soal for provided ID Agenda
     * @param  string $idAgenda
     * @return float
     */
    private function getSumSoalByAgenda($idAgenda)
    {
        $sumSoal = 0;
        $soal = $this->getAllPoinSoalByAgenda($idAgenda);
        if ($soal) {
            $sumSoal = F\sum($soal);
            if ($sumSoal > 0) {
                return $sumSoal;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Get Percentage of Nilai for provided ID Agenda
     * @param  string $idAgenda
     * @return float
     */
    public function getPercentageByAgenda($idAgenda)
    {
        $percentage = 0;
        $sumJawaban = 0;
        $sumSoal = 0;
        $jawaban = array();
        $soal = $this->getAllSoalByAgenda($idAgenda);
        if ($soal) {
            foreach ($soal as $keySoal => $valSoal) {
                $hasil = $this->getAvgJawabanBySoal($soal[$keySoal]['IDSOAL']);
                array_push($jawaban, $hasil);
            }
            $sumJawaban = F\sum($jawaban);
        }
        $sumSoal = $this->getSumSoalByAgenda($idAgenda);
        if ($sumJawaban == 0 || $sumSoal == 0) {
            $percentage = 0;
        } else {
            $percentage = ($sumJawaban/$sumSoal)*100;
        }
        return $percentage;
    }

    /**
     * Get total percentage by Matkul ID
     * @param  string    $idMatkul
     * @param  date('Y') $year OPTIONAL
     * @return float
     */
    public function getTotalPercentageByMatkul($idMatkul, $year = null)
    {
        $sar = new SelfAssest();
        $agenda = new Agenda();
        $agendaTemp = array();
        $totalTemp = array();
        $ifTemp = 0;
        $total = 0;
        if ($year === null) {
            $agendas = $agenda->getAgendaByMatkul($idMatkul);
        } else {
            $agendas = $agenda->getAgendaByMatkul($idMatkul, $year);
        }
        if ($agendas) {
            foreach ($agendas as $keyAgenda => $valAgenda) {
                array_push($agendaTemp, array(
                    'ID_SUB_KOMPETENSI' => $agendas[$keyAgenda]['ID_SUB_KOMPETENSI'],
                    'NAMA_SAR' => $sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['NAMA_SAR'],
                    'PERSENTASE_SAR' => ($sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['PERSENTASE'] == '0' || $sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['PERSENTASE'] == '' ? '0': $sar->getSARByAgenda($agendas[$keyAgenda]['ID_SUB_KOMPETENSI'])['PERSENTASE'])
                ));
            }
            foreach ($agendaTemp as $tempKey => $tempVal) {
                $agendaTemp[$tempKey]['PERSENTASE_NILAI'] = $this->getPercentageByAgenda($agendaTemp[$tempKey]['ID_SUB_KOMPETENSI']);
                array_push($totalTemp, $agendaTemp[$tempKey]['PERSENTASE_NILAI'] + $agendaTemp[$tempKey]['PERSENTASE_SAR']);
            }
            $ifTemp = F\sum($totalTemp);
            if ($ifTemp = 0) {
                return $total;
            } else {
                $total = F\sum($totalTemp) / count($totalTemp);
                return round($total, 0, PHP_ROUND_HALF_EVEN);
            }
        } else {
            return 0;
        }
    }
}
