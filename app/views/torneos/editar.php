<div class="welcome">
    <h1>Editar torneo</h1>
    <p><?= htmlspecialchars($torneo['nombre']) ?></p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/torneos/editar/<?= $torneo['id'] ?>" enctype="multipart/form-data">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Imagen del torneo</label>
                <div class="escudo-preview" id="escudo-preview">
                    <span class="escudo-placeholder-icon" id="escudo-placeholder" style="<?= !empty($torneo['imagen']) ? 'display:none;' : '' ?>"><i class="fa-solid fa-sitemap"></i></span>
                    <img id="escudo-preview-img" src="<?= !empty($torneo['imagen']) ? BASE_URL . '/' . htmlspecialchars($torneo['imagen']) : '' ?>" alt="Imagen" style="<?= empty($torneo['imagen']) ? 'display:none;' : '' ?>">
                </div>
                <input type="file" name="imagen" accept="image/*" class="form-control" data-preview-img="escudo-preview-img" data-preview-placeholder="escudo-placeholder">
            </div>

            <div class="col-md-12">
                <label class="form-label">Nombre del torneo</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($torneo['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($torneo['fecha_inicio']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha de fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($torneo['fecha_fin']) ?>" required>
            </div>
        </div>

        <div class="form-text mt-2">La liga y el tipo de eliminatoria no se pueden cambiar una vez generado el bracket.</div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/torneos/detalle/<?= $torneo['id'] ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
