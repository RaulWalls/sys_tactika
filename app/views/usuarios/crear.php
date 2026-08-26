<div class="welcome">
    <h1>Nuevo usuario</h1>
    <p>Crea un acceso al sistema.</p>
</div>

<div class="card form-card">
    <form method="post" action="<?= BASE_URL ?>/usuarios/crear">

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="col-md-3">
                <label class="form-label">Rol</label>
                <select name="rol" class="form-select">
                    <option value="administrador">Administrador</option>
                    <option value="entrenador">Entrenador</option>
                    <option value="jugador">Jugador</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Estado</label>
                <select name="estado" class="form-select">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <a href="<?= BASE_URL ?>/usuarios" class="btn btn-outline-secondary">Cancelar</a>
        </div>

    </form>
</div>
