<?php

/**
 * PDFgen Class for SAR Application
 *
 * this file contains the PDF Generation function for SAR Application.
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @package SAR\helpers
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 */
namespace SAR\helpers;

use SAR\models;
use fpdf\FPDF_EXTENDED as PDF;

/**
* Pdfgen Class
* @package SAR\helpers
*/
class Pdfgen
{
    private $pdf;

    public function __construct($pageOrientation = 'P', $unit = 'mm', $paperSize = 'a4')
    {
        $this->pdf = new PDF($pageOrientation, $unit, $paperSize);
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

    public function render()
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('Arial', 'B', 16);
        $this->pdf->Cell(40, 10, 'Hello World');
        $this->pdf->output();
    }
}
