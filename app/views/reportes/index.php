<div class="welcome">
    <h1>Reportes</h1>
    <p>Resumen general del equipo.</p>
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

<div class="grid bottom-grid" style="margin-top:20px;">

    <div class="card">
        <div class="section-header">
            <h2>Rendimiento deportivo</h2>
        </div>

        <div class="finance">
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
    </div>

    <div class="card">
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

</div>
