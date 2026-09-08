<div class="welcome">
    <h1>Historial</h1>
    <p>Temporadas (ligas) finalizadas y el nivel de badge que cada jugador alcanzó durante cada una.</p>
</div>

<div class="card">
    <?php if (empty($ligas)): ?>
        <p class="text-muted mb-0">Aún no hay temporadas finalizadas.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Temporada</th>
                        <th>Periodo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ligas as $liga): ?>
                        <tr>
                            <td><?= htmlspecialchars($liga['nombre']) ?> <?= $liga['temporada'] ? '(' . htmlspecialchars($liga['temporada']) . ')' : '' ?></td>
                            <td><?= date('d M Y', strtotime($liga['fecha_inicio'])) ?> — <?= date('d M Y', strtotime($liga['fecha_fin'])) ?></td>
                            <td class="text-end">
                                <a href="#" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#historialLiga<?= $liga['id'] ?>">Ver historial</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php foreach ($ligas as $liga): ?>
    <div class="modal fade" id="historialLiga<?= $liga['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mb-0"><?= htmlspecialchars($liga['nombre']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <h6 class="detalle-sub mb-2">Jugadores</h6>
                    <?php if (empty($liga['registrosJugador'])): ?>
                        <p class="text-muted">Ningún jugador alcanzó un nivel de badge registrado en esta temporada.</p>
                    <?php else: ?>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Jugador</th>
                                        <th>Badge</th>
                                        <th>Nivel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($liga['registrosJugador'] as $r): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($r['jugador_nombre'] . ' ' . $r['jugador_apellido']) ?></td>
                                            <td><?= htmlspecialchars($r['badge_nombre']) ?></td>
                                            <td><?= (int) $r['nivel'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <h6 class="detalle-sub mb-2">Equipos</h6>
                    <?php if (empty($liga['registrosEquipo'])): ?>
                        <p class="text-muted mb-0">Ningún equipo alcanzó un nivel de badge registrado en esta temporada.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Equipo</th>
                                        <th>Badge</th>
                                        <th>Nivel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($liga['registrosEquipo'] as $r): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($r['equipo_nombre']) ?></td>
                                            <td><?= htmlspecialchars($r['badge_nombre']) ?></td>
                                            <td><?= (int) $r['nivel'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
