<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Torneos</h1>
        <p>Eliminatorias directas generadas a partir de la tabla de posiciones de una liga.</p>
    </div>
    <?php if ($this->tienePermiso('crear')): ?>
        <a href="<?= BASE_URL ?>/torneos/crear" class="btn btn-primary">+ Nuevo torneo</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Torneo</th>
                    <th>Liga</th>
                    <th>Equipos</th>
                    <th>Periodo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($torneos)): ?>
                    <?php foreach ($torneos as $t): ?>
                        <tr>
                            <td>
                                <a href="<?= BASE_URL ?>/torneos/detalle/<?= $t['id'] ?>" class="link-equipo"><?= htmlspecialchars($t['nombre']) ?></a>
                            </td>
                            <td><?= htmlspecialchars($t['liga_nombre'] ?? '-') ?></td>
                            <td><?= (int) $t['total_equipos'] ?></td>
                            <td><?= date('d M Y', strtotime($t['fecha_inicio'])) ?> — <?= date('d M Y', strtotime($t['fecha_fin'])) ?></td>
                            <td><span class="badge-estado badge-<?= $t['estado'] === 'finalizado' ? 'activo' : 'pendiente' ?>"><?= ucfirst(str_replace('_', ' ', $t['estado'])) ?></span></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/torneos/detalle/<?= $t['id'] ?>" class="btn btn-sm btn-outline-secondary">Ver bracket</a>
                                <?php if ($this->tienePermiso('eliminar')): ?>
                                    <a href="<?= BASE_URL ?>/torneos/eliminar/<?= $t['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar este torneo y todo su bracket?">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay torneos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
