<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Usuarios</h1>
        <p>Personas con acceso al sistema.</p>
    </div>
    <a href="<?= BASE_URL ?>/usuarios/crear" class="btn btn-primary">+ Nuevo usuario</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= ucfirst($u['rol']) ?></td>
                            <td><span class="badge-estado badge-<?= $u['estado'] ?>"><?= ucfirst($u['estado']) ?></span></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/usuarios/editar/<?= $u['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <a href="<?= BASE_URL ?>/usuarios/eliminar/<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar este usuario?">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
