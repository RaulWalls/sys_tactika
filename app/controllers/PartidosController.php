<?php

class PartidosController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $partidoModel = $this->model('Partido');

        $porPagina = 15;
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $totalPartidos = $partidoModel->contarTodos();
        $totalPaginas = max(1, (int) ceil($totalPartidos / $porPagina));
        $pagina = min($pagina, $totalPaginas);

        $this->view('partidos/index', [
            'titulo' => 'Calendario de partidos',
            'activeMenu' => 'partidos',
            'partidos' => $partidoModel->todosOrdenados($porPagina, ($pagina - 1) * $porPagina),
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
        ]);
    }

    public function crear(): void
    {
        $this->exigirPermiso('crear');

        if ($this->isPost()) {
            $partidoModel = $this->model('Partido');

            $partidoModel->create([
                'liga_id' => $_POST['liga_id'] ?: null,
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'rival' => trim($_POST['rival']),
                'fecha' => $_POST['fecha'],
                'hora' => $_POST['hora'],
                'sede' => $_POST['sede'] ?? null,
                'tipo' => $_POST['tipo'] ?? 'local',
                'competencia' => $this->nombreLiga($_POST['liga_id'] ?? null),
                'jornada' => $_POST['jornada'] ?? null,
                'estado' => $_POST['estado'] ?? 'programado',
            ]);

            $this->redirect('partidos');
            return;
        }

        $equipoModel = $this->model('Equipo');
        $ligaModel = $this->model('Liga');

        $this->view('partidos/crear', [
            'titulo' => 'Nuevo partido',
            'activeMenu' => 'partidos',
            'equipos' => $equipoModel->all(),
            'ligas' => $ligaModel->all('nombre ASC'),
        ]);
    }

    public function editar(int $id): void
    {
        $this->exigirPermiso('editar');

        $partidoModel = $this->model('Partido');

        if ($this->isPost()) {
            $equipoId = $_POST['equipo_id'] ?: null;
            $ligaId = $_POST['liga_id'] ?: null;
            $rival = trim($_POST['rival']);
            $estado = $_POST['estado'] ?? 'programado';

            $partidoModel->update($id, [
                'liga_id' => $ligaId,
                'equipo_id' => $equipoId,
                'rival' => $rival,
                'fecha' => $_POST['fecha'],
                'hora' => $_POST['hora'],
                'sede' => $_POST['sede'] ?? null,
                'tipo' => $_POST['tipo'] ?? 'local',
                'competencia' => $this->nombreLiga($ligaId),
                'jornada' => $_POST['jornada'] ?? null,
                'goles_favor' => $_POST['goles_favor'] !== '' ? $_POST['goles_favor'] : null,
                'goles_contra' => $_POST['goles_contra'] !== '' ? $_POST['goles_contra'] : null,
                'estado' => $estado,
            ]);

            // El marcador afecta directamente a los logros/badges de equipo
            // (victorias, rachas, porterías en cero...), aunque no se hayan
            // capturado las estadísticas por jugador de este partido.
            if ($estado === 'jugado' && $equipoId) {
                $engine = new LogroEngine();
                $ligaIdInt = $ligaId ? (int) $ligaId : null;
                $nuevos = array_merge(
                    $engine->evaluarLogrosEquipo((int) $equipoId),
                    $engine->evaluarBadgesEquipo((int) $equipoId, $ligaIdInt)
                );

                $rivalEquipo = $this->model('Equipo')->porNombre($rival);
                if ($rivalEquipo) {
                    $nuevos = array_merge(
                        $nuevos,
                        $engine->evaluarLogrosEquipo((int) $rivalEquipo['id']),
                        $engine->evaluarBadgesEquipo((int) $rivalEquipo['id'], $ligaIdInt)
                    );
                }

                if ($nuevos) {
                    $this->flashExito('Nuevo logro/badge de equipo desbloqueado:<br>' . implode('<br>', array_map('htmlspecialchars', $nuevos)));
                }
            }

            $this->redirect('partidos');
            return;
        }

        $equipoModel = $this->model('Equipo');
        $ligaModel = $this->model('Liga');

        $this->view('partidos/editar', [
            'titulo' => 'Editar partido',
            'activeMenu' => 'partidos',
            'partido' => $partidoModel->find($id),
            'equipos' => $equipoModel->all(),
            'ligas' => $ligaModel->all('nombre ASC'),
        ]);
    }

    private function nombreLiga($ligaId): ?string
    {
        if (!$ligaId) {
            return null;
        }

        $liga = $this->model('Liga')->find((int) $ligaId);

        return $liga['nombre'] ?? null;
    }

    /**
     * Captura goles/asistencias/minutos/titular/rating/MVP por jugador
     * para un partido. Se gestiona como parte de la gestión de Jugadores
     * (mismo permiso que editar jugadores), no del calendario en sí, ya
     * que el entrenador tiene el calendario en solo lectura pero sí puede
     * editar a los jugadores de su equipo.
     */
    public function eventos(int $id): void
    {
        $this->exigirPermiso('editar', 'JugadoresController');

        if (($_SESSION['usuario_rol'] ?? '') === 'jugador') {
            http_response_code(403);
            die('Solo un administrador o entrenador puede capturar estadísticas de partido.');
        }

        $partidoModel = $this->model('Partido');
        $partido = $partidoModel->find($id);

        if (!$partido || !$partido['equipo_id']) {
            http_response_code(404);
            die('Partido no encontrado');
        }

        $misEquipos = $this->misEquiposIds();
        if ($misEquipos !== null && !in_array((int) $partido['equipo_id'], $misEquipos, true)) {
            http_response_code(403);
            die('No tienes acceso a este partido.');
        }

        $eventoModel = $this->model('JugadorPartidoEvento');

        if ($this->isPost()) {
            $filas = $_POST['evento'] ?? [];
            $eventoModel->guardarLote($id, $filas);

            // Los eventos por partido son la fuente de verdad: sincroniza
            // goles/asistencias/minutos hacia las estadísticas de rendimiento
            // que se muestran en el detalle del jugador.
            $estadisticaModel = $this->model('JugadorEstadistica');
            $ligaId = $partido['liga_id'] ? (int) $partido['liga_id'] : null;
            $engine = new LogroEngine();
            $nuevos = [];

            foreach (array_keys($filas) as $jugadorId) {
                $jugadorId = (int) $jugadorId;

                $totales = $eventoModel->totalesDe($jugadorId);
                $estadisticaModel->sincronizarPorNombre($jugadorId, [
                    'Goles' => $totales['goles'],
                    'Asistencias' => $totales['asistencias'],
                    'Minutos' => $totales['minutos'],
                    'Partidos' => $eventoModel->contarPartidos($jugadorId),
                ]);

                $nuevos = array_merge(
                    $nuevos,
                    $engine->evaluarLogrosJugador($jugadorId),
                    $engine->evaluarBadgesJugador($jugadorId, $ligaId)
                );
            }

            // Los equipos de ambos lados del partido también se reevalúan.
            $equiposAEvaluar = [(int) $partido['equipo_id']];
            $rivalEquipo = $this->model('Equipo')->porNombre($partido['rival']);
            if ($rivalEquipo) {
                $equiposAEvaluar[] = (int) $rivalEquipo['id'];
            }

            foreach (array_unique($equiposAEvaluar) as $equipoId) {
                $nuevos = array_merge(
                    $nuevos,
                    $engine->evaluarLogrosEquipo($equipoId),
                    $engine->evaluarBadgesEquipo($equipoId, $ligaId)
                );
            }

            if ($nuevos) {
                $this->flashExito('Nuevo logro/badge desbloqueado:<br>' . implode('<br>', array_map('htmlspecialchars', $nuevos)));
            }

            $this->redirect('partidos');
            return;
        }

        $equipoModel = $this->model('Equipo');
        $equipo = $equipoModel->find((int) $partido['equipo_id']);

        $grupos = [
            [
                'equipo_nombre' => $equipo['nombre'] ?? 'Nuestro equipo',
                'jugadores' => $eventoModel->porPartido($id, (int) $partido['equipo_id']),
            ],
        ];

        $rivalEquipo = $equipoModel->porNombre($partido['rival']);
        if ($rivalEquipo) {
            $grupos[] = [
                'equipo_nombre' => $rivalEquipo['nombre'],
                'jugadores' => $eventoModel->porPartido($id, (int) $rivalEquipo['id']),
            ];
        }

        $this->view('partidos/eventos', [
            'titulo' => 'Estadísticas del partido',
            'activeMenu' => 'partidos',
            'partido' => $partido,
            'equipoNombre' => $equipo['nombre'] ?? 'Nuestro equipo',
            'grupos' => $grupos,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->exigirPermiso('eliminar');

        $partidoModel = $this->model('Partido');
        $partidoModel->delete($id);
        $this->redirect('partidos');
    }

    public function generar(): void
    {
        $this->exigirPermiso('crear');

        $ligaModel = $this->model('Liga');
        $ligas = $ligaModel->all('nombre ASC');

        if ($this->isPost()) {
            $ligaId = (int) ($_POST['liga_id'] ?? 0);
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $hora = $_POST['hora'] ?? '';

            $liga = $ligaId ? $ligaModel->find($ligaId) : null;
            $equipoModel = $this->model('Equipo');
            $equipos = $liga ? $equipoModel->porLiga($ligaId) : [];

            if (!$liga || !$fechaInicio || !$hora) {
                $this->view('partidos/generar', [
                    'titulo' => 'Generar calendario',
                    'activeMenu' => 'partidos',
                    'ligas' => $ligas,
                    'error' => 'Selecciona una liga, la fecha de inicio y la hora de los partidos.',
                ]);
                return;
            }

            $errorEquipos = $this->validarEquiposParaCalendario($liga, count($equipos));

            if ($errorEquipos) {
                $this->view('partidos/generar', [
                    'titulo' => 'Generar calendario',
                    'activeMenu' => 'partidos',
                    'ligas' => $ligas,
                    'error' => $errorEquipos,
                ]);
                return;
            }

            $fixtures = $this->generarFixtures($equipos, (int) $liga['dias_partido_semana'], $fechaInicio, $hora, $liga['nombre']);

            $partidoModel = $this->model('Partido');
            $partidoModel->regenerarCalendario($ligaId, $fixtures);

            $this->redirect('partidos');
            return;
        }

        $this->view('partidos/generar', [
            'titulo' => 'Generar calendario',
            'activeMenu' => 'partidos',
            'ligas' => $ligas,
        ]);
    }

    /**
     * Valida el número de equipos de la liga antes de generar el calendario:
     * mínimo 2, siempre número par, y si la liga tiene configurado un total
     * de equipos, debe coincidir salvo que se permita calendario incompleto.
     */
    private function validarEquiposParaCalendario(array $liga, int $totalActual): ?string
    {
        if ($totalActual < 2) {
            return 'La liga "' . $liga['nombre'] . '" necesita al menos 2 equipos para generar el calendario.';
        }

        if ($totalActual % 2 !== 0) {
            return 'El número de equipos registrados en la liga debe ser par (actualmente hay ' . $totalActual . ').';
        }

        $totalEsperado = $liga['total_equipos'] ?? null;

        if ($totalEsperado !== null && $totalActual !== (int) $totalEsperado && !$liga['permite_calendario_incompleto']) {
            return 'La liga "' . $liga['nombre'] . '" está configurada para ' . (int) $totalEsperado . ' equipos, pero actualmente hay ' . $totalActual . '. '
                . 'Marca la opción "Permitir calendario incompleto" en la liga para generarlo de todas formas.';
        }

        return null;
    }

    /**
     * Genera los enfrentamientos de todos contra todos (método del círculo) y
     * les asigna fecha según los "días de partido a la semana" de la liga,
     * dejando siempre un solo partido por día.
     */
    private function generarFixtures(array $equipos, int $diasPorSemana, string $fechaInicio, string $hora, string $competencia): array
    {
        $arr = array_values($equipos);
        $n = count($arr);

        if ($n % 2 !== 0) {
            $arr[] = null;
            $n++;
        }

        $rondas = $n - 1;
        $mitad = intdiv($n, 2);
        $partidos = [];

        for ($ronda = 0; $ronda < $rondas; $ronda++) {
            for ($i = 0; $i < $mitad; $i++) {
                $a = $arr[$i];
                $b = $arr[$n - 1 - $i];

                if ($a === null || $b === null) {
                    continue;
                }

                [$local, $visitante] = $ronda % 2 === 0 ? [$a, $b] : [$b, $a];

                $partidos[] = [
                    'equipo_id' => $local['id'],
                    'rival' => $visitante['nombre'],
                    'sede' => $local['sede'] ?? null,
                    'tipo' => 'local',
                    'competencia' => $competencia,
                    'jornada' => 'Jornada ' . ($ronda + 1),
                ];
            }

            $ultimo = $arr[$n - 1];
            for ($i = $n - 1; $i > 1; $i--) {
                $arr[$i] = $arr[$i - 1];
            }
            $arr[1] = $ultimo;
        }

        $diasPorSemana = max(1, min(7, $diasPorSemana));
        $offsets = [];
        for ($i = 0; $i < $diasPorSemana; $i++) {
            $offsets[] = intdiv($i * 7, $diasPorSemana);
        }

        $fecha = new DateTime($fechaInicio);
        $semana = 0;
        $slot = 0;

        foreach ($partidos as &$partido) {
            $dias = $semana * 7 + $offsets[$slot];
            $partido['fecha'] = (clone $fecha)->modify("+{$dias} days")->format('Y-m-d');
            $partido['hora'] = $hora;

            $slot++;
            if ($slot >= $diasPorSemana) {
                $slot = 0;
                $semana++;
            }
        }

        return $partidos;
    }
}
