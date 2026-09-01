<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= APP_NAME ?> | Iniciar sesión</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
</head>
<body class="auth-body">

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="logo-icon">⚽</div>
            <div class="logo-text">Tácti<span>ka</span></div>
        </div>

        <div class="auth-welcome">
            <h1>Bienvenido</h1>
            <p>Inicia sesión para administrar tu equipo.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>/auth/login">
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required autofocus>
            </div>

            <div class="mb-2">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="auth-forgot">
                <a href="<?= BASE_URL ?>/auth/recuperar">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 auth-submit">Iniciar sesión</button>
        </form>
    </div>
</div>

</body>
</html>
