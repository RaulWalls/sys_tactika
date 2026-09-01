    <!-- =========================
         SIDEBAR
    ========================== -->
    <aside class="sidebar">

        <div class="logo">
            <div class="logo-icon">⚽</div>
            <div class="logo-text">Tácti<span>ka</span></div>
        </div>

        <div class="menu-title">Principal</div>

        <nav class="menu">
            <a href="<?= BASE_URL ?>/dashboard" class="menu-item <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                <span class="menu-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <a href="<?= BASE_URL ?>/ligas" class="menu-item <?= ($activeMenu ?? '') === 'ligas' ? 'active' : '' ?>">
                <span class="menu-icon">🏆</span>
                <span>Ligas</span>
            </a>

            <a href="<?= BASE_URL ?>/equipos" class="menu-item <?= ($activeMenu ?? '') === 'equipos' ? 'active' : '' ?>">
                <span class="menu-icon">⚽</span>
                <span>Equipos</span>
            </a>

            <a href="<?= BASE_URL ?>/jugadores" class="menu-item <?= ($activeMenu ?? '') === 'jugadores' ? 'active' : '' ?>">
                <span class="menu-icon">👥</span>
                <span>Jugadores</span>
            </a>

            <a href="<?= BASE_URL ?>/partidos" class="menu-item <?= ($activeMenu ?? '') === 'partidos' ? 'active' : '' ?>">
                <span class="menu-icon">📅</span>
                <span>Calendario</span>
            </a>

            <a href="<?= BASE_URL ?>/pagos" class="menu-item <?= ($activeMenu ?? '') === 'pagos' ? 'active' : '' ?>">
                <span class="menu-icon">💰</span>
                <span>Pagos</span>
            </a>

            <a href="<?= BASE_URL ?>/reportes" class="menu-item <?= ($activeMenu ?? '') === 'reportes' ? 'active' : '' ?>">
                <span class="menu-icon">📊</span>
                <span>Reportes</span>
            </a>
        </nav>

        <div class="menu-title">Administración</div>

        <nav class="menu">
            <a href="<?= BASE_URL ?>/usuarios" class="menu-item <?= ($activeMenu ?? '') === 'usuarios' ? 'active' : '' ?>">
                <span class="menu-icon">🧑‍💼</span>
                <span>Usuarios</span>
            </a>

            <?php /*
            <a href="<?= BASE_URL ?>/staff" class="menu-item <?= ($activeMenu ?? '') === 'staff' ? 'active' : '' ?>">
                <span class="menu-icon">🎽</span>
                <span>Representantes y DT</span>
            </a>
            */ ?>
        </nav>

        <div class="sidebar-bottom">
            <a href="#" class="menu-item">
                <span class="menu-icon">⚙</span>
                <span>Configuración</span>
            </a>

            <a href="<?= BASE_URL ?>/auth/logout" class="menu-item">
                <span class="menu-icon">↪</span>
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
                <div class="notification">🔔</div>

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
