<div class="welcome">
    <h1>Nuevo pago</h1>
    <p>Registra un ingreso o gasto del equipo.</p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/pagos/crear">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Concepto</label>
                <select name="concepto_id" class="form-select" data-fill-target="monto" required>
                    <option value="">Selecciona un concepto</option>
                    <?php foreach ($conceptos as $c): ?>
                        <option value="<?= $c['id'] ?>" data-monto="<?= $c['monto'] !== null ? $c['monto'] : '' ?>">
                            <?= htmlspecialchars($c['nombre']) ?><?= $c['monto'] !== null ? ' ($' . number_format($c['monto'], 2) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Jugador (opcional)</label>
                <select name="jugador_id" class="form-select">
                    <option value="">Ninguno</option>
                    <?php foreach ($jugadores as $j): ?>
                        <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nombre'] . ' ' . $j['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Equipo (opcional)</label>
                <select name="equipo_id" class="form-select">
                    <option value="">Ninguno</option>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Monto</label>
                <input type="number" step="0.01" name="monto" id="monto" class="form-control" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="ingreso">Ingreso</option>
                    <option value="gasto">Gasto</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Método de pago</label>
                <select name="metodo_pago_id" class="form-select">
                    <option value="">Sin especificar</option>
                    <?php foreach ($metodosPago as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagado">Pagado</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/pagos" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
