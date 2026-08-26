-- =========================================================
-- Táctika - Esquema inicial de base de datos (MySQL/MariaDB)
-- =========================================================

CREATE DATABASE IF NOT EXISTS tactika CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tactika;

-- ---------------------------------------------------------
-- Usuarios (administración de accesos al sistema)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'entrenador', 'jugador') NOT NULL DEFAULT 'entrenador',
    estado ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Equipos
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    categoria VARCHAR(50),
    temporada VARCHAR(20),
    escudo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Jugadores
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS jugadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    posicion VARCHAR(50),
    numero INT,
    fecha_nacimiento DATE NULL,
    telefono VARCHAR(20),
    email VARCHAR(150),
    foto VARCHAR(255),
    estado ENUM('activo', 'lesionado', 'inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Partidos (calendario)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
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
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Pagos (finanzas: ingresos y gastos)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jugador_id INT NULL,
    equipo_id INT NULL,
    concepto VARCHAR(150) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    tipo ENUM('ingreso', 'gasto') NOT NULL DEFAULT 'ingreso',
    metodo_pago VARCHAR(50),
    fecha DATE NOT NULL,
    estado ENUM('pagado', 'pendiente') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jugador_id) REFERENCES jugadores(id) ON DELETE SET NULL,
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Datos de ejemplo (opcional, para probar el dashboard)
-- ---------------------------------------------------------
INSERT INTO equipos (nombre, categoria, temporada) VALUES
    ('Táctika FC', 'Primera fuerza', '2026');

INSERT INTO usuarios (nombre, email, password, rol) VALUES
    ('Jorge Oliver', 'jorge@tactika.com', '$2y$10$Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1QOQ1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1Q1', 'administrador');

INSERT INTO jugadores (equipo_id, nombre, apellido, posicion, numero, estado) VALUES
    (1, 'Carlos', 'Ramírez', 'Delantero', 9, 'activo'),
    (1, 'Luis', 'Hernández', 'Portero', 1, 'activo'),
    (1, 'Miguel', 'Torres', 'Defensa', 4, 'lesionado');

INSERT INTO partidos (equipo_id, rival, fecha, hora, sede, tipo, competencia, jornada, estado) VALUES
    (1, 'Deportivo Puebla', '2026-08-30', '10:00:00', 'San Manuel', 'local', 'Liga', 'Jornada 12', 'programado');

INSERT INTO partidos (equipo_id, rival, fecha, hora, sede, tipo, competencia, jornada, goles_favor, goles_contra, estado) VALUES
    (1, 'Atlético Sur', '2026-08-16', '10:00:00', 'San Manuel', 'local', 'Liga', 'Jornada 10', 2, 2, 'jugado'),
    (1, 'Real San Manuel', '2026-08-09', '10:00:00', 'Visitante', 'visitante', 'Liga', 'Jornada 9', 4, 0, 'jugado');

INSERT INTO pagos (jugador_id, concepto, monto, tipo, metodo_pago, fecha, estado) VALUES
    (1, 'Mensualidad agosto', 500.00, 'ingreso', 'Efectivo', '2026-08-01', 'pagado'),
    (NULL, 'Renta de cancha', 800.00, 'gasto', 'Transferencia', '2026-08-10', 'pagado');
