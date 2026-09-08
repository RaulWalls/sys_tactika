<?php

class TorneosController extends Controller
{
    /**
     * Orden de siembra estándar de bracket (evita que los mejores
     * sembrados se enfrenten antes de la final). Cada par son las
     * semillas (1-based) que se enfrentan en la primera ronda.
     */
    private const SIEMBRA_8 = [[1, 8], [4, 5], [2, 7], [3, 6]];
    private const SIEMBRA_16 = [[1, 16], [8, 9], [4, 13], [5, 12], [2, 15], [7, 10], [3, 14], [6, 11]];

    public function index(): void
    {
        $this->exigirPermiso('ver');

        $this->view('torneos/index', [
            'titulo' => 'Torneos',
            'activeMenu' => 'torneos',
            'torneos' => $this->model('Torneo')->conLiga(),
        ]);
    }

    public function crear(): void
    {
        $this->exigirPermiso('crear');

        $ligaModel = $this->model('Liga');

        if ($this->isPost()) {
            $ligaId = (int) ($_POST['liga_id'] ?? 0);
            $totalEquipos = (int) ($_POST['total_equipos'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $fechaInicio = $_POST['fecha_inicio'] ?? '';
            $fechaFin = $_POST['fecha_fin'] ?? '';

            $error = $this->validarTorneo($nombre, $ligaId, $totalEquipos, $fechaInicio, $fechaFin);
            $tabla = $ligaId ? $ligaModel->tablaPosiciones($ligaId) : [];

            if (!$error && count($tabla) < $totalEquipos) {
                $error = 'La liga seleccionada solo tiene ' . count($tabla) . ' equipo(s) con partidos jugados; se necesitan al menos ' . $totalEquipos . '.';
            }

            if ($error) {
                $this->view('torneos/crear', [
                    'titulo' => 'Nuevo torneo',
                    'activeMenu' => 'torneos',
                    'ligas' => $ligaModel->all('nombre ASC'),
                    'error' => $error,
                    'torneo' => $_POST,
                ]);
                return;
            }

            $torneoId = $this->generarTorneo($nombre, $ligaId, $totalEquipos, $fechaInicio, $fechaFin, $tabla);

            $this->redirect('torneos/detalle/' . $torneoId);
            return;
        }

        $this->view('torneos/crear', [
            'titulo' => 'Nuevo torneo',
            'activeMenu' => 'torneos',
            'ligas' => $ligaModel->all('nombre ASC'),
        ]);
    }

    private function validarTorneo(string $nombre, int $ligaId, int $totalEquipos, string $fechaInicio, string $fechaFin): ?string
    {
        if ($nombre === '' || !$ligaId || !$fechaInicio || !$fechaFin) {
            return 'Completa el nombre, la liga y las fechas de inicio y fin.';
        }

        if (!in_array($totalEquipos, [8, 16], true)) {
            return 'El tipo de eliminatoria debe ser de 8 o 16 equipos.';
        }

        if ($fechaFin < $fechaInicio) {
            return 'La fecha de fin no puede ser anterior a la fecha de inicio.';
        }

        return null;
    }

    /**
     * Crea el torneo, siembra a los mejores equipos de la liga (según su
     * tabla de posiciones) y genera todas las rondas del bracket. Las
     * rondas posteriores a la primera quedan "por definir" hasta que se
     * resuelvan sus partidos alimentadores.
     */
    private function generarTorneo(string $nombre, int $ligaId, int $totalEquipos, string $fechaInicio, string $fechaFin, array $tabla): int
    {
        $torneoModel = $this->model('Torneo');
        $torneoEquipoModel = $this->model('TorneoEquipo');
        $partidoModel = $this->model('Partido');
        $ligaModel = $this->model('Liga');

        $liga = $ligaModel->find($ligaId);

        $torneoId = $torneoModel->create([
            'nombre' => $nombre,
            'liga_id' => $ligaId,
            'total_equipos' => $totalEquipos,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'estado' => 'programado',
        ]);

        $participantes = array_slice($tabla, 0, $totalEquipos);
        $porSemilla = [];
        foreach ($participantes as $i => $p) {
            $semilla = $i + 1;
            $porSemilla[$semilla] = $p;
            $torneoEquipoModel->create([
                'torneo_id' => $torneoId,
                'equipo_id' => $p['equipo_id'],
                'semilla' => $semilla,
            ]);
        }

        $rondas = $this->rondasDe($totalEquipos);
        $siembraInicial = $totalEquipos === 16 ? self::SIEMBRA_16 : self::SIEMBRA_8;

        // Primera ronda: equipos reales según la siembra.
        $primeraRonda = $rondas[0];
        foreach ($siembraInicial as $posicion => [$semillaA, $semillaB]) {
            $equipoA = $porSemilla[$semillaA];
            $equipoB = $porSemilla[$semillaB];

            $partidoModel->create([
                'liga_id' => $ligaId,
                'torneo_id' => $torneoId,
                'ronda' => $primeraRonda,
                'posicion' => $posicion,
                'equipo_id' => $equipoA['equipo_id'],
                'rival' => $equipoB['nombre'],
                'fecha' => $fechaInicio,
                'hora' => '00:00:00',
                'tipo' => 'local',
                'competencia' => $liga['nombre'] ?? null,
                'jornada' => ucfirst(str_replace('_', ' ', $primeraRonda)),
                'estado' => 'programado',
            ]);
        }

        // Rondas siguientes: "por definir" hasta que se resuelva quién avanza.
        for ($r = 1; $r < count($rondas); $r++) {
            $numPartidos = intdiv(count($siembraInicial), 1 << $r);
            for ($posicion = 0; $posicion < $numPartidos; $posicion++) {
                $partidoModel->create([
                    'liga_id' => $ligaId,
                    'torneo_id' => $torneoId,
                    'ronda' => $rondas[$r],
                    'posicion' => $posicion,
                    'equipo_id' => null,
                    'rival' => null,
                    'fecha' => $fechaFin,
                    'hora' => '00:00:00',
                    'tipo' => 'local',
                    'competencia' => $liga['nombre'] ?? null,
                    'jornada' => ucfirst(str_replace('_', ' ', $rondas[$r])),
                    'estado' => 'programado',
                ]);
            }
        }

        return $torneoId;
    }

    /**
     * Nombres de las rondas en orden, según el tamaño del bracket.
     */
    private function rondasDe(int $totalEquipos): array
    {
        return $totalEquipos === 16
            ? ['octavos', 'cuartos', 'semifinal', 'final']
            : ['cuartos', 'semifinal', 'final'];
    }

    public function detalle(int $id): void
    {
        $this->exigirPermiso('ver');

        $torneoModel = $this->model('Torneo');
        $torneo = $torneoModel->conDetalle($id);

        if (!$torneo) {
            http_response_code(404);
            die('Torneo no encontrado');
        }

        $partidos = $this->model('Partido')->deTorneo($id);
        $rondas = $this->rondasDe((int) $torneo['total_equipos']);

        $porRonda = [];
        foreach ($rondas as $ronda) {
            $porRonda[$ronda] = [];
        }
        foreach ($partidos as $p) {
            $porRonda[$p['ronda']][] = $p;
        }

        $this->view('torneos/detalle', [
            'titulo' => $torneo['nombre'],
            'activeMenu' => 'torneos',
            'torneo' => $torneo,
            'rondas' => $rondas,
            'porRonda' => $porRonda,
            'sedes' => $this->model('CatSede')->all('nombre ASC'),
            'canchas' => $this->model('CatCancha')->all('nombre ASC'),
        ]);
    }

    /**
     * Guarda fecha/hora/sede/cancha/marcador de un partido del bracket y,
     * si queda definido un ganador, lo propaga a la siguiente ronda.
     */
    public function guardarPartido(int $id): void
    {
        $this->exigirPermiso('editar');

        $partidoModel = $this->model('Partido');
        $partido = $partidoModel->find($id);

        if (!$partido || !$partido['torneo_id']) {
            http_response_code(404);
            die('Partido de torneo no encontrado');
        }

        $golesFavor = $_POST['goles_favor'] !== '' ? (int) $_POST['goles_favor'] : null;
        $golesContra = $_POST['goles_contra'] !== '' ? (int) $_POST['goles_contra'] : null;
        $estado = ($golesFavor !== null && $golesContra !== null) ? 'jugado' : 'programado';

        $partidoModel->update($id, [
            'fecha' => $_POST['fecha'] ?: $partido['fecha'],
            'hora' => $_POST['hora'] ?: $partido['hora'],
            'sede_id' => $_POST['sede_id'] ?: null,
            'cancha_id' => $_POST['cancha_id'] ?: null,
            'goles_favor' => $golesFavor,
            'goles_contra' => $golesContra,
            'estado' => $estado,
        ]);

        if ($estado === 'jugado') {
            $this->propagarGanador((int) $partido['torneo_id'], $id);
        }

        $this->redirect('torneos/detalle/' . $partido['torneo_id']);
    }

    /**
     * Determina el ganador de un partido de bracket ya jugado y lo coloca
     * en el partido correspondiente de la siguiente ronda (o marca el
     * torneo como finalizado si era la final).
     */
    private function propagarGanador(int $torneoId, int $partidoId): void
    {
        $partidoModel = $this->model('Partido');
        $partido = $partidoModel->find($partidoId);

        $torneo = $this->model('Torneo')->find($torneoId);
        $rondas = $this->rondasDe((int) $torneo['total_equipos']);
        $indiceRonda = array_search($partido['ronda'], $rondas, true);

        if ($indiceRonda === false) {
            return;
        }

        if ($indiceRonda === count($rondas) - 1) {
            $this->model('Torneo')->update($torneoId, ['estado' => 'finalizado']);
            return;
        }

        $equipoModel = $this->model('Equipo');
        $ganadorEsLocal = (int) $partido['goles_favor'] > (int) $partido['goles_contra'];

        if ($ganadorEsLocal) {
            $ganador = $partido['equipo_id'] ? $equipoModel->find((int) $partido['equipo_id']) : null;
        } else {
            $ganador = $partido['rival'] ? $equipoModel->porNombre($partido['rival']) : null;
        }

        if (!$ganador) {
            return;
        }

        $siguienteRonda = $rondas[$indiceRonda + 1];
        $siguientePosicion = intdiv((int) $partido['posicion'], 2);
        $esLocalEnSiguiente = ((int) $partido['posicion'] % 2) === 0;

        $siguientePartido = $partidoModel->deTorneoRonda($torneoId, $siguienteRonda, $siguientePosicion);

        if (!$siguientePartido) {
            return;
        }

        $partidoModel->update((int) $siguientePartido['id'], $esLocalEnSiguiente
            ? ['equipo_id' => $ganador['id']]
            : ['rival' => $ganador['nombre']]);

        if ($torneo['estado'] === 'programado') {
            $this->model('Torneo')->update($torneoId, ['estado' => 'en_curso']);
        }
    }

    public function eliminar(int $id): void
    {
        $this->exigirPermiso('eliminar');

        $this->model('Partido')->eliminarDeTorneo($id);

        $stmt = Database::getConnection()->prepare('DELETE FROM torneo_equipos WHERE torneo_id = :id');
        $stmt->execute(['id' => $id]);

        $this->model('Torneo')->delete($id);

        $this->redirect('torneos');
    }

    /**
     * Alta rápida de catálogos de sede/cancha desde el propio modal de
     * captura del partido del bracket.
     */
    public function agregarSede(): void
    {
        $this->exigirPermiso('editar');

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre !== '') {
            $this->model('CatSede')->create(['nombre' => $nombre]);
        }

        $this->redirect('torneos/detalle/' . (int) ($_POST['torneo_id'] ?? 0));
    }

    public function agregarCancha(): void
    {
        $this->exigirPermiso('editar');

        $nombre = trim($_POST['nombre'] ?? '');
        if ($nombre !== '') {
            $this->model('CatCancha')->create(['nombre' => $nombre]);
        }

        $this->redirect('torneos/detalle/' . (int) ($_POST['torneo_id'] ?? 0));
    }
}
