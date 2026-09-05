<?php

class ReportesController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $jugadorModel = $this->model('Jugador');
        $equipoModel = $this->model('Liga');
        $partidoModel = $this->model('Partido');
        $pagoModel = $this->model('Pago');

        $partidos = $partidoModel->all();
        $jugados = array_filter($partidos, fn ($p) => $p['estado'] === 'jugado');

        $victorias = count(array_filter($jugados, fn ($p) => $p['goles_favor'] > $p['goles_contra']));
        $empates = count(array_filter($jugados, fn ($p) => $p['goles_favor'] == $p['goles_contra']));
        $derrotas = count(array_filter($jugados, fn ($p) => $p['goles_favor'] < $p['goles_contra']));

        $this->view('reportes/index', [
            'titulo' => 'Reportes',
            'activeMenu' => 'reportes',
            'totalJugadores' => count($jugadorModel->all()),
            'totalLigas' => $equipoModel->totalLigas()['total_ligas'],
            'totalEquipos' => count($equipoModel->all()),
            'totalPartidos' => count($partidos),
            'partidosJugados' => count($jugados),
            'victorias' => $victorias,
            'empates' => $empates,
            'derrotas' => $derrotas,
            'resumenFinanciero' => $pagoModel->resumen(),
        ]);
    }
}
