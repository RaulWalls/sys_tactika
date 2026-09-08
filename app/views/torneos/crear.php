<div class="welcome">
    <h1>Nuevo torneo</h1>
    <p>Se genera automáticamente con los mejores equipos de la liga según su tabla de posiciones.</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/torneos/crear">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Nombre del torneo</label>
                <input type="text" name="nombre" class="form-control" placeholder="Copa Táctika 2026" value="<?= htmlspecialchars($torneo['nombre'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Liga</label>
                <select name="liga_id" class="form-select" required>
                    <option value="">Selecciona una liga</option>
                    <?php foreach ($ligas as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= (string) ($torneo['liga_id'] ?? '') === (string) $l['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($l['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Tipo de eliminatoria</label>
                <select name="total_equipos" class="form-select" required>
                    <option value="8" <?= (string) ($torneo['total_equipos'] ?? '') === '8' ? 'selected' : '' ?>>8 equipos (cuartos de final)</option>
                    <option value="16" <?= (string) ($torneo['total_equipos'] ?? '') === '16' ? 'selected' : '' ?>>16 equipos (octavos de final)</option>
                </select>
                <div class="form-text">Se toman los mejores equipos de la tabla de posiciones de la liga.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($torneo['fecha_inicio'] ?? '') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha de fin</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($torneo['fecha_fin'] ?? '') ?>" required>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Generar bracket</button>
            <a href="<?= BASE_URL ?>/torneos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
