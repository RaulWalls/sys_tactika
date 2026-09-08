<?php

class HistorialController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $ligaModel = $this->model('Liga');
        $temporadaJugadorModel = $this->model('JugadorBadgeTemporada');
        $temporadaEquipoModel = $this->model('EquipoBadgeTemporada');

        $ligas = $ligaModel->finalizadas();
        foreach ($ligas as &$liga) {
            $liga['registrosJugador'] = $temporadaJugadorModel->historialDeLiga((int) $liga['id']);
            $liga['registrosEquipo'] = $temporadaEquipoModel->historialDeLiga((int) $liga['id']);
        }
        unset($liga);

        $this->view('historial/index', [
            'titulo' => 'Historial',
            'activeMenu' => 'historial',
            'ligas' => $ligas,
        ]);
    }
}
