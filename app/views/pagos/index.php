<?php $esAdmin = ($_SESSION['usuario_rol'] ?? '') === 'administrador'; ?>
<?php $esEntrenador = ($_SESSION['usuario_rol'] ?? '') === 'entrenador'; ?>
<div class="welcome d-flex align-items-center justify-content-between">
    <div>
        <h1>Pagos</h1>
        <?php if ($esEntrenador): ?>
            <p>Ingresos y gastos del equipo.</p>
        <?php else: ?>
            <p>Resumen financiero.</p>
        <?php endif; ?>
    </div>
    <?php if ($this->tienePermiso('crear')): ?>
        <a href="<?= BASE_URL ?>/pagos/crear" class="btn btn-primary">+ Nuevo pago</a>
    <?php endif; ?>
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
                            <td>
                                <a href="#" class="link-equipo" data-bs-toggle="modal" data-bs-target="#pagoModal<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['concepto_nombre'] ?? '-') ?>
                                </a>
                            </td>
                            <td><?= $p['jugador_nombre'] ? htmlspecialchars($p['jugador_nombre'] . ' ' . $p['jugador_apellido']) : '-' ?></td>
                            <td><?= ucfirst($p['tipo']) ?></td>
                            <td class="<?= $p['tipo'] === 'ingreso' ? 'positive' : 'negative' ?>">
                                $<?= number_format($p['monto'], 2) ?>
                            </td>
                            <td><span class="badge-estado badge-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span></td>
                            <td class="text-end">
                                <?php if ($this->tienePermiso('editar')): ?>
                                    <a href="<?= BASE_URL ?>/pagos/editar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php endif; ?>
                                <?php if ($this->tienePermiso('eliminar')): ?>
                                    <a href="<?= BASE_URL ?>/pagos/eliminar/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="¿Eliminar este pago?">Eliminar</a>
                                <?php endif; ?>
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

<?php if (!empty($pagos)): ?>
    <?php foreach ($pagos as $p): ?>
        <div class="modal fade" id="pagoModal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title mb-0"><?= htmlspecialchars($p['concepto_nombre'] ?? 'Pago') ?></h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body pago-modal-body">
                        <div class="pago-modal-row">
                            <span class="detalle-label">Monto</span>
                            <div class="detalle-valor <?= $p['tipo'] === 'ingreso' ? 'positive' : 'negative' ?>">
                                $<?= number_format($p['monto'], 2) ?> (<?= ucfirst($p['tipo']) ?>)
                            </div>
                        </div>

                        <div class="pago-modal-row">
                            <span class="detalle-label">Fecha</span>
                            <div class="detalle-valor"><?= date('d M Y', strtotime($p['fecha'])) ?></div>
                        </div>

                        <div class="pago-modal-row">
                            <span class="detalle-label">Estado</span>
                            <div class="detalle-valor">
                                <span class="badge-estado badge-<?= $p['estado'] ?>"><?= ucfirst($p['estado']) ?></span>
                            </div>
                        </div>

                        <div class="pago-modal-row">
                            <span class="detalle-label">Jugador</span>
                            <div class="detalle-valor">
                                <?= $p['jugador_nombre'] ? htmlspecialchars($p['jugador_nombre'] . ' ' . $p['jugador_apellido']) : '-' ?>
                            </div>
                        </div>

                        <div class="pago-modal-row">
                            <span class="detalle-label">Equipo</span>
                            <div class="detalle-valor"><?= htmlspecialchars($p['equipo_nombre'] ?? '-') ?></div>
                        </div>

                        <div class="pago-modal-row">
                            <span class="detalle-label">Método de pago</span>
                            <div class="detalle-valor"><?= htmlspecialchars($p['metodo_nombre'] ?? '-') ?></div>
                        </div>

                        <div class="pago-modal-row">
                            <span class="detalle-label">Observaciones</span>
                            <div class="detalle-valor detalle-sub"><?= nl2br(htmlspecialchars($p['observaciones'] ?: 'Sin observaciones')) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
