<div class="welcome d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <div class="team-logo detalle-logo">
            <?php if (!empty($equipo['escudo'])): ?>
                <img src="<?= BASE_URL . '/' . htmlspecialchars($equipo['escudo']) ?>" alt="Escudo">
            <?php else: ?>
                <i class="fa-solid fa-futbol"></i>
            <?php endif; ?>
        </div>

        <div>
            <h1><?= htmlspecialchars($equipo['nombre']) ?></h1>
            <p><?= htmlspecialchars($equipo['categoria'] ?? 'Sin categoría') ?> · <?= htmlspecialchars($equipo['temporada'] ?? 'Sin temporada') ?></p>
        </div>
    </div>

    <div class="d-flex gap-2">
        <?php if ($this->tienePermiso('editar')): ?>
            <a href="<?= BASE_URL ?>/equipos/editar/<?= $equipo['id'] ?>" class="btn btn-outline-secondary">Editar</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/equipos" class="btn btn-outline-secondary">← Volver</a>
    </div>
</div>

<!-- DATOS GENERALES -->
<div class="card mb-3">
    <div class="section-header">
        <h2>Datos generales</h2>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <span class="detalle-label">Liga / Competencia</span>
            <div class="detalle-valor"><?= htmlspecialchars($equipo['liga_nombre'] ?? 'Sin asignar') ?></div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Fecha de ingreso</span>
            <div class="detalle-valor">
                <?= !empty($equipo['fecha_ingreso']) ? date('d M Y', strtotime($equipo['fecha_ingreso'])) : '-' ?>
            </div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Sede</span>
            <div class="detalle-valor"><?= htmlspecialchars($equipo['sede'] ?? '-') ?></div>
        </div>

        <div class="col-md-6">
            <span class="detalle-label">Representante</span>
            <div class="detalle-valor">
                <?php if (!empty($equipo['representante_nombre'])): ?>
                    <?= htmlspecialchars($equipo['representante_nombre'] . ' ' . $equipo['representante_apellido']) ?>
                    <?php if (!empty($equipo['representante_telefono'])): ?>
                        <div class="detalle-sub"><?= htmlspecialchars($equipo['representante_telefono']) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-6">
            <span class="detalle-label">Director técnico</span>
            <div class="detalle-valor">
                <?php if (!empty($equipo['dt_nombre'])): ?>
                    <?= htmlspecialchars($equipo['dt_nombre'] . ' ' . $equipo['dt_apellido']) ?>
                    <?php if (!empty($equipo['dt_telefono'])): ?>
                        <div class="detalle-sub"><?= htmlspecialchars($equipo['dt_telefono']) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- JUGADORES -->
<div class="card">
    <div class="section-header">
        <h2>Jugadores</h2>
        <?php if ($this->tienePermiso('crear', 'JugadoresController')): ?>
            <a href="<?= BASE_URL ?>/jugadores/crear">+ Agregar jugador →</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
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
                                <?php if ($this->tienePermiso('editar', 'JugadoresController') && (($_SESSION['usuario_rol'] ?? '') !== 'jugador' || $esMiPropioPerfil)): ?>
                                    <a href="<?= BASE_URL ?>/jugadores/editar/<?= $j['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Este equipo aún no tiene jugadores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
