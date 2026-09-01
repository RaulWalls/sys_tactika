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

<div class="grid main-grid" style="margin-top:20px;">

    <!-- DATOS GENERALES -->
    <div class="card">
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

    <!-- ESTADÍSTICAS -->
    <div class="card">
        <div class="section-header">
            <h2>Estadísticas</h2>
            <a href="<?= BASE_URL ?>/jugadores/estadisticas/<?= $jugador['id'] ?>">Editar →</a>
        </div>

        <canvas id="statsRadar" height="220"></canvas>

        <div class="stats-legend">
            <?php foreach ($estadisticas as $e): ?>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('statsRadar'), {
    type: 'radar',
    data: {
        labels: <?= json_encode(array_column($estadisticas, 'abreviatura')) ?>,
        datasets: [{
            data: <?= json_encode(array_map(fn ($e) => (int) $e['valor'], $estadisticas)) ?>,
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
