<?php
/**
 * views/form_view.php — Vista del formulario de inscripción
 * 
 */

// Helpers para errores y valores anteriores
$err = $_SESSION['errors'] ?? [];
$old = $_SESSION['old']    ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

function fieldError(array $err, string $key): string {
    if (!isset($err[$key])) return '';
    return '<span class="error-msg">⚠ ' . htmlspecialchars($err[$key]) . '</span>';
}
function hasError(array $err, string $key): string {
    return isset($err[$key]) ? ' is-invalid' : '';
}
function oldVal(array $old, string $key): string {
    return htmlspecialchars($old[$key] ?? '');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTECH — Formulario de Inscripción</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="wrapper">

    <!-- ── Navegación ───────────────────────── -->
    <nav class="nav-bar">
        <a href="index.php"    class="nav-btn active">📝 Inscripción</a>
        <a href="reporte.php"  class="nav-btn">📊 Reporte</a>
    </nav>

    <!-- ── Header ───────────────────────────── -->
    <header class="site-header">
        <div class="logo-badge">iTECH</div>
        <h1>Formulario de Inscripción</h1>
        <p>Evento Tecnológico Panamá &bull; <?= date('Y') ?></p>
    </header>

    <!-- ── Mensajes ─────────────────────────── -->
    <div class="card" style="padding:0">
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success" style="margin:1.5rem 2rem 0">
            ✅ <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($err['general'])): ?>
        <div class="alert alert-danger" style="margin:1.5rem 2rem 0">
            ❌ <?= htmlspecialchars($err['general']) ?>
        </div>
    <?php endif; ?>
    </div>

    <!-- ── Formulario ───────────────────────── -->
    <form method="POST" action="controllers/InscriptorController.php" novalidate>
    <div class="card">

        <!-- 1. Datos Personales -->
        <h2 class="section-title">👤 Datos Personales</h2>
        <div class="grid-2">

            <!-- 1. Identidad -->
            <div class="form-group">
                <label>Identidad (Documento) <span class="req">*</span></label>
                <input type="text" name="identidad"
                       placeholder="Ej: 8-888-8888"
                       value="<?= oldVal($old,'identidad') ?>"
                       class="<?= hasError($err,'identidad') ?>"
                       maxlength="20">
                <?= fieldError($err,'identidad') ?>
            </div>

            <!-- 4. Edad -->
            <div class="form-group">
                <label>Edad <span class="req">*</span></label>
                <input type="number" name="edad"
                       placeholder="Años"
                       value="<?= oldVal($old,'edad') ?>"
                       class="<?= hasError($err,'edad') ?>"
                       min="1" max="120">
                <?= fieldError($err,'edad') ?>
            </div>

            <!-- 2. Nombre -->
            <div class="form-group">
                <label>Nombre <span class="req">*</span></label>
                <input type="text" name="nombre"
                       placeholder="Ej: Juan Carlos"
                       value="<?= oldVal($old,'nombre') ?>"
                       class="<?= hasError($err,'nombre') ?>"
                       maxlength="100">
                <?= fieldError($err,'nombre') ?>
            </div>

            <!-- 3. Apellido -->
            <div class="form-group">
                <label>Apellido <span class="req">*</span></label>
                <input type="text" name="apellido"
                       placeholder="Ej: González Pérez"
                       value="<?= oldVal($old,'apellido') ?>"
                       class="<?= hasError($err,'apellido') ?>"
                       maxlength="100">
                <?= fieldError($err,'apellido') ?>
            </div>

        </div><!-- /grid-2 -->

        <!-- 5. Sexo -->
        <div class="form-group" style="margin-top:1rem">
            <label>Sexo <span class="req">*</span></label>
            <div class="radio-group">
                <?php foreach (['M'=>'Masculino','F'=>'Femenino','Otro'=>'Otro'] as $val=>$lbl): ?>
                <label class="radio-item">
                    <input type="radio" name="sexo" value="<?= $val ?>"
                        <?= (($old['sexo'] ?? '') === $val) ? 'checked' : '' ?>>
                    <span><?= $lbl ?></span>
                </label>
                <?php endforeach; ?>
            </div>
            <?= fieldError($err,'sexo') ?>
        </div>

        <div class="divider"></div>

        <!-- 2. Residencia y Nacionalidad -->
        <h2 class="section-title">🌎 Residencia y Nacionalidad</h2>
        <div class="grid-2">

            <!-- 6. País de Residencia -->
            <div class="form-group">
                <label>País de Residencia <span class="req">*</span></label>
                <select name="id_pais" class="<?= hasError($err,'id_pais') ?>">
                    <option value="">-- Seleccione un país --</option>
                    <?php foreach ($paises as $p): ?>
                    <option value="<?= $p['id_pais'] ?>"
                        <?= (($old['id_pais'] ?? '') == $p['id_pais']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_pais']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?= fieldError($err,'id_pais') ?>
            </div>

            <!-- 7. Nacionalidad -->
            <div class="form-group">
                <label>Nacionalidad <span class="req">*</span></label>
                <input type="text" name="nacionalidad"
                       placeholder="Ej: Panameña"
                       value="<?= oldVal($old,'nacionalidad') ?>"
                       class="<?= hasError($err,'nacionalidad') ?>"
                       maxlength="100">
                <?= fieldError($err,'nacionalidad') ?>
            </div>

        </div>

        <div class="divider"></div>

        <!-- 3. Información de Contacto -->
        <h2 class="section-title">📞 Información de Contacto</h2>
        <div class="grid-2">

            <!-- 8. Correo -->
            <div class="form-group">
                <label>Correo Electrónico <span class="req">*</span></label>
                <input type="email" name="correo"
                       placeholder="usuario@correo.com"
                       value="<?= oldVal($old,'correo') ?>"
                       class="<?= hasError($err,'correo') ?>"
                       maxlength="150">
                <?= fieldError($err,'correo') ?>
            </div>

            <!-- 9. Celular -->
            <div class="form-group">
                <label>Celular <span class="req">*</span></label>
                <input type="tel" name="celular"
                       placeholder="Ej: +507 6XXX-XXXX"
                       value="<?= oldVal($old,'celular') ?>"
                       class="<?= hasError($err,'celular') ?>"
                       maxlength="20">
                <?= fieldError($err,'celular') ?>
            </div>

        </div>

        <div class="divider"></div>

        <!-- 4. Temas de Interés (checkboxes) -->
        <h2 class="section-title">💻 Temas Tecnológicos de Interés <span class="req">*</span></h2>
        <?= fieldError($err,'areas') ?>
        <div class="checkbox-grid" style="margin-top:.6rem">
            <?php foreach ($areas as $area): ?>
            <label class="checkbox-item">
                <input type="checkbox" name="areas[]" value="<?= $area['id_area'] ?>"
                    <?php
                    $oldAreas = array_map('intval', $old['areas'] ?? []);
                    echo in_array((int)$area['id_area'], $oldAreas) ? 'checked' : '';
                    ?>>
                <span><?= htmlspecialchars($area['nombre_area']) ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <div class="divider"></div>

        <!-- 5. Observaciones -->
        <h2 class="section-title">💬 Observaciones / Consulta sobre el Evento</h2>
        <div class="form-group">
            <label>Observaciones (opcional)</label>
            <textarea name="observaciones"
                      placeholder="Escriba aquí sus dudas o consultas sobre el evento..."
                      maxlength="1000"><?= oldVal($old,'observaciones') ?></textarea>
        </div>

        <div class="divider"></div>

        <!-- Submit -->
        <button type="submit" class="btn-submit">
            🚀 Confirmar Inscripción
        </button>

    </div><!-- /card -->
    </form>

    <!-- ── Footer (Rubrica #12) ──────────────── -->
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
