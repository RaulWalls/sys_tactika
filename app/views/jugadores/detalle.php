<div class="welcome d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
        <div class="team-logo detalle-logo">
            <?php if (!empty($jugador['foto'])): ?>
                <img src="<?= BASE_URL . '/' . htmlspecialchars($jugador['foto']) ?>" alt="Foto">
            <?php else: ?>
                👕
            <?php endif; ?>
        </div>

        <div>
            <h1>
                <?= htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido']) ?>
                <?php if (!empty($jugador['capitan'])): ?>
                    <span class="badge-estado badge-capitan">Capitán</span>
                <?php endif; ?>
            </h1>
            <p>
                <?= htmlspecialchars($jugador['equipo_nombre'] ?? 'Sin equipo') ?>
                <?php if (!empty($jugador['posicion_nombre'])): ?>
                    ·
                    <span class="badge-estado" style="background: <?= htmlspecialchars($jugador['posicion_tag']) ?>1a; color: <?= htmlspecialchars($jugador['posicion_tag']) ?>;">
                        <?= htmlspecialchars($jugador['posicion_nombre']) ?>
                    </span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/jugadores/editar/<?= $jugador['id'] ?>" class="btn btn-outline-secondary">Editar</a>
        <a href="<?= BASE_URL ?>/jugadores" class="btn btn-outline-secondary">← Volver</a>
    </div>
</div>

<!-- DATOS GENERALES -->
<div class="card" style="margin-top:20px;">
    <div class="section-header">
        <h2>Datos generales</h2>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <span class="detalle-label">Número</span>
            <div class="detalle-valor"><?= $jugador['numero'] ?? '-' ?></div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Estado</span>
            <div class="detalle-valor">
                <span class="badge-estado badge-<?= $jugador['estado'] ?>"><?= ucfirst($jugador['estado']) ?></span>
            </div>
        </div>

        <div class="col-md-4">
            <span class="detalle-label">Fecha de nacimiento</span>
            <div class="detalle-valor">
                <?= !empty($jugador['fecha_nacimiento']) ? date('d M Y', strtotime($jugador['fecha_nacimiento'])) : '-' ?>
            </div>
        </div>

        <div class="col-md-6">
            <span class="detalle-label">Teléfono</span>
            <div class="detalle-valor"><?= htmlspecialchars($jugador['telefono'] ?: '-') ?></div>
        </div>

        <div class="col-md-6">
            <span class="detalle-label">Email</span>
            <div class="detalle-valor"><?= htmlspecialchars($jugador['email'] ?: '-') ?></div>
        </div>
    </div>
</div>

<!-- ATRIBUTOS + RENDIMIENTO -->
<div class="grid bottom-grid" style="margin-top:20px;">


    <!-- RENDIMIENTO  -->
    <div class="card">
        <div class="section-header">
            <h2>Estadísticas de rendimiento</h2>
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalRendimiento">Editar →</a>
        </div>

        <div class="row g-3">
            <?php $esPortero = ($jugador['posicion_nombre'] ?? '') === 'Portero'; ?>
            <!-- "Atajadas" solo aplica al portero; el resto de posiciones se la salta -->
            <?php foreach ($rendimiento as $r): ?>
                <?php if ($r['nombre'] !== 'Atajadas' || $esPortero): ?>
                    <div class="col-md-6">
                        <span class="detalle-label"><?= htmlspecialchars($r['nombre']) ?></span>
                        <div class="detalle-valor"><?= (int) $r['valor'] ?></div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

            <div class="col-md-6">
                <span class="detalle-label">Amonestaciones</span>
                <div class="detalle-valor">
                    T. A.:
                    <span class="badge-estado badge-tarjeta-amarilla"><?= $jugador_amonestado['tarjeta_amarilla'] ?? 0 ?></span><br>
                    T. R.:
                    <span class="badge-estado badge-tarjeta-roja"><?= $jugador_amonestado['tarjeta_roja'] ?? 0 ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ATRIBUTOS -->
    <div class="card">
        <div class="section-header">
            <h2>Atributos</h2>
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalAtributos">Editar →</a>
        </div>

        <canvas id="statsRadar" height="220"></canvas>

        <div class="stats-legend">
            <?php foreach ($atributos as $e): ?>
                <?php
                    $valor = (int) $e['valor'];
                    $clase = $valor > 0 ? 'badge-positivo' : ($valor < 0 ? 'badge-negativo' : 'badge-neutro');
                ?>
                <div class="stats-legend-item">
                    <span class="badge-estado <?= $clase ?>"><?= $valor > 0 ? '+' . $valor : $valor ?></span>
                    <?= htmlspecialchars($e['abreviatura']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<!-- MODAL: EDITAR ATRIBUTOS -->
<div class="modal fade" id="modalAtributos" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="post" action="<?= BASE_URL ?>/jugadores/atributos/<?= $jugador['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">Editar atributos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <?php foreach ($atributos as $a): ?>

                            <div class="col-6">
                                <label class="form-label"><?= htmlspecialchars($a['nombre']) ?> (<?= htmlspecialchars($a['abreviatura']) ?>)</label>
                                <input type="number" name="valor[<?= $a['atributo_id'] ?>]" class="form-control" min="-20" max="20" value="<?= (int) $a['valor'] ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-text mt-2">Valores de -20 a 20.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: EDITAR RENDIMIENTO -->
<div class="modal fade" id="modalRendimiento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="post" action="<?= BASE_URL ?>/jugadores/rendimiento/<?= $jugador['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title mb-0">Editar estadísticas de rendimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <?php foreach ($rendimiento as $r): ?>
                            <?php if ($r['nombre'] !== 'Atajadas' || $esPortero): ?>
                                <div class="col-6">
                                    <label class="form-label"><?= htmlspecialchars($r['nombre']) ?> (<?= htmlspecialchars($r['abreviatura']) ?>)</label>
                                    <input type="number" name="valor[<?= $r['estadistica_id'] ?>]" class="form-control" min="0" value="<?= (int) $r['valor'] ?>">
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('statsRadar'), {
    type: 'radar',
    data: {
        labels: <?= json_encode(array_column($atributos, 'abreviatura')) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn ($e) => (int) $e['valor'], $atributos)) ?>,
            backgroundColor: 'rgba(34, 160, 107, 0.25)',
            borderColor: '#22a06b',
            pointBackgroundColor: '#22a06b',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            r: {
                min: -20,
                max: 20,
                ticks: { display: false },
                pointLabels: { font: { size: 12, weight: '600' } },
                grid: { color: '#e8e9ef' },
                angleLines: { color: '#e8e9ef' }
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
});
</script>
