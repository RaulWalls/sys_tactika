<?php

class PagosController extends Controller
{
    public function index(): void
    {
        $this->exigirPermiso('ver');

        $pagoModel = $this->model('Pago');
        $equipoSeleccionado = null;
        $filtroEquipos = null;

        $porPagina = 15;
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        if ($this->esAdmin()) {
            $equipos = $this->model('Equipo')->conLiga();
            $equipoSeleccionado = !empty($_GET['equipo_id']) ? (int) $_GET['equipo_id'] : null;
            $filtroEquipos = $equipoSeleccionado ? [$equipoSeleccionado] : null;
        }

        $totalPagos = $pagoModel->contarConJugador($filtroEquipos);
        $totalPaginas = max(1, (int) ceil($totalPagos / $porPagina));
        $pagina = min($pagina, $totalPaginas);

        $this->view('pagos/index', [
            'titulo' => 'Pagos',
            'activeMenu' => 'pagos',
            'equipos' => $equipos ?? [],
            'equipoSeleccionado' => $equipoSeleccionado,
            'pagos' => $pagoModel->conJugador($filtroEquipos, $porPagina, ($pagina - 1) * $porPagina),
            'resumenFinanciero' => $pagoModel->resumen($filtroEquipos),
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
        ]);
    }

    public function crear(): void
    {
        $this->exigirPermiso('crear');

        if ($this->isPost()) {
            $pagoModel = $this->model('Pago');

            $pagoModel->create([
                'jugador_id' => $_POST['jugador_id'] ?: null,
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'concepto_id' => $_POST['concepto_id'] ?: null,
                'monto' => $_POST['monto'],
                'tipo' => $_POST['tipo'] ?? 'ingreso',
                'metodo_pago_id' => $_POST['metodo_pago_id'] ?: null,
                'fecha' => $_POST['fecha'],
                'estado' => $_POST['estado'] ?? 'pendiente',
                'observaciones' => $_POST['observaciones'] ?? null,
            ]);

            $this->redirect('pagos');
            return;
        }

        $jugadorModel = $this->model('Jugador');
        $equipoModel = $this->model('Equipo');
        $catPagoModel = $this->model('CatPago');
        $catMetodoPagoModel = $this->model('CatMetodoPago');

        $this->view('pagos/crear', [
            'titulo' => 'Nuevo pago',
            'activeMenu' => 'pagos',
            'jugadores' => $jugadorModel->all(),
            'equipos' => $equipoModel->all(),
            'conceptos' => $catPagoModel->all('nombre ASC'),
            'metodosPago' => $catMetodoPagoModel->all('nombre ASC'),
            'observaciones' => $_POST['observaciones'] ?? null,
        ]);
    }

    public function editar(int $id): void
    {
        $this->exigirPermiso('editar');

        $pagoModel = $this->model('Pago');

        if ($this->isPost()) {
            $pagoModel->update($id, [
                'jugador_id' => $_POST['jugador_id'] ?: null,
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'concepto_id' => $_POST['concepto_id'] ?: null,
                'monto' => $_POST['monto'],
                'tipo' => $_POST['tipo'] ?? 'ingreso',
                'metodo_pago_id' => $_POST['metodo_pago_id'] ?: null,
                'fecha' => $_POST['fecha'],
                'estado' => $_POST['estado'] ?? 'pendiente',
                'observaciones' => $_POST['observaciones'] ?? null,
            ]);

            $this->redirect('pagos');
            return;
        }

        $jugadorModel = $this->model('Jugador');
        $equipoModel = $this->model('Equipo');
        $catPagoModel = $this->model('CatPago');
        $catMetodoPagoModel = $this->model('CatMetodoPago');

        $this->view('pagos/editar', [
            'titulo' => 'Editar pago',
            'activeMenu' => 'pagos',
            'pago' => $pagoModel->find($id),
            'jugadores' => $jugadorModel->all(),
            'equipos' => $equipoModel->all(),
            'conceptos' => $catPagoModel->all('nombre ASC'),
            'metodosPago' => $catMetodoPagoModel->all('nombre ASC'),
            'observaciones' => $_POST['observaciones'] ?? null,
        ]);
    }

    public function eliminar(int $id): void
    {
        $this->exigirPermiso('eliminar');

        $pagoModel = $this->model('Pago');
        $pagoModel->delete($id);
        $this->redirect('pagos');
    }
}
