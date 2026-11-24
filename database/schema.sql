-- Tablas del Colegio Colibrí (MySQL / Aiven)
CREATE TABLE IF NOT EXISTS prematriculas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  creado_en DATETIME NOT NULL,
  radicado VARCHAR(20) NOT NULL UNIQUE,
  nombres VARCHAR(60) NOT NULL,
  apellidos VARCHAR(60) NOT NULL,
  nacimiento DATE NOT NULL,
  grado VARCHAR(20) NOT NULL,
  acudiente VARCHAR(80) NOT NULL,
  parentesco VARCHAR(20) NOT NULL,
  telefono VARCHAR(10) NOT NULL,
  correo VARCHAR(254) NOT NULL,
  observaciones VARCHAR(300) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS citas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  creado_en DATETIME NOT NULL,
  radicado VARCHAR(20) NOT NULL UNIQUE,
  acudiente VARCHAR(80) NOT NULL,
  telefono VARCHAR(10) NOT NULL,
  correo VARCHAR(254) NOT NULL,
  tipo VARCHAR(20) NOT NULL,
  fecha DATE NOT NULL,
  franja CHAR(5) NOT NULL,
  ninos TINYINT UNSIGNED NOT NULL,
  comentarios VARCHAR(300) NOT NULL DEFAULT '',
  KEY idx_fecha_franja (fecha, franja)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
