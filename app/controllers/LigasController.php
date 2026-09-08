<?php

class LigasController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $ligaModel = $this->model('Liga');

        $this->view('ligas/index', [
            'titulo' => 'Ligas y competencias',
            'activeMenu' => 'ligas',
            'ligas' => $ligaModel->all(),
        ]);
    }

    public function crear(): void
    {
        $this->exigirPermiso('crear');

        if ($this->isPost()) {
            $error = $this->validarTotalEquipos($_POST['total_equipos'] ?? '');

            if ($error) {
                $this->view('ligas/crear', [
                    'titulo' => 'Nueva liga',
                    'activeMenu' => 'ligas',
                    'error' => $error,
                    'liga' => $_POST,
                ]);
                return;
            }

            $ligaModel = $this->model('Liga');

            $ligaModel->create([
                'nombre' => trim($_POST['nombre']),
                'temporada' => $_POST['temporada'] ?? null,
                'fecha_inicio' => $_POST['fecha_inicio'] ?: null,
                'fecha_fin' => $_POST['fecha_fin'] ?: null,
                'dias_partido_semana' => $_POST['dias_partido_semana'] ?? 1,
                'total_equipos' => $_POST['total_equipos'] !== '' ? (int) $_POST['total_equipos'] : null,
                'permite_calendario_incompleto' => isset($_POST['permite_calendario_incompleto']) ? 1 : 0,
                'estado' => $_POST['estado'] ?? 'activa',
            ]);

            $this->redirect('ligas');
            return;
        }

        $this->view('ligas/crear', [
            'titulo' => 'Nueva liga',
            'activeMenu' => 'ligas',
        ]);
    }

    public function editar(int $id): void
    {
        $this->exigirPermiso('editar');

        $ligaModel = $this->model('Liga');

        if ($this->isPost()) {
            $error = $this->validarTotalEquipos($_POST['total_equipos'] ?? '');

            if ($error) {
                $this->view('ligas/editar', [
                    'titulo' => 'Editar liga',
                    'activeMenu' => 'ligas',
                    'error' => $error,
                    'liga' => ['id' => $id] + $_POST,
                ]);
                return;
            }

            $ligaModel->update($id, [
                'nombre' => trim($_POST['nombre']),
                'temporada' => $_POST['temporada'] ?? null,
                'fecha_inicio' => $_POST['fecha_inicio'] ?: null,
                'fecha_fin' => $_POST['fecha_fin'] ?: null,
                'dias_partido_semana' => $_POST['dias_partido_semana'] ?? 1,
                'total_equipos' => $_POST['total_equipos'] !== '' ? (int) $_POST['total_equipos'] : null,
                'permite_calendario_incompleto' => isset($_POST['permite_calendario_incompleto']) ? 1 : 0,
                'estado' => $_POST['estado'] ?? 'activa',
            ]);

            $this->redirect('ligas');
            return;
        }

        $this->view('ligas/editar', [
            'titulo' => 'Editar liga',
            'activeMenu' => 'ligas',
            'liga' => $ligaModel->find($id),
        ]);
    }

    private function validarTotalEquipos(string $valor): ?string
    {
        if ($valor === '') {
            return null;
        }

        if (!ctype_digit($valor) || (int) $valor < 2 || (int) $valor % 2 !== 0) {
            return 'El total de equipos debe ser un número par (2, 4, 6...).';
        }

        return null;
    }

    public function eliminar(int $id): void
    {
        $this->exigirPermiso('eliminar');

        $ligaModel = $this->model('Liga');
        $ligaModel->delete($id);
        $this->redirect('ligas');
    }
}
