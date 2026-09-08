<?php

class ProgresoController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $miJugadorId = (int) ($_SESSION['usuario_jugador_id'] ?? 0);
        $misEquipos = $this->misEquiposIds();
        $esAdmin = $this->esAdmin();

        // Solo puede ver progreso "de jugador" quien tiene un perfil de
        // jugador vinculado, o el administrador (que puede elegir cualquiera).
        $puedeVistaJugador = $esAdmin || $miJugadorId > 0;

        $vista = $_GET['vista'] ?? ($puedeVistaJugador ? 'jugador' : 'equipo');
        if ($vista === 'jugador' && !$puedeVistaJugador) {
            $vista = 'equipo';
        }

        $jugador = null;
        $equipo = null;
        $jugadoresDisponibles = [];
        $equiposDisponibles = [];
        $nivel = 0;
        $rango = null;
        $logros = [];
        $badges = [];

        if ($vista === 'jugador') {
            $jugadorModel = $this->model('Jugador');

            if ($esAdmin) {
                $jugadoresDisponibles = $jugadorModel->conEquipo();
                $jugadorId = !empty($_GET['jugador_id']) ? (int) $_GET['jugador_id'] : ($jugadoresDisponibles[0]['id'] ?? 0);
            } else {
                $jugadorId = $miJugadorId;
            }

            if ($jugadorId) {
                $jugador = $jugadorModel->find($jugadorId);
            }

            if ($jugador) {
                $nivel = $this->model('JugadorRango')->nivelDe($jugador['id']);
                $rango = $this->model('CatRango')->paraNivel($nivel);
                $logros = $this->model('JugadorLogro')->porJugador($jugador['id']);
                $badges = $this->model('JugadorBadge')->porJugador($jugador['id']);
            }
        } else {
            $equipoModel = $this->model('Equipo');
            $equiposDisponibles = $equipoModel->conLiga($misEquipos);

            $equipoId = !empty($_GET['equipo_id']) ? (int) $_GET['equipo_id'] : ($equiposDisponibles[0]['id'] ?? 0);

            // Un entrenador/jugador no puede forzar un equipo fuera de los suyos.
            if (!$esAdmin && $misEquipos !== null && !in_array($equipoId, $misEquipos, true)) {
                $equipoId = $equiposDisponibles[0]['id'] ?? 0;
            }

            if ($equipoId) {
                $equipo = $equipoModel->find($equipoId);
            }

            if ($equipo) {
                $nivel = $this->model('EquipoRango')->nivelDe($equipo['id']);
                $rango = $this->model('CatRango')->paraNivel($nivel);
                $logros = $this->model('EquipoLogro')->porEquipo($equipo['id']);
                $badges = $this->model('EquipoBadge')->porEquipo($equipo['id']);
            }
        }

        $this->view('progreso/index', [
            'titulo' => 'Progreso',
            'activeMenu' => 'progreso',
            'vista' => $vista,
            'puedeVistaJugador' => $puedeVistaJugador,
            'esAdmin' => $esAdmin,
            'jugador' => $jugador,
            'equipo' => $equipo,
            'jugadoresDisponibles' => $jugadoresDisponibles,
            'equiposDisponibles' => $equiposDisponibles,
            'nivel' => $nivel,
            'rango' => $rango,
            'logros' => $logros,
            'badges' => $badges,
        ]);
    }
}
