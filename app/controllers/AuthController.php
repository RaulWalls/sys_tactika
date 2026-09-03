<?php

class AuthController extends Controller
{
    public function login(): void
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->redirect('dashboard');
            return;
        }

        $error = null;

        if ($this->isPost()) {
            $usuarioModel = $this->model('Usuario');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $usuario = $usuarioModel->porEmail($email);

            if ($usuario && $usuario['estado'] === 'activo' && password_verify($password, $usuario['password'])) {
                session_regenerate_id(true);

                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol_nombre'];
                $_SESSION['usuario_rol_id'] = $usuario['rol_id'];
                $_SESSION['usuario_liga_id'] = $usuario['liga_id'];
                $_SESSION['usuario_jugador_id'] = $usuario['jugador_id'];
                $_SESSION['usuario_tema'] = $usuario['tema'] ?? 'default';
                $_SESSION['usuario_modo_oscuro'] = (bool) ($usuario['modo_oscuro'] ?? false);

                $this->redirect('dashboard');
                return;
            }

            $error = 'Usuario o contraseña incorrectos.';
        }

        $this->view('auth/login', [
            'titulo' => 'Iniciar sesión',
            'error' => $error,
        ], false);
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirect('auth/login');
    }

    public function recuperar(): void
    {
        $enviado = false;

        if ($this->isPost()) {
            $enviado = true;
        }

        $this->view('auth/recuperar', [
            'titulo' => 'Recuperar contraseña',
            'enviado' => $enviado,
        ], false);
    }
}
