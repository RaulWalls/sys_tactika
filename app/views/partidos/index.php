<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Calendario de partidos</h1>
        <p>Próximos partidos y resultados registrados.</p>
    </div>
    <a href="<?= BASE_URL ?>/partidos/crear" class="btn btn-primary">+ Nuevo partido</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Rival</th>
                    <th>Tipo</th>
                    <th>Competencia</th>
                    <th>Marcador</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($partidos)): ?>
                    <?php foreach ($partidos as $p): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($p['fecha'])) ?> · <?= date('H:i', strtotime($p['hora'])) ?></td>
                            <td><?= htmlspecialchars($p['rival']) ?></td>
                            <td><?= ucfirst($p['tipo']) ?></td>
                            <td><?= htmlspecialchars($p['competencia'] ?? '-') ?> <?= htmlspecialchars($p['jornada'] ?? '') ?></td>
                            <td>
                                <?= $p['goles_favor'] !== null ? $p['goles_favor'] . ' - ' . $p['goles_contra'] : '-' ?>
                            </td>
                            <td><span class="badge-estado badge-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/partidos/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <a href="<?= BASE_URL ?>/partidos/eliminar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar este partido?">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay partidos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
