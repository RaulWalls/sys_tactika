<?php

class PartidosController extends Controller
{
    public function index(): void
    {
        $partidoModel = $this->model('Partido');

        $this->view('partidos/index', [
            'titulo' => 'Calendario de partidos',
            'activeMenu' => 'partidos',
            'partidos' => $partidoModel->todosOrdenados(),
        ]);
    }

    public function crear(): void
    {
        if ($this->isPost()) {
            $partidoModel = $this->model('Partido');

            $partidoModel->create([
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'rival' => trim($_POST['rival']),
                'fecha' => $_POST['fecha'],
                'hora' => $_POST['hora'],
                'sede' => $_POST['sede'] ?? null,
                'tipo' => $_POST['tipo'] ?? 'local',
                'competencia' => $_POST['competencia'] ?? null,
                'jornada' => $_POST['jornada'] ?? null,
                'estado' => $_POST['estado'] ?? 'programado',
            ]);

            $this->redirect('partidos');
            return;
        }

        $equipoModel = $this->model('Equipo');

        $this->view('partidos/crear', [
            'titulo' => 'Nuevo partido',
            'activeMenu' => 'partidos',
            'equipos' => $equipoModel->all(),
        ]);
    }

    public function editar(int $id): void
    {
        $partidoModel = $this->model('Partido');

        if ($this->isPost()) {
            $partidoModel->update($id, [
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'rival' => trim($_POST['rival']),
                'fecha' => $_POST['fecha'],
                'hora' => $_POST['hora'],
                'sede' => $_POST['sede'] ?? null,
                'tipo' => $_POST['tipo'] ?? 'local',
                'competencia' => $_POST['competencia'] ?? null,
                'jornada' => $_POST['jornada'] ?? null,
                'goles_favor' => $_POST['goles_favor'] !== '' ? $_POST['goles_favor'] : null,
                'goles_contra' => $_POST['goles_contra'] !== '' ? $_POST['goles_contra'] : null,
                'estado' => $_POST['estado'] ?? 'programado',
            ]);

            $this->redirect('partidos');
            return;
        }

        $equipoModel = $this->model('Equipo');

        $this->view('partidos/editar', [
            'titulo' => 'Editar partido',
            'activeMenu' => 'partidos',
            'partido' => $partidoModel->find($id),
            'equipos' => $equipoModel->all(),
        ]);
    }

    public function eliminar(int $id): void
    {
        $partidoModel = $this->model('Partido');
        $partidoModel->delete($id);
        $this->redirect('partidos');
    }
}
