<?php

/**
 * Generate router for SAR Application
 *
 * this file contains route definition and logic for `/generate` route, which
 * responsible to exporting the data inside SAR application into a easy-to-read
 * format, such as PDF. Please note that this file is literally a huge know-it-all
 * php file, and still unoptimized.
 *
 * PHP version 5.4
 *
 * LICENSE: This source file is subject to version 2 of the GNU General Public
 * License that is avalaible in the LICENSE file on the project root directory.
 * If you did not receive a copy of the LICENSE file, please send a note to
 * 321110001@student.machung.ac.id so I can mail you a copy immidiately.
 *
 * @author Achmad Mahardi <321110001@student.machung.ac.id>
 * @copyright 2014 Achmad Mahardi
 * @license GNU General Public License v2
 */

use SAR\helpers\SARPdf;
use SAR\models\Silabus;
use SAR\models\Matkul;
use SAR\models\User;
use SAR\models\Agenda;
use SAR\models\Kategori;
use SAR\models\Rps;
use SAR\models\Task;
use SAR\models\Approval;

/**
 * -*-*-*- BEWARE -*-*-*-
 * HUGE SHITLOADS OF CODE.
 */
$app->get('/generate/:idMatkul/pdf', function ($idMatkul) use ($app) {
    // Send PDF Directly to browser A.K.A download
    $app->response->headers->set('Content-Type', 'application/pdf');
    $app->response->headers->set('Content-Transfer-Encoding', 'binary');

    $matkul = new Matkul();
    $user = new User();
    $kategori = new Kategori();
    $agenda = new Agenda();
    $rps = new Rps();
    $task = new Task();
    $approval = new Approval();
    $silabus = new Silabus($idMatkul);

    $rps->getRpsByIdMatkul($idMatkul);
    $allKategori = $kategori->getAllKategoriIndikator();
    $groupKategori = $kategori->groupKategoriIndikator();
    $countKategori = count($allKategori);
    $kompetensi = $silabus->kompetensi;
    $matkuls = $matkul->getMatkulDetails($idMatkul);
    $revs = $approval->getAllApprovalByMatkul($idMatkul);
    $agendaSimple = $agenda->getAgendaByMatkul($idMatkul);
    $agendas = $agenda->getAgendaByMatkul($idMatkul, 'verbose');
    $approvals = $approval->getApprovalByIdMatkul($idMatkul);
    $tasks = $task->getDetailAktivitasByMatkul($idMatkul);
    $dosen = $user->getUserFromMatkul($idMatkul);

    $creator = 'Ma Chung Silabus & SAR Management System/TCPDFv6.0';
    $author = 'Ma Chung Silabus & SAR Management System';
    $title = 'RPS Mata Kuliah ' . $matkuls[0]['NamaMK'] . ' ' . $matkuls[0]['TahunAjaranMK'];

    // We use TCPDF to render.
    $pdf = new SARPdf();
    $pdf->SetCreator($creator);
    $pdf->SetAuthor($author);
    $pdf->SetTitle($title);
    $pdf->SetMargins('20', '20', '20', true);
    $pdf->SetAutoPageBreak(true);

    // Passing Variables
    $pdf->idMatkul = $idMatkul;
    $pdf->groupKategori = $groupKategori;
    $pdf->countKategori = $countKategori;

    // Cover
    $pdf->phase = 'cover';
    $pdf->setPrintHeader(false);
    $pdf->AddPage('P', 'A4');
    $pdf->SetFont('helvetica', 'B', 14, '', false);
    $pdf->SetY(20);
    $pdf->Cell(0, 15, 'Rencana Pembelajaran Mata Kuliah', 0, false, 'L', 0, '', 0, false, 'M', 'M');
    $pdf->SetY(28);
    $pdf->SetFont('helvetica', 'B', 20, '', false);
    $pdf->Cell(0, 15, $matkuls[0]['NamaMK'], 0, false, 'L', 0, '', 0, false, 'M', 'M');
    $pdf->SetY(40);
    $pdf->SetFont('helvetica', '', 10, '', false);
    $pdf->Cell(0, 15, $approval->tglDisahkan . ' Release ' . $approval->versi, 0, false, 'L', 0, '', 0, false, 'M', 'M');
    $pdf->SetY(48);
    $pdf->writeHTMLCell(0, 10, '', '', '<strong>Disiapkan:</strong>', 0, 1, 0, true, '', true);
    $listDosen =
    '<p>';
    foreach ($dosen as $user) {
        $listDosen .=
        '<span>' . $user['NAMA'] . ',</span>';
    }
    $listDosen .= ' Sebagai kelengkapan <em>proses belajar mengajar</em> di Jurusan Sistem Informasi Universitas Ma Chung</p>';
    $pdf->writeHTMLCell(0, 10, '', '', $listDosen, 0, 1, 0, true, '', true);

    // Maklumat
    $pdf->setPrintHeader(false);
    $pdf->AddPage('P', 'A4');
    $pdf->SetFont('helvetica', '', 10, '', false);
    $pdf->writeHTMLCell(0, 25, '', '', '<h2>Maklumat Release Dokumen</h2>', 0, 1, 0, true, '', true);
    $pdf->MultiCell(0, 15, 'Seluruh release dari dokumen ini didaftar berdasar kronologisnya', 0, 'C', 0, 1, '', '', true, null, false);
    $revtable =
    '<table border="1" width="100%">
        <thead>
            <tr style="background-color:#d0d0d0">
                <td width="15%" align="center"><strong>Revisi Dokumen</strong></td>
                <td width="15%" align="center"><strong>Tanggal</strong></td>
                <td width="70%" align="center"><strong>Alasan Perubahan</strong></td>
            </tr>
        </thead>';
    foreach ($revs as $rev) {
        if ($rev['Approval'] == '1') {
            $currRev = $rev['Versi'];
            $revtable .=
            '<tr>
                <td width="15%">Revisi ' . $currRev . '</td>
                <td width="15%">' . $rev['TglPeriksa'] . '</td>
                <td width="70%">' . $rev['NotePeriksa'] . '</td>
            </tr>';
        }
    }
    $revtable .=
    '</table>';
    $pdf->writeHTMLCell(0, 0, '', '', $revtable, 0, 1, 0, true, '', true);
    $pdf->setY(180);
    $pdf->MultiCell(0, 0, 'Dokumen ini dibuat oleh Tim Teaching dengan pengawasan dari Jurusan Sistem Informasi Universitas Ma Chung sebagai upaya untuk menjamin keakurasian dokumen saat akan dicetak. Penggandaan dokumen, sebaiknya dari release yang terakhir (up to date) dan setelah mendapatkan ijin tertulis.', 0, 'L', 0, 1, '', '', true, null, false);
    $pdf->setY(200);
    $pdf->MultiCell(0, 0, 'Ketua Jurusan Sistem Informasi
Fakultas Sains dan Teknologi
Universitas Ma Chung
Malang', 0, 'L', 0, 1, '', '', true, null, false);
    $pdf->setY(225);
    $pdf->MultiCell(0, 0, 'Copyright © ' . date('Y') . ' Jurusan Sistem Informasi Universitas Ma Chung
Seluruh informasinya adalah hak milik Jurusan Sistem Informasi Universitas Ma Chung yang tidak dipublikasikan dan bersifat rahasia.', 0, 'L', 0, 1, '', '', true, null, false);

    // Silabus
    $pdf->phase = 'silabus';
    $pdf->setPrintHeader(false);
    $pdf->AddPage('P', 'A4');
    $pdf->SetFont('helvetica', '', 10, '', false);
    $pdf->writeHTMLCell(0, 8, '', '', '<h2>Silabus Kurikulum</h2>', 0, 1, 0, true, '', true);
    $pdf->SetFont('helvetica', '', 14, '', false);
    $pdf->writeHTMLCell(0, 15, '', '', '<h3>' . $matkuls[0]['NamaMK'] . ', 6SKS</h3>', 0, 1, 0, true, '', true);
    $pdf->setCellHeightRatio(2.5);
    $sill =
    '<table width="100%">
        <tr>
            <td width="20%"><strong><u>Semester</u></strong></td>
            <td width="3%">:</td>
            <td width="77%">Ganjil 2014</td>
        </tr>
        <tr>
            <td width="20%"><strong><u>Tujuan</u></strong></td>
            <td width="3%">:</td>
            <td width="77%">' . $silabus->tujuan . '</td>
        </tr>
        <tr>
            <td width="20%"><strong><u>Kompetensi</u></strong></td>
            <td width="3%">:</td>
            <td width="77%"></td>
        </tr>
        <tr>
            <td width="100%" colspan="2"><ol>';
    foreach ($silabus->kompetensi as $kompetensi) {
        $sill .=
        '           <li>
        ';
        $sill .= $kompetensi['NAMA_KOMPETENSI'] . ' (';
        foreach ($kompetensi['KATEGORI_KOMPETENSI'] as $kategori) {
            if ($kategori['SELECTED'] === true) {
                $sill .= $kategori['ID_KATEGORI_KOMPETENSI'] . ',';
            }
        }
        $sill .= ')
                    </li>
        ';
    }
        $sill .=
        '   </ol></td>
        </tr>
        <tr>
            <td width="20%"><strong><u>Pokok Bahasan</u></strong></td>
            <td width="3%">:</td>
            <td width="77%"></td>
        </tr>
        <tr>
            <td width="100%" colspan="2">' . $silabus->pokokBahasan . '</td>
        </tr>
        <tr>
            <td width="20%"><strong><u>Daftar Pustaka</u></strong></td>
            <td width="3%">:</td>
            <td width="100%"></td>
        </tr>
        <tr>
            <td width="100%" colspan="2"><ol>';
    foreach ($silabus->pustaka as $pustaka) {
        $sill .= '<li><strong>' . $pustaka['PENGARANG_PUSTAKA'] . ', </strong><em>' . $pustaka['JUDUL_PUSTAKA'] . '</em>, ' . $pustaka['PENERBIT_PUSTAKA'] . ', ' . $pustaka['TAHUN_TERBIT_PUSTAKA'] . '</li>';
    }
        $sill .=
        '   </ol></td>
        </tr>
        <tr>
            <td width="20%"><strong><u>Team Teaching</u></strong></td>
            <td width="3%">:</td>
            <td width="77%"></td>
        </tr>
        <tr>
            <td width="100%" colspan="2"><ol>';
    foreach ($dosen as $user) {
        $sill .= '<li>' . $user['NAMA'] . '</li>';
    }
        $sill .=
        '   </ol></td>
        </tr>
    </table>';
    $pdf->SetFont('helvetica', '', 10, '', false);
    $pdf->setListIndentWidth(3.15);
    $pdf->writeHTMLCell(0, 0, '', '', $sill, 0, 1, 0, true, '', true);

    // Agenda
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    $pdf->AddPage('L', 'A4');
    $pdf->setPageOrientation('L', true, '60');
    $pdf->setCellHeightRatio(1.25);
    $agenda =
    '<table border="1" width="100%">
        <thead>
            <tr style="background-color:#d0d0d0">
                <td rowspan="2" align="center" width="5%">
                    <strong><span></span>Pertemuan Ke</strong>
                </td>
                <td rowspan="2" align="center" width="20%">
                    <strong><span></span>Sub-Kompetensi</strong>
                </td>
                <td rowspan="2" align="center" width="20%">
                    <strong><span></span>Pustaka</strong>
                </td>
                <td rowspan="2" align="center" width="20%">
                    <strong><span></span>Indikator Pencapaian</strong>
                </td>
                <td rowspan="2" align="center" width="20%">
                    <strong><span></span>Aktivitas Pembelajaran</strong>
                </td>
                <td colspan="2" align="center" width="15%">
                    <strong><span></span>Assesmen</strong>
                </td>
            </tr>
            <tr style="background-color:#d0d0d0">
                <td align="center" width="10%">
                    <strong><span></span>Bentuk/Unsur</strong>
                </td>
                <td align="center" width="5%">
                    <strong><span></span>Bobot</strong>
                </td>
            </tr>
        </thead>';
    foreach ($agendaSimple as $item) {
        $agenda .=
        '<tr nobr="true">
            <td align="center" width="5%">' . $item['RANGE_PERTEMUAN'] . '</td>
            <td width="20%">' . $item['TEXT_SUB_KOMPETENSI'] . '</td>
            <td width="20%">' . $item['TEXT_MATERI_BELAJAR'] . '</td>
            <td width="20%">
                <ul>';
        foreach ($item['INDIKATOR'] as $indikator) {
            $agenda .=
            '<li>' . $indikator['TEXT_INDIKATOR'] . '(' . $indikator['INDIKATOR'][0]['ID_KETERANGAN'] . ')</li>';
        }
            $agenda .=
            '   </ul>
            </td>
            <td width="20%">
                <ul>';
        foreach ($item['AKTIVITAS'] as $aktivitas) {
            if ($aktivitas['TASK'] == '1') {
                $agenda .= '<li><strong>Task: </strong>';
            } elseif ($aktivitas['PROJECT'] == '1') {
                $agenda .= '<li><strong>Project: </strong>';
            } else {
                $agenda .= '<li>';
            }
            $agenda .= $aktivitas['TEXT_AKTIVITAS_AGENDA'] . '</li>';
        }
            $agenda .=
            '   </ul>
            </td>
            <td width="10%">';
        if (is_array($item['ASESMEN'])) {
            foreach ($item['ASESMEN'] as $asesmenKey => $asesmenVal) {
                if ($asesmenKey == 'tes' && count($asesmenVal) > 0) {
                    $agenda .= 'Tes :
                    <ul>';
                    foreach ($asesmenVal as $tes) {
                        $agenda .= '<li>' . $tes['NAMA_ASSESMENT_SUB_KOMPETENSI'] . '</li>';
                    }
                    $agenda .=
                    '</ul>';
                } elseif ($asesmenKey == 'nontes' && count($asesmenVal) > 0) {
                    $agenda .= 'Non Tes:
                    <ul>';
                    foreach ($asesmenVal as $nontes) {
                        $agenda .= '<li>' . $nontes['NAMA_ASSESMENT_SUB_KOMPETENSI'] . '</li>';
                    }
                    $agenda .=
                    '</ul>';
                }
            }
        }
            $agenda .=
            '</td>
            <td align="center" width="5%">' . $item['BOBOT'] . '</td>
        </tr>';
    }
    $agenda .=
    '</table>';
    $pdf->SetFont('helvetica', '', 10, '', false);
    $pdf->writeHTMLCell(0, 8, '', '', '<h2>Agenda Pembelajaran</h2>', 0, 1, 0, true, '', true);
    $pdf->SetFont('helvetica', '', 14, '', false);
    $pdf->writeHTMLCell(0, 10, '', '', '<h3>' . $matkuls[0]['NamaMK'] . ', 6SKS</h3>', 0, 1, 0, true, '', true);
    $pdf->SetFont('dejavusans', '', 7, '', false);
    $pdf->setListIndentWidth(3.15);
    $pdf->writeHTMLCell(0, 0, '', '', $agenda, 0, 1, 0, true, '', true);

    // Evaluasi
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    $pdf->AddPage('L', 'A4');
    $pdf->setPageOrientation('L', true, '60');
    $pdf->setCellHeightRatio(1.25);
    $evaluasi =
    '<table border="1" width="100%">
        <thead>
            <tr style="background-color:#d0d0d0">
                <td rowspan="3" align="center" width="5%">
                    <strong><span></span>Pertemuan Ke</strong>
                </td>
                <td rowspan="3" align="center" width="20%">
                    <strong><span></span>Specific Learning Objective <br> (Sub-Kompetensi)</strong>
                </td>
                <td colspan="' . $countKategori . '" align="center" width="50%">
                    <strong>Elemen Kompetensi dalam Asesmen</strong>
                </td>
                <td rowspan="3" align="center" width="20%">
                    <strong><span style="font-size:5px">'. str_repeat('&nbsp;<br>', 1) .'</span>Bentuk Asesmen</strong>
                </td>
                <td rowspan="3" align="center" width="5%">
                    <strong><span style="font-size:5px">'. str_repeat('&nbsp;<br>', 1) .'</span>%</strong>
                </td>
            </tr>
            <tr style="background-color:#d0d0d0">';
    foreach ($groupKategori as $key => $value) {
        $evaluasi .=
                '<td colspan="' . $value['LENGTH'] . '" align="center"><strong>' . $key . '</strong></td>';
    }
    $evaluasi .=
            '</tr>
            <tr style="background-color:#d0d0d0">';
    foreach ($groupKategori as $item) {
        foreach ($item as $keyItem => $valueItem) {
            if (is_array($valueItem)) {
                $evaluasi .=
                '<td align="center" width="' . 50/$countKategori . '%">' . $valueItem['ID_KETERANGAN'] . '</td>';
            }
        }
    }
    $evaluasi .=
            '</tr>
        </thead>';
    foreach ($agendas as $itemAgenda) {
        $evaluasi .=
        '<tr width="100%">
            <td align="center" width="5%">' . $itemAgenda['RANGE_PERTEMUAN'] . '</td>
            <td width="20%">' . $itemAgenda['TEXT_SUB_KOMPETENSI'] . '</td>';
        foreach ($itemAgenda['UNIQUE_INDIKATOR'] as $kategori) {
            foreach ($kategori as $keyKategori => $valueKategori) {
                if (is_array($valueKategori)) {
                    $evaluasi .= '<td align="center" width="' . 50/$countKategori . '%">';
                    if ($valueKategori['SELECTED'] === true) {
                        $evaluasi .= '✔';
                    }
                    $evaluasi .= '</td>';
                }
            }
        }
        $evaluasi .=
            '<td width="20%">';
        if (!empty($itemAgenda['ASESMAN']['tes'])) {
            $evaluasi .=
                '<strong>Tes</strong>:
                <ul style="padding-top:0;">';
            foreach ($itemAgenda['ASESMEN']['tes'] as $asesmenTes) {
                $evaluasi .=
                    '<li>' . $asesmenTes['NAMA_ASSESMENT_SUB_KOMPETENSI'] . '</li>';
            }
            $evaluasi .=
                '</ul>';
        }
        if (!empty($itemAgenda['ASESMEN']['nontes'])) {
            $evaluasi .=
                '<strong>Non Tes</strong>:
                <ul style="padding-top:0;">';
            foreach ($itemAgenda['ASESMEN']['nontes'] as $asesmenNontes) {
                $evaluasi .=
                    '<li>' . $asesmenNontes['NAMA_ASSESMENT_SUB_KOMPETENSI'] . '</li>';
            }
            $evaluasi .=
                '</ul>';
        }
        $evaluasi .=
            '</td>
            <td align="center" width="5%">' . $itemAgenda['BOBOT'] . '</td>
        </tr>';
    }
    $evaluasi .= '</table>';
    $pdf->SetFont('helvetica', '', 10, '', false);
    $pdf->writeHTMLCell(0, 8, '', '', '<h2>Rencana Evaluasi Pembelajaran</h2>', 0, 1, 0, true, '', true);
    $pdf->SetFont('helvetica', '', 14, '', false);
    $pdf->writeHTMLCell(0, 10, '', '', '<h3>Kertakes, 6SKS</h3>', 0, 1, 0, true, '', true);
    $pdf->SetFont('dejavusans', '', 7, '', false);
    $pdf->setListIndentWidth(3.15);
    $pdf->writeHTMLCell(0, 0, '', '', $evaluasi, 0, 1, 0, true, '', true);

    // Task/Project
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(true);
    $pdf->AddPage('L', 'A4');
    $pdf->setPageOrientation('L', true, '60');
    $pdf->setCellHeightRatio(2.5);
    $taskpro =
    '<table border="1" width="100%">
        <thead>
            <tr style="background-color:#d0d0d0">
                <td width="5%" align="center"><strong>Pertemuan Ke</strong></td>
                <td width="19%" align="center"><strong>Sub Kompetensi</strong></td>
                <td width="19%" align="center"><strong>Task/Project</strong></td>
                <td width="19%" align="center"><strong>Scope Task/Project</strong></td>
                <td width="19%" align="center"><strong>Metode Pengerjaan Task/Project</strong></td>
                <td width="19%" align="center"><strong>Kriteria Luaran</strong></td>
            </tr>
        </thead>';
    foreach ($tasks as $task) {
        $counter = false;
        foreach ($task['AKTIVITAS'] as $aktivitas) {
            $taskpro .=
            '<tr>';
            if ($counter === false) {
                $taskpro .=
                '<td width="5%" rowspan="' . count($task['AKTIVITAS']) . '">' . $task['RANGE_PERTEMUAN'] . '</td>
                <td width="19%" rowspan="' . count($task['AKTIVITAS']) . '">' . $task['TEXT_SUB_KOMPETENSI'] . '</td>';
            }
            $taskpro .=
                '<td width="19%">' . $aktivitas['TEXT_AKTIVITAS_AGENDA'] . '</td>
                <td width="19%">';
            if (is_array($aktivitas['SCOPE'])) {
                $taskpro .=
                    '<ul>';
                foreach ($aktivitas['SCOPE'] as $scope) {
                    $taskpro .= '<li>' . $scope['TEXT_SCOPE'] . '</li>';
                }
                $taskpro .=
                    '</ul>';
            }
            $taskpro .=
                '</td>
                <td width="19%">';
            if (is_array($aktivitas['METODE'])) {
                $taskpro .=
                    '<ul>';
                foreach ($aktivitas['METODE'] as $metode) {
                    $taskpro .= '<li>' . $metode['TEXT_METODE'] . '</li>';
                }
                $taskpro .=
                    '</ul>';
            }
            $taskpro .=
                '</td>
                <td width="19%">';
            if (is_array($aktivitas['KRITERIA'])) {
                $taskpro .=
                    '<ul>';
                foreach ($aktivitas['KRITERIA'] as $kriteria) {
                    $taskpro .= '<li>' . $kriteria['TEXT_KRITERIA'] . '</li>';
                }
                $taskpro .=
                    '</ul>';
            }
            $taskpro .=
                '</td>
            </tr>';
            $counter = true;
        }
    }
    $taskpro .=
    '</table>';
    $pdf->SetFont('helvetica', '', 10, '', false);
    $pdf->writeHTMLCell(0, 8, '', '', '<h2>Rencana Task/Project Pembelajaran</h2>', 0, 1, 0, true, '', true);
    $pdf->SetFont('helvetica', '', 14, '', false);
    $pdf->writeHTMLCell(0, 10, '', '', '<h3>' . $matkuls[0]['NamaMK'] . ', 6SKS</h3>', 0, 1, 0, true, '', true);
    $pdf->SetFont('dejavusans', '', 7, '', false);
    $pdf->setListIndentWidth(3.15);
    $pdf->writeHTMLCell(0, 0, '', '', $taskpro, 0, 1, 0, true, '', true);

    // Uraian Task/Project
    foreach ($tasks as $task) {
        foreach ($task['AKTIVITAS'] as $aktivitas) {
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(true);
            $pdf->AddPage('P', 'A4');
            $pdf->setPageOrientation('P', true, '60');
            $pdf->setCellHeightRatio(2.5);
            $pdf->SetFont('helvetica', '', 10, '', false);
            $pdf->writeHTMLCell(0, 8, '', '', '<h2>Uraian Task/Project</h2>', 0, 1, 0, true, '', true);
            $pdf->SetFont('helvetica', '', 14, '', false);
            $pdf->writeHTMLCell(0, 10, '', '', '<h3>' . $matkuls[0]['NamaMK'] . ', 6SKS</h3>', 0, 1, 0, true, '', true);
            $uraian =
            '<table width="100%">
                <tr>
                    <td width="20%"><strong><u>Task/Project</u></strong></td>
                    <td width="3%">:</td>
                    <td width="77%">' . $aktivitas['TEXT_AKTIVITAS_AGENDA'] . '</td>
                </tr>
                <tr>
                    <td width="20%"><strong><u>Scope</u></strong></td>
                    <td width="3%">:</td>
                    <td width="77%">' . $aktivitas['TEXT_AKTIVITAS_AGENDA'] . '</td>
                </tr>
                <tr>
                    <td width="100%" colspan="2"><ul>';
            foreach ($aktivitas['SCOPE'] as $scope) {
                $uraian .= '<li>' . $scope['TEXT_SCOPE'] . '</li>';
            }
                $uraian .=
                '   </ul></td>
                </tr>
                <tr>
                    <td width="20%"><strong><u>Metodologi</u></strong></td>
                    <td width="3%">:</td>
                    <td width="77%"></td>
                </tr>
                <tr>
                    <td width="100%" colspan="2"><ul>';
            foreach ($aktivitas['METODE'] as $metode) {
                $uraian .= '<li>' . $metode['TEXT_METODE'] . '</li>';
            }
                $uraian .=
                '   </ul></td>
                </tr>
                <tr>
                    <td width="20%"><strong><u>Kriteria Keluaran</u></strong></td>
                    <td width="3%">:</td>
                    <td width="77%"></td>
                </tr>
                <tr>
                    <td width="100%" colspan="2"><ul>';
            foreach ($aktivitas['KRITERIA'] as $kriteria) {
                $uraian .= '<li>' . $kriteria['TEXT_KRITERIA'] . '</li>';
            }
                $uraian .=
                '   </ul></td>
                </tr>
            </table>';
            $pdf->SetFont('helvetica', '', 10, '', false);
            $pdf->setListIndentWidth(3.15);
            $pdf->writeHTMLCell(0, 0, '', '', $uraian, 0, 1, 0, true, '', true);
        }
    }

    // BRING THE FUCKERS DOOOOOWN
    $pdf->output($title, 'I');
});

$app->get('/generate/:idMatkul/pdf/per-page', $authenticate($app), function ($idMatkul) use ($app) {
    $app->response->headers->set('Content-Type', 'application/pdf');
    $app->response->headers->set('Content-Transfer-Encoding', 'binary');

    $matkul = new Matkul();
    $user = new User();
    $kategori = new Kategori();
    $agenda = new Agenda();
    $rps = new Rps();
    $task = new Task();
    $approval = new Approval();
    $silabus = new Silabus($idMatkul);

    $rps->getRpsByIdMatkul($idMatkul);
    $allKategori = $kategori->getAllKategoriIndikator();
    $groupKategori = $kategori->groupKategoriIndikator();
    $countKategori = count($allKategori);
    $kompetensi = $silabus->kompetensi;
    $matkuls = $matkul->getMatkulDetails($idMatkul);
    $revs = $approval->getAllApprovalByMatkul($idMatkul);
    $agendaSimple = $agenda->getAgendaByMatkul($idMatkul);
    $agendas = $agenda->getAgendaByMatkul($idMatkul, 'verbose');
    $approvals = $approval->getApprovalByIdMatkul($idMatkul);
    $tasks = $task->getDetailAktivitasByMatkul($idMatkul);
    $dosen = $user->getUserFromMatkul($idMatkul);

    $creator = 'Ma Chung Silabus & SAR Management System/TCPDFv6.0';
    $author = 'Ma Chung Silabus & SAR Management System';
    $title = 'RPS Mata Kuliah ' . $matkuls[0]['NamaMK'] . ' ' . $matkuls[0]['TahunAjaranMK'];

    // We use TCPDF to render.
    $pdf = new SARPdf();
    $pdf->SetCreator($creator);
    $pdf->SetAuthor($author);
    $pdf->SetTitle($title);
    $pdf->SetMargins('20', '20', '20', true);
    $pdf->SetAutoPageBreak(true);

    // Passing Variables
    $pdf->idMatkul = $idMatkul;
    $pdf->groupKategori = $groupKategori;
    $pdf->countKategori = $countKategori;
});
