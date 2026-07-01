📋 Parcial Práctico 1 — iTECH Inscripciones
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![WAMP](https://img.shields.io/badge/WAMP-Server-purple?style=for-the-badge)
![MVC](https://img.shields.io/badge/Arquitectura-MVC-blue?style=for-the-badge)
![OpenSSL](https://img.shields.io/badge/Firma-OpenSSL-red?style=for-the-badge)
![Composer](https://img.shields.io/badge/Composer-2.9-885630?style=for-the-badge&logo=composer&logoColor=white)
![PhpSpreadsheet](https://img.shields.io/badge/PhpSpreadsheet-Excel-217346?style=for-the-badge)
> Aplicación web de inscripciones para el evento tecnológico **iTECH**, desarrollada en PHP con arquitectura MVC, base de datos MySQL, firma digital con OpenSSL y exportación Excel con PhpSpreadsheet.
---
📑 Tabla de Contenidos
Descripción
Objetivos
Tecnologías
Estructura del Proyecto
Base de Datos
Instalación y Uso
Funcionalidades
Validaciones
Seguridad y Firma Digital
Dificultades y Soluciones
Desarrollado por
---
📌 Descripción
Sistema web que permite registrar participantes para el evento tecnológico iTECH. El sistema captura datos personales, de contacto y temas de interés, los almacena de forma segura con firma digital, y genera un reporte con indicadores visuales de integridad de datos exportable a Excel en formato `.xlsx` real usando PhpSpreadsheet.
---
🎯 Objetivos
#	Criterio	Puntaje
1-7	Formulario con campos: Identidad, Nombre, Apellido, Edad, Sexo, País, Nacionalidad	17 pts
8-9	Información de contacto: Correo y Celular con restricciones en BD	10 pts
10	Checkboxes de temas tecnológicos	2 pts
11	Observaciones / Consulta	2 pts
12	Footer con año dinámico	4 pts
13	CSS con color	10 pts
14	Base de datos en phpMyAdmin	4 pts
15	Clase de conexión PDO	5 pts
16	Tablas: Inscriptores, Países, Áreas de Interés	6 pts
17	Llaves foráneas ON DELETE RESTRICT / ON UPDATE CASCADE	10 pts
18	Reporte con temas separados por comas	5 pts
19	Exportar datos en Excel (.xlsx con PhpSpreadsheet)	5 pts
20	Firma digital OpenSSL + badges verde/rojo	5 pts
21	Validación PHP con métodos estáticos	5 pts
22	Sanitización con métodos estáticos	5 pts
23	Nombres en formato título (Data Cleaning)	4 pts
24	Arquitectura MVC	3 pts
Total		100 pts
---
🛠 Tecnologías
Tecnología	Uso
PHP 8.4	Backend y lógica del servidor
MySQL	Base de datos relacional
WAMP Server	Entorno de desarrollo local
PDO	Conexión segura a base de datos
OpenSSL / HMAC-SHA256	Firma digital de registros
Composer 2.9	Gestor de dependencias
PhpSpreadsheet	Generación de archivos `.xlsx` reales
HTML5 + CSS3	Interfaz sin frameworks externos
---
📁 Estructura del Proyecto
```
parcial_practico/
├── index.php                    ← Entrada: formulario de inscripción
├── reporte.php                  ← Entrada: reporte de inscriptos
├── composer.json                ← Dependencias del proyecto
├── composer.lock                ← Versiones exactas instaladas
├── config/
│   └── Database.php             ← Clase de conexión PDO (Singleton)
├── models/
│   └── InscriptorModel.php      ← Lógica de datos + firma OpenSSL
├── controllers/
│   └── InscriptorController.php ← Manejo de POST, validación, sanitización
├── views/
│   ├── form_view.php            ← Vista del formulario
│   └── report_view.php          ← Vista del reporte
├── helpers/
│   ├── Validator.php            ← Métodos estáticos de validación
│   └── Sanitizer.php            ← Métodos estáticos de sanitización
├── exports/
│   └── export.php               ← Exportación .xlsx con PhpSpreadsheet
├── assets/
│   └── css/
│       └── style.css            ← Estilos del sistema
├── keys/
│   ├── .htaccess                ← Bloquea acceso web a las llaves
│   ├── private.pem              ← Llave privada RSA (auto-generada)
│   └── public.pem               ← Llave pública RSA (auto-generada)
├── sql/
│   └── parcial.sql              ← Script BD: tablas, FK y datos semilla
└── vendor/                      ← Dependencias Composer (PhpSpreadsheet)
```
---
🗄 Base de Datos
Diagrama de tablas
```
paises                    inscriptores                   areas_interes
──────────────────        ──────────────────────────     ─────────────────
PK id_pais          ←──  FK id_pais                     PK id_area
   nombre_pais            PK id_inscriptor        ──→      nombre_area
                             identidad (UNIQUE)      │
                             nombre                  │   inscriptor_areas
                             apellido                │   ─────────────────
                             edad                    │   FK id_inscriptor
                             sexo                    └── FK id_area
                             nacionalidad
                             correo (UNIQUE)
                             celular (UNIQUE)
                             observaciones
                             fecha_registro
                             firma_hash
```
Restricciones de integridad
```sql
-- Inscriptor → País
ON DELETE RESTRICT ON UPDATE CASCADE

-- InscriptorAreas → Inscriptor
ON DELETE RESTRICT ON UPDATE CASCADE

-- InscriptorAreas → Área
ON DELETE RESTRICT ON UPDATE CASCADE
```
---
🚀 Instalación y Uso
Requisitos
WAMP Server con PHP 8.x
MySQL 5.7+
Composer instalado
Pasos
1. Clonar el repositorio
```bash
git clone https://github.com/abrahammagin05/parcial_practico.git
cd parcial_practico
```
2. Instalar dependencias
```bash
composer install
```
3. Crear la base de datos
Abrir `http://127.1.1.1/phpmyadmin/`
Pestaña SQL → pegar contenido de `sql/parcial.sql` → Ejecutar
4. Verificar credenciales en `config/Database.php`
```php
private string $host = '127.1.1.1';
private string $user = 'root';
private string $pass = '';   // tu contraseña si tienes
```
5. Acceder al sistema
Página	URL
Formulario	http://localhost/parcial_practico/
Reporte	http://localhost/parcial_practico/reporte.php
Exportar Excel	http://localhost/parcial_practico/exports/export.php
---
⚙️ Funcionalidades
Formulario de Inscripción
7 campos de datos personales
Selector de País de Residencia (20 países precargados)
8 checkboxes de temas tecnológicos
Mensajes de error por campo al fallar validación
Recuperación de valores ingresados al fallar
Reporte
Listado completo de inscriptos ordenado por fecha
Temas de interés separados por comas (`GROUP_CONCAT`)
Badge ✅ Íntegro o 🚨 Comprometido por registro
Botón de exportación a Excel
Exportación Excel (.xlsx)
Archivo `.xlsx` real generado con PhpSpreadsheet
Fila de título con fondo morado
Encabezados con fondo oscuro
Filas alternas en morado claro
Columna de integridad con fondo verde o rojo
Encabezados congelados al hacer scroll
---
✅ Validaciones
Implementadas en `helpers/Validator.php` con métodos estáticos:
Método	Campo	Regla
`isValidIdentidad()`	Identidad	Letras, números y guiones, 4-20 chars
`isValidNombre()`	Nombre / Apellido	Solo letras UTF-8 y espacios, mín. 2 chars
`isValidEdad()`	Edad	Número entre 1 y 120
`isValidSexo()`	Sexo	Solo M, F u Otro
`isValidCorreo()`	Correo	RFC válido con `FILTER_VALIDATE_EMAIL`
`isValidCelular()`	Celular	Dígitos, +, guiones y espacios
`isValidAreas()`	Áreas	Al menos 1 seleccionada
Sanitización — `helpers/Sanitizer.php`
Método	Acción
`cleanString()`	`strip_tags` + `htmlspecialchars` UTF-8
`cleanEmail()`	`FILTER_SANITIZE_EMAIL` + lowercase
`cleanPhone()`	Elimina caracteres no permitidos
`cleanIdentidad()`	Mayúsculas + solo chars válidos
`toTitleCase()`	Primera letra mayúscula por palabra (UTF-8)
---
🔐 Seguridad y Firma Digital
Cada registro se firma al guardarse usando los campos críticos:
```
Payload = nombre|apellido|identidad|correo|celular|sexo

Firma RSA   = openssl_sign(payload, privateKey, SHA256)  → prefijo "rsa:"
Firma HMAC  = hash_hmac('sha256', payload, secret)        → prefijo "hmac:"
```
En el reporte se recalcula la firma y se compara con la almacenada:
Estado	Significado
✅ Íntegro	Los datos no han sido alterados desde el registro
🚨 Comprometido	Alguien modificó los datos directamente en la BD
La carpeta `keys/` está protegida con `.htaccess` para bloquear el acceso web a las llaves privadas.
---
🧩 Dificultades y Soluciones
Dificultad	Solución
`openssl_pkey_new()` retorna `false` en WAMP/Windows	Se detecta automáticamente la ruta de `openssl.cnf` con `putenv('OPENSSL_CONF=...')` y se aplica fallback a HMAC-SHA256
Tildes y ñ en formato título	Se usa `mb_convert_case()` con `MB_CASE_TITLE` y encoding UTF-8 en vez de `ucwords()`
Inserción múltiple de áreas con llave foránea	Transacción PDO con `rollBack()` completo si falla alguna inserción en `inscriptor_areas`
Exportar Excel real sin perder formato	Se integró PhpSpreadsheet via Composer para generar `.xlsx` con estilos, colores y encabezados congelados
---
👨‍💻 Desarrollado por
<table>
  <tr>
    <td align="center">
      <b>Abraham Magin</b><br>
      <sub>Lic.Desarrollo y Gestión de Software</sub><br>
      <sub>Universidad Tecnológica de Panamá</sub><br>
      <sub>Facultad de Ingeniería en Sistemas Computacionales</sub>
    </td>
  </tr>
</table>
Curso: Desarrollo de Software VII  
Docente: Ing. Irina Fong  
Año: 2025
---
© 2025 iTECH. All rights reserved.
