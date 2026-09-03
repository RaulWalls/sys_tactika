<?php

class DashboardController extends Controller
{
    public function index(): void
    {
        $rol = $_SESSION['usuario_rol'] ?? '';
        $misEquipos = $this->misEquiposIds();

        $partidoModel = $this->model('Partido');

        $data = [
            'titulo' => 'Dashboard',
            'activeMenu' => 'dashboard',
            'totalPartidosJugados' => $partidoModel->contarJugados($misEquipos),
            'totalPartidos' => $partidoModel->contarTodos($misEquipos),
            'proximosPartidos' => $partidoModel->proximos(4, $misEquipos),
            'ultimosResultados' => $partidoModel->ultimosResultados(4, $misEquipos),
        ];

        // Jugador: solo bienvenida, partidos jugados, próximo partido y
        // últimos resultados. No necesita totales de plantilla ni finanzas.
        if ($rol !== 'jugador') {
            $equipoModel = $this->model('Equipo');
            $jugadorModel = $this->model('Jugador');
            $pagoModel = $this->model('Pago');

            $data['totalJugadores'] = count($jugadorModel->conEquipo($misEquipos));
            $data['totalEquipos'] = count($equipoModel->conLiga($misEquipos));
            $data['resumenFinanciero'] = $pagoModel->resumen($misEquipos);
        } else {
            // Para la validación de "sin partidos pendientes" del próximo partido.
            $data['totalEquipos'] = count($this->model('Equipo')->conLiga($misEquipos));
        }

        $this->view('dashboard/index', $data);
    }
}
