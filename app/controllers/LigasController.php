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
                'escudo' => $this->subirEscudo($_FILES['escudo'] ?? null),
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
        $liga = $ligaModel->find($id);

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
                'escudo' => $this->subirEscudo($_FILES['escudo'] ?? null, $liga['escudo'] ?? null),
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
            'liga' => $liga,
        ]);
    }

    public function detalle(int $id): void
    {
        $this->exigirPermiso('ver');

        $ligaModel = $this->model('Liga');
        $liga = $ligaModel->find($id);

        if (!$liga) {
            http_response_code(404);
            die('Liga no encontrada');
        }

        $this->view('ligas/detalle', [
            'titulo' => $liga['nombre'],
            'activeMenu' => 'ligas',
            'liga' => $liga,
            'equipos' => $this->model('Equipo')->porLiga($id),
            'tabla' => $ligaModel->tablaPosiciones($id),
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
        $liga = $ligaModel->find($id);

        if ($liga && !empty($liga['escudo'])) {
            $this->borrarEscudo($liga['escudo']);
        }

        $ligaModel->delete($id);
        $this->redirect('ligas');
    }

    private function subirEscudo(?array $file, ?string $actual = null): ?string
    {
        if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return $actual;
        }

        if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 3 * 1024 * 1024) {
            return $actual;
        }

        $extensionesPermitidas = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];

        $tipo = mime_content_type($file['tmp_name']);

        if (!isset($extensionesPermitidas[$tipo])) {
            return $actual;
        }

        if (!is_dir(UPLOADS_PATH_LIGAS)) {
            mkdir(UPLOADS_PATH_LIGAS, 0755, true);
        }

        $nombreArchivo = 'liga_' . uniqid() . '.' . $extensionesPermitidas[$tipo];

        if (!move_uploaded_file($file['tmp_name'], UPLOADS_PATH_LIGAS . '/' . $nombreArchivo)) {
            return $actual;
        }

        if ($actual) {
            $this->borrarEscudo($actual);
        }

        return 'uploads/ligas/' . $nombreArchivo;
    }

    private function borrarEscudo(string $rutaRelativa): void
    {
        $ruta = PUBLIC_PATH . '/' . ltrim($rutaRelativa, '/');

        if (str_starts_with($rutaRelativa, 'uploads/ligas/') && is_file($ruta)) {
            unlink($ruta);
        }
    }
}
