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
        if ($rol === 'jugador') {
            // Para la validación de "sin partidos pendientes" del próximo partido.
            $data['totalEquipos'] = count($this->model('Equipo')->conLiga($misEquipos));

            $miJugadorId = (int) ($_SESSION['usuario_jugador_id'] ?? 0);
            $data['totalGoles'] = $miJugadorId ? $this->model('JugadorEstadistica')->golesDe($miJugadorId) : 0;
            $data['totalAsistencias'] = $miJugadorId ? $this->model('JugadorEstadistica')->asistenciasDe($miJugadorId) : 0;
            $data['amonestaciones'] = $miJugadorId
                ? $this->model('JugadorAmonestaciones')->porJugador($miJugadorId)
                : ['tarjeta_amarilla' => 0, 'tarjeta_roja' => 0];

            $data['miJugador'] = $miJugadorId ? $this->model('Jugador')->conDetalle($miJugadorId) : null;

            if ($miJugadorId) {
                $nivel = $this->model('JugadorRango')->nivelDe($miJugadorId);
                $data['miNivel'] = $nivel;
                $data['miRango'] = $this->model('CatRango')->paraNivel($nivel);
                $data['miTotalLogros'] = $this->model('JugadorLogro')->contarDesbloqueados($miJugadorId);
                $data['miTotalBadges'] = $this->model('JugadorBadge')->contarObtenidas($miJugadorId);
                $data['misBadges'] = $this->model('JugadorBadge')->obtenidas($miJugadorId, 6);
                $data['misLogrosRecientes'] = $this->model('JugadorLogro')->recientes($miJugadorId, 4);

                $data['misAtributos'] = $this->model('JugadorAtributo')->porJugador($miJugadorId);

                // Valoración estilo "carta": 50 de base + el promedio de los
                // atributos (-20 a 20), acotada entre 0 y 99.
                $valores = array_column($data['misAtributos'], 'valor');
                $promedio = $valores ? array_sum($valores) / count($valores) : 0;
                $data['miValoracion'] = (int) max(0, min(99, round(50 + $promedio)));
            } else {
                $data['miNivel'] = 0;
                $data['miRango'] = null;
                $data['miTotalLogros'] = 0;
                $data['miTotalBadges'] = 0;
                $data['misBadges'] = [];
                $data['misLogrosRecientes'] = [];
                $data['misAtributos'] = [];
                $data['miValoracion'] = 50;
            }
        } else {
            $equipoModel = $this->model('Equipo');
            $jugadorModel = $this->model('Jugador');
            $pagoModel = $this->model('Pago');

            $data['totalJugadores'] = count($jugadorModel->conEquipo($misEquipos));
            $data['totalEquipos'] = count($equipoModel->conLiga($misEquipos));
            $data['resumenFinanciero'] = $pagoModel->resumen($misEquipos);

            if ($rol === 'administrador') {
                $data['totalLigas'] = $this->model('Liga')->totalLigas();
                $data['totalTorneos'] = $this->model('Torneo')->totalTorneos();
            }

            // Entrenador: tarjeta de identidad + logros/badges de cada
            // equipo a su cargo (puede tener varios).
            if ($rol === 'entrenador') {
                $rangoModel = $this->model('EquipoRango');
                $catRangoModel = $this->model('CatRango');
                $logroModel = $this->model('EquipoLogro');
                $badgeModel = $this->model('EquipoBadge');

                $misEquiposInfo = $equipoModel->conLiga($misEquipos);

                foreach ($misEquiposInfo as &$eq) {
                    $nivel = $rangoModel->nivelDe((int) $eq['id']);
                    $eq['nivel'] = $nivel;
                    $eq['rango'] = $catRangoModel->paraNivel($nivel);
                    $eq['totalLogros'] = $logroModel->contarDesbloqueados((int) $eq['id']);
                    $eq['totalBadges'] = $badgeModel->contarObtenidas((int) $eq['id']);
                    $eq['badges'] = $badgeModel->obtenidas((int) $eq['id'], 6);
                    $eq['logrosRecientes'] = $logroModel->recientes((int) $eq['id'], 4);
                }
                unset($eq);

                $data['misEquiposInfo'] = $misEquiposInfo;
            }
        }

        $this->view('dashboard/index', $data);
    }
}
