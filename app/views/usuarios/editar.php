<div class="welcome">
    <h1>Editar usuario</h1>
    <p><?= htmlspecialchars($usuario['nombre']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/usuarios/editar/<?= $usuario['id'] ?>">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar">
            </div>

            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select name="rol_id" class="form-select" required>
                    <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= (int) $usuario['rol_id'] === (int) $r['id'] ? 'selected' : '' ?>><?= ucfirst($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <?php foreach (['activo', 'inactivo'] as $estado): ?>
                        <option value="<?= $estado ?>" <?= $usuario['estado'] === $estado ? 'selected' : '' ?>><?= ucfirst($estado) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Liga / Competencia</label>
                <select name="liga_id" class="form-select">
                    <option value="">Sin asignar (acceso a todas, solo administrador)</option>
                    <?php foreach ($ligas as $l): ?>
                        <option value="<?= $l['id'] ?>" <?= (int) ($usuario['liga_id'] ?? 0) === (int) $l['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($l['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Jugador vinculado (rol jugador)</label>
                <select name="jugador_id" class="form-select">
                    <option value="">Sin vincular</option>
                    <?php foreach ($jugadores as $j): ?>
                        <option value="<?= $j['id'] ?>" <?= (int) ($usuario['jugador_id'] ?? 0) === (int) $j['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($j['nombre'] . ' ' . $j['apellido']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Solo aplica si el rol es "Jugador": conecta esta cuenta con su perfil de jugador.</div>
            </div>

            <div class="col-md-12">
                <label class="form-label">Equipos asignados (rol entrenador)</label>
                <div class="equipos-checklist">
                    <?php foreach ($equipos as $e): ?>
                        <div class="form-check">
                            <input type="checkbox" name="equipos[]" id="usuario-equipo-<?= $e['id'] ?>" class="form-check-input" value="<?= $e['id'] ?>" <?= in_array((int) $e['id'], $equiposUsuario, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="usuario-equipo-<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></label>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($equipos)): ?>
                        <p class="text-muted small mb-0">No hay equipos disponibles.</p>
                    <?php endif; ?>
                </div>
                <div class="form-text">Solo aplica si el rol es "Entrenador": un entrenador puede tener varios equipos.</div>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Actualizar</button>
            <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
