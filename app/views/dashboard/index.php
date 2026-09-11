<?php
    $esJugador = ($_SESSION['usuario_rol'] ?? '') === 'jugador'; 
    $esEntrenador = ($_SESSION['usuario_rol'] ?? '') === 'entrenador'; 
    $esAdmin = ($_SESSION['usuario_rol'] ?? '') === 'administrador';
?>

<!-- WELCOME -->
<div class="welcome">
    <?php 
        $horaActual = (int) date('H');
        if ($horaActual >= 6 && $horaActual < 12) {
            echo '<h1>Buenos días <i class="fa-solid fa-sun"></i></h1>';
        } elseif ($horaActual >= 12 && $horaActual < 18) {
            echo '<h1>Buenas tardes <i class="fa-solid fa-clock"></i></h1>';
        } else {
            echo '<h1>Buenas noches <i class="fa-solid fa-moon"></i></h1>';
        }
    ?>
    <?php if ($esJugador): ?>
        <p>Aquí tienes el resumen de tu desempeño.</p>
    <?php elseif ($esEntrenador): ?>
        <p>Aquí tienes el resumen de tu equipo.</p>
    <?php elseif ($esAdmin): ?>
        <p>Aquí tienes el resumen de tus Ligas.</p>
        <?php endif; ?>

</div>

