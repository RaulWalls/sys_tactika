<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= APP_NAME ?> | <?= htmlspecialchars($titulo ?? 'Panel') ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=<?= filemtime(PUBLIC_PATH . '/css/style.css') ?>">
</head>
<body data-theme="<?= htmlspecialchars($_SESSION['usuario_tema'] ?? 'default') ?>" data-modo="<?= !empty($_SESSION['usuario_modo_oscuro']) ? 'oscuro' : 'claro' ?>">

<div class="app">
