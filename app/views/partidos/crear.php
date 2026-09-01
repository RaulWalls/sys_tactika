<div class="welcome">
    <h1>Nuevo partido</h1>
    <p>Agenda un partido en el calendario.</p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/partidos/crear">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Equipo</label>
                <select name="equipo_id" id="equipo_id" class="form-select" data-datalist-target="rivales-list" data-equipos="<?= htmlspecialchars(json_encode(array_map(fn ($e) => ['id' => (int) $e['id'], 'nombre' => $e['nombre']], $equipos)), ENT_QUOTES) ?>">
                    <option value="">Sin equipo</option>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Rival</label>
                <input type="text" name="rival" class="form-control" list="rivales-list" required>
                <datalist id="rivales-list">
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= htmlspecialchars($e['nombre']) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <div class="form-text">Elige un equipo registrado o escribe el nombre de un rival externo.</div>
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Hora</label>
                <input type="time" name="hora" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="local">Local</option>
                    <option value="visitante">Visitante</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Sede</label>
                <input type="text" name="sede" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">Liga / Competencia</label>
                <select name="liga_id" class="form-select">
                    <option value="">Sin liga</option>
                    <?php foreach ($ligas as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Jornada</label>
                <input type="text" name="jornada" class="form-control" placeholder="Jornada 12">
            </div>

            <div class="col-md-12">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="programado">Programado</option>
                    <option value="jugado">Jugado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/partidos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
