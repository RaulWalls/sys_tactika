<?php

class JugadoresController extends Controller
{
    public function index(): void
    {
        $jugadorModel = $this->model('Jugador');

        $this->view('jugadores/index', [
            'titulo' => 'Jugadores',
            'activeMenu' => 'jugadores',
            'jugadores' => $jugadorModel->conEquipo(),
        ]);
    }

    public function crear(): void
    {
        if ($this->isPost()) {
            $jugadorModel = $this->model('Jugador');

            $jugadorModel->create([
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'posicion_id' => $_POST['posicion_id'] ?: null,
                'numero' => $_POST['numero'] !== '' ? $_POST['numero'] : null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'estado' => $_POST['estado'] ?? 'activo',
                'capitan' => isset($_POST['capitan']) ? 1 : 0,
                'foto' => $this->subirFoto($_FILES['foto'] ?? null),
            ]);

            $this->redirect('jugadores');
            return;
        }

        $equipoModel = $this->model('Equipo');
        $posicionModel = $this->model('CatPosicion');

        $this->view('jugadores/crear', [
            'titulo' => 'Nuevo jugador',
            'activeMenu' => 'jugadores',
            'equipos' => $equipoModel->all(),
            'posiciones' => $posicionModel->all('nombre ASC'),
        ]);
    }

    public function editar(int $id): void
    {
        $jugadorModel = $this->model('Jugador');
        $jugador = $jugadorModel->find($id);

        if ($this->isPost()) {
            $jugadorModel->update($id, [
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'posicion_id' => $_POST['posicion_id'] ?: null,
                'numero' => $_POST['numero'] !== '' ? $_POST['numero'] : null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'estado' => $_POST['estado'] ?? 'activo',
                'capitan' => isset($_POST['capitan']) ? 1 : 0,
                'foto' => $this->subirFoto($_FILES['foto'] ?? null, $jugador['foto'] ?? null),
            ]);

            $this->redirect('jugadores');
            return;
        }

        $equipoModel = $this->model('Equipo');
        $posicionModel = $this->model('CatPosicion');

        $this->view('jugadores/editar', [
            'titulo' => 'Editar jugador',
            'activeMenu' => 'jugadores',
            'jugador' => $jugador,
            'equipos' => $equipoModel->all(),
            'posiciones' => $posicionModel->all('nombre ASC'),
        ]);
    }

    public function detalle(int $id): void
    {
        $jugadorModel = $this->model('Jugador');
        $estadisticaModel = $this->model('JugadorEstadistica');

        $jugador = $jugadorModel->conDetalle($id);

        if (!$jugador) {
            http_response_code(404);
            die('Jugador no encontrado');
        }

        $this->view('jugadores/detalle', [
            'titulo' => $jugador['nombre'] . ' ' . $jugador['apellido'],
            'activeMenu' => 'jugadores',
            'jugador' => $jugador,
            'estadisticas' => $estadisticaModel->porJugador($id),
        ]);
    }

    public function estadisticas(int $id): void
    {
        $jugadorModel = $this->model('Jugador');
        $estadisticaModel = $this->model('JugadorEstadistica');

        $jugador = $jugadorModel->find($id);

        if (!$jugador) {
            http_response_code(404);
            die('Jugador no encontrado');
        }

        if ($this->isPost()) {
            $valores = [];

            foreach ($_POST['valor'] ?? [] as $estadisticaId => $valor) {
                $valores[(int) $estadisticaId] = (int) $valor;
            }

            $estadisticaModel->guardar($id, $valores);

            $this->redirect('jugadores/detalle/' . $id);
            return;
        }

        $this->view('jugadores/estadisticas', [
            'titulo' => 'Estadísticas de ' . $jugador['nombre'],
            'activeMenu' => 'jugadores',
            'jugador' => $jugador,
            'estadisticas' => $estadisticaModel->porJugador($id),
        ]);
    }

    public function eliminar(int $id): void
    {
        $jugadorModel = $this->model('Jugador');
        $jugador = $jugadorModel->find($id);

        if ($jugador && !empty($jugador['foto'])) {
            $this->borrarFoto($jugador['foto']);
        }

        $jugadorModel->delete($id);
        $this->redirect('jugadores');
    }

    private function subirFoto(?array $file, ?string $actual = null): ?string
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

        if (!is_dir(UPLOADS_PATH_JUGADORES)) {
            mkdir(UPLOADS_PATH_JUGADORES, 0755, true);
        }

        $nombreArchivo = 'jugador_' . uniqid() . '.' . $extensionesPermitidas[$tipo];

        if (!move_uploaded_file($file['tmp_name'], UPLOADS_PATH_JUGADORES . '/' . $nombreArchivo)) {
            return $actual;
        }

        if ($actual) {
            $this->borrarFoto($actual);
        }

        return 'uploads/jugadores/' . $nombreArchivo;
    }

    private function borrarFoto(string $rutaRelativa): void
    {
        $ruta = PUBLIC_PATH . '/' . ltrim($rutaRelativa, '/');

        if (str_starts_with($rutaRelativa, 'uploads/jugadores/') && is_file($ruta)) {
            unlink($ruta);
        }
    }
}
