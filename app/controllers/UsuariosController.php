<?php

class UsuariosController extends Controller
{
    public function index(): void
    {
        $usuarioModel = $this->model('Usuario');

        $this->view('usuarios/index', [
            'titulo' => 'Usuarios',
            'activeMenu' => 'usuarios',
            'usuarios' => $usuarioModel->all(),
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
                'rol' => $_POST['rol'] ?? 'entrenador',
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $this->redirect('usuarios');
            return;
        }

        $this->view('usuarios/crear', [
            'titulo' => 'Nuevo usuario',
            'activeMenu' => 'usuarios',
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
                'rol' => $_POST['rol'] ?? 'entrenador',
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $this->redirect('usuarios');
            return;
        }

        $this->view('usuarios/editar', [
            'titulo' => 'Editar usuario',
            'activeMenu' => 'usuarios',
            'usuario' => $usuarioModel->find($id),
        ]);
    }

    public function eliminar(int $id): void
    {
        $usuarioModel = $this->model('Usuario');
        $usuarioModel->delete($id);
        $this->redirect('usuarios');
    }
}
