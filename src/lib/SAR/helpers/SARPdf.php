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

use SAR\models\User;
use SAR\models\Approval;
use SAR\models\Kategori;

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
            // @codingStandardsIgnoreEnd
        } elseif ($this->page == 3) {
            $approval = new Approval;
            $kategori = new Kategori;
            $user = new User;
            $approval->getApprovalByIdMatkul($this->idMatkul);
            $allKategori = $kategori->getAllKategori();
            $this->SetY(-25);
            $this->SetFont('helvetica', '', 6, '', false);
            $this->setListIndentWidth(0);
            $table =
            '<table border="1" width="100%">
                <thead>
                    <tr style="background-color:#d0d0d0">
                        <td width="17.5%"><strong>Dokumen</strong></td>
                        <td width="17.5%"><strong>Dibuat</strong></td>
                        <td width="17.5%"><strong>Diperiksa</strong></td>
                        <td width="17.5%"><strong>Disahkan</strong></td>
                        <td width="30%"><strong>Keterangan</strong></td>
                    </tr>
                </thead>
                <tr>
                    <td width="17.5%">Nomer: <br><strong>' . $approval->idApproval . '</strong></td>
                    <td width="17.5%">Tanggal: <br><strong>' . $approval->tglMasuk . '</strong></td>
                    <td width="17.5%">Tanggal: <br><strong>' . $approval->tglPeriksa . '</strong></td>
                    <td width="17.5%">Tanggal: <br><strong>' . $approval->tglDisahkan . '</strong></td>
                    <td width="30%" rowspan="2"><ul style="list-style-type:none;margin:0;padding:0">';
            foreach ($allKategori as $item) {
                // @codingStandardsIgnoreStart
                $table .= '<li>' . $item['ID_KATEGORI_KOMPETENSI'] . '  ' . $item['NAMA_KATEGORI_KOMPETENSI'] . '</li>';
                // @codingStandardsIgnoreEnd
            }
            $table .=
                    '</ul></td>
                </tr>
                <tr>
                    <td width="17.5%">Revisi: <br><br><strong>' . $approval->versi . '</strong></td>
                    <td width="17.5%">Oleh: <br><br><strong>' . $user->getUserName($approval->nip) . '</strong></td>
                    <td width="17.5%">Oleh: <br><br><strong>' . $user->getUserName($approval->nipPeriksa) . '</strong></td>
                    <td width="17.5%">Oleh: <br><br><strong>' . $user->getUserName($approval->nipSahkan) . '</strong></td>
                </tr>
            </table>';
            $this->writeHTMLCell(0, 0, '', '', $table, 0, 1, 0, true, '', true);
        } else {
            $approval = new Approval;
            $kategori = new Kategori;
            $user = new User;
            $approval->getApprovalByIdMatkul($this->idMatkul);
            $allKategori = $kategori->getAllKategori();
            $this->SetY(-25);
            $this->SetFont('helvetica', '', 6, '', false);
            $this->setListIndentWidth(0);
            $table =
            '<table border="1" width="100%">
                <thead>
                    <tr style="background-color:#d0d0d0">
                        <td width="17.5%"><strong>Dokumen</strong></td>
                        <td width="17.5%"><strong>Dibuat</strong></td>
                        <td width="17.5%"><strong>Diperiksa</strong></td>
                        <td width="17.5%"><strong>Disahkan</strong></td>
                        <td width="30%" colspan="' . count($this->groupKategori) . '">
                            <strong>Keterangan: ';
            foreach ($this->groupKategori as $groupKey => $groupVal) {
                $table .= $groupKey . ', ';
            }
            $table .=
                            '</strong></td>
                    </tr>
                </thead>
                <tr>
                    <td width="17.5%">Nomer: <br><strong>' . $approval->idApproval . '</strong></td>
                    <td width="17.5%">Tanggal: <br><strong>' . $approval->tglMasuk . '</strong></td>
                    <td width="17.5%">Tanggal: <br><strong>' . $approval->tglPeriksa . '</strong></td>
                    <td width="17.5%">Tanggal: <br><strong>' . $approval->tglDisahkan . '</strong></td>';
            foreach ($this->groupKategori as $groupKey => $groupVal) {
                $table .=
                    '<td width="' . 30/count($this->groupKategori) . '%" rowspan="2">';
                foreach ($groupVal as $kategori) {
                    $table .= $kategori['ID_KETERANGAN'] . ': ' . $kategori['NAMA'] . '<br>';
                }
                $table .=
                    '</td>';
            }
            $table .=
                '</tr>
                <tr>
                    <td width="17.5%">Revisi: <br><br><strong>' . $approval->versi . '</strong></td>
                    <td width="17.5%">Oleh: <br><br><strong>' . $user->getUserName($approval->nip) . '</strong></td>
                    <td width="17.5%">Oleh: <br><br><strong>' . $user->getUserName($approval->nipPeriksa) . '</strong></td>
                    <td width="17.5%">Oleh: <br><br><strong>' . $user->getUserName($approval->nipSahkan) . '</strong></td>
                </tr>
            </table>';
            $this->writeHTMLCell(0, 0, '', '', $table, 0, 1, 0, true, '', true);
        }
    }
}
