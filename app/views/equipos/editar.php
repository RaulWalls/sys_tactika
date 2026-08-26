<div class="welcome">
    <h1>Editar equipo</h1>
    <p><?= htmlspecialchars($equipo['nombre']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/equipos/editar/<?= $equipo['id'] ?>">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($equipo['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($equipo['categoria'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Temporada</label>
                <input type="text" name="temporada" class="form-control" value="<?= htmlspecialchars($equipo['temporada'] ?? '') ?>">
            </div>

            <div class="col-md-12">
                <label class="form-label">URL del escudo (opcional)</label>
                <input type="text" name="escudo" class="form-control" value="<?= htmlspecialchars($equipo['escudo'] ?? '') ?>">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/equipos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
