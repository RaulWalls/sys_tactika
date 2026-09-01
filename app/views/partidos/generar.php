<div class="welcome">
    <h1>Generar calendario</h1>
    <p>Crea automáticamente los partidos de todos contra todos para una liga.</p>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/partidos/generar">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Liga / Competencia</label>
                <select name="liga_id" class="form-select" required>
                    <option value="">Selecciona una liga</option>
                    <?php foreach ($ligas as $l): ?>
                        <option value="<?= $l['id'] ?>">
                            <?= htmlspecialchars($l['nombre']) ?>
                            (<?= (int) $l['dias_partido_semana'] ?> día<?= (int) $l['dias_partido_semana'] === 1 ? '' : 's' ?> de partido/semana)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($ligas)): ?>
                    <div class="form-text">No hay ligas registradas todavía. <a href="<?= BASE_URL ?>/ligas/crear">Crea una liga primero</a>.</div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" class="form-control" min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Hora de los partidos</label>
                <input type="time" name="hora" class="form-control" value="16:00" required>
            </div>
        </div>

        <div class="form-text mt-3">
            Se generarán los partidos de todos contra todos entre los equipos de la liga seleccionada (mínimo 2 equipos), repartidos según sus días de partido a la semana, con un solo partido por día.
            Si la liga ya tenía un calendario pendiente, se reemplaza; los partidos ya jugados no se modifican.
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Generar calendario</button>
            <a href="<?= BASE_URL ?>/partidos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
