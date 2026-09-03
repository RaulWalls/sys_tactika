<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Jugadores</h1>
        <p>Plantilla registrada del equipo.</p>
    </div>
    <?php if ($this->tienePermiso('crear')): ?>
        <a href="<?= BASE_URL ?>/jugadores/crear" class="btn btn-primary">+ Nuevo jugador</a>
    <?php endif; ?>
</div>

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
