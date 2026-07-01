<?php
/**
 * exports/export.php — Exportar inscripciones a Excel
 * Rubrica #19: Exportar datos en Excel
 * Usa PhpSpreadsheet (via Composer) para generar .xlsx real
 */
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/InscriptorModel.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;

InscriptorModel::initKeys();
$inscriptores = InscriptorModel::getAll();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Inscripciones iTECH');

// ── Título principal ──────────────────────────────────────────
$sheet->mergeCells('A1:N1');
$sheet->setCellValue('A1', '© ' . date('Y') . ' iTECH — Reporte de Inscripciones');
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 14,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4C1D95'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
]);
$sheet->getRowDimension(1)->setRowHeight(30);

// ── Subtítulo ─────────────────────────────────────────────────
$sheet->mergeCells('A2:N2');
$sheet->setCellValue('A2', 'Generado el: ' . date('d/m/Y H:i:s') . '   |   Total registros: ' . count($inscriptores));
$sheet->getStyle('A2')->applyFromArray([
    'font' => ['size' => 10, 'italic' => true, 'color' => ['rgb' => '6D28D9']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
]);

// ── Encabezados ───────────────────────────────────────────────
$headers = [
    'A' => '#',
    'B' => 'Integridad',
    'C' => 'Identidad',
    'D' => 'Nombre',
    'E' => 'Apellido',
    'F' => 'Correo',
    'G' => 'Celular',
    'H' => 'Sexo',
    'I' => 'Edad',
    'J' => 'País de Residencia',
    'K' => 'Nacionalidad',
    'L' => 'Temas de Interés',
    'M' => 'Observaciones',
    'N' => 'Fecha de Registro',
];

foreach ($headers as $col => $title) {
    $sheet->setCellValue($col . '3', $title);
}

$sheet->getStyle('A3:N3')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '312E81']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '4C1D95']]],
]);
$sheet->getRowDimension(3)->setRowHeight(22);

// ── Datos ─────────────────────────────────────────────────────
$row = 4;
foreach ($inscriptores as $r) {
    $integro = InscriptorModel::verify($r);
    $bgColor = ($row % 2 === 0) ? 'F5F3FF' : 'FFFFFF';

    $sheet->setCellValue('A' . $row, $r['id_inscriptor']);
    $sheet->setCellValue('B' . $row, $integro ? '✅ Íntegro' : '🚨 Comprometido');
    $sheet->setCellValue('C' . $row, $r['identidad']);
    $sheet->setCellValue('D' . $row, $r['nombre']);
    $sheet->setCellValue('E' . $row, $r['apellido']);
    $sheet->setCellValue('F' . $row, $r['correo']);
    $sheet->setCellValue('G' . $row, $r['celular']);
    $sheet->setCellValue('H' . $row, $r['sexo']);
    $sheet->setCellValue('I' . $row, $r['edad']);
    $sheet->setCellValue('J' . $row, $r['nombre_pais']);
    $sheet->setCellValue('K' . $row, $r['nacionalidad']);
    $sheet->setCellValue('L' . $row, $r['temas'] ?? '');
    $sheet->setCellValue('M' . $row, $r['observaciones'] ?? '');
    $sheet->setCellValue('N' . $row, $r['fecha_registro']);

    // Color de la celda de integridad
    $intColor = $integro ? '065F46' : '991B1B';
    $intBg    = $integro ? 'D1FAE5' : 'FEE2E2';

    $sheet->getStyle('B' . $row)->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => $intColor]],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $intBg]],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ]);

    // Fondo alterno en el resto de celdas
    $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    ]);

    $sheet->getRowDimension($row)->setRowHeight(18);
    $row++;
}

// ── Ancho de columnas ─────────────────────────────────────────
$widths = ['A'=>6,'B'=>16,'C'=>14,'D'=>18,'E'=>18,'F'=>28,'G'=>16,
           'H'=>8,'I'=>7,'J'=>20,'K'=>16,'L'=>40,'M'=>30,'N'=>20];
foreach ($widths as $col => $w) {
    $sheet->getColumnDimension($col)->setWidth($w);
}

// ── Congelar encabezados ──────────────────────────────────────
$sheet->freezePane('A4');

// ── Descargar ─────────────────────────────────────────────────
$filename = 'inscripciones_itech_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
