<div class="welcome">
    <h1>Editar jugador</h1>
    <p><?= htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido']) ?></p>
</div>

<?php if (!empty($esRolJugador)): ?>
    <div class="alert alert-warning py-2 small">
        Solo puedes modificar tus datos generales. <!-- El resto (posición, número, estado, capitán, foto y equipos) lo activa un administrador o entrenador. -->
    </div>
<?php endif; ?>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/jugadores/editar/<?= $jugador['id'] ?>" enctype="multipart/form-data">

        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label">Foto del jugador</label>
                <div class="escudo-preview" id="foto-preview">
                    <span class="escudo-placeholder-icon" id="foto-placeholder" style="<?= !empty($jugador['foto']) ? 'display:none;' : '' ?>">👕</span>
                    <img id="foto-preview-img" src="<?= !empty($jugador['foto']) ? BASE_URL . '/' . htmlspecialchars($jugador['foto']) : '' ?>" alt="Foto" style="<?= empty($jugador['foto']) ? 'display:none;' : '' ?>">
                </div>
                <input type="file" name="foto" accept="image/*" class="form-control" data-preview-img="foto-preview-img" data-preview-placeholder="foto-placeholder" <?= !empty($esRolJugador) ? 'disabled' : '' ?>>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($jugador['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" value="<?= htmlspecialchars($jugador['apellido']) ?>" required>
            </div>

            <?php $equiposJugador = array_column($jugador['equipos'] ?? [], 'id'); ?>
            <div class="col-md-12">
                <label class="form-label">Equipos</label>
                <div class="equipos-checklist">
                    <?php foreach ($equipos as $e): ?>
                        <div class="form-check">
                            <input type="checkbox" name="equipos[]" id="equipo-<?= $e['id'] ?>" class="form-check-input" value="<?= $e['id'] ?>" <?= in_array((int) $e['id'], $equiposJugador, true) ? 'checked' : '' ?> <?= !empty($esRolJugador) ? 'disabled' : '' ?>>
                            <label class="form-check-label" for="equipo-<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></label>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($equipos)): ?>
                        <p class="text-muted small mb-0">No hay equipos disponibles.</p>
                    <?php endif; ?>
                </div>
                <div class="form-text">Un jugador puede pertenecer a más de un equipo.</div>
            </div>

            <div class="col-md-3">
                <label class="form-label">Posición</label>
                <select name="posicion_id" class="form-select" <?= !empty($esRolJugador) ? 'disabled' : '' ?>>
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
                <input type="number" name="numero" class="form-control" min="0" value="<?= htmlspecialchars($jugador['numero'] ?? '') ?>" <?= !empty($esRolJugador) ? 'disabled' : '' ?>>
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
                <select name="estado" class="form-select" <?= !empty($esRolJugador) ? 'disabled' : '' ?>>
                    <?php foreach (['activo', 'lesionado', 'inactivo'] as $estado): ?>
                        <option value="<?= $estado ?>" <?= $jugador['estado'] === $estado ? 'selected' : '' ?>>
                            <?= ucfirst($estado) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-12 d-flex align-items-center">
                <div class="form-check">
                    <input type="checkbox" name="capitan" id="capitan" class="form-check-input" value="1" <?= !empty($jugador['capitan']) ? 'checked' : '' ?> <?= !empty($esRolJugador) ? 'disabled' : '' ?>>
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
