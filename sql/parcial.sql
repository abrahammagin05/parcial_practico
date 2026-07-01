-- ============================================================
--  Parcial Práctico 1 — iTECH Inscripciones
--  Ejecutar en: http://127.1.1.1/phpmyadmin/
-- ============================================================

CREATE DATABASE IF NOT EXISTS parcial_practico
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE parcial_practico;

-- ── Tabla de países ──────────────────────────────────────────
CREATE TABLE paises (
    id_pais      INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    nombre_pais  VARCHAR(100)     NOT NULL
) ENGINE=InnoDB;

INSERT INTO paises (nombre_pais) VALUES
('Panamá'),('Colombia'),('Venezuela'),('Costa Rica'),('México'),
('Argentina'),('Chile'),('Perú'),('Ecuador'),('Guatemala'),
('Honduras'),('El Salvador'),('Nicaragua'),('Bolivia'),('Paraguay'),
('Uruguay'),('Cuba'),('República Dominicana'),('España'),('Estados Unidos');

-- ── Tabla de áreas de interés ────────────────────────────────
CREATE TABLE areas_interes (
    id_area      INT UNSIGNED     AUTO_INCREMENT PRIMARY KEY,
    nombre_area  VARCHAR(100)     NOT NULL
) ENGINE=InnoDB;

INSERT INTO areas_interes (nombre_area) VALUES
('Cloud Computing'),('Big Data'),('Desarrollo Móvil'),
('Ciberseguridad'),('IoT (Internet de las Cosas)'),
('Machine Learning'),('DevOps'),('Python');

-- ── Tabla de inscriptores ────────────────────────────────────
CREATE TABLE inscriptores (
    id_inscriptor   INT UNSIGNED        AUTO_INCREMENT PRIMARY KEY,
    identidad       VARCHAR(20)         NOT NULL UNIQUE,
    nombre          VARCHAR(100)        NOT NULL,
    apellido        VARCHAR(100)        NOT NULL,
    edad            TINYINT UNSIGNED    NOT NULL,
    sexo            ENUM('M','F','Otro') NOT NULL,
    id_pais         INT UNSIGNED        NOT NULL,
    nacionalidad    VARCHAR(100)        NOT NULL,
    correo          VARCHAR(150)        NOT NULL UNIQUE,
    celular         VARCHAR(20)         NOT NULL UNIQUE,
    observaciones   TEXT,
    fecha_registro  DATETIME            DEFAULT CURRENT_TIMESTAMP,
    firma_hash      VARCHAR(600)        NULL,
    CONSTRAINT fk_inscriptor_pais
        FOREIGN KEY (id_pais) REFERENCES paises(id_pais)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ── Tabla relacional inscriptor ↔ áreas ──────────────────────
CREATE TABLE inscriptor_areas (
    id_inscriptor   INT UNSIGNED  NOT NULL,
    id_area         INT UNSIGNED  NOT NULL,
    PRIMARY KEY (id_inscriptor, id_area),
    CONSTRAINT fk_ia_inscriptor
        FOREIGN KEY (id_inscriptor) REFERENCES inscriptores(id_inscriptor)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_ia_area
        FOREIGN KEY (id_area) REFERENCES areas_interes(id_area)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;
