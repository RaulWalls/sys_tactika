<div class="welcome detalle-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <div class="team-logo detalle-logo">
            <?php if (!empty($liga['escudo'])): ?>
                <img src="<?= BASE_URL . '/' . htmlspecialchars($liga['escudo']) ?>" alt="Escudo">
            <?php else: ?>
                <i class="fa-solid fa-trophy"></i>
            <?php endif; ?>
        </div>

        <div>
            <h1><?= htmlspecialchars($liga['nombre']) ?></h1>
            <p>
                <?= htmlspecialchars($liga['temporada'] ?? 'Sin temporada') ?> ·
                <span class="badge-estado badge-<?= $liga['estado'] === 'activa' ? 'activo' : 'inactivo' ?>"><?= ucfirst($liga['estado']) ?></span>
            </p>
        </div>
    </div>

    <div class="d-flex gap-2">
        <?php if ($this->tienePermiso('editar')): ?>
            <a href="<?= BASE_URL ?>/ligas/editar/<?= $liga['id'] ?>" class="btn btn-outline-secondary">Editar</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/ligas" class="btn btn-outline-secondary">← Volver</a>
    </div>
</div>

<!-- DATOS GENERALES -->
<div class="card mb-3">
    <div class="section-header">
        <h2>Datos generales</h2>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <span class="detalle-label">Periodo</span>
            <div class="detalle-valor">
                <?= !empty($liga['fecha_inicio']) ? date('d M Y', strtotime($liga['fecha_inicio'])) : '-' ?>
                —
                <?= !empty($liga['fecha_fin']) ? date('d M Y', strtotime($liga['fecha_fin'])) : '-' ?>
            </div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Días de partido a la semana</span>
            <div class="detalle-valor"><?= (int) $liga['dias_partido_semana'] ?></div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Total de equipos</span>
            <div class="detalle-valor"><?= $liga['total_equipos'] ?? 'Sin definir' ?></div>
        </div>

        <div class="col-md-6">
            <span class="detalle-label">Calendario incompleto</span>
            <div class="detalle-valor"><?= !empty($liga['permite_calendario_incompleto']) ? 'Permitido' : 'No permitido' ?></div>
        </div>
    </div>
</div>

<!-- TABLA DE POSICIONES -->
<div class="card mb-3">
    <div class="section-header">
        <h2>Tabla de posiciones</h2>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Equipo</th>
                    <th class="text-center">PJ</th>
                    <th class="text-center">G</th>
                    <th class="text-center">E</th>
                    <th class="text-center">P</th>
                    <th class="text-center">GF</th>
                    <th class="text-center">GC</th>
                    <th class="text-center">DG</th>
                    <th class="text-center">Pts</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tabla)): ?>
                    <?php foreach ($tabla as $i => $t): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/equipos/detalle/<?= $t['equipo_id'] ?>" class="link-equipo"><?= htmlspecialchars($t['nombre']) ?></a>
                            </td>
                            <td class="text-center"><?= $t['pj'] ?></td>
                            <td class="text-center"><?= $t['g'] ?></td>
                            <td class="text-center"><?= $t['e'] ?></td>
                            <td class="text-center"><?= $t['p'] ?></td>
                            <td class="text-center"><?= $t['gf'] ?></td>
                            <td class="text-center"><?= $t['gc'] ?></td>
                            <td class="text-center"><?= $t['dg'] ?></td>
                            <td class="text-center"><strong><?= $t['pts'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Aún no hay partidos jugados en esta liga.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- EQUIPOS -->
<div class="card">
    <div class="section-header">
        <h2>Equipos</h2>
        <?php if ($this->tienePermiso('crear', 'EquiposController')): ?>
            <a href="<?= BASE_URL ?>/equipos/crear">+ Agregar equipo →</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Categoría</th>
                    <th>Temporada</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($equipos)): ?>
                    <?php foreach ($equipos as $e): ?>
                        <tr>
                            <td>
                                <a href="<?= BASE_URL ?>/equipos/detalle/<?= $e['id'] ?>" class="link-equipo"><?= htmlspecialchars($e['nombre']) ?></a>
                            </td>
                            <td><?= htmlspecialchars($e['categoria'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($e['temporada'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">Esta liga aún no tiene equipos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
