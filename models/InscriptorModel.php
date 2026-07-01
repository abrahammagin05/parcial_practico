<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * InscriptorModel — Capa de datos + lógica de negocio.
 * Clase para la Conexión con funciones de BD.
 *  Firma digital con OpenSSL (con fallback HMAC para WAMP).
 */
class InscriptorModel
{
    private const PRIV_KEY = __DIR__ . '/../keys/private.pem';
    private const PUB_KEY  = __DIR__ . '/../keys/public.pem';
    private const HMAC_KEY = 'itech_parcial_2025_secret';

    // ── Rutas comunes de openssl.cnf en WAMP/Windows ──────────
    private static array $opensslConfPaths = [
        'C:\\wamp64\\bin\\apache\\apache2.4.54.2\\conf\\openssl.cnf',
        'C:\\wamp64\\bin\\apache\\apache2.4.58.2\\conf\\openssl.cnf',
        'C:\\wamp64\\bin\\apache\\apache2.4.51.0\\conf\\openssl.cnf',
        'C:\\wamp\\bin\\apache\\apache2.4.54.2\\conf\\openssl.cnf',
        'C:\\xampp\\apache\\conf\\openssl.cnf',
        'C:\\Program Files\\OpenSSL-Win64\\bin\\openssl.cfg',
    ];

    // ── Configurar OPENSSL_CONF para WAMP ─────────────────────
    private static function fixOpenSSLConf(): void
    {
        if (!empty(getenv('OPENSSL_CONF'))) return; // ya está configurado

        foreach (self::$opensslConfPaths as $path) {
            if (file_exists($path)) {
                putenv('OPENSSL_CONF=' . $path);
                return;
            }
        }

        // Buscar automáticamente en directorios de apache dentro de wamp64
        $wampApache = 'C:\\wamp64\\bin\\apache\\';
        if (is_dir($wampApache)) {
            $dirs = glob($wampApache . 'apache*', GLOB_ONLYDIR);
            foreach ((array)$dirs as $d) {
                $candidate = $d . '\\conf\\openssl.cnf';
                if (file_exists($candidate)) {
                    putenv('OPENSSL_CONF=' . $candidate);
                    return;
                }
            }
        }
    }

