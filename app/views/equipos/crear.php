<div class="welcome">
    <h1>Nuevo equipo</h1>
    <p>Registra un nuevo equipo.</p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/equipos/crear" enctype="multipart/form-data">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Escudo del equipo</label>
                <div class="escudo-preview" id="escudo-preview">
                    <span class="escudo-placeholder-icon" id="escudo-placeholder">⚽</span>
                    <img id="escudo-preview-img" src="" alt="Escudo" style="display:none;">
                </div>
                <input type="file" name="escudo" accept="image/*" class="form-control" data-preview-img="escudo-preview-img" data-preview-placeholder="escudo-placeholder">
            </div>

            <div class="col-md-12">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-12">
                <label class="form-label">Liga / Competencia</label>
                <select name="liga_id" class="form-select">
                    <option value="">Sin asignar</option>
                    <?php foreach ($ligas as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Categoría</label>
                <input type="text" name="categoria" class="form-control" placeholder="Primera fuerza, Sub-17...">
            </div>

            <div class="col-md-6">
                <label class="form-label">Temporada</label>
                <input type="text" name="temporada" class="form-control" placeholder="2026">
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha de ingreso</label>
                <input type="date" name="fecha_ingreso" class="form-control">
            </div>

            <div class="col-md-6">
                <label class="form-label">Sede</label>
                <input type="text" name="sede" class="form-control" placeholder="Cancha, ciudad...">
            </div>

            <div class="col-md-6">
                <label class="form-label">Representante</label>
                <select name="representante_id" class="form-select">
                    <option value="">Sin asignar</option>
                    <?php foreach ($staff as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php /* <div class="form-text">¿No aparece? <a href="<?= BASE_URL ?>/staff/crear">Agrégalo aquí</a>.</div> */ ?>
            </div>

            <div class="col-md-6">
                <label class="form-label">Director técnico</label>
                <select name="dt_id" class="form-select">
                    <option value="">Sin asignar</option>
                    <?php foreach ($staff as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php /* <div class="form-text">¿No aparece? <a href="<?= BASE_URL ?>/staff/crear">Agrégalo aquí</a>.</div> */ ?>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/equipos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
