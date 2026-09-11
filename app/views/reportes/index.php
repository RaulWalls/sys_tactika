<div class="welcome">
    <h1>Reportes</h1>
    <p>Resumen general del sistema.</p>
</div>

<div class="grid stats-grid">
    <div class="card stat-card">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-number"><?= $totalJugadores ?></div>
        <div class="stat-label">Jugadores</div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon"><i class="fa-solid fa-futbol"></i></div>
        <div class="stat-number"><?= $totalEquipos ?></div>
        <div class="stat-label">Equipos</div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
        <div class="stat-number"><?= $totalPartidos ?></div>
        <div class="stat-label">Partidos totales</div>
    </div>

    <div class="card stat-card">
        <div class="stat-icon"><i class="fa-solid fa-chart-column"></i></div>
        <div class="stat-number"><?= $partidosJugados ?></div>
        <div class="stat-label">Partidos jugados</div>
    </div>
</div>

<!-- RENDIMIENTO DEPORTIVO -->
<div class="card mt-3">
    <div class="section-header">
        <h2>Rendimiento deportivo</h2>
    </div>

    <form method="get" action="<?= BASE_URL ?>/reportes" class="d-flex gap-2 mb-3 flex-wrap align-items-center">
        <div class="btn-group" role="group">
            <a href="<?= BASE_URL ?>/reportes?tipo=liga" class="btn btn-sm <?= $tipoFiltro === 'liga' ? 'btn-primary' : 'btn-outline-secondary' ?>">Liga</a>
            <a href="<?= BASE_URL ?>/reportes?tipo=torneo" class="btn btn-sm <?= $tipoFiltro === 'torneo' ? 'btn-primary' : 'btn-outline-secondary' ?>">Torneo</a>
        </div>

        <input type="hidden" name="tipo" value="<?= $tipoFiltro ?>">

        <?php if ($tipoFiltro === 'liga'): ?>
            <select name="valor" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <?php foreach ($ligas as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= $seleccionado === (int) $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php else: ?>
            <select name="valor" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                <?php if (empty($torneos)): ?>
                    <option value="">No hay torneos</option>
                <?php endif; ?>
                <?php foreach ($torneos as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $seleccionado === (int) $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </form>

    <div class="finance mb-3">
        <div class="finance-item">
            <span>VICTORIAS</span>
            <strong class="positive"><?= $victorias ?></strong>
        </div>
        <div class="finance-item">
            <span>EMPATES</span>
            <strong><?= $empates ?></strong>
        </div>
        <div class="finance-item">
            <span>DERROTAS</span>
            <strong class="negative"><?= $derrotas ?></strong>
        </div>
    </div>

    <!-- TABLA DE POSICIONES -->
    <h3 class="detalle-sub mb-2">Tabla de posiciones</h3>
    <div class="table-responsive mb-4">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Equipo</th>
                    <th class="text-center">PJ</th>
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
                            <td class="text-center"><?= $t['gf'] ?></td>
                            <td class="text-center"><?= $t['gc'] ?></td>
                            <td class="text-center"><?= $t['dg'] ?></td>
                            <td class="text-center"><strong><?= $t['pts'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Aún no hay partidos jugados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- GOLEO -->
    <h3 class="detalle-sub mb-2">Goleo</h3>
    <div class="table-responsive">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Equipo</th>
                    <th>Jugador</th>
                    <th class="text-center">Goles</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($goleadores)): ?>
                    <?php foreach ($goleadores as $i => $g): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if (!empty($g['equipo_id'])): ?>
                                    <a href="<?= BASE_URL ?>/equipos/detalle/<?= $g['equipo_id'] ?>" class="link-equipo"><?= htmlspecialchars($g['equipo_nombre']) ?></a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= BASE_URL ?>/jugadores/detalle/<?= $g['jugador_id'] ?>" class="link-equipo"><?= htmlspecialchars($g['nombre'] . ' ' . $g['apellido']) ?></a>
                            </td>
                            <td class="text-center"><strong><?= (int) $g['goles'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Aún no hay goles registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($topEquipos)): ?>
        <div class="d-flex flex-wrap gap-2 mt-3">
            <?php foreach ($topEquipos as $i => $t): ?>
                <span class="badge-estado badge-activo">
                    #<?= $i + 1 ?> <?= htmlspecialchars($t['nombre']) ?> (DG <?= $t['dg'] >= 0 ? '+' . $t['dg'] : $t['dg'] ?>)
                </span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div>
        <br>
        <button class="btn btn-primary" style="display: flex; align-items: center; justify-content: center; gap: 8px;" >
            <i class="fas fa-file-pdf me-2"></i>
            Generar Reporte
        </button>
    </div>
</div>

<!-- REPORTE FINANCIERO -->
<div class="card mt-3">
    <div class="section-header">
        <h2>Reporte financiero</h2>
        <a href="<?= BASE_URL ?>/pagos">Ver pagos →</a>
    </div>

    <div class="finance">
        <div class="finance-item">
            <span>INGRESOS</span>
            <strong class="positive">$<?= number_format($resumenFinanciero['ingresos'], 2) ?></strong>
        </div>
        <div class="finance-item">
            <span>GASTOS</span>
            <strong class="negative">$<?= number_format($resumenFinanciero['gastos'], 2) ?></strong>
        </div>
        <div class="finance-item">
            <span>BALANCE</span>
            <strong class="<?= $resumenFinanciero['balance'] >= 0 ? 'positive' : 'negative' ?>">
                $<?= number_format($resumenFinanciero['balance'], 2) ?>
            </strong>
        </div>
    </div>
</div>
