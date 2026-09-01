<?php

class EquiposController extends Controller
{
    public function index(): void
    {
        $equipoModel = $this->model('Equipo');

        $this->view('equipos/index', [
            'titulo' => 'Equipos',
            'activeMenu' => 'equipos',
            'equipos' => $equipoModel->conLiga(),
        ]);
    }

    public function crear(): void
    {
        if ($this->isPost()) {
            $equipoModel = $this->model('Equipo');

            $equipoModel->create([
                'liga_id' => $_POST['liga_id'] ?: null,
                'nombre' => trim($_POST['nombre']),
                'categoria' => $_POST['categoria'] ?? null,
                'temporada' => $_POST['temporada'] ?? null,
                'fecha_ingreso' => $_POST['fecha_ingreso'] ?: null,
                'sede' => $_POST['sede'] ?? null,
                'representante_id' => $_POST['representante_id'] ?: null,
                'dt_id' => $_POST['dt_id'] ?: null,
                'escudo' => $this->subirEscudo($_FILES['escudo'] ?? null),
            ]);

            $this->redirect('equipos');
            return;
        }

        $staffModel = $this->model('Staff');
        $ligaModel = $this->model('Liga');

        $this->view('equipos/crear', [
            'titulo' => 'Nuevo equipo',
            'activeMenu' => 'equipos',
            'staff' => $staffModel->all('apellido ASC'),
            'ligas' => $ligaModel->all('nombre ASC'),
        ]);
    }

    public function editar(int $id): void
    {
        $equipoModel = $this->model('Equipo');
        $equipo = $equipoModel->find($id);

        if ($this->isPost()) {
            $equipoModel->update($id, [
                'liga_id' => $_POST['liga_id'] ?: null,
                'nombre' => trim($_POST['nombre']),
                'categoria' => $_POST['categoria'] ?? null,
                'temporada' => $_POST['temporada'] ?? null,
                'fecha_ingreso' => $_POST['fecha_ingreso'] ?: null,
                'sede' => $_POST['sede'] ?? null,
                'representante_id' => $_POST['representante_id'] ?: null,
                'dt_id' => $_POST['dt_id'] ?: null,
                'escudo' => $this->subirEscudo($_FILES['escudo'] ?? null, $equipo['escudo'] ?? null),
            ]);

            $this->redirect('equipos');
            return;
        }

        $staffModel = $this->model('Staff');
        $ligaModel = $this->model('Liga');

        $this->view('equipos/editar', [
            'titulo' => 'Editar equipo',
            'activeMenu' => 'equipos',
            'equipo' => $equipo,
            'staff' => $staffModel->all('apellido ASC'),
            'ligas' => $ligaModel->all('nombre ASC'),
        ]);
    }

    public function detalle(int $id): void
    {
        $equipoModel = $this->model('Equipo');
        $jugadorModel = $this->model('Jugador');

        $equipo = $equipoModel->conStaff($id);

        if (!$equipo) {
            http_response_code(404);
            die('Equipo no encontrado');
        }

        $this->view('equipos/detalle', [
            'titulo' => $equipo['nombre'],
            'activeMenu' => 'equipos',
            'equipo' => $equipo,
            'jugadores' => $jugadorModel->porEquipo($id),
        ]);
    }

    public function eliminar(int $id): void
    {
        $equipoModel = $this->model('Equipo');
        $equipo = $equipoModel->find($id);

        if ($equipo && !empty($equipo['escudo'])) {
            $this->borrarEscudo($equipo['escudo']);
        }

        $equipoModel->delete($id);
        $this->redirect('equipos');
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

        if (!is_dir(UPLOADS_PATH)) {
            mkdir(UPLOADS_PATH, 0755, true);
        }

        $nombreArchivo = 'equipo_' . uniqid() . '.' . $extensionesPermitidas[$tipo];

        if (!move_uploaded_file($file['tmp_name'], UPLOADS_PATH . '/' . $nombreArchivo)) {
            return $actual;
        }

        if ($actual) {
            $this->borrarEscudo($actual);
        }

        return 'uploads/equipos/' . $nombreArchivo;
    }

    private function borrarEscudo(string $rutaRelativa): void
    {
        $ruta = PUBLIC_PATH . '/' . ltrim($rutaRelativa, '/');

        if (str_starts_with($rutaRelativa, 'uploads/equipos/') && is_file($ruta)) {
            unlink($ruta);
        }
    }
}
