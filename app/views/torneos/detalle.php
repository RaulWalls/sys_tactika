<?php
    $es16 = (int) $torneo['total_equipos'] === 16;

    // Divide las posiciones de cada ronda en mitad izquierda/derecha (solo aplica al bracket de 16).
    $mitad = function (array $partidos, bool $derecha) {
        $n = count($partidos);
        $corte = intdiv($n, 2);
        return $derecha ? array_slice($partidos, $corte) : array_slice($partidos, 0, $corte);
    };

    $renderMatch = function (array $p) use ($sedes, $canchas) {
        $ganadorLocal = $p['estado'] === 'jugado' && (int) $p['goles_favor'] > (int) $p['goles_contra'];
        $ganadorRival = $p['estado'] === 'jugado' && (int) $p['goles_contra'] > (int) $p['goles_favor'];
        $listoParaJugar = $p['equipo_id'] && !empty($p['rival']);
        ?>
        <div class="bracket-match">
            <div class="bracket-team <?= $ganadorLocal ? 'ganador' : '' ?>">
                <?php if ($p['equipo_id']): ?>
                    <a href="<?= BASE_URL ?>/equipos/detalle/<?= $p['equipo_id'] ?>" class="bracket-team-name"><?= htmlspecialchars($p['equipo_nombre']) ?></a>
                <?php else: ?>
                    <span class="bracket-team-name text-muted">Por definir</span>
                <?php endif; ?>
                <span class="bracket-score"><?= $p['goles_favor'] ?? '' ?></span>
            </div>
            <div class="bracket-team <?= $ganadorRival ? 'ganador' : '' ?>">
                <?php if (!empty($p['rival'])): ?>
                    <?php if (!empty($p['rival_equipo_id'])): ?>
                        <a href="<?= BASE_URL ?>/equipos/detalle/<?= $p['rival_equipo_id'] ?>" class="bracket-team-name"><?= htmlspecialchars($p['rival']) ?></a>
                    <?php else: ?>
                        <span class="bracket-team-name"><?= htmlspecialchars($p['rival']) ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="bracket-team-name text-muted">Por definir</span>
                <?php endif; ?>
                <span class="bracket-score"><?= $p['goles_contra'] ?? '' ?></span>
            </div>

            <?php if ($listoParaJugar): ?>
                <button type="button" class="bracket-edit-btn" data-bs-toggle="modal" data-bs-target="#partidoModal<?= $p['id'] ?>">
                    <i class="fa-solid fa-pen"></i>
                </button>
            <?php endif; ?>
        </div>
    <?php };
?>

<div class="welcome detalle-header d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <div class="team-logo detalle-logo">
            <?php if (!empty($torneo['imagen'])): ?>
                <img src="<?= BASE_URL . '/' . htmlspecialchars($torneo['imagen']) ?>" alt="Imagen">
            <?php else: ?>
                <i class="fa-solid fa-sitemap"></i>
            <?php endif; ?>
        </div>

        <div>
            <h1><?= htmlspecialchars($torneo['nombre']) ?></h1>
            <p>
                <?= htmlspecialchars($torneo['liga_nombre'] ?? '') ?> ·
                <span class="badge-estado badge-<?= $torneo['estado'] === 'finalizado' ? 'activo' : 'pendiente' ?>"><?= ucfirst(str_replace('_', ' ', $torneo['estado'])) ?></span>
            </p>
        </div>
    </div>

    <div class="d-flex gap-2">
        <?php if ($this->tienePermiso('editar')): ?>
            <a href="<?= BASE_URL ?>/torneos/editar/<?= $torneo['id'] ?>" class="btn btn-outline-secondary">Editar</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/torneos" class="btn btn-outline-secondary">← Volver</a>
    </div>
</div>

