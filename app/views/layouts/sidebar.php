    <!-- =========================
         SIDEBAR
    ========================== -->
    <aside class="sidebar">

        <div class="logo">
            <div class="logo-icon"><i class="fa-solid fa-futbol"></i></div>
            <div class="logo-text">Tácti<span class="logo-text-highlight">ka</span></div>
        </div>

        <div class="menu-title">Principal</div>

        <nav class="menu">
            <a href="<?= BASE_URL ?>/dashboard" class="menu-item <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fa-solid fa-gauge"></i></span>
                <span>Dashboard</span>
            </a>

            <?php if ($this->tienePermiso('ver', 'LigasController')): ?>
                <a href="<?= BASE_URL ?>/ligas" class="menu-item <?= ($activeMenu ?? '') === 'ligas' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-trophy"></i></span>
                    <span>Ligas</span>
                </a>
            <?php endif; ?>

            <?php if ($this->tienePermiso('ver', 'EquiposController')): ?>
                <a href="<?= BASE_URL ?>/equipos" class="menu-item <?= ($activeMenu ?? '') === 'equipos' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-futbol"></i></span>
                    <span>Equipos</span>
                </a>
            <?php endif; ?>

            <?php if ($this->tienePermiso('ver', 'JugadoresController')): ?>
                <a href="<?= BASE_URL ?>/jugadores" class="menu-item <?= ($activeMenu ?? '') === 'jugadores' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-users"></i></span>
                    <span>Jugadores</span>
                </a>
            <?php endif; ?>

            <?php if ($this->tienePermiso('ver', 'PartidosController')): ?>
                <a href="<?= BASE_URL ?>/partidos" class="menu-item <?= ($activeMenu ?? '') === 'partidos' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-calendar-days"></i></span>
                    <span>Calendario</span>
                </a>
            <?php endif; ?>

            <?php if ($this->tienePermiso('ver', 'PagosController')): ?>
                <a href="<?= BASE_URL ?>/pagos" class="menu-item <?= ($activeMenu ?? '') === 'pagos' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-sack-dollar"></i></span>
                    <span>Pagos</span>
                </a>
            <?php endif; ?>

            <?php if ($this->tienePermiso('ver', 'ReportesController')): ?>
                <a href="<?= BASE_URL ?>/reportes" class="menu-item <?= ($activeMenu ?? '') === 'reportes' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-chart-column"></i></span>
                    <span>Reportes</span>
                </a>
            <?php endif; ?>
        </nav>

        <?php if ($this->esAdmin()): ?>
            <div class="menu-title">Administración</div>

            <nav class="menu">
                <a href="<?= BASE_URL ?>/usuarios" class="menu-item <?= ($activeMenu ?? '') === 'usuarios' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-users"></i></i></span>
                    <span>Usuarios</span>
                </a>

                <?php /*
                <a href="<?= BASE_URL ?>/staff" class="menu-item <?= ($activeMenu ?? '') === 'staff' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fa-solid fa-shirt"></i></span>
                    <span>Representantes y DT</span>
                </a>
                */ ?>
            </nav>
        <?php endif; ?>

        <div class="sidebar-bottom">
            <a href="<?= BASE_URL ?>/configuracion" class="menu-item <?= ($activeMenu ?? '') === 'configuracion' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fa-solid fa-gear"></i></span>
                <span>Configuración</span>
            </a>

            <a href="<?= BASE_URL ?>/auth/logout" class="menu-item">
                <span class="menu-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                <span>Cerrar sesión</span>
            </a>
        </div>

    </aside>


    <!-- =========================
         MAIN
    ========================== -->
    <main class="main">

        <header class="topbar">
            <div class="breadcrumb">
                <?= APP_NAME ?> / <strong><?= htmlspecialchars($titulo ?? '') ?></strong>
            </div>

            <div class="top-actions">
                <div class="theme-switcher">
                    <button type="button" class="notification theme-toggle-btn" id="themeToggle" aria-label="Cambiar tema"><i class="fa-solid fa-palette"></i></button>

                    <div class="theme-menu" id="themeMenu">
                        <div class="theme-menu-title">Elegir tema</div>

                        <form method="post" action="<?= BASE_URL ?>/usuarios/tema">
                            <?php
                                $temas = [
                                    'default' => ['nombre' => 'Táctika (predeterminado)', 'colores' => ['#5b3cc4', '#452c9c']],
                                    'barcelona' => ['nombre' => 'Barcelona', 'colores' => ['#8e0044', '#294c97', '#bd002c', '#e1bb00']],
                                    'real_madrid' => ['nombre' => 'Real Madrid', 'colores' => ['#2c519e',"#ffffff", '#d0324b', '#efbe0b']],
                                    'manchester_city' => ['nombre' => 'Manchester City', 'colores' => ['#a2bfe7', '#f2c757', '#172b5f']],
                                    'psg' => ['nombre' => 'PSG', 'colores' => ['#2c5195', '#ac9755', '#d02f36']],
                                    'juventus' => ['nombre' => 'Juventus', 'colores' => ['#000000', '#1c1c1c', '#f0f0f0']],
                                    'bayern_munich' => ['nombre' => 'Bayern Múnich', 'colores' => ['#d32f2f', '#1c1c1c', '#fbe5e5']],
                                    'liverpool' => ['nombre' => 'Liverpool', 'colores' => ['#c8102e', '#1c1c1c', '#fbe5e5']],
                                    'chelsea' => ['nombre' => 'Chelsea', 'colores' => ['#034694', '#1c1c1c', '#e5f0fb']],
                                    'america' => ['nombre' => 'Club América', 'colores' => ['#f6303e', '#0f1d41', '#f0eb9a']],
                                ];
                                $temaActual = $_SESSION['usuario_tema'] ?? 'default';
                            ?>
                            <?php foreach ($temas as $valor => $tema): ?>
                                <button type="submit" name="tema" value="<?= $valor ?>" class="theme-option <?= $temaActual === $valor ? 'active' : '' ?>">
                                    <span class="theme-swatch">
                                        <?php foreach ($tema['colores'] as $color): ?>
                                            <span style="background: <?= $color ?>;"></span>
                                        <?php endforeach; ?>
                                    </span>
                                    <?= htmlspecialchars($tema['nombre']) ?>
                                </button>
                            <?php endforeach; ?>
                        </form>
                    </div>
                </div>

                <div class="profile">
                    <div class="avatar"><?= htmlspecialchars(strtoupper(substr($_SESSION['usuario_nombre'] ?? '', 0, 2))) ?></div>
                    <div class="profile-info">
                        <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></strong>
                        <span><?= htmlspecialchars(ucfirst($_SESSION['usuario_rol'] ?? '')) ?></span>
                    </div>
                </div>
            </div>
        </header>

        <section class="content">
