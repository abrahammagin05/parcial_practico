<?php
/**
 * Validator — Validación del lado del servidor (métodos estáticos)
 * Validación de datos del lado de PHP.
 */
class Validator
{
    // ── Identidad: números, guiones, letras (ej: 8-888-888) ──
    public static function isValidIdentidad(string $v): bool
    {
        return (bool) preg_match('/^[0-9A-Z\-]{4,20}$/i', trim($v));
    }

    // ── Nombre / apellido: solo letras y espacios ─────────────
    public static function isValidNombre(string $v): bool
    {
        $v = trim($v);
        return mb_strlen($v) >= 2
            && mb_strlen($v) <= 100
            && (bool) preg_match('/^[\p{L}\s]+$/u', $v);
    }

    // ── Edad: entero entre 1 y 120 ────────────────────────────
    public static function isValidEdad(mixed $v): bool
    {
        return is_numeric($v) && (int)$v >= 1 && (int)$v <= 120;
    }

    // ── Sexo: valores permitidos ──────────────────────────────
    public static function isValidSexo(string $v): bool
    {
        return in_array($v, ['M', 'F', 'Otro'], true);
    }

    // ── País: entero positivo ─────────────────────────────────
    public static function isValidPais(mixed $v): bool
    {
        return is_numeric($v) && (int)$v > 0;
    }

    // ── Email estándar RFC ────────────────────────────────────
    public static function isValidCorreo(string $v): bool
    {
        return filter_var(trim($v), FILTER_VALIDATE_EMAIL) !== false;
    }

    // ── Celular: dígitos, guiones, +, espacios ────────────────
    public static function isValidCelular(string $v): bool
    {
        return (bool) preg_match('/^\+?[\d\s\-]{6,20}$/', trim($v));
    }

    // ── Al menos un área seleccionada ─────────────────────────
    public static function isValidAreas(mixed $v): bool
    {
        return is_array($v) && count($v) >= 1;
    }

    // ── Campo requerido no vacío ──────────────────────────────
    public static function isRequired(mixed $v): bool
    {
        return isset($v) && trim((string)$v) !== '';
    }
}
