<?php
/**
 * index.php — Punto de entrada: Formulario de Inscripción
 * MVC: Este archivo actúa como Router → llama al Modelo para
 *      obtener datos y carga la Vista.
 * 
 */
session_start();

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/InscriptorModel.php';

// Generar llaves OpenSSL al inicio (solo una vez)
InscriptorModel::initKeys();

// Obtener datos para el formulario (países y áreas)
$paises = InscriptorModel::getPaises();
$areas  = InscriptorModel::getAreas();

// Cargar vista
require_once __DIR__ . '/views/form_view.php';
