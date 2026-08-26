<?php

/**
 * Router auxiliar SOLO para el servidor embebido de PHP (php -S),
 * que no lee archivos .htaccess. Simula la reescritura de Apache
 * definida en public/.htaccess.
 *
 * Uso: php -S localhost:8000 -t public public/router.php
 *
 * En XAMPP/Apache con mod_rewrite este archivo no se usa.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri !== '/' && file_exists(__DIR__ . $uri) && is_file(__DIR__ . $uri)) {
    return false;
}

$_GET['url'] = ltrim($uri, '/');

require __DIR__ . '/index.php';
