<?php

/**
 * Controlador base: carga de modelos y renderizado de vistas con layout.
 */
abstract class Controller
{
    protected function model(string $model)
    {
        require_once APP_PATH . '/models/' . $model . '.php';

        return new $model();
    }

    protected function view(string $view, array $data = [], bool $withLayout = true): void
    {
        extract($data);

        $viewFile = VIEWS_PATH . '/' . $view . '.php';

        if (!file_exists($viewFile)) {
            die("Vista no encontrada: {$view}");
        }

        if ($withLayout) {
            require VIEWS_PATH . '/layouts/header.php';
            require VIEWS_PATH . '/layouts/sidebar.php';
            require $viewFile;
            require VIEWS_PATH . '/layouts/footer.php';
        } else {
            require $viewFile;
        }
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}
