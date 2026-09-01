<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Representantes y DT</h1>
        <p>Personas que pueden asignarse a un equipo como representante o director técnico.</p>
    </div>
    <a href="<?= BASE_URL ?>/staff/crear" class="btn btn-primary">+ Nuevo integrante</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($staff)): ?>
                    <?php foreach ($staff as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre'] . ' ' . $p['apellido']) ?></td>
                            <td><?= htmlspecialchars($p['telefono'] ?? '-') ?></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/staff/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <a href="<?= BASE_URL ?>/staff/eliminar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar a esta persona?">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">No hay representantes ni DT registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