<!-- STATS -->
<?php if ($esJugador): ?>
    <!-- Player ID Card -->
    <!-- <div class="player-id-card">
        <span class="player-id-corner tl"></span>
        <span class="player-id-corner tr"></span>
        <span class="player-id-corner bl"></span>
        <span class="player-id-corner br"></span>

        <div class="player-id-nombre"><?= htmlspecialchars(mb_strtoupper(($miJugador['nombre'] ?? '') . ' ' . ($miJugador['apellido'] ?? ''))) ?></div>
        <div class="player-id-sub">
            #<?= htmlspecialchars($miJugador['numero'] ?? '-') ?> · <?= htmlspecialchars(mb_strtoupper($miJugador['posicion_nombre'] ?? 'Sin posición')) ?>
        </div>

        <div class="player-id-rango">
            <i class="<?= htmlspecialchars($miRango['icono'] ?? 'fa-solid fa-shield-halved') ?>"></i>
            <?= htmlspecialchars(mb_strtoupper($miRango['nombre'] ?? 'Sin rango')) ?>
            <div class="player-id-nivel">NIVEL <?= (int) $miNivel ?></div>
        </div>

        <div class="player-id-stats">
            <div><i class="fa-solid fa-trophy"></i> <?= (int) $miTotalLogros ?> LOGROS</div>
            <div><i class="fa-solid fa-medal"></i> <?= (int) $miTotalBadges ?> BADGES</div>
        </div>
    </div> -->
    <!-- Player ID Card 2-->
    <?php
        $clubEscudo = $miJugador['equipos'][0]['escudo'] ?? null;
        $posicionAbrev = mb_strtoupper(mb_substr(preg_replace('/\s+/', '', $miJugador['posicion_nombre'] ?? ''), 0, 3)) ?: '-';
    ?>
    <div class="player-id-card col-md-4">
        
        <span class="player-id-corner tl"></span>
        <span class="player-id-corner tr"></span>
        <span class="player-id-corner bl"></span>
        <span class="player-id-corner br"></span>

        <div class="fut-photo-wrap">
            <?php if (!empty($miJugador['foto'])): ?>
                <img class="fut-photo" src="<?= BASE_URL . '/' . htmlspecialchars($miJugador['foto']) ?>" alt="">
            <?php else: ?>
                <i class="fa-solid fa-shirt fut-photo-placeholder"></i>
            <?php endif; ?>

            <div class="fut-overlay-top">
                <div class="fut-valoracion "><?= (int) $miValoracion ?></div>
                <div class="fut-posicion"><?= htmlspecialchars($posicionAbrev) ?></div>
                <i class="fut-rango <?= htmlspecialchars($miRango['icono'] ?? 'fa-solid fa-shield-halved') ?>" title="<?= htmlspecialchars($miRango['nombre'] ?? 'Sin rango') ?>"></i>
            </div> 

            <div class="fut-nombre"><?= htmlspecialchars(mb_strtoupper(($miJugador['nombre'] ?? '') . ' ' . ($miJugador['apellido'] ?? ''))) ?></div>
        </div>

        <div class="fut-stats">
            <?php foreach ($misAtributos as $a): ?>
                <div class="fut-stat">
                    <span class="fut-stat-valor"><?= (int) $a['valor'] ?></span>
                    <span class="fut-stat-abrev"><?= htmlspecialchars($a['abreviatura']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="fut-footer">
            <span class="fut-footer-icon" title="<?= htmlspecialchars($miJugador['equipos'][0]['nombre'] ?? 'Sin equipo') ?>">
                <?php if ($clubEscudo): ?>
                    <img src="<?= BASE_URL . '/' . htmlspecialchars($clubEscudo) ?>" alt="">
                <?php else: ?>
                    <i class="fa-solid fa-shield-halved"></i>
                <?php endif; ?>
            </span>
            <span class="fut-footer-icon" title="Táctika">
                <i class="fa-solid fa-futbol"></i>
            </span>
            <span class="fut-footer-flag" title="México">🇲🇽</span>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); margin-top: 20px;">
        <div class="card">
            <div class="section-header">
                <h2>Badges</h2>
                <a href="<?= BASE_URL ?>/progreso">Ver todas →</a>
            </div>
            <?php if (empty($misBadges)): ?>
                <p class="text-muted mb-0">Aún no has obtenido ninguna badge.</p>
            <?php else: ?>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($misBadges as $b): ?>
                        <span class="badge-mini-icon" title="<?= htmlspecialchars($b['nombre']) ?>">
                            <?php if (!empty($b['imagen'])): ?>
                                <img src="<?= BASE_URL . '/' . htmlspecialchars($b['imagen']) ?>" alt="">
                            <?php else: ?>
                                <i class="<?= htmlspecialchars($b['icono']) ?>"></i>
                            <?php endif; ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="section-header">
                <h2>Últimos logros</h2>
                <a href="<?= BASE_URL ?>/progreso">Ver todos →</a>
            </div>
            <?php if (empty($misLogrosRecientes)): ?>
                <p class="text-muted mb-0">Aún no has desbloqueado ningún logro.</p>
            <?php else: ?>
                <?php foreach ($misLogrosRecientes as $l): ?>
                    <div class="player-logro-reciente">
                        <i class="<?= htmlspecialchars($l['icono']) ?>"></i>
                        <?= htmlspecialchars($l['nombre']) ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>
    <div class="grid stats-grid">
        <?php if($esAdmin): ?>
            <div class="card stat-card">
                <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
                <div class="stat-number"><?= $totalLigas ?></div>
                <div class="stat-label">Ligas</div>
            </div>

            <div class="card stat-card">
                <div class="stat-icon"><i class="fa-solid fa-sitemap"></i></div>
                <div class="stat-number"><?= $totalTorneos ?></div>
                <div class="stat-label">Competencias</div>
            </div>
        <?php endif; ?>
        
        <div class="card stat-card">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-number"><?= $totalJugadores ?></div>
            <div class="stat-label">Jugadores registrados</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon"><i class="fa-solid fa-futbol"></i></div>
            <div class="stat-number"><?= $totalEquipos ?></div>
            <div class="stat-label">Equipos</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon"><i class="fa-solid fa-trophy"></i></div>
            <div class="stat-number"><?= $totalPartidosJugados ?></div>
            <div class="stat-label">Partidos jugados</div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="stat-number">$<?= number_format($resumenFinanciero['balance'], 2) ?></div>
            <div class="stat-label">Balance</div>
        </div>
    </div>
<?php endif; ?>

