<div class="welcome">
    <h1>Nueva liga</h1>
    <p>Registra una liga o competencia.</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/ligas/crear" enctype="multipart/form-data">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Escudo de la liga</label>
                <div class="escudo-preview" id="escudo-preview">
                    <span class="escudo-placeholder-icon" id="escudo-placeholder"><i class="fa-solid fa-trophy"></i></span>
                    <img id="escudo-preview-img" src="" alt="Escudo" style="display:none;">
                </div>
                <input type="file" name="escudo" accept="image/*" class="form-control" data-preview-img="escudo-preview-img" data-preview-placeholder="escudo-placeholder">
            </div>

            <div class="col-md-12">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" placeholder="Liga Municipal San Manuel" value="<?= htmlspecialchars($liga['nombre'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Temporada</label>
                <input type="text" name="temporada" class="form-control" placeholder="2026" value="<?= htmlspecialchars($liga['temporada'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($liga['fecha_inicio'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Fecha de fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($liga['fecha_fin'] ?? '') ?>">
                <div class="form-text">Define la temporada para el historial de badges por nivel.</div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Días de partido a la semana</label>
                <select name="dias_partido_semana" class="form-select">
                    <?php for ($i = 1; $i <= 7; $i++): ?>
                        <option value="<?= $i ?>" <?= (int) ($liga['dias_partido_semana'] ?? 1) === $i ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <div class="form-text">Se limitará a 1 partido por día.</div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="activa" <?= ($liga['estado'] ?? 'activa') === 'activa' ? 'selected' : '' ?>>Activa</option>
                    <option value="inactiva" <?= ($liga['estado'] ?? '') === 'inactiva' ? 'selected' : '' ?>>Inactiva</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Total de equipos (opcional)</label>
                <input type="number" name="total_equipos" class="form-control" min="2" step="2" placeholder="Ej. 8" value="<?= htmlspecialchars($liga['total_equipos'] ?? '') ?>">
                <div class="form-text">Debe ser un número par. Se usa para validar que la liga esté completa antes de generar el calendario.</div>
            </div>

            <div class="col-md-6 d-flex align-items-end">
                <div class="form-check">
                    <input type="checkbox" name="permite_calendario_incompleto" id="permite_calendario_incompleto" class="form-check-input" value="1" <?= !empty($liga['permite_calendario_incompleto']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="permite_calendario_incompleto">
                        Permitir generar el calendario con un total de equipos incompleto (siempre que sea par)
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/ligas" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
