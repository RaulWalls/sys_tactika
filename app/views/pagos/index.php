<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Pagos</h1>
        <p>Ingresos y gastos del equipo.</p>
    </div>
    <a href="<?= BASE_URL ?>/pagos/crear" class="btn btn-primary">+ Nuevo pago</a>
</div>

<div class="finance mb-4">
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

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Concepto</th>
                    <th>Jugador</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($pagos)): ?>
                    <?php foreach ($pagos as $p): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                            <td><?= htmlspecialchars($p['concepto']) ?></td>
                            <td><?= $p['jugador_nombre'] ? htmlspecialchars($p['jugador_nombre'] . ' ' . $p['jugador_apellido']) : '-' ?></td>
                            <td><?= ucfirst($p['tipo']) ?></td>
                            <td class="<?= $p['tipo'] === 'ingreso' ? 'positive' : 'negative' ?>">
                                $<?= number_format($p['monto'], 2) ?>
                            </td>
                            <td><span class="badge-estado badge-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                            <td class="text-end">
                                <a href="<?= BASE_URL ?>/pagos/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <a href="<?= BASE_URL ?>/pagos/eliminar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar este pago?">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay pagos registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
