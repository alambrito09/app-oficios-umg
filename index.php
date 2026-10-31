<?php
/**
 * CodeIgniter 3 Front Controller
 * Proyecto: App Oficios Bajo Demanda (Monolito)
 * Stack: Apache + PHP + MySQL + CodeIgniter 3
 *
 * Requisitos previos:
 * 1. Instalar Docker y Docker Compose
 * 2. Ejecutar: docker-compose up -d
 * 3. Acceder: http://localhost:8080/
 */

define('ENVIRONMENT', getenv('CI_ENV') ?: 'development');
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

$root_path = __DIR__;
$system_path = $root_path . DIRECTORY_SEPARATOR . 'system';
$application_folder = $root_path . DIRECTORY_SEPARATOR . 'application';
$view_folder = '';

if (!is_dir($system_path) || !is_file($system_path . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'CodeIgniter.php')) {
    header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
    echo 'La carpeta system/ de CodeIgniter 3 no está configurada.';
    exit(1);
}

define('BASEPATH', str_replace('\\', '/', realpath($system_path) . '/'));
define('APPPATH', str_replace('\\', '/', realpath($application_folder) . '/'));
define('VIEWPATH', (!empty($view_folder) ? realpath($view_folder).'/' : APPPATH.'views/'));

require_once BASEPATH . 'core/CodeIgniter.php';