<!-- IDENTIDAD DE EQUIPO(S) (entrenador) -->
<?php if ($esEntrenador && !empty($misEquiposInfo)): ?>
    <?php foreach ($misEquiposInfo as $eq): ?>
        <div class="player-id-card mt-3">
            <span class="player-id-corner tl"></span>
            <span class="player-id-corner tr"></span>
            <span class="player-id-corner bl"></span>
            <span class="player-id-corner br"></span>

            <div class="player-id-nombre"><?= htmlspecialchars(mb_strtoupper($eq['nombre'])) ?></div>
            <div class="player-id-sub"><?= htmlspecialchars(mb_strtoupper($eq['liga_nombre'] ?? 'Sin liga')) ?></div>

            <div class="player-id-rango">
                <i class="<?= htmlspecialchars($eq['rango']['icono'] ?? 'fa-solid fa-shield-halved') ?>"></i>
                <?= htmlspecialchars(mb_strtoupper($eq['rango']['nombre'] ?? 'Sin rango')) ?>
                <div class="player-id-nivel">NIVEL <?= (int) $eq['nivel'] ?></div>
            </div>

            <div class="player-id-stats">
                <div><i class="fa-solid fa-trophy"></i> <?= (int) $eq['totalLogros'] ?> LOGROS</div>
                <div><i class="fa-solid fa-medal"></i> <?= (int) $eq['totalBadges'] ?> BADGES</div>
            </div>
        </div>

        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); margin-top: 20px;">
            <div class="card">
                <div class="section-header">
                    <h2>Badges</h2>
                    <a href="<?= BASE_URL ?>/progreso?vista=equipo&equipo_id=<?= $eq['id'] ?>">Ver todas →</a>
                </div>
                <?php if (empty($eq['badges'])): ?>
                    <p class="text-muted mb-0">Este equipo aún no tiene badges vigentes.</p>
                <?php else: ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($eq['badges'] as $b): ?>
                            <span class="badge-mini-icon" title="<?= htmlspecialchars($b['nombre']) ?>">
                                <?php if (!empty($b['imagen'])): ?>
                                    <img src="<?= BASE_URL . '/' . htmlspecialchars($b['imagen']) ?>" alt="">
                                <?php else: ?>
                                    <i class="<?= htmlspecialchars($b['icono']) ?>"></i>
                                <?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <div class="section-header">
                    <h2>Últimos logros</h2>
                    <a href="<?= BASE_URL ?>/progreso?vista=equipo&equipo_id=<?= $eq['id'] ?>">Ver todos →</a>
                </div>
                <?php if (empty($eq['logrosRecientes'])): ?>
                    <p class="text-muted mb-0">Este equipo aún no ha desbloqueado ningún logro.</p>
                <?php else: ?>
                    <?php foreach ($eq['logrosRecientes'] as $l): ?>
                        <div class="player-logro-reciente">
                            <i class="<?= htmlspecialchars($l['icono']) ?>"></i>
                            <?= htmlspecialchars($l['nombre']) ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
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
                                <i class="fa-solid fa-futbol"></i>
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
                                <i class="fa-solid fa-bolt"></i>
                            <?php endif; ?>
                        </div>
                        <div class="team-name"><?= htmlspecialchars($siguiente['rival']) ?></div>
                    </div>
                </div>

                <div class="match-date">
                    <span><i class="fa-solid fa-calendar-days"></i> <?= date('d M Y', strtotime($siguiente['fecha'])) ?></span>
                    <span><i class="fa-regular fa-clock"></i> <?= date('h:i A', strtotime($siguiente['hora'])) ?></span>
                    <span><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($siguiente['sede'] ?? 'Por definir') ?></span>
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
            <!-- <a href="<?= BASE_URL ?>/partidos">Ver todos →</a> -->
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
                                <i class="fa-solid fa-futbol"></i>
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
                                <i class="fa-solid fa-bolt"></i>
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
                <?php if ($esAdmin): ?>
                    <h2>Finanzas de la liga</h2>
                <?php elseif ($esEntrenador): ?>
                    <h2>Finanzas del equipo</h2>
                <?php endif; ?>
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