    // ── Generar llaves RSA (solo la primera vez) ───────────────
    public static function initKeys(): void
    {
        $dir = __DIR__ . '/../keys';
        if (!is_dir($dir)) mkdir($dir, 0750, true);

        // Proteger carpeta con .htaccess
        if (!file_exists($dir . '/.htaccess')) {
            file_put_contents($dir . '/.htaccess', "Deny from all\n");
        }

        if (file_exists(self::PRIV_KEY) && file_exists(self::PUB_KEY)) {
            return; // Ya existen, no regenerar
        }

        self::fixOpenSSLConf(); // Arreglar config de WAMP

        $config = [
            'digest_alg'       => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $res = openssl_pkey_new($config);

        // Si OpenSSL no pudo generar la llave, usar solo HMAC (fallback)
        if ($res === false) return;

        $exported = openssl_pkey_export($res, $privPem);
        if (!$exported || empty($privPem)) return;

        $details = openssl_pkey_get_details($res);
        if ($details === false || empty($details['key'])) return;

        file_put_contents(self::PRIV_KEY, $privPem);
        file_put_contents(self::PUB_KEY, $details['key']);
    }

    // ── Firmar los datos críticos del inscriptor ───────────────
    private static function sign(array $data): string
    {
        $payload = self::buildPayload($data);

        // Intentar firma RSA
        if (file_exists(self::PRIV_KEY)) {
            self::fixOpenSSLConf();
            $key = openssl_pkey_get_private(file_get_contents(self::PRIV_KEY));
            if ($key !== false) {
                if (openssl_sign($payload, $signature, $key, OPENSSL_ALGO_SHA256)) {
                    return 'rsa:' . base64_encode($signature);
                }
            }
        }

        // Fallback: HMAC-SHA256
        return 'hmac:' . hash_hmac('sha256', $payload, self::HMAC_KEY);
    }

    // ── Verificar integridad de un registro ────────────────────
    public static function verify(array $record): bool
    {
        if (empty($record['firma_hash'])) return false;

        $payload = self::buildPayload($record);
        $firma   = $record['firma_hash'];

        // Firma RSA
        if (str_starts_with($firma, 'rsa:') && file_exists(self::PUB_KEY)) {
            self::fixOpenSSLConf();
            $pub = openssl_pkey_get_public(file_get_contents(self::PUB_KEY));
            if ($pub !== false) {
                $sig = base64_decode(substr($firma, 4), true);
                if ($sig !== false) {
                    return openssl_verify($payload, $sig, $pub, OPENSSL_ALGO_SHA256) === 1;
                }
            }
        }

        // Firma HMAC (fallback)
        if (str_starts_with($firma, 'hmac:')) {
            $expected = 'hmac:' . hash_hmac('sha256', $payload, self::HMAC_KEY);
            return hash_equals($expected, $firma);
        }

        // Firma legada (sin prefijo) - compatibilidad hacia atrás
        $expected = hash_hmac('sha256', $payload, self::HMAC_KEY);
        return hash_equals($expected, $firma);
    }

    // ── Construir payload para firma/verificación ─────────────
    private static function buildPayload(array $data): string
    {
        return implode('|', [
            $data['nombre']    ?? '',
            $data['apellido']  ?? '',
            $data['identidad'] ?? '',
            $data['correo']    ?? '',
            $data['celular']   ?? '',
            $data['sexo']      ?? '',
        ]);
    }

    // ── Obtener todos los países ───────────────────────────────
    public static function getPaises(): array
    {
        return Database::fetchAll(
            "SELECT id_pais, nombre_pais FROM paises ORDER BY nombre_pais"
        );
    }

    // ── Obtener todas las áreas de interés ────────────────────
    public static function getAreas(): array
    {
        return Database::fetchAll(
            "SELECT id_area, nombre_area FROM areas_interes ORDER BY id_area"
        );
    }

    // ── Guardar inscripción en transacción ─────────────────────
    public static function save(array $data): int|false
    {
        try {
            Database::beginTransaction();

            $firma = self::sign($data);

            Database::query(
                "INSERT INTO inscriptores
                    (identidad, nombre, apellido, edad, sexo, id_pais,
                     nacionalidad, correo, celular, observaciones, firma_hash)
                 VALUES
                    (:identidad, :nombre, :apellido, :edad, :sexo, :id_pais,
                     :nacionalidad, :correo, :celular, :observaciones, :firma)",
                [
                    ':identidad'     => $data['identidad'],
                    ':nombre'        => $data['nombre'],
                    ':apellido'      => $data['apellido'],
                    ':edad'          => $data['edad'],
                    ':sexo'          => $data['sexo'],
                    ':id_pais'       => $data['id_pais'],
                    ':nacionalidad'  => $data['nacionalidad'],
                    ':correo'        => $data['correo'],
                    ':celular'       => $data['celular'],
                    ':observaciones' => $data['observaciones'],
                    ':firma'         => $firma,
                ]
            );

            $id = (int) Database::lastId();

            foreach ($data['areas'] as $idArea) {
                Database::query(
                    "INSERT INTO inscriptor_areas (id_inscriptor, id_area)
                     VALUES (:id_inscriptor, :id_area)",
                    [':id_inscriptor' => $id, ':id_area' => $idArea]
                );
            }

            Database::commit();
            return $id;

        } catch (PDOException $e) {
            Database::rollBack();
            $_SESSION['db_error'] = $e->getMessage();
            return false;
        }
    }

    // ── Obtener todos los registros con país y áreas ───────────
    public static function getAll(): array
    {
        return Database::fetchAll(
            "SELECT
                 i.id_inscriptor, i.identidad, i.nombre, i.apellido,
                 i.edad, i.sexo, i.nacionalidad, i.correo, i.celular,
                 i.observaciones, i.fecha_registro, i.firma_hash,
                 p.nombre_pais,
                 GROUP_CONCAT(a.nombre_area ORDER BY a.id_area SEPARATOR ', ') AS temas
             FROM inscriptores i
             JOIN paises p ON i.id_pais = p.id_pais
             LEFT JOIN inscriptor_areas ia ON i.id_inscriptor = ia.id_inscriptor
             LEFT JOIN areas_interes a     ON ia.id_area = a.id_area
             GROUP BY i.id_inscriptor
             ORDER BY i.fecha_registro DESC"
        );
    }
}
