<?php
/**
 * Sanitizer — Sanitización y limpieza de datos (métodos estáticos)
 *  Sanitización de Datos.
 *  Nombres/Apellidos en formato título (Data Cleaning).
 */
class Sanitizer
{
    // ── Limpiar texto genérico: strip tags + htmlspecialchars ─
    public static function cleanString(string $v): string
    {
        return htmlspecialchars(strip_tags(trim($v)), ENT_QUOTES, 'UTF-8');
    }

    // ── Correo: filtro nativo de PHP ──────────────────────────
    public static function cleanEmail(string $v): string
    {
        return strtolower((string) filter_var(trim($v), FILTER_SANITIZE_EMAIL));
    }

    // ── Solo números y símbolos de teléfono ───────────────────
    public static function cleanPhone(string $v): string
    {
        return preg_replace('/[^\+\d\s\-]/', '', trim($v));
    }

    // ── Identidad: mayúsculas, solo chars válidos ─────────────
    public static function cleanIdentidad(string $v): string
    {
        return strtoupper(preg_replace('/[^0-9A-Za-z\-]/', '', trim($v)));
    }

    // ── Entero seguro ─────────────────────────────────────────
    public static function cleanInt(mixed $v): int
    {
        return (int) filter_var($v, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Formato título: primera letra de cada palabra en mayúscula.
     * Rubrica #23 — Data Cleaning / Sanitization.
     * Soporta UTF-8 para tildes y ñ.
     */
    public static function toTitleCase(string $v): string
    {
        $v = mb_strtolower(trim($v), 'UTF-8');
        return mb_convert_case($v, MB_CASE_TITLE, 'UTF-8');
    }

    // ── Limpiar array de IDs (áreas) ─────────────────────────
    public static function cleanIntArray(array $arr): array
    {
        return array_map('intval', $arr);
    }
}
