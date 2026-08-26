<div class="welcome">
    <h1>Editar pago</h1>
    <p><?= htmlspecialchars($pago['concepto']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/pagos/editar/<?= $pago['id'] ?>">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Concepto</label>
                <input type="text" name="concepto" class="form-control" value="<?= htmlspecialchars($pago['concepto']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Jugador (opcional)</label>
                <select name="jugador_id" class="form-select">
                    <option value="">Ninguno</option>
                    <?php foreach ($jugadores as $j): ?>
                        <option value="<?= $j['id'] ?>" <?= $pago['jugador_id'] == $j['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($j['nombre'] . ' ' . $j['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Equipo (opcional)</label>
                <select name="equipo_id" class="form-select">
                    <option value="">Ninguno</option>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $pago['equipo_id'] == $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Monto</label>
                <input type="number" step="0.01" name="monto" class="form-control" value="<?= htmlspecialchars($pago['monto']) ?>" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <?php foreach (['ingreso', 'gasto'] as $tipo): ?>
                        <option value="<?= $tipo ?>" <?= $pago['tipo'] === $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Método de pago</label>
                <input type="text" name="metodo_pago" class="form-control" value="<?= htmlspecialchars($pago['metodo_pago'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($pago['fecha']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <?php foreach (['pendiente', 'pagado'] as $estado): ?>
                        <option value="<?= $estado ?>" <?= $pago['estado'] === $estado ? 'selected' : '' ?>><?= ucfirst($estado) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/pagos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
