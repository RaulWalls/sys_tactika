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

    protected function esAdmin(): bool
    {
        return ($_SESSION['usuario_rol'] ?? '') === 'administrador';
    }

    /**
     * ¿El rol del usuario en sesión tiene permiso de $accion (ver/crear/
     * editar/eliminar) sobre $controlador? Si no se indica $controlador,
     * se usa el controlador actual (static::class).
     */
    protected function tienePermiso(string $accion, ?string $controlador = null): bool
    {
        if ($this->esAdmin()) {
            return true;
        }

        $rolId = $_SESSION['usuario_rol_id'] ?? null;

        if (!$rolId) {
            return false;
        }

        $permisoModel = $this->model('PermisoRol');

        return $permisoModel->tienePermiso((int) $rolId, $controlador ?? static::class, $accion);
    }

    protected function exigirPermiso(string $accion, ?string $controlador = null): void
    {
        if (!$this->tienePermiso($accion, $controlador)) {
            http_response_code(403);
            die('No tienes permiso para realizar esta acción.');
        }
    }

    /**
     * IDs de equipos a los que el usuario en sesión está acotado:
     * null = sin restricción (administrador), [] = ninguno.
     */
    protected function misEquiposIds(): ?array
    {
        if ($this->esAdmin()) {
            return null;
        }

        $rol = $_SESSION['usuario_rol'] ?? '';

        if ($rol === 'entrenador') {
            $stmt = Database::getConnection()->prepare('SELECT equipo_id FROM usuario_equipos WHERE usuario_id = :id');
            $stmt->execute(['id' => $_SESSION['usuario_id'] ?? 0]);

            return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }

        if ($rol === 'jugador') {
            $jugadorId = $_SESSION['usuario_jugador_id'] ?? null;

            if (!$jugadorId) {
                return [];
            }

            $stmt = Database::getConnection()->prepare('SELECT equipo_id FROM jugador_equipos WHERE jugador_id = :id');
            $stmt->execute(['id' => $jugadorId]);

            return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }

        return [];
    }

    /**
     * Guarda un mensaje de error para mostrarlo como alerta (SweetAlert)
     * en la siguiente página que se cargue tras una redirección.
     */
    protected function flashError(string $mensaje): void
    {
        $_SESSION['flash_error'] = $mensaje;
    }
}
