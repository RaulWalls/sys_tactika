<div class="welcome">
    <h1>Editar usuario</h1>
    <p><?= htmlspecialchars($usuario['nombre']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/usuarios/editar/<?= $usuario['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
            </div>

            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select">
                    <?php foreach (['administrador', 'entrenador', 'jugador'] as $rol): ?>
                        <option value="<?= $rol ?>" <?= $usuario['rol'] === $rol ? 'selected' : '' ?>><?= ucfirst($rol) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <?php foreach (['activo', 'inactivo'] as $estado): ?>
                        <option value="<?= $estado ?>" <?= $usuario['estado'] === $estado ? 'selected' : '' ?>><?= ucfirst($estado) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
