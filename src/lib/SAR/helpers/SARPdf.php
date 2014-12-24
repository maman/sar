<?php

/**
 * SARPdf Class for SAR Application
 *
 * this file extends the PDF Generation function from TCPDF
 * to enable custom header & footer for SAR Application.
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

/**
* SARPdf Class
* @package SAR\helpers
*/
class SARPdf extends \TCPDF
{
    // @codingStandardsIgnoreStart
    public function Header()
    {
    // @codingStandardsIgnoreEnd

    }

    // @codingStandardsIgnoreStart
    public function Footer()
    {
    // @codingStandardsIgnoreEnd
        if ($this->page == 1 || $this->page == 2) {
            $this->SetY(-25);
            $this->SetFont('helvetica', 'B', 10, '', false);
            $this->Cell(0, 15, 'SIFAT RAHASIA', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->SetY(-20);
            $this->SetFont('helvetica', '', 10, '', false);
            // @codingStandardsIgnoreStart
            $this->Cell(0, 15, 'Khusus diproduksi dan didistribusikan kepada', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            $this->SetY(-15);
            $this->Cell(0, 15, 'yang berhak mengetahui di lingkungan Jurusan Sistem Informasi Universitas Ma Chung', 0, false, 'C', 0, '', 0, false, 'M', 'M');
            // $this->writeHTMLCell(0, 10, '', '', '<center><strong>Sifat Rahasia</strong></center>', 0, 1, 0, true, '', true);
            // $this->writeHTMLCell(0, 10, '', '', '<center>Khusus diproduksi dan didistribusikan kepada</center>', 0, 1, 0, true, '', true);
            // $this->writeHTMLCell(0, 10, '', '', '<center>yang berhak mengetahui di lingkungan Jurusan Sistem Informasi Universitas Ma Chung</center>', 0, 1, 0, true, '', true);
            // @codingStandardsIgnoreEnd
        } elseif ($this->page == 3) {
            $this->SetY(-15);
            $this->SetFont('helvetica', '', 10, '', false);
            $this->Cell(0, 15, '<< TCPDF Silabus >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        } else {
            $this->SetY(-15);
            $this->SetFont('helvetica', '', 10, '', false);
            $this->Cell(0, 15, '<< TCPDF Example >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        }
    }
}
