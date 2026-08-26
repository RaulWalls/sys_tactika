<?php

class EquiposController extends Controller
{
    public function index(): void
    {
        $equipoModel = $this->model('Equipo');

        $this->view('equipos/index', [
            'titulo' => 'Equipos',
            'activeMenu' => 'equipos',
            'equipos' => $equipoModel->all(),
        ]);
    }

    public function crear(): void
    {
        if ($this->isPost()) {
            $equipoModel = $this->model('Equipo');

            $equipoModel->create([
                'nombre' => trim($_POST['nombre']),
                'categoria' => $_POST['categoria'] ?? null,
                'temporada' => $_POST['temporada'] ?? null,
                'escudo' => $_POST['escudo'] ?? null,
            ]);

            $this->redirect('equipos');
            return;
        }

        $this->view('equipos/crear', [
            'titulo' => 'Nuevo equipo',
            'activeMenu' => 'equipos',
        ]);
    }

    public function editar(int $id): void
    {
        $equipoModel = $this->model('Equipo');

        if ($this->isPost()) {
            $equipoModel->update($id, [
                'nombre' => trim($_POST['nombre']),
                'categoria' => $_POST['categoria'] ?? null,
                'temporada' => $_POST['temporada'] ?? null,
                'escudo' => $_POST['escudo'] ?? null,
            ]);

            $this->redirect('equipos');
            return;
        }

        $this->view('equipos/editar', [
            'titulo' => 'Editar equipo',
            'activeMenu' => 'equipos',
            'equipo' => $equipoModel->find($id),
        ]);
    }

    public function eliminar(int $id): void
    {
        $equipoModel = $this->model('Equipo');
        $equipoModel->delete($id);
        $this->redirect('equipos');
    }
}
