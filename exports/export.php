<?php
/**
 * exports/export.php — Exportar inscripciones a Excel
 * Exportar datos en Excel
 * Genera un archivo .xls descargable usando HTML table
 * (compatible con Excel y LibreOffice Calc sin dependencias)
 */
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/InscriptorModel.php';

InscriptorModel::initKeys();
$inscriptores = InscriptorModel::getAll();

// ── Encabezados para descarga de Excel ───────────────────────
$filename = 'inscripciones_itech_' . date('Ymd_His') . '.xls';

header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');
?>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11pt; }
        h2   { font-size: 14pt; color: #4C1D95; margin-bottom: 4px; }
        p    { font-size: 9pt; color: #555; margin: 0 0 10px; }
        table { border-collapse: collapse; width: 100%; }
        th {
            background-color: #4C1D95;
            color: white;
            padding: 6px 8px;
            text-align: left;
            font-size: 10pt;
            border: 1px solid #3730A3;
        }
        td {
            padding: 5px 8px;
            border: 1px solid #CBD5E1;
            font-size: 10pt;
            vertical-align: top;
        }
        tr:nth-child(even) td { background-color: #F5F3FF; }
        .ok  { color: #065F46; font-weight: bold; }
        .err { color: #991B1B; font-weight: bold; }
    </style>
</head>
<body>
    <h2>iTECH — Reporte de Inscripciones</h2>
    <p>Generado el: <?= date('d/m/Y H:i:s') ?> &nbsp;|&nbsp; Total registros: <?= count($inscriptores) ?></p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Integridad</th>
                <th>Identidad</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Celular</th>
                <th>Sexo</th>
                <th>Edad</th>
                <th>País de Residencia</th>
                <th>Nacionalidad</th>
                <th>Temas de Interés</th>
                <th>Observaciones</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($inscriptores as $r): ?>
            <?php $integro = InscriptorModel::verify($r); ?>
            <tr>
                <td><?= (int)$r['id_inscriptor'] ?></td>
                <td class="<?= $integro ? 'ok' : 'err' ?>">
                    <?= $integro ? '✅ Íntegro' : '🚨 Comprometido' ?>
                </td>
                <td><?= htmlspecialchars($r['identidad']) ?></td>
                <td><?= htmlspecialchars($r['nombre']) ?></td>
                <td><?= htmlspecialchars($r['apellido']) ?></td>
                <td><?= htmlspecialchars($r['correo']) ?></td>
                <td><?= htmlspecialchars($r['celular']) ?></td>
                <td><?= htmlspecialchars($r['sexo']) ?></td>
                <td><?= (int)$r['edad'] ?></td>
                <td><?= htmlspecialchars($r['nombre_pais']) ?></td>
                <td><?= htmlspecialchars($r['nacionalidad']) ?></td>
                <td><?= htmlspecialchars($r['temas'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['observaciones'] ?? '') ?></td>
                <td><?= htmlspecialchars($r['fecha_registro']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p style="margin-top:14px; font-size:9pt; color:#888">
        © <?= date('Y') ?> iTECH. All rights reserved. | contacto@itech.pa
    </p>
</body>
</html>
