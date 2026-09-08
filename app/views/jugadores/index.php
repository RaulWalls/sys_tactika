<?php
    $esJugador = ($_SESSION['usuario_rol'] ?? '') === 'jugador'; 
    $esEntrenador = ($_SESSION['usuario_rol'] ?? '') === 'entrenador'; 
    $esAdmin = ($_SESSION['usuario_rol'] ?? '') === 'administrador';
?>

<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Jugadores</h1>
        <p>Plantilla registrada de <?= htmlspecialchars($nombreEquipo ?? '') ?>.</p>
    </div>
    <?php if ($this->tienePermiso('crear')): ?>
        <a href="<?= BASE_URL ?>/jugadores/crear" class="btn btn-primary">+ Nuevo jugador</a>
    <?php endif; ?>
</div>
    <?php if ($esAdmin): ?>
        <form method="get" action="<?= BASE_URL ?>/jugadores">
            <div class="col-md-3">
                <select name="equipo_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Todos los equipos</option>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $equipoSeleccionado === (int) $e['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        <br>
    <?php else: ?>
        <p class="text-muted">Equipo: <strong><?= htmlspecialchars($nombreEquipo ?: 'Sin equipo asignado') ?></strong></p>
    <?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Equipo</th>
                    <th>Posición</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($jugadores)): ?>
                    <?php foreach ($jugadores as $j): ?>
                        <tr>
                            <td><?= $j['numero'] ?? '-' ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/jugadores/detalle/<?= $j['id'] ?>" class="link-equipo">
                                    <?= htmlspecialchars($j['nombre'] . ' ' . $j['apellido']) ?>
                                </a>
                                <?php if (!empty($j['capitan'])): ?>
                                    <span class="badge-estado badge-capitan">Capitán</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($j['equipos_nombres'] ?? '') ?: 'Sin equipo' ?></td>
                            <td>
                                <?php if (!empty($j['posicion_nombre'])): ?>
                                    <span class="badge-estado" style="background: <?= htmlspecialchars($j['posicion_tag']) ?>1a; color: <?= htmlspecialchars($j['posicion_tag']) ?>;">
                                        <?= htmlspecialchars($j['posicion_nombre']) ?>
                                    </span>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><span class="badge-estado badge-<?= $j['estado'] ?>"><?= ucfirst($j['estado']) ?></span></td>
                            <?php $esMiPropioPerfil = ($_SESSION['usuario_rol'] ?? '') === 'jugador' && (int) ($_SESSION['usuario_jugador_id'] ?? 0) === (int) $j['id']; ?>
                            <td class="text-end">
                                <?php if ($this->tienePermiso('editar') && (($_SESSION['usuario_rol'] ?? '') !== 'jugador' || $esMiPropioPerfil)): ?>
                                    <a href="<?= BASE_URL ?>/jugadores/editar/<?= $j['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php endif; ?>
                                <?php if ($this->tienePermiso('eliminar')): ?>
                                    <a href="<?= BASE_URL ?>/jugadores/eliminar/<?= $j['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar a este jugador?">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay jugadores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (($totalPaginas ?? 1) > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-end mb-0">
            <?php
                $queryBase = $equipoSeleccionado ? ['equipo_id' => $equipoSeleccionado] : [];
            ?>
            <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>/jugadores?<?= http_build_query($queryBase + ['pagina' => $pagina - 1]) ?>">Anterior</a>
            </li>

            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="<?= BASE_URL ?>/jugadores?<?= http_build_query($queryBase + ['pagina' => $p]) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>/jugadores?<?= http_build_query($queryBase + ['pagina' => $pagina + 1]) ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>
