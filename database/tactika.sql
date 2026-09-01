-- =========================================================
-- Táctika - Esquema inicial de base de datos (MySQL/MariaDB)
-- =========================================================

CREATE DATABASE IF NOT EXISTS tactika CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tactika;

-- ---------------------------------------------------------
-- Catálogos
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS cat_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cat_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    monto DECIMAL(10, 2) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cat_metodo_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cat_posiciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    tag VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Ligas / Competencias
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS ligas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    temporada VARCHAR(20),
    dias_partido_semana TINYINT NOT NULL DEFAULT 1 CHECK (dias_partido_semana BETWEEN 1 AND 7),
    total_equipos INT NULL CHECK (total_equipos IS NULL OR total_equipos % 2 = 0),
    permite_calendario_incompleto TINYINT(1) NOT NULL DEFAULT 0,
    estado ENUM('activa', 'inactiva') NOT NULL DEFAULT 'activa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Usuarios (administración de accesos al sistema)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    liga_id INT NULL,
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES cat_roles(id),
    FOREIGN KEY (liga_id) REFERENCES ligas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Staff (representantes y directores técnicos)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Equipos
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    liga_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50),
    temporada VARCHAR(20),
    escudo VARCHAR(255),
    fecha_ingreso DATE NULL,
    sede VARCHAR(150),
    representante_id INT NULL,
    dt_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (liga_id) REFERENCES ligas(id) ON DELETE SET NULL,
    FOREIGN KEY (representante_id) REFERENCES staff(id) ON DELETE SET NULL,
    FOREIGN KEY (dt_id) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Jugadores
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS jugadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    posicion_id INT NULL,
    numero INT,
    fecha_nacimiento DATE NULL,
    telefono VARCHAR(20),
    email VARCHAR(150),
    foto VARCHAR(255),
    estado ENUM('activo', 'lesionado', 'inactivo') NOT NULL DEFAULT 'activo',
    capitan TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE SET NULL,
    FOREIGN KEY (posicion_id) REFERENCES cat_posiciones(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Estadísticas de jugadores (gráfica de radar en el perfil)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS cat_estadisticas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    abreviatura VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS jugador_estadisticas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jugador_id INT NOT NULL,
    estadistica_id INT NOT NULL,
    valor INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_jugador_estadistica (jugador_id, estadistica_id),
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id) ON DELETE CASCADE,
    FOREIGN KEY (estadistica_id) REFERENCES cat_estadisticas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Partidos (calendario)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    liga_id INT NULL,
    equipo_id INT NULL,
    rival VARCHAR(100) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    sede VARCHAR(150),
    tipo ENUM('local', 'visitante') NOT NULL DEFAULT 'local',
    competencia VARCHAR(100),
    jornada VARCHAR(50),
    goles_favor INT NULL,
    goles_contra INT NULL,
    estado ENUM('programado', 'jugado', 'cancelado') NOT NULL DEFAULT 'programado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (liga_id) REFERENCES ligas(id) ON DELETE SET NULL,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Pagos (finanzas: ingresos y gastos)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jugador_id INT NULL,
    equipo_id INT NULL,
    concepto_id INT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    tipo ENUM('ingreso', 'gasto') NOT NULL DEFAULT 'ingreso',
    metodo_pago_id INT NULL,
    fecha DATE NOT NULL,
    estado ENUM('pagado', 'pendiente') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id) ON DELETE SET NULL,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE SET NULL,
    FOREIGN KEY (concepto_id) REFERENCES cat_pagos(id) ON DELETE SET NULL,
    FOREIGN KEY (metodo_pago_id) REFERENCES cat_metodo_pagos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Datos de ejemplo (opcional, para probar el dashboard)
-- ---------------------------------------------------------
INSERT INTO cat_roles (nombre) VALUES
    ('administrador'), ('entrenador'), ('jugador');

INSERT INTO cat_metodo_pagos (nombre) VALUES
    ('Efectivo'), ('Transferencia'), ('Pago TC'), ('Pago TD');

INSERT INTO cat_pagos (nombre, monto) VALUES
    ('Inscripción', 150.00),
    ('Reinscripción', NULL),
    ('Renta de cancha', NULL),
    ('Pago de uniforme', 290.00),
    ('Pago de arbitraje total', 400.00),
    ('Arbitraje por jugador', 33.00),
    ('Mensualidad', 500.00),
    ('Material deportivo', NULL),
    ('Trofeos y premiación', NULL);

INSERT INTO ligas (nombre, temporada, dias_partido_semana, estado) VALUES
    ('Liga Municipal San Manuel', '2026', 1, 'activa');

INSERT INTO equipos (liga_id, nombre, categoria, temporada) VALUES
    (1, 'Táctika FC', 'Primera fuerza', '2026');

-- Contraseña de ejemplo: admin123
INSERT INTO usuarios (nombre, email, password, rol_id, liga_id) VALUES
    ('Jorge Oliver', 'jorge@tactika.com', '$2y$10$9OPxslm9A8l0Ccz7l8tb7uT2rTfiGbBT0mDKWmJTvkn8qtGWSRoce', 1, NULL);

INSERT INTO cat_estadisticas (nombre, abreviatura) VALUES
    ('Ataque', 'ATT'),
    ('Técnica', 'TEC'),
    ('Táctica', 'TAC'),
    ('Defensa', 'DEF'),
    ('Creatividad', 'CRE');

INSERT INTO cat_posiciones (nombre, tag) VALUES
    ('Portero', '#f2994a'),
    ('Defensa', '#2f80ed'),
    ('Defensa Central', '#2f80ed'),
    ('Lateral Derecho', '#2f80ed'),
    ('Lateral Izquierdo', '#2f80ed'),
    ('Centro', '#17a2b8'),
    ('Centro Defensivo', '#17a2b8'),
    ('Centro Ofensivo', '#17a2b8'),
    ('Delantero', '#eb5757'),
    ('Extremo', '#eb5757');

INSERT INTO jugadores (equipo_id, nombre, apellido, posicion_id, numero, estado, capitan) VALUES
    (1, 'Carlos', 'Ramírez', 9, 9, 'activo', 1),
    (1, 'Luis', 'Hernández', 1, 1, 'activo', 0),
    (1, 'Miguel', 'Torres', 2, 4, 'lesionado', 0);

INSERT INTO partidos (liga_id, equipo_id, rival, fecha, hora, sede, tipo, competencia, jornada, estado) VALUES
    (1, 1, 'Deportivo Puebla', '2026-08-30', '10:00:00', 'San Manuel', 'local', 'Liga', 'Jornada 12', 'programado');

INSERT INTO partidos (liga_id, equipo_id, rival, fecha, hora, sede, tipo, competencia, jornada, goles_favor, goles_contra, estado) VALUES
    (1, 1, 'Atlético Sur', '2026-08-16', '10:00:00', 'San Manuel', 'local', 'Liga', 'Jornada 10', 2, 2, 'jugado'),
    (1, 1, 'Real San Manuel', '2026-08-09', '10:00:00', 'Visitante', 'visitante', 'Liga', 'Jornada 9', 4, 0, 'jugado');

INSERT INTO pagos (jugador_id, concepto_id, monto, tipo, metodo_pago_id, fecha, estado) VALUES
    (1, 7, 500.00, 'ingreso', 1, '2026-08-01', 'pagado'),
    (NULL, 3, 800.00, 'gasto', 2, '2026-08-10', 'pagado');
