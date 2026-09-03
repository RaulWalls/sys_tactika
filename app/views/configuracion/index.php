<div class="welcome">
    <h1>Configuración</h1>
    <p>Preferencias de apariencia y permisos del sistema.</p>
</div>

<!-- APARIENCIA -->
<div class="card mb-3">
    <div class="section-header">
        <h2>Apariencia</h2>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Tema del sistema</label>
            <form method="post" action="<?= BASE_URL ?>/usuarios/tema">
                <select name="tema" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($temasDisponibles as $valor => $nombre): ?>
                        <option value="<?= $valor ?>" <?= ($_SESSION['usuario_tema'] ?? 'default') === $valor ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nombre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
            <div class="form-text">Cambia los colores de marca del sistema. Se guarda por usuario.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Modo oscuro</label>
            <form method="post" action="<?= BASE_URL ?>/usuarios/modoOscuro">
                <input type="hidden" name="modo_oscuro" value="0">
                <div class="form-check form-switch mt-2">
                    <input type="checkbox" class="form-check-input" role="switch" id="modoOscuro" name="modo_oscuro" value="1" <?= !empty($_SESSION['usuario_modo_oscuro']) ? 'checked' : '' ?> onchange="this.form.submit()">
                    <label class="form-check-label" for="modoOscuro">Activar modo oscuro</label>
                </div>
            </form>
            <div class="form-text">Cambia el tono de fondo y texto del sistema. Se guarda por usuario.</div>
        </div>
    </div>
</div>

<!-- PERMISOS POR ROL (solo administrador) -->
<?php if ($this->esAdmin()): ?>
    <div class="card">
        <div class="section-header">
            <h2>Permisos por rol</h2>
        </div>

        <p class="text-muted" style="font-size:13px;">
            El rol Administrador siempre tiene acceso completo a todo el sistema, incluida esta pantalla.
            Aquí defines lo que pueden Ver, Crear, Editar y Eliminar los roles Entrenador y Jugador en cada módulo.
        </p>

        <form method="post" action="<?= BASE_URL ?>/configuracion/permisos">
            <?php
                $porRol = [];
                foreach ($matriz as $fila) {
                    $porRol[$fila['rol_id']]['nombre'] = $fila['rol_nombre'];
                    $porRol[$fila['rol_id']]['modulos'][] = $fila;
                }
            ?>

            <?php foreach ($porRol as $rolId => $rol): ?>
                <?php if ($rol['nombre'] === 'administrador'): ?>
                    <?php continue; ?>
                <?php endif; ?>

                <h3 style="font-size:14px; font-weight:700; margin: 20px 0 10px;"><?= ucfirst($rol['nombre']) ?></h3>

                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Módulo</th>
                                <th class="text-center">Ver</th>
                                <th class="text-center">Crear</th>
                                <th class="text-center">Editar</th>
                                <th class="text-center">Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rol['modulos'] as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars($m['modulo_nombre']) ?></td>
                                    <?php foreach (['ver', 'crear', 'editar', 'eliminar'] as $accion): ?>
                                        <td class="text-center">
                                            <input type="hidden" name="permiso[<?= $rolId ?>][<?= $m['modulo_id'] ?>][<?= $accion ?>]" value="0">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                name="permiso[<?= $rolId ?>][<?= $m['modulo_id'] ?>][<?= $accion ?>]"
                                                value="1"
                                                <?= $m[$accion] ? 'checked' : '' ?>
                                            >
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Guardar permisos</button>
            </div>
        </form>
    </div>
<?php endif; ?>
