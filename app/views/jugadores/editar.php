<div class="welcome">
    <h1>Editar jugador</h1>
    <p><?= htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/jugadores/editar/<?= $jugador['id'] ?>" enctype="multipart/form-data">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Foto del jugador</label>
                <div class="escudo-preview" id="foto-preview">
                    <span class="escudo-placeholder-icon" id="foto-placeholder" style="<?= !empty($jugador['foto']) ? 'display:none;' : '' ?>">👕</span>
                    <img id="foto-preview-img" src="<?= !empty($jugador['foto']) ? BASE_URL . '/' . htmlspecialchars($jugador['foto']) : '' ?>" alt="Foto" style="<?= empty($jugador['foto']) ? 'display:none;' : '' ?>">
                </div>
                <input type="file" name="foto" accept="image/*" class="form-control" data-preview-img="foto-preview-img" data-preview-placeholder="foto-placeholder">
            </div>

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
                <select name="posicion_id" class="form-select">
                    <option value="">Sin posición</option>
                    <?php foreach ($posiciones as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= (int) ($jugador['posicion_id'] ?? 0) === (int) $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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

            <div class="col-md-12 d-flex align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="capitan" id="capitan" class="form-check-input" value="1" <?= !empty($jugador['capitan']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="capitan">Capitán del equipo</label>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/jugadores" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
