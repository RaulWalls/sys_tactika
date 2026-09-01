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
        $partidoModel = $this->model('Partido');

        if ($this->isPost()) {
            $partidoModel->update($id, [
                'liga_id' => $_POST['liga_id'] ?: null,
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'rival' => trim($_POST['rival']),
                'fecha' => $_POST['fecha'],
                'hora' => $_POST['hora'],
                'sede' => $_POST['sede'] ?? null,
                'tipo' => $_POST['tipo'] ?? 'local',
                'competencia' => $this->nombreLiga($_POST['liga_id'] ?? null),
                'jornada' => $_POST['jornada'] ?? null,
                'goles_favor' => $_POST['goles_favor'] !== '' ? $_POST['goles_favor'] : null,
                'goles_contra' => $_POST['goles_contra'] !== '' ? $_POST['goles_contra'] : null,
                'estado' => $_POST['estado'] ?? 'programado',
            ]);

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

    public function eliminar(int $id): void
    {
        $partidoModel = $this->model('Partido');
        $partidoModel->delete($id);
        $this->redirect('partidos');
    }

    public function generar(): void
    {
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
