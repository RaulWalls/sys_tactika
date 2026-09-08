<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Equipos</h1>
        <p>Equipos gestionados en Táctika.</p>
    </div>
    <?php if ($this->tienePermiso('crear')): ?>
        <a href="<?= BASE_URL ?>/equipos/crear" class="btn btn-primary">+ Nuevo equipo</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Liga</th>
                    <th>Categoría</th>
                    <th>Temporada</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($equipos)): ?>
                    <?php foreach ($equipos as $e): ?>
                        <tr>
                            <td>
                                <a href="<?= BASE_URL ?>/equipos/detalle/<?= $e['id'] ?>" class="link-equipo">
                                    <?= htmlspecialchars($e['nombre']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($e['liga_nombre'] ?? 'Sin liga') ?></td>
                            <td><?= htmlspecialchars($e['categoria'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($e['temporada'] ?? '-') ?></td>
                            <td class="text-end">
                                <?php if ($this->tienePermiso('editar')): ?>
                                    <a href="<?= BASE_URL ?>/equipos/editar/<?= $e['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php endif; ?>
                                <?php if ($this->tienePermiso('eliminar')): ?>
                                    <a href="<?= BASE_URL ?>/equipos/eliminar/<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar este equipo?">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay equipos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (($totalPaginas ?? 1) > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-end mb-0">
            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>/equipos?pagina=<?= $pagina - 1 ?>">Anterior</a>
            </li>

            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/equipos?pagina=<?= $p ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>/equipos?pagina=<?= $pagina + 1 ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
