<?php
/**
 * reporte.php — Punto de entrada: Reporte de Inscripciones
 * 
 */
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/InscriptorModel.php';

InscriptorModel::initKeys();

// Obtener todos los inscriptores con sus áreas
$inscriptores = InscriptorModel::getAll();

// Cargar vista
require_once __DIR__ . '/views/report_view.php';
