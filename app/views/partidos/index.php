<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Calendario de partidos</h1>
        <p>Próximos partidos y resultados registrados.</p>
    </div>
    <?php if ($this->tienePermiso('crear')): ?>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/partidos/generar" class="btn btn-primary"><i class="fa-solid fa-calendar-plus"></i> Generar calendario</a>
            <?php /* <a href="<?= BASE_URL ?>/partidos/crear" class="btn btn-primary">+ Nuevo partido</a> */ ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Local</th>
                    <th>Visitante</th>
                    <th>Competencia</th>
                    <th>Marcador</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($partidos)): ?>
                    <?php foreach ($partidos as $p): ?>
                        <?php
                            $equipoNombre = $p['equipo_nombre'] ?? 'Sin equipo';
                            $local = $p['tipo'] === 'visitante' ? $p['rival'] : $equipoNombre;
                            $visitante = $p['tipo'] === 'visitante' ? $equipoNombre : $p['rival'];
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($p['fecha'])) ?> · <?= date('H:i', strtotime($p['hora'])) ?></td>
                            <td><?= htmlspecialchars($local) ?></td>
                            <td><?= htmlspecialchars($visitante) ?></td>
                            <td><?= htmlspecialchars($p['competencia'] ?? '-') ?> <?= htmlspecialchars($p['jornada'] ?? '') ?></td>
                            <td>
                                <?= $p['goles_favor'] !== null ? $p['goles_favor'] . ' - ' . $p['goles_contra'] : '-' ?>
                            </td>
                            <td><span class="badge-estado badge-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                            <td class="text-end">
                                <?php if ($this->tienePermiso('editar', 'JugadoresController') && $p['equipo_id'] && ($_SESSION['usuario_rol'] ?? '') !== 'jugador'): ?>
                                    <a href="<?= BASE_URL ?>/partidos/eventos/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Estadísticas</a>
                                <?php endif; ?>
                                <?php if ($this->tienePermiso('editar')): ?>
                                    <a href="<?= BASE_URL ?>/partidos/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php endif; ?>
                                <?php if ($this->tienePermiso('eliminar')): ?>
                                    <a href="<?= BASE_URL ?>/partidos/eliminar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar este partido?">Eliminar</a>
                                <?php endif; ?>
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

<?php if (($totalPaginas ?? 1) > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-end mb-0">
            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>/partidos?pagina=<?= $pagina - 1 ?>">Anterior</a>
            </li>

            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/partidos?pagina=<?= $p ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>/partidos?pagina=<?= $pagina + 1 ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
