<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= APP_NAME ?> | Recuperar contraseña</title>

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
            <h1>Recuperar contraseña</h1>
            <p>Ingresa tu correo y te enviaremos instrucciones.</p>
        </div>

        <?php if ($enviado): ?>
            <div class="alert alert-success py-2 small">
                Si el correo existe en nuestro sistema, recibirás instrucciones para restablecer tu contraseña.
            </div>
        <?php else: ?>
            <form method="post" action="<?= BASE_URL ?>/auth/recuperar">
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" placeholder="tu@correo.com" required autofocus>
                </div>

                <button type="submit" class="btn btn-primary w-100 auth-submit">Enviar instrucciones</button>
            </form>
        <?php endif; ?>

        <div class="auth-back">
            <a href="<?= BASE_URL ?>/auth/login">← Volver a iniciar sesión</a>
        </div>
    </div>
</div>

</body>
</html>
