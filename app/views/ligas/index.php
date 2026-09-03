<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Ligas y competencias</h1>
        <p>Administra las ligas y sus equipos y jugadores asociados.</p>
    </div>
    <?php if ($this->tienePermiso('crear')): ?>
        <a href="<?= BASE_URL ?>/ligas/crear" class="btn btn-primary">+ Nueva liga</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Temporada</th>
                    <th>Días de partido/semana</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ligas)): ?>
                    <?php foreach ($ligas as $l): ?>
                        <tr>
                            <td><?= htmlspecialchars($l['nombre']) ?></td>
                            <td><?= htmlspecialchars($l['temporada'] ?? '-') ?></td>
                            <td><?= (int) $l['dias_partido_semana'] ?></td>
                            <td><span class="badge-estado badge-<?= $l['estado'] === 'activa' ? 'activo' : 'inactivo' ?>"><?= ucfirst($l['estado']) ?></span></td>
                            <td class="text-end">
                                <?php if ($this->tienePermiso('editar')): ?>
                                    <a href="<?= BASE_URL ?>/ligas/editar/<?= $l['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php endif; ?>
                                <?php if ($this->tienePermiso('eliminar')): ?>
                                    <a href="<?= BASE_URL ?>/ligas/eliminar/<?= $l['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar esta liga?">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay ligas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
