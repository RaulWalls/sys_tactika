<?php

class UsuariosController extends Controller
{
    public function index(): void
    {
        $usuarioModel = $this->model('Usuario');

        $this->view('usuarios/index', [
            'titulo' => 'Usuarios',
            'activeMenu' => 'usuarios',
            'usuarios' => $usuarioModel->conRol(),
        ]);
    }

    public function crear(): void
    {
        if ($this->isPost()) {
            $usuarioModel = $this->model('Usuario');

            $usuarioModel->create([
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'rol_id' => (int) $_POST['rol_id'],
                'liga_id' => $_POST['liga_id'] ?: null,
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $this->redirect('usuarios');
            return;
        }

        $ligaModel = $this->model('Liga');
        $catRolModel = $this->model('CatRol');

        $this->view('usuarios/crear', [
            'titulo' => 'Nuevo usuario',
            'activeMenu' => 'usuarios',
            'ligas' => $ligaModel->all('nombre ASC'),
            'roles' => $catRolModel->all('nombre ASC'),
        ]);
    }

    public function editar(int $id): void
    {
        $usuarioModel = $this->model('Usuario');

        if ($this->isPost()) {
            $usuarioModel->update($id, [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'] ?? '',
                'rol_id' => (int) $_POST['rol_id'],
                'liga_id' => $_POST['liga_id'] ?: null,
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $this->redirect('usuarios');
            return;
        }

        $ligaModel = $this->model('Liga');
        $catRolModel = $this->model('CatRol');

        $this->view('usuarios/editar', [
            'titulo' => 'Editar usuario',
            'activeMenu' => 'usuarios',
            'usuario' => $usuarioModel->find($id),
            'ligas' => $ligaModel->all('nombre ASC'),
            'roles' => $catRolModel->all('nombre ASC'),
        ]);
    }

    public function eliminar(int $id): void
    {
        $usuarioModel = $this->model('Usuario');
        $usuarioModel->delete($id);
        $this->redirect('usuarios');
    }
}
