<?php

class StaffController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $staffModel = $this->model('Staff');

        $this->view('staff/index', [
            'titulo' => 'Representantes y DT',
            'activeMenu' => 'staff',
            'staff' => $staffModel->all('apellido ASC'),
        ]);
    }

    public function crear(): void
    {
        $this->exigirPermiso('crear');

        if ($this->isPost()) {
            $staffModel = $this->model('Staff');

            $staffModel->create([
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'telefono' => $_POST['telefono'] ?? null,
            ]);

            $this->redirect('staff');
            return;
        }

        $this->view('staff/crear', [
            'titulo' => 'Nuevo integrante de staff',
            'activeMenu' => 'staff',
        ]);
    }

    public function editar(int $id): void
    {
        $this->exigirPermiso('editar');

        $staffModel = $this->model('Staff');

        if ($this->isPost()) {
            $staffModel->update($id, [
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'telefono' => $_POST['telefono'] ?? null,
            ]);

            $this->redirect('staff');
            return;
        }

        $this->view('staff/editar', [
            'titulo' => 'Editar integrante de staff',
            'activeMenu' => 'staff',
            'persona' => $staffModel->find($id),
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->exigirPermiso('eliminar');

        $staffModel = $this->model('Staff');
        $staffModel->delete($id);
        $this->redirect('staff');
    }
}
