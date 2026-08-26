<div class="welcome">
    <h1>Nuevo equipo</h1>
    <p>Registra un nuevo equipo.</p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/equipos/crear">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control" placeholder="Primera fuerza, Sub-17...">
            </div>

            <div class="col-md-6">
                <label class="form-label">Temporada</label>
                <input type="text" name="temporada" class="form-control" placeholder="2026">
            </div>

            <div class="col-md-12">
                <label class="form-label">URL del escudo (opcional)</label>
                <input type="text" name="escudo" class="form-control">
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/equipos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
