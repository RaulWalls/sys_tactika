<?php
    $sujeto = $vista === 'jugador' ? $jugador : $equipo;
    $nombreSujeto = $sujeto
        ? ($vista === 'jugador' ? $sujeto['nombre'] . ' ' . $sujeto['apellido'] : $sujeto['nombre'])
        : null;
?>

<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Progreso</h1>
        <p>Rango, logros y badges<?= $nombreSujeto ? ' de ' . htmlspecialchars($nombreSujeto) : '' ?>.</p>
    </div>
</div>

<form method="get" action="<?= BASE_URL ?>/progreso" class="d-flex gap-2 mb-3 flex-wrap align-items-center">
    <?php if ($puedeVistaJugador): ?>
        <div class="btn-group" role="group">
            <a href="<?= BASE_URL ?>/progreso?vista=jugador" class="btn btn-sm <?= $vista === 'jugador' ? 'btn-primary' : 'btn-outline-secondary' ?>">Jugador</a>
            <a href="<?= BASE_URL ?>/progreso?vista=equipo" class="btn btn-sm <?= $vista === 'equipo' ? 'btn-primary' : 'btn-outline-secondary' ?>">Equipo</a>
        </div>
    <?php endif; ?>

    <?php if ($vista === 'jugador' && $esAdmin && !empty($jugadoresDisponibles)): ?>
        <input type="hidden" name="vista" value="jugador">
        <select name="jugador_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            <?php foreach ($jugadoresDisponibles as $j): ?>
                <option value="<?= $j['id'] ?>" <?= ($jugador['id'] ?? null) === $j['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($j['nombre'] . ' ' . $j['apellido']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <?php if ($vista === 'equipo' && count($equiposDisponibles) > 1): ?>
        <input type="hidden" name="vista" value="equipo">
        <select name="equipo_id" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
            <?php foreach ($equiposDisponibles as $e): ?>
                <option value="<?= $e['id'] ?>" <?= ($equipo['id'] ?? null) === $e['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($e['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
</form>

<?php if (!$sujeto): ?>
    <div class="card">
        <p class="text-muted mb-0">
            <?= $vista === 'jugador' ? 'No tienes un perfil de jugador vinculado.' : 'No perteneces a ningún equipo todavía.' ?>
        </p>
    </div>
<?php else: ?>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabRango" type="button" role="tab">Rango</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabLogros" type="button" role="tab">Logros</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabBadges" type="button" role="tab">Badges</button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- RANGO -->
        <div class="tab-pane fade show active" id="tabRango" role="tabpanel">
            <div class="card rango-card">
                <div class="rango-icono">
                    <i class="<?= htmlspecialchars($rango['icono'] ?? 'fa-solid fa-shield-halved') ?>"></i>
                </div>
                <div>
                    <div class="rango-nombre"><?= htmlspecialchars(mb_strtoupper($rango['nombre'] ?? 'Sin rango')) ?> — Nivel <?= (int) $nivel ?></div>
                    <div class="text-muted">Próximo rango en base a tu progresión.</div>
                </div>
            </div>
        </div>

        <!-- LOGROS -->
        <div class="tab-pane fade" id="tabLogros" role="tabpanel">
            <?php if (empty($logros)): ?>
                <div class="card"><p class="text-muted mb-0">Aún no hay logros configurados.</p></div>
            <?php else: ?>
                <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
                    <?php foreach ($logros as $l): ?>
                        <?php
                            $porcentaje = !empty($l['meta']) ? min(100, (int) round($l['progreso'] / $l['meta'] * 100)) : 0;
                            $desbloqueado = !empty($l['desbloqueado_en']);
                        ?>
                        <div class="card logro-card <?= $desbloqueado ? '' : 'logro-bloqueado' ?>" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#logroModal<?= $l['logro_id'] ?>">
                            <div class="logro-icono">
                                <?php if (!empty($l['imagen'])): ?>
                                    <img src="<?= BASE_URL . '/' . htmlspecialchars($l['imagen']) ?>" alt="">
                                <?php else: ?>
                                    <i class="<?= htmlspecialchars($l['icono']) ?>"></i>
                                <?php endif; ?>
                            </div>
                            <div class="logro-nombre"><?= htmlspecialchars($l['nombre']) ?></div>
                            <span class="badge-estado badge-rareza-<?= $l['categoria'] ?>"><?= ucfirst($l['categoria']) ?></span>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar" style="width: <?= $porcentaje ?>%;"></div>
                            </div>
                            <div class="text-muted mt-1" style="font-size: 11px;">
                                <?= (int) $l['progreso'] ?><?= $l['meta'] ? ' / ' . (int) $l['meta'] : '' ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- BADGES -->
        <div class="tab-pane fade" id="tabBadges" role="tabpanel">
            <?php if (empty($badges)): ?>
                <div class="card"><p class="text-muted mb-0">Aún no hay badges configuradas.</p></div>
            <?php else: ?>
                <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
                    <?php foreach ($badges as $b): ?>
                        <div class="card logro-card <?= $b['cantidad'] > 0 ? '' : 'logro-bloqueado' ?>" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#badgeModal<?= $b['badge_id'] ?>">
                            <div class="logro-icono">
                                <?php if (!empty($b['imagen'])): ?>
                                    <img src="<?= BASE_URL . '/' . htmlspecialchars($b['imagen']) ?>" alt="">
                                <?php else: ?>
                                    <i class="<?= htmlspecialchars($b['icono']) ?>"></i>
                                <?php endif; ?>
                            </div>
                            <div class="logro-nombre"><?= htmlspecialchars($b['nombre']) ?></div>
                            <span class="badge-estado badge-rareza-<?= $b['categoria'] ?>"><?= ucfirst($b['categoria']) ?></span>
                            <div class="stat-number mt-2" style="font-size: 22px;">x<?= (int) $b['cantidad'] ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- MODALES: DETALLE DE LOGRO -->
    <?php foreach ($logros as $l): ?>
        <div class="modal fade" id="logroModal<?= $l['logro_id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mb-0"><?= htmlspecialchars($l['nombre']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="logro-icono mx-auto mb-3" style="width:80px;height:80px;font-size:32px;">
                            <?php if (!empty($l['imagen'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($l['imagen']) ?>" alt="">
                            <?php else: ?>
                                <i class="<?= htmlspecialchars($l['icono']) ?>"></i>
                            <?php endif; ?>
                        </div>
                        <span class="badge-estado badge-rareza-<?= $l['categoria'] ?> mb-2"><?= ucfirst($l['categoria']) ?></span>
                        <p class="text-muted"><?= htmlspecialchars($l['descripcion']) ?></p>
                        <div class="detalle-label">Fecha de obtención</div>
                        <div class="detalle-valor">
                            <?= !empty($l['desbloqueado_en']) ? date('d M Y', strtotime($l['desbloqueado_en'])) : 'Aún no desbloqueado' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- MODALES: DETALLE DE BADGE -->
    <?php foreach ($badges as $b): ?>
        <div class="modal fade" id="badgeModal<?= $b['badge_id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mb-0"><?= htmlspecialchars($b['nombre']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="logro-icono mx-auto mb-3" style="width:80px;height:80px;font-size:32px;">
                            <?php if (!empty($b['imagen'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($b['imagen']) ?>" alt="">
                            <?php else: ?>
                                <i class="<?= htmlspecialchars($b['icono']) ?>"></i>
                            <?php endif; ?>
                        </div>
                        <span class="badge-estado badge-rareza-<?= $b['categoria'] ?> mb-2"><?= ucfirst($b['categoria']) ?></span>
                        <p class="text-muted"><?= htmlspecialchars($b['descripcion']) ?></p>
                        <div class="detalle-label">Obtenidas</div>
                        <div class="detalle-valor">x<?= (int) $b['cantidad'] ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
