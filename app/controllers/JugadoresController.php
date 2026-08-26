<?php

class JugadoresController extends Controller
{
    public function index(): void
    {
        $jugadorModel = $this->model('Jugador');

        $this->view('jugadores/index', [
            'titulo' => 'Jugadores',
            'activeMenu' => 'jugadores',
            'jugadores' => $jugadorModel->conEquipo(),
        ]);
    }

    public function crear(): void
    {
        if ($this->isPost()) {
            $jugadorModel = $this->model('Jugador');

            $jugadorModel->create([
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'posicion' => $_POST['posicion'] ?? null,
                'numero' => $_POST['numero'] !== '' ? $_POST['numero'] : null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $this->redirect('jugadores');
            return;
        }

        $equipoModel = $this->model('Equipo');

        $this->view('jugadores/crear', [
            'titulo' => 'Nuevo jugador',
            'activeMenu' => 'jugadores',
            'equipos' => $equipoModel->all(),
        ]);
    }

    public function editar(int $id): void
    {
        $jugadorModel = $this->model('Jugador');

        if ($this->isPost()) {
            $jugadorModel->update($id, [
                'equipo_id' => $_POST['equipo_id'] ?: null,
                'nombre' => trim($_POST['nombre']),
                'apellido' => trim($_POST['apellido']),
                'posicion' => $_POST['posicion'] ?? null,
                'numero' => $_POST['numero'] !== '' ? $_POST['numero'] : null,
                'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?: null,
                'telefono' => $_POST['telefono'] ?? null,
                'email' => $_POST['email'] ?? null,
                'estado' => $_POST['estado'] ?? 'activo',
            ]);

            $this->redirect('jugadores');
            return;
        }

        $equipoModel = $this->model('Equipo');

        $this->view('jugadores/editar', [
            'titulo' => 'Editar jugador',
            'activeMenu' => 'jugadores',
            'jugador' => $jugadorModel->find($id),
            'equipos' => $equipoModel->all(),
        ]);
    }

    public function eliminar(int $id): void
    {
        $jugadorModel = $this->model('Jugador');
        $jugadorModel->delete($id);
        $this->redirect('jugadores');
    }
}
