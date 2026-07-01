<?php
/**
 * views/report_view.php — Vista del reporte de inscripciones
 *  Temas separados por comas
 *  Exportar a Excel
 *  Indicadores visuales de integridad (verde/rojo)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTECH — Reporte de Inscripciones</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrapper">

    <!-- ── Navegación ───────────────────────── -->
    <nav class="nav-bar">
        <a href="index.php"    class="nav-btn">📝 Inscripción</a>
        <a href="reporte.php"  class="nav-btn active">📊 Reporte</a>
    </nav>

    <!-- ── Header ───────────────────────────── -->
    <header class="site-header">
        <div class="logo-badge">iTECH</div>
        <h1>Reporte de Inscripciones</h1>
        <p>Total registrados: <strong style="color:#A5B4FC"><?= count($inscriptores) ?></strong></p>
    </header>

    <div class="card">

        <!-- Barra de herramientas -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; flex-wrap:wrap; gap:.75rem">
            <h2 class="section-title" style="margin:0">Listado de Inscriptos</h2>
            <?php if (!empty($inscriptores)): ?>
            <a href="exports/export.php" class="btn-export">
                📥 Exportar a Excel
            </a>
            <?php endif; ?>
        </div>

        <!-- Leyenda de integridad -->
        <div style="display:flex; gap:1rem; margin-bottom:1rem; flex-wrap:wrap">
            <span class="badge badge-ok">✅ Íntegro — firma digital válida</span>
            <span class="badge badge-err">🚨 Comprometido — datos alterados</span>
        </div>

        <?php if (empty($inscriptores)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p style="font-size:1.1rem; font-weight:600">No hay inscripciones todavía.</p>
            <p style="margin-top:.4rem"><a href="index.php" style="color:#7C3AED">Registrar el primero →</a></p>
        </div>
        <?php else: ?>

        <div class="report-table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Integridad</th>
                    <th>Identidad</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Sexo</th>
                    <th>Edad</th>
                    <th>País</th>
                    <th>Nacionalidad</th>
                    <th>Temas de Interés</th>
                    <th>Observaciones</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($inscriptores as $i => $r): ?>
                <?php $integro = InscriptorModel::verify($r); ?>
                <tr>
                    <td><?= $r['id_inscriptor'] ?></td>
                    <td>
                        <?php if ($integro): ?>
                            <span class="badge badge-ok">✅ Íntegro</span>
                        <?php else: ?>
                            <span class="badge badge-err">🚨 Comprometido</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($r['identidad']) ?></td>
                    <td><?= htmlspecialchars($r['nombre']) ?></td>
                    <td><?= htmlspecialchars($r['apellido']) ?></td>
                    <td><?= htmlspecialchars($r['correo']) ?></td>
                    <td><?= htmlspecialchars($r['celular']) ?></td>
                    <td><?= htmlspecialchars($r['sexo']) ?></td>
                    <td><?= htmlspecialchars($r['edad']) ?></td>
                    <td><?= htmlspecialchars($r['nombre_pais']) ?></td>
                    <td><?= htmlspecialchars($r['nacionalidad']) ?></td>
                    <!-- Rubrica #18: temas separados por comas (GROUP_CONCAT) -->
                    <td style="max-width:200px; font-size:.82rem; color:#6D28D9">
                        <?= htmlspecialchars($r['temas'] ?? '—') ?>
                    </td>
                    <td style="max-width:160px; font-size:.82rem">
                        <?= htmlspecialchars(mb_strimwidth($r['observaciones'] ?? '', 0, 60, '…')) ?>
                    </td>
                    <td style="white-space:nowrap; font-size:.82rem">
                        <?= htmlspecialchars($r['fecha_registro']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>

        <?php endif; ?>

    </div><!-- /card -->

    <!-- ── Footer ───────────────────────────── -->
    <footer class="site-footer">
        <strong>© <?= date('Y') ?> iTECH. All rights reserved.</strong><br>
        📧 <a href="mailto:contacto@itech.pa">contacto@itech.pa</a>
        &nbsp;&bull;&nbsp;
        📞 +507 6000-0000
        &nbsp;&bull;&nbsp;
        🌐 <a href="#">www.itech.pa</a>
    </footer>

</div><!-- /wrapper -->
</body>
</html>
