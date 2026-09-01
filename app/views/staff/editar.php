<div class="welcome">
    <h1>Editar integrante de staff</h1>
    <p><?= htmlspecialchars($persona['nombre'] . ' ' . $persona['apellido']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/staff/editar/<?= $persona['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($persona['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($persona['apellido']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($persona['telefono'] ?? '') ?>">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/staff" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
