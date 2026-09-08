<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Estadísticas del partido</h1>
        <p>
            <?= htmlspecialchars($equipoNombre) ?> vs. <?= htmlspecialchars($partido['rival']) ?>
            · <?= date('d M Y', strtotime($partido['fecha'])) ?>
        </p>
    </div>
    <a href="<?= BASE_URL ?>/partidos" class="btn btn-outline-secondary">← Volver</a>
</div>

<form method="post" action="<?= BASE_URL ?>/partidos/eventos/<?= $partido['id'] ?>">
    <?php $hayJugadores = false; ?>
    <?php foreach ($grupos as $grupo): ?>
        <?php if (!empty($grupo['jugadores'])) { $hayJugadores = true; } ?>

        <h6 class="detalle-sub mt-3 mb-2"><?= htmlspecialchars($grupo['equipo_nombre']) ?></h6>
        <div class="card mb-3">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Jugador</th>
                            <th style="width: 90px;">Goles</th>
                            <th style="width: 110px;">Asistencias</th>
                            <th style="width: 100px;">Minutos</th>
                            <th style="width: 80px;">Titular</th>
                            <th style="width: 100px;">Rating</th>
                            <th style="width: 70px;">MVP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($grupo['jugadores'])): ?>
                            <?php foreach ($grupo['jugadores'] as $ev): ?>
                                <tr>
                                    <td><?= $ev['numero'] ?? '-' ?></td>
                                    <td><?= htmlspecialchars($ev['nombre'] . ' ' . $ev['apellido']) ?></td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm" name="evento[<?= $ev['jugador_id'] ?>][goles]" value="<?= (int) $ev['goles'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control form-control-sm" name="evento[<?= $ev['jugador_id'] ?>][asistencias]" value="<?= (int) $ev['asistencias'] ?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="120" class="form-control form-control-sm" name="evento[<?= $ev['jugador_id'] ?>][minutos]" value="<?= (int) $ev['minutos'] ?>">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input" name="evento[<?= $ev['jugador_id'] ?>][titular]" value="1" <?= $ev['titular'] ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="10" step="0.1" class="form-control form-control-sm" name="evento[<?= $ev['jugador_id'] ?>][rating]" value="<?= $ev['rating'] ?? '' ?>">
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input" name="evento[<?= $ev['jugador_id'] ?>][mvp]" value="1" <?= $ev['mvp'] ? 'checked' : '' ?>>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Este equipo no tiene jugadores registrados.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if ($hayJugadores): ?>
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Guardar estadísticas</button>
        </div>
    <?php endif; ?>
</form>
