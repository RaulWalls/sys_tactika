<div class="welcome">
    <h1>Estadísticas</h1>
    <p><?= htmlspecialchars($jugador['nombre'] . ' ' . $jugador['apellido']) ?></p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/jugadores/estadisticas/<?= $jugador['id'] ?>">

        <div class="row g-3">
            <?php foreach ($estadisticas as $e): ?>
                <div class="col-md-6">
                    <label class="form-label"><?= htmlspecialchars($e['nombre']) ?> (<?= htmlspecialchars($e['abreviatura']) ?>)</label>
                    <input type="number" name="valor[<?= $e['estadistica_id'] ?>]" class="form-control" min="-20" max="20" value="<?= (int) $e['valor'] ?>">
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-text mt-2">Valores de -20 a 20. Se usan para la gráfica de estadísticas del perfil del jugador.</div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/jugadores/detalle/<?= $jugador['id'] ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