<!-- DATOS GENERALES -->
<div class="card mb-3">
    <div class="section-header">
        <h2>Datos generales</h2>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <span class="detalle-label">Liga</span>
            <div class="detalle-valor"><?= htmlspecialchars($torneo['liga_nombre'] ?? '-') ?></div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Tipo de eliminatoria</span>
            <div class="detalle-valor"><?= (int) $torneo['total_equipos'] ?> equipos</div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Periodo</span>
            <div class="detalle-valor">
                <?= date('d M Y', strtotime($torneo['fecha_inicio'])) ?> — <?= date('d M Y', strtotime($torneo['fecha_fin'])) ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="section-header">
        <h2>Bracket</h2>
    </div>

    <div class="table-responsive">

        <?php if (!$es16): ?>
            <!-- BRACKET DE 8: una sola cadena hacia la final -->
            <div class="bracket-wrap">
                <?php foreach ($rondas as $ronda): ?>
                    <div class="bracket-round">
                        <?php foreach ($porRonda[$ronda] as $p) { $renderMatch($p); } ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- BRACKET DE 16: dos mitades convergiendo al centro -->
            <div class="bracket-two-sided">
                <div class="bracket-wrap">
                    <?php foreach ($rondas as $ronda): ?>
                        <?php if ($ronda === 'final') { continue; } ?>
                        <div class="bracket-round">
                            <?php foreach ($mitad($porRonda[$ronda], false) as $p) { $renderMatch($p); } ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bracket-round bracket-final">
                    <?php foreach ($porRonda['final'] as $p) { $renderMatch($p); } ?>
                    <div class="bracket-final-label">
                        <div>Ganador</div>
                        <div class="text-muted">Subcampeón</div>
                    </div>
                </div>

                <div class="bracket-wrap bracket-wrap-reverse">
                    <?php foreach (array_reverse($rondas) as $ronda): ?>
                        <?php if ($ronda === 'final') { continue; } ?>
                        <div class="bracket-round">
                            <?php foreach ($mitad($porRonda[$ronda], true) as $p) { $renderMatch($p); } ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- MODALES: CAPTURA DE CADA PARTIDO -->
<?php foreach ($rondas as $ronda): ?>
    <?php foreach ($porRonda[$ronda] as $p): ?>
        <?php if (!$p['equipo_id'] || empty($p['rival'])) { continue; } ?>
        <div class="modal fade" id="partidoModal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="post" action="<?= BASE_URL ?>/torneos/guardarPartido/<?= $p['id'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title mb-0"><?= htmlspecialchars($p['equipo_nombre'] . ' vs. ' . $p['rival']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label">Fecha</label>
                                    <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($p['fecha']) ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Hora</label>
                                    <input type="time" name="hora" class="form-control" value="<?= htmlspecialchars(substr($p['hora'], 0, 5)) ?>">
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Sede</label>
                                    <select name="sede_id" class="form-select">
                                        <option value="">Sin definir</option>
                                        <?php foreach ($sedes as $s): ?>
                                            <option value="<?= $s['id'] ?>" <?= (string) $p['sede_id'] === (string) $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-link btn-sm p-0 mt-1" data-bs-toggle="modal" data-bs-target="#modalNuevaSede">+ Agregar sede</button>
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Cancha</label>
                                    <select name="cancha_id" class="form-select">
                                        <option value="">Sin definir</option>
                                        <?php foreach ($canchas as $c): ?>
                                            <option value="<?= $c['id'] ?>" <?= (string) $p['cancha_id'] === (string) $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="btn btn-link btn-sm p-0 mt-1" data-bs-toggle="modal" data-bs-target="#modalNuevaCancha">+ Agregar cancha</button>
                                </div>

                                <div class="col-6">
                                    <label class="form-label"><?= htmlspecialchars($p['equipo_nombre']) ?></label>
                                    <input type="number" min="0" name="goles_favor" class="form-control" value="<?= $p['goles_favor'] ?? '' ?>">
                                </div>
                                <div class="col-6">
                                    <label class="form-label"><?= htmlspecialchars($p['rival']) ?></label>
                                    <input type="number" min="0" name="goles_contra" class="form-control" value="<?= $p['goles_contra'] ?? '' ?>">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-between">
                            <a href="<?= BASE_URL ?>/partidos/eventos/<?= $p['id'] ?>" class="btn btn-outline-secondary btn-sm">Capturar estadísticas</a>
                            <div>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>

<!-- MODALES: ALTA RÁPIDA DE SEDE / CANCHA -->
<div class="modal fade" id="modalNuevaSede" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="post" action="<?= BASE_URL ?>/torneos/agregarSede">
                <input type="hidden" name="torneo_id" value="<?= $torneo['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">Nueva sede</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la sede" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaCancha" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="post" action="<?= BASE_URL ?>/torneos/agregarCancha">
                <input type="hidden" name="torneo_id" value="<?= $torneo['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">Nueva cancha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la cancha" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
