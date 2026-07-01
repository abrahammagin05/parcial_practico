<?php
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/InscriptorModel.php';
require_once __DIR__ . '/../helpers/Validator.php';
require_once __DIR__ . '/../helpers/Sanitizer.php';

/**
 * InscriptorController — Maneja el envío del formulario.
 *  (MVC, Validación, Sanitización)
 */
class InscriptorController
{
    private array $errors = [];

    public function handle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../index.php');
            exit;
        }

        $data = $this->sanitize($_POST);
        $this->validate($data);

        if (!empty($this->errors)) {
            $_SESSION['errors'] = $this->errors;
            $_SESSION['old']    = $_POST;
            header('Location: ../index.php');
            exit;
        }

        $id = InscriptorModel::save($data);

        if ($id !== false) {
            $_SESSION['success'] = '¡Inscripción exitosa! Tu número de registro es <strong>#' . $id . '</strong>.';
            unset($_SESSION['old']);
        } else {
            $dbErr = $_SESSION['db_error'] ?? '';
            unset($_SESSION['db_error']);

            // Detectar duplicados (clave única)
            if (str_contains($dbErr, 'Duplicate')) {
                $_SESSION['errors'] = ['general' => 'Ya existe un registro con esa Identidad, Correo o Celular.'];
            } else {
                $_SESSION['errors'] = ['general' => 'Error al guardar. Intente de nuevo.'];
            }
            $_SESSION['old'] = $_POST;
        }

        header('Location: ../index.php');
        exit;
    }

    // ── Sanitización completa del input ───────────────────────
    private function sanitize(array $post): array
    {
        return [
            'identidad'    => Sanitizer::cleanIdentidad($post['identidad']    ?? ''),
            'nombre'       => Sanitizer::toTitleCase(Sanitizer::cleanString($post['nombre']   ?? '')),
            'apellido'     => Sanitizer::toTitleCase(Sanitizer::cleanString($post['apellido'] ?? '')),
            'edad'         => Sanitizer::cleanInt($post['edad'] ?? 0),
            'sexo'         => Sanitizer::cleanString($post['sexo'] ?? ''),
            'id_pais'      => Sanitizer::cleanInt($post['id_pais'] ?? 0),
            'nacionalidad' => Sanitizer::toTitleCase(Sanitizer::cleanString($post['nacionalidad'] ?? '')),
            'correo'       => Sanitizer::cleanEmail($post['correo'] ?? ''),
            'celular'      => Sanitizer::cleanPhone($post['celular'] ?? ''),
            'areas'        => Sanitizer::cleanIntArray($post['areas'] ?? []),
            'observaciones'=> Sanitizer::cleanString($post['observaciones'] ?? ''),
        ];
    }

    // ── Validaciones del lado de PHP ──────────────────────────
    private function validate(array $d): void
    {
        if (!Validator::isRequired($d['identidad']))
            $this->errors['identidad'] = 'La identidad es requerida.';
        elseif (!Validator::isValidIdentidad($d['identidad']))
            $this->errors['identidad'] = 'Formato inválido (ej: 8-888-8888 ó PE-1234).';

        if (!Validator::isRequired($d['nombre']))
            $this->errors['nombre'] = 'El nombre es requerido.';
        elseif (!Validator::isValidNombre($d['nombre']))
            $this->errors['nombre'] = 'Solo letras y espacios (mín. 2 caracteres).';

        if (!Validator::isRequired($d['apellido']))
            $this->errors['apellido'] = 'El apellido es requerido.';
        elseif (!Validator::isValidNombre($d['apellido']))
            $this->errors['apellido'] = 'Solo letras y espacios (mín. 2 caracteres).';

        if (!Validator::isValidEdad($d['edad']))
            $this->errors['edad'] = 'Ingrese una edad válida entre 1 y 120.';

        if (!Validator::isValidSexo($d['sexo']))
            $this->errors['sexo'] = 'Seleccione una opción de sexo.';

        if (!Validator::isValidPais($d['id_pais']))
            $this->errors['id_pais'] = 'Seleccione un país de residencia.';

        if (!Validator::isRequired($d['nacionalidad']))
            $this->errors['nacionalidad'] = 'La nacionalidad es requerida.';
        elseif (!Validator::isValidNombre($d['nacionalidad']))
            $this->errors['nacionalidad'] = 'Solo letras y espacios.';

        if (!Validator::isValidCorreo($d['correo']))
            $this->errors['correo'] = 'Ingrese un correo electrónico válido.';

        if (!Validator::isValidCelular($d['celular']))
            $this->errors['celular'] = 'Ingrese un número de celular válido.';

        if (!Validator::isValidAreas($d['areas']))
            $this->errors['areas'] = 'Seleccione al menos un tema tecnológico.';
    }
}

// Ejecutar controlador
(new InscriptorController())->handle();
