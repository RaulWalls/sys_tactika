<div class="welcome">
    <h1>Editar jugador</h1>
    <p><?= htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/jugadores/editar/<?= $jugador['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($jugador['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($jugador['apellido']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Equipo</label>
                <select name="equipo_id" class="form-select">
                    <option value="">Sin equipo</option>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $jugador['equipo_id'] == $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Posición</label>
                <input type="text" name="posicion" class="form-control" value="<?= htmlspecialchars($jugador['posicion'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label class="form-label">Número</label>
                <input type="number" name="numero" class="form-control" min="0" value="<?= htmlspecialchars($jugador['numero'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($jugador['fecha_nacimiento'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($jugador['telefono'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($jugador['email'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <?php foreach (['activo', 'lesionado', 'inactivo'] as $estado): ?>
                        <option value="<?= $estado ?>" <?= $jugador['estado'] === $estado ? 'selected' : '' ?>>
                            <?= ucfirst($estado) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/jugadores" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
