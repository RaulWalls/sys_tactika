<div class="welcome">
    <h1>Editar partido</h1>
    <p>Vs. <?= htmlspecialchars($partido['rival']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/partidos/editar/<?= $partido['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Equipo</label>
                <select name="equipo_id" class="form-select">
                    <option value="">Sin equipo</option>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $partido['equipo_id'] == $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Rival</label>
                <input type="text" name="rival" class="form-control" value="<?= htmlspecialchars($partido['rival']) ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($partido['fecha']) ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Hora</label>
                <input type="time" name="hora" class="form-control" value="<?= htmlspecialchars(substr($partido['hora'], 0, 5)) ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <?php foreach (['local', 'visitante'] as $tipo): ?>
                        <option value="<?= $tipo ?>" <?= $partido['tipo'] === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Sede</label>
                <input type="text" name="sede" class="form-control" value="<?= htmlspecialchars($partido['sede'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Competencia</label>
                <input type="text" name="competencia" class="form-control" value="<?= htmlspecialchars($partido['competencia'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Jornada</label>
                <input type="text" name="jornada" class="form-control" value="<?= htmlspecialchars($partido['jornada'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Goles a favor</label>
                <input type="number" name="goles_favor" class="form-control" min="0" value="<?= htmlspecialchars($partido['goles_favor'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Goles en contra</label>
                <input type="number" name="goles_contra" class="form-control" min="0" value="<?= htmlspecialchars($partido['goles_contra'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <?php foreach (['programado', 'jugado', 'cancelado'] as $estado): ?>
                        <option value="<?= $estado ?>" <?= $partido['estado'] === $estado ? 'selected' : '' ?>><?= ucfirst($estado) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/partidos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
