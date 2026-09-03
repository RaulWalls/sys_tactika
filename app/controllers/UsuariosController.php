<?php

class UsuariosController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $usuarioModel = $this->model('Usuario');

        $this->view('usuarios/index', [
            'titulo' => 'Usuarios',
            'activeMenu' => 'usuarios',
            'usuarios' => $usuarioModel->conRol(),
        ]);
    }

    public function crear(): void
    {
        $this->exigirPermiso('crear');

        if ($this->isPost()) {
            $usuarioModel = $this->model('Usuario');

            $id = $usuarioModel->create([
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'rol_id' => (int) $_POST['rol_id'],
                'jugador_id' => $_POST['jugador_id'] ?: null,
                'liga_id' => $_POST['liga_id'] ?: null,
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $usuarioModel->guardarEquipos($id, $_POST['equipos'] ?? []);

            $this->redirect('usuarios');
            return;
        }

        $ligaModel = $this->model('Liga');
        $catRolModel = $this->model('CatRol');
        $equipoModel = $this->model('Equipo');
        $jugadorModel = $this->model('Jugador');

        $this->view('usuarios/crear', [
            'titulo' => 'Nuevo usuario',
            'activeMenu' => 'usuarios',
            'ligas' => $ligaModel->all('nombre ASC'),
            'roles' => $catRolModel->all('nombre ASC'),
            'equipos' => $equipoModel->all('nombre ASC'),
            'jugadores' => $jugadorModel->all('apellido ASC'),
        ]);
    }

    public function editar(int $id): void
    {
        $this->exigirPermiso('editar');

        $usuarioModel = $this->model('Usuario');

        if ($this->isPost()) {
            $usuarioModel->update($id, [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'] ?? '',
                'rol_id' => (int) $_POST['rol_id'],
                'jugador_id' => $_POST['jugador_id'] ?: null,
                'liga_id' => $_POST['liga_id'] ?: null,
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $usuarioModel->guardarEquipos($id, $_POST['equipos'] ?? []);

            $this->redirect('usuarios');
            return;
        }

        $ligaModel = $this->model('Liga');
        $catRolModel = $this->model('CatRol');
        $equipoModel = $this->model('Equipo');
        $jugadorModel = $this->model('Jugador');

        $this->view('usuarios/editar', [
            'titulo' => 'Editar usuario',
            'activeMenu' => 'usuarios',
            'usuario' => $usuarioModel->find($id),
            'ligas' => $ligaModel->all('nombre ASC'),
            'roles' => $catRolModel->all('nombre ASC'),
            'equipos' => $equipoModel->all('nombre ASC'),
            'equiposUsuario' => array_column($usuarioModel->equiposDe($id), 'id'),
            'jugadores' => $jugadorModel->all('apellido ASC'),
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->exigirPermiso('eliminar');

        $usuarioModel = $this->model('Usuario');
        $usuarioModel->delete($id);
        $this->redirect('usuarios');
    }

    /**
     * Cambiar el tema visual propio: disponible para cualquier usuario
     * autenticado, sin pasar por el sistema de permisos por módulo.
     */
    public function tema(): void
    {
        $temasValidos = [
            'default', 'barcelona', 'real_madrid', 'manchester_city', 'psg',
            'juventus', 'bayern_munich', 'liverpool', 'chelsea', 'america',
        ];
        $tema = $_POST['tema'] ?? 'default';

        if (!in_array($tema, $temasValidos, true)) {
            $tema = 'default';
        }

        if (!empty($_SESSION['usuario_id'])) {
            $usuarioModel = $this->model('Usuario');
            $usuarioModel->update((int) $_SESSION['usuario_id'], ['tema' => $tema]);
            $_SESSION['usuario_tema'] = $tema;
        }

        $this->volverAOrigen();
    }

    /**
     * Alternar modo oscuro: disponible para cualquier usuario autenticado.
     */
    public function modoOscuro(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $activo = ($_POST['modo_oscuro'] ?? '') === '1' ? 1 : 0;

            $usuarioModel = $this->model('Usuario');
            $usuarioModel->update((int) $_SESSION['usuario_id'], ['modo_oscuro' => $activo]);
            $_SESSION['usuario_modo_oscuro'] = (bool) $activo;
        }

        $this->volverAOrigen();
    }

    private function volverAOrigen(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $destino = (parse_url($referer, PHP_URL_HOST) === $_SERVER['HTTP_HOST']) ? $referer : (BASE_URL . '/dashboard');

        header('Location: ' . $destino);
        exit;
    }
}
