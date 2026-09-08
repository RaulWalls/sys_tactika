<?php

class JugadoresController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $jugadorModel = $this->model('Jugador');
        $equipoModel = $this->model('Equipo');
        $misEquipos = $this->misEquiposIds();
        $equipoSeleccionado = null;

        $porPagina = 15;
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        if ($this->esAdmin()) {
            $equipos = $equipoModel->conLiga();
            $equipoSeleccionado = !empty($_GET['equipo_id']) ? (int) $_GET['equipo_id'] : null;
            $filtroEquipos = $equipoSeleccionado ? [$equipoSeleccionado] : null;
            $nombreEquipo = $equipoSeleccionado
                ? (array_column($equipos, 'nombre', 'id')[$equipoSeleccionado] ?? '')
                : 'todos los equipos';
        } else {
            $equipos = $equipoModel->conLiga($misEquipos);
            $filtroEquipos = $misEquipos;
            $nombreEquipo = implode(', ', array_column($equipos, 'nombre'));
        }

        $totalJugadores = $jugadorModel->contarConEquipo($filtroEquipos);
        $totalPaginas = max(1, (int) ceil($totalJugadores / $porPagina));
        $pagina = min($pagina, $totalPaginas);

        $jugadores = $jugadorModel->conEquipo($filtroEquipos, $porPagina, ($pagina - 1) * $porPagina);

        $this->view('jugadores/index', [
            'titulo' => 'Jugadores',
            'activeMenu' => 'jugadores',
            'jugadores' => $jugadores,
            'equipos' => $equipos,
            'equipoSeleccionado' => $equipoSeleccionado,
            'nombreEquipo' => $nombreEquipo,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
        ]);
    }

    public function crear(): void
    {
        $this->exigirPermiso('crear');

        if ($this->isPost()) {
            $jugadorModel = $this->model('Jugador');

            $id = $jugadorModel->create([
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

            $jugadorModel->guardarEquipos($id, $_POST['equipos'] ?? []);

            $this->redirect('jugadores');
            return;
        }

        $equipoModel = $this->model('Equipo');
        $posicionModel = $this->model('CatPosicion');

        $this->view('jugadores/crear', [
            'titulo' => 'Nuevo jugador',
            'activeMenu' => 'jugadores',
            'equipos' => $equipoModel->conLiga($this->misEquiposIds()),
            'posiciones' => $posicionModel->all('nombre ASC'),
        ]);
    }

    public function editar(int $id): void
    {
        $this->exigirPermiso('editar');
        $this->verificarAccesoJugador($id);

        $esRolJugador = ($_SESSION['usuario_rol'] ?? '') === 'jugador';

        $jugadorModel = $this->model('Jugador');
        $jugador = $jugadorModel->conDetalle($id);

        if ($this->isPost()) {
            // Un jugador solo puede modificar sus datos generales; el
            // resto (posición, número, estado, capitán, foto, equipos)
            // solo lo activa un administrador o entrenador.
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
            ];

            if (!$esRolJugador) {
                $datos['posicion_id'] = $_POST['posicion_id'] ?: null;
                $datos['numero'] = $_POST['numero'] !== '' ? $_POST['numero'] : null;
                $datos['estado'] = $_POST['estado'] ?? 'activo';
                $datos['capitan'] = isset($_POST['capitan']) ? 1 : 0;
                $datos['foto'] = $this->subirFoto($_FILES['foto'] ?? null, $jugador['foto'] ?? null);
            }

            $jugadorModel->update($id, $datos);

            if (!$esRolJugador) {
                $jugadorModel->guardarEquipos($id, $_POST['equipos'] ?? []);
            }

            $this->redirect($esRolJugador ? 'jugadores/detalle/' . $id : 'jugadores');
            return;
        }

        $equipoModel = $this->model('Equipo');
        $posicionModel = $this->model('CatPosicion');

        $this->view('jugadores/editar', [
            'titulo' => 'Editar jugador',
            'activeMenu' => 'jugadores',
            'jugador' => $jugador,
            'equipos' => $equipoModel->conLiga($this->misEquiposIds()),
            'posiciones' => $posicionModel->all('nombre ASC'),
            'esRolJugador' => $esRolJugador,
        ]);
    }

    public function detalle(int $id): void
    {
        $this->exigirPermiso('ver');
        $this->verificarAccesoJugador($id);

        $jugadorModel = $this->model('Jugador');
        $atributoModel = $this->model('JugadorAtributo');
        $estadisticaModel = $this->model('JugadorEstadistica');
        $jugadorAmonestacionesModel = $this->model('JugadorAmonestaciones');

        $jugador = $jugadorModel->conDetalle($id);

        if (!$jugador) {
            http_response_code(404);
            die('Jugador no encontrado');
        }

        $this->view('jugadores/detalle', [
            'titulo' => $jugador['nombre'] . ' ' . $jugador['apellido'],
            'activeMenu' => 'jugadores',
            'jugador' => $jugador,
            'jugador_amonestado' => $jugadorAmonestacionesModel->porJugador($id),
            'atributos' => $atributoModel->porJugador($id),
            'rendimiento' => $estadisticaModel->porJugador($id),
        ]);
    }

    public function atributos(int $id): void
    {
        $this->exigirPermiso('editar');
        $this->verificarAccesoJugador($id);
        $this->bloquearRolJugador();

        $jugadorModel = $this->model('Jugador');

        if (!$jugadorModel->find($id)) {
            http_response_code(404);
            die('Jugador no encontrado');
        }

        if ($this->isPost()) {
            $atributoModel = $this->model('JugadorAtributo');
            $valores = [];

            foreach ($_POST['valor'] ?? [] as $atributoId => $valor) {
                $valores[(int) $atributoId] = (int) $valor;
            }

            $atributoModel->guardar($id, $valores);
        }

        $this->redirect('jugadores/detalle/' . $id);
    }

    public function rendimiento(int $id): void
    {
        $this->exigirPermiso('editar');
        $this->verificarAccesoJugador($id);
        $this->bloquearRolJugador();

        $jugadorModel = $this->model('Jugador');

        if (!$jugadorModel->find($id)) {
            http_response_code(404);
            die('Jugador no encontrado');
        }

        if ($this->isPost()) {
            $estadisticaModel = $this->model('JugadorEstadistica');
            $valores = [];

            foreach ($_POST['valor'] ?? [] as $estadisticaId => $valor) {
                $valores[(int) $estadisticaId] = (int) $valor;
            }

            $estadisticaModel->guardar($id, $valores);
        }

        $this->redirect('jugadores/detalle/' . $id);
    }

    public function eliminar(int $id): void
    {
        $this->exigirPermiso('eliminar');
        $this->verificarAccesoJugador($id);

        $jugadorModel = $this->model('Jugador');
        $jugador = $jugadorModel->find($id);

        if ($jugador && !empty($jugador['foto'])) {
            $this->borrarFoto($jugador['foto']);
        }

        $jugadorModel->delete($id);
        $this->redirect('jugadores');
    }

    /**
     * Bloquea el acceso a un jugador que no corresponda al usuario en sesión:
     * - jugador: solo su propio perfil.
     * - entrenador: solo jugadores de alguno de sus equipos.
     * - administrador: sin restricción.
     */
    private function verificarAccesoJugador(int $id): void
    {
        $rol = $_SESSION['usuario_rol'] ?? '';

        if ($rol === 'jugador') {
            $miJugadorId = (int) ($_SESSION['usuario_jugador_id'] ?? 0);

            if ($miJugadorId !== $id) {
                $this->flashError('No puedes ver el detalle de otro jugador.');
                $this->redirect('jugadores');
            }

            return;
        }

        $misEquipos = $this->misEquiposIds();

        if ($misEquipos === null) {
            return;
        }

        $jugadorModel = $this->model('Jugador');
        $equiposDelJugador = array_column($jugadorModel->equiposDe($id), 'id');

        if (!array_intersect($misEquipos, $equiposDelJugador)) {
            http_response_code(403);
            die('No tienes acceso a este jugador.');
        }
    }

    /**
     * Atributos y estadísticas de rendimiento los captura siempre un
     * entrenador o administrador, nunca el propio jugador.
     */
    private function bloquearRolJugador(): void
    {
        if (($_SESSION['usuario_rol'] ?? '') === 'jugador') {
            http_response_code(403);
            die('Solo un administrador o entrenador puede modificar estos datos.');
        }
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
