<?php $esJugador = ($_SESSION['usuario_rol'] ?? '') === 'jugador'; ?>

<!-- WELCOME -->
<div class="welcome">
    <h1>Buenos días 👋</h1>
    <p>Aquí tienes el resumen de tu equipo.</p>
</div>

<!-- STATS -->
<?php if ($esJugador): ?>
    <div class="grid" style="grid-template-columns: minmax(0, 220px);">
        <div class="card stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-number"><?= $totalPartidosJugados ?></div>
            <div class="stat-label">Partidos jugados</div>
        </div>
    </div>
<?php else: ?>
    <div class="grid stats-grid">
        <div class="card stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number"><?= $totalJugadores ?></div>
            <div class="stat-label">Jugadores registrados</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon">⚽</div>
            <div class="stat-number"><?= $totalEquipos ?></div>
            <div class="stat-label">Equipos</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon">🏆</div>
            <div class="stat-number"><?= $totalPartidosJugados ?></div>
            <div class="stat-label">Partidos jugados</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-number">$<?= number_format($resumenFinanciero['balance'], 2) ?></div>
            <div class="stat-label">Balance</div>
        </div>
    </div>
<?php endif; ?>

<!-- MATCH (+ EVENTS para admin/entrenador) -->
<div class="grid <?= $esJugador ? '' : 'main-grid' ?>" style="margin-top:20px;<?= $esJugador ? ' grid-template-columns: 1fr;' : '' ?>">

    <!-- NEXT MATCH -->
    <div class="card">
        <div class="section-header">
            <h2>Próximo partido</h2>
            <a href="<?= BASE_URL ?>/partidos">Ver calendario →</a>
        </div>

        <?php if ($totalEquipos <= 1 && $totalPartidos === 0): ?>
            <p class="text-muted">Sin partidos pendientes.</p>
        <?php elseif (!empty($proximosPartidos)): ?>
            <?php $siguiente = $proximosPartidos[0]; ?>
            <div class="match-card">
                <div class="match-label">
                    <?= htmlspecialchars($siguiente['competencia'] ?? 'Liga') ?> · <?= htmlspecialchars($siguiente['jornada'] ?? '') ?>
                </div>

                <div class="match-info">
                    <div class="team">
                        <div class="team-logo">
                            <?php if (!empty($siguiente['equipo_escudo'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($siguiente['equipo_escudo']) ?>" alt="Escudo">
                            <?php else: ?>
                                ⚽
                            <?php endif; ?>
                        </div>
                        <div class="team-name"><?= htmlspecialchars($siguiente['equipo_nombre'] ?? APP_NAME) ?></div>
                    </div>

                    <div class="vs">VS</div>

                    <div class="team">
                        <div class="team-logo">
                            <?php if (!empty($siguiente['rival_escudo'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($siguiente['rival_escudo']) ?>" alt="Escudo">
                            <?php else: ?>
                                ⚡
                            <?php endif; ?>
                        </div>
                        <div class="team-name"><?= htmlspecialchars($siguiente['rival']) ?></div>
                    </div>
                </div>

                <div class="match-date">
                    <span>📅 <?= date('d M Y', strtotime($siguiente['fecha'])) ?></span>
                    <span>🕐 <?= date('h:i A', strtotime($siguiente['hora'])) ?></span>
                    <span>📍 <?= htmlspecialchars($siguiente['sede'] ?? 'Por definir') ?></span>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted">No hay partidos programados.</p>
        <?php endif; ?>
    </div>

    <?php if (!$esJugador): ?>
        <!-- EVENTS -->
        <div class="card">
            <div class="section-header">
                <h2>Próximos eventos</h2>
                <a href="<?= BASE_URL ?>/partidos">Calendario →</a>
            </div>

            <?php if (!empty($proximosPartidos)): ?>
                <?php foreach ($proximosPartidos as $partido): ?>
                    <div class="event">
                        <div class="event-date">
                            <strong><?= date('d', strtotime($partido['fecha'])) ?></strong>
                            <span><?= date('M', strtotime($partido['fecha'])) ?></span>
                        </div>
                        <div class="event-info">
                            <strong>Vs. <?= htmlspecialchars($partido['rival']) ?></strong>
                            <span><?= date('H:i', strtotime($partido['hora'])) ?> · <?= htmlspecialchars($partido['sede'] ?? '') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Sin eventos próximos.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>

<!-- RESULTS (+ FINANCE para admin/entrenador) -->
<div class="grid <?= $esJugador ? '' : 'bottom-grid' ?>" style="margin-top:20px;<?= $esJugador ? ' grid-template-columns: 1fr;' : '' ?>">

    <!-- RESULTS -->
    <div class="card">
        <div class="section-header">
            <h2>Últimos resultados</h2>
            <a href="<?= BASE_URL ?>/partidos">Ver todos →</a>
        </div>

        <?php if (!empty($ultimosResultados)): ?>
            <?php foreach ($ultimosResultados as $r): ?>
                <?php
                    $clase = 'draw';
                    $texto = 'EMPATE';
                    if ($r['goles_favor'] > $r['goles_contra']) { $clase = 'win'; $texto = 'VICTORIA'; }
                    if ($r['goles_favor'] < $r['goles_contra']) { $clase = 'loss'; $texto = 'DERROTA'; }
                ?>
                <div class="result">
                    <div class="result-team">
                        <div class="mini-logo">
                            <?php if (!empty($r['equipo_escudo'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($r['equipo_escudo']) ?>" alt="Escudo">
                            <?php else: ?>
                                ⚽
                            <?php endif; ?>
                        </div>
                        <?= htmlspecialchars($r['equipo_nombre'] ?? APP_NAME) ?>
                        <strong><?= $r['goles_favor'] ?></strong>
                    </div>

                    <div class="score <?= $clase ?>"><?= $texto ?></div>

                    <div class="result-team">
                        <div class="mini-logo">
                            <?php if (!empty($r['rival_escudo'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($r['rival_escudo']) ?>" alt="Escudo">
                            <?php else: ?>
                                ⚡
                            <?php endif; ?>
                        </div>
                        <?= htmlspecialchars($r['rival']) ?>
                        <strong><?= $r['goles_contra'] ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">Aún no hay resultados registrados.</p>
        <?php endif; ?>
    </div>

    <?php if (!$esJugador): ?>
        <!-- FINANCE -->
        <div class="card">
            <div class="section-header">
                <h2>Finanzas del equipo</h2>
                <a href="<?= BASE_URL ?>/pagos">Administrar →</a>
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
    <?php endif; ?>

</div>
