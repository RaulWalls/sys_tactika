<div class="welcome">
    <h1>Nuevo jugador</h1>
    <p>Registra un jugador en la plantilla.</p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/jugadores/crear">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Apellido</label>
                <input type="text" name="apellido" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Equipo</label>
                <select name="equipo_id" class="form-select">
                    <option value="">Sin equipo</option>
                    <?php foreach ($equipos as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Posición</label>
                <input type="text" name="posicion" class="form-control" placeholder="Delantero, Portero...">
            </div>

            <div class="col-md-3">
                <label class="form-label">Número</label>
                <input type="number" name="numero" class="form-control" min="0">
            </div>

            <div class="col-md-4">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="col-md-4">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="activo">Activo</option>
                    <option value="lesionado">Lesionado</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/jugadores" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
