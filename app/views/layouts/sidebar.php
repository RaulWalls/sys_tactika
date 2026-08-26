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

            <a href="<?= BASE_URL ?>/equipos" class="menu-item <?= ($activeMenu ?? '') === 'equipos' ? 'active' : '' ?>">
                <span class="menu-icon">⚽</span>
                <span>Mi equipo</span>
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
        </nav>

        <div class="sidebar-bottom">
            <a href="#" class="menu-item">
                <span class="menu-icon">⚙</span>
                <span>Configuración</span>
            </a>

            <a href="#" class="menu-item">
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
                    <div class="avatar">JO</div>
                    <div class="profile-info">
                        <strong>Jorge Oliver</strong>
                        <span>Administrador</span>
                    </div>
                </div>
            </div>
        </header>

        <section class="content">
