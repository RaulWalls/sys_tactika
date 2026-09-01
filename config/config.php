<?php
/**
 * Configuración general de Táctika.
 */

// ---- Base de datos ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'tactika');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ---- Aplicación ----
define('APP_NAME', 'Táctika');

// Se calcula automáticamente a partir de la URL con la que se accede a
// public/index.php, para que funcione tanto en el docroot
// (http://localhost/) como en una subcarpeta (http://localhost/tactika/public).
if (php_sapi_name() !== 'cli') {
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    define('BASE_URL', $scriptDir === '/' ? '' : rtrim($scriptDir, '/'));
} else {
    define('BASE_URL', '');
}

// ---- Rutas del sistema de archivos ----
define('APP_PATH', dirname(__DIR__) . '/app');
define('VIEWS_PATH', APP_PATH . '/views');
define('PUBLIC_PATH', dirname(__DIR__) . '/public');
define('UPLOADS_PATH', PUBLIC_PATH . '/uploads/equipos');
define('UPLOADS_URL', BASE_URL . '/uploads/equipos');
define('UPLOADS_PATH_JUGADORES', PUBLIC_PATH . '/uploads/jugadores');
define('UPLOADS_URL_JUGADORES', BASE_URL . '/uploads/jugadores');

// ---- Errores (desactivar display_errors en producción) ----
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
