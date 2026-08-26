<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $equipoModel = $this->model('Equipo');
        $jugadorModel = $this->model('Jugador');
        $partidoModel = $this->model('Partido');
        $pagoModel = $this->model('Pago');

        $this->view('dashboard/index', [
            'titulo' => 'Dashboard',
            'activeMenu' => 'dashboard',
            'totalJugadores' => count($jugadorModel->all()),
            'totalEquipos' => count($equipoModel->all()),
            'proximosPartidos' => $partidoModel->proximos(4),
            'ultimosResultados' => $partidoModel->ultimosResultados(4),
            'resumenFinanciero' => $pagoModel->resumen(),
        ]);
    }
}
