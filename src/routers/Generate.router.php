<?php

/**
 * Generate router for SAR Application
 *
 * this file contains route definition and logic for `/generate` route.
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

use SAR\models\Matkul;
use SAR\models\Agenda;
use SAR\models\Kategori;
use SAR\models\Rps;

/**
 * -*-*-*- BEWARE -*-*-*-
 * HUGE SHITLOADS OF CODE.
 */
$app->get('/generate/:idMatkul', $authenticate($app), function ($idMatkul) use ($app) {
    // Send PDF Directly to browser A.K.A download
    $app->response->headers->set('Content-Type', 'application/pdf');
    $app->response->headers->set('Content-Transfer-Encoding', 'binary');

    $kategori = new Kategori();
    $agenda = new Agenda();
    $rps = new Rps();

    $rps->getRpsByIdMatkul($idMatkul); // TODO: CHANGE THIS!
    $allKategori = $kategori->getAllKategoriIndikator();
    $groupKategori = $kategori->groupKategoriIndikator();
    $countKategori = count($allKategori);
    $agendas = $agenda->getAgendaByMatkul($idMatkul, 'verbose'); // TODO: CHANGE THIS!

    $creator = 'Ma Chung Silabus & SAR Management System/TCPDFv6.0';
    $author = 'BUDI';
    $title = 'RPS Mata Kuliah Kertakes 2010';

    // We use TCPDF to render.
    $pdf = new TCPDF();
    $pdf->SetCreator($creator);
    $pdf->SetAuthor($author);
    $pdf->SetTitle($title);
    $pdf->SetMargins('20', '20', '20', true);
    $pdf->SetAutoPageBreak(true);

    // Evaluasi
    $pdf->AddPage('L', 'A4');
    $evaluasi =
    '<table border="1" width="100%">
        <thead>
            <tr>
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
            <tr>';
    foreach ($groupKategori as $key => $value) {
        $evaluasi .=
                '<td colspan="' . $value['LENGTH'] . '" align="center"><strong>' . $key . '</strong></td>';
    }
    $evaluasi .=
            '</tr>
            <tr>';
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
    $pdf->SetFont('dejavusans', '', 8, '', false);
    $pdf->writeHTMLCell(0, 0, '', '', $evaluasi, 0, 1, 0, true, '', true);

    // BRING THE FUCKERS DOOOOOWN
    $pdf->output($title, 'I');
});
