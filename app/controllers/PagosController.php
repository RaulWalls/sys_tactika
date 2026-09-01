<?php

class PagosController extends Controller
{
    public function index(): void
    {
        $pagoModel = $this->model('Pago');

        $this->view('pagos/index', [
            'titulo' => 'Pagos',
            'activeMenu' => 'pagos',
            'pagos' => $pagoModel->conJugador(),
            'resumenFinanciero' => $pagoModel->resumen(),
        ]);
    }

    public function crear(): void
    {
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
        ]);
    }

    public function editar(int $id): void
    {
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
        ]);
    }

    public function eliminar(int $id): void
    {
        $pagoModel = $this->model('Pago');
        $pagoModel->delete($id);
        $this->redirect('pagos');
    }
}
