<div class="welcome">
    <h1>Nuevo usuario</h1>
    <p>Crea un acceso al sistema.</p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/usuarios/crear">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select name="rol_id" class="form-select" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= ucfirst($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Liga / Competencia</label>
                <select name="liga_id" class="form-select">
                    <option value="">Sin asignar (acceso a todas, solo administrador)</option>
                    <?php foreach ($ligas as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
