<?php

/**
 * Front controller / router.
 *
 * Convierte una URL tipo /jugadores/editar/3 en:
 *   Controlador: JugadoresController
 *   Método:      editar
 *   Parámetros:  [3]
 *
 * Si no se indica controlador o método, cae por defecto en
 * DashboardController::index().
 */
class App
{
    protected string $controllerName = 'DashboardController';
    protected string $method = 'index';
    protected array $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        if (!empty($url[0]) && file_exists(APP_PATH . '/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
            $this->controllerName = ucfirst($url[0]) . 'Controller';
            unset($url[0]);
        }

        require_once APP_PATH . '/controllers/' . $this->controllerName . '.php';
        $controller = new $this->controllerName();

        if (!empty($url[1]) && method_exists($controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];

        if (!method_exists($controller, $this->method)) {
            http_response_code(404);
            die('404 - Página no encontrada');
        }

        call_user_func_array([$controller, $this->method], $this->params);
    }

    protected function parseUrl(): array
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);

            return explode('/', $url);
        }

        return [];
    }
}
