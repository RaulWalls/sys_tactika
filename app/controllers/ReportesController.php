<?php

class ReportesController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $jugadorModel = $this->model('Jugador');
        $equipoModel = $this->model('Equipo');
        $ligaModel = $this->model('Liga');
        $torneoModel = $this->model('Torneo');
        $partidoModel = $this->model('Partido');
        $eventoModel = $this->model('JugadorPartidoEvento');
        $pagoModel = $this->model('Pago');

        $partidos = $partidoModel->all();
        $jugados = array_filter($partidos, fn ($p) => $p['estado'] === 'jugado');

        // Filtro de liga o torneo para la tabla de posiciones y goleo.
        $tipoFiltro = ($_GET['tipo'] ?? 'liga') === 'torneo' ? 'torneo' : 'liga';
        $ligas = $ligaModel->all('nombre ASC');
        $torneos = $torneoModel->conLiga();

        $columna = $tipoFiltro === 'torneo' ? 'torneo_id' : 'liga_id';
        $opciones = $tipoFiltro === 'torneo' ? $torneos : $ligas;

        $seleccionado = !empty($_GET['valor']) ? (int) $_GET['valor'] : (int) ($opciones[0]['id'] ?? 0);

        $tabla = [];
        $goleadores = [];
        $victorias = 0;
        $empates = 0;
        $derrotas = 0;

        if ($seleccionado) {
            $tabla = $partidoModel->tablaPosiciones($columna, $seleccionado);
            $goleadores = $eventoModel->goleadores($columna, $seleccionado, 10);

            $partidosFiltrados = $partidoModel->jugadosPorFiltro($columna, $seleccionado);
            $victorias = count(array_filter($partidosFiltrados, fn ($p) => $p['goles_favor'] > $p['goles_contra']));
            $empates = count(array_filter($partidosFiltrados, fn ($p) => $p['goles_favor'] == $p['goles_contra']));
            $derrotas = count(array_filter($partidosFiltrados, fn ($p) => $p['goles_favor'] < $p['goles_contra']));
        }

        $this->view('reportes/index', [
            'titulo' => 'Reportes',
            'activeMenu' => 'reportes',
            'totalJugadores' => count($jugadorModel->all()),
            'totalLigas' => $ligaModel->totalLigas(),
            'totalEquipos' => count($equipoModel->all()),
            'totalPartidos' => count($partidos),
            'partidosJugados' => count($jugados),
            'tipoFiltro' => $tipoFiltro,
            'ligas' => $ligas,
            'torneos' => $torneos,
            'seleccionado' => $seleccionado,
            'tabla' => $tabla,
            'topEquipos' => array_slice($tabla, 0, 3),
            'goleadores' => $goleadores,
            'victorias' => $victorias,
            'empates' => $empates,
            'derrotas' => $derrotas,
            'resumenFinanciero' => $pagoModel->resumenReporte(),
        ]);
    }
}
