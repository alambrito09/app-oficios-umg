-- ============================================================================
-- Esquema de base de datos — App Oficios Bajo Demanda
-- Monolito: Apache + PHP + CodeIgniter 3 + MySQL
-- Base de datos: app_oficios
-- ============================================================================

-- -----------------------------------------------------
-- Tabla: roles
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE COMMENT 'cliente, prestador, administrador, soporte',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: users
-- Distingue persona (datos personales), cuenta (credenciales) y rol (permiso)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    role_id INT NOT NULL,
    status ENUM('activo','inactivo','suspendido') DEFAULT 'activo',
    email_verified TINYINT(1) DEFAULT 0,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: login_attempts (limitación temporal por IP)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS login_attempts (
    attempt_key CHAR(64) PRIMARY KEY,
    failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    window_started_at DATETIME NOT NULL,
    locked_until DATETIME DEFAULT NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: trades (oficios)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS trades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE COMMENT 'Fontanero, Electricista, Mecánico, Herrero, Encargado de pinchazos, Carpintero, Albañil, Lavandera, Otros',
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: specialties (especialidades por oficio)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trade_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (trade_id) REFERENCES trades(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: provider_profiles
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS provider_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    experience_years INT DEFAULT 0,
    bio TEXT,
    coverage_area VARCHAR(200) COMMENT 'Zona geográfica de cobertura (ej. "Zona 1, Mixco")',
    availability_status ENUM('disponible','no_disponible','ocupado') DEFAULT 'no_disponible',
    is_verified TINYINT(1) DEFAULT 0 COMMENT 'Etiqueta verificado: indica qué se revisó, no garantía absoluta',
    verification_status ENUM('pendiente','verificado','rechazado') DEFAULT 'pendiente',
    verification_notes TEXT COMMENT 'Quién revisó, qué datos se revisaron, fecha',
    rating_average DECIMAL(3,2) DEFAULT 0.00,
    total_ratings INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: provider_trades (relación N:N proveedor ↔ oficio)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS provider_trades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    trade_id INT NOT NULL,
    is_primary TINYINT(1) DEFAULT 0,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trade_id) REFERENCES trades(id) ON DELETE CASCADE,
    UNIQUE KEY uk_provider_trade (provider_id, trade_id)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: availabilities (disponibilidad del proveedor)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS availabilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    day_of_week TINYINT NOT NULL COMMENT '0=domingo ... 6=sábado',
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available TINYINT(1) DEFAULT 1,
    UNIQUE KEY uk_provider_availability (provider_id, day_of_week, start_time, end_time),
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: service_requests (solicitudes)
-- Estados: solicitado, en_evaluacion, pendiente_aprobacion, confirmado, en_ejecucion, finalizado, rechazado, vencido, cancelado
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    trade_id INT NOT NULL,
    specialty_id INT DEFAULT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL COMMENT 'Dirección exacta protegida — solo visible para partes autorizadas',
    latitude DECIMAL(10,7) DEFAULT NULL COMMENT 'Simulado — ubicación en tiempo real no está en alcance',
    longitude DECIMAL(10,7) DEFAULT NULL,
    scheduled_date DATETIME DEFAULT NULL COMMENT 'Para modalidad programada',
    service_modality ENUM('inmediata','programada','diagnostico','por_presupuesto') DEFAULT 'inmediata',
    status ENUM('solicitado','en_evaluacion','pendiente_aprobacion','confirmado','en_ejecucion','finalizado','rechazado','vencido','cancelado') DEFAULT 'solicitado',
    photos JSON DEFAULT NULL COMMENT 'Fotos opcionales adjuntas',
    client_notes TEXT,
    assigned_provider_id INT DEFAULT NULL COMMENT 'Doble aceptación: un solo proveedor por solicitud',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at DATETIME DEFAULT NULL COMMENT 'Fecha de vencimiento de la solicitud',
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (trade_id) REFERENCES trades(id) ON DELETE RESTRICT,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_provider_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: quotes (cotizaciones)
-- Estados: borrador, enviado, aprobado, rechazado, vencido
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL UNIQUE COMMENT 'Una cotización por solicitud (doble aceptación)',
    provider_id INT NOT NULL,
    labor_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    materials_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    other_charges DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    status ENUM('borrador','enviado','aprobado','rechazado','vencido') DEFAULT 'borrador',
    visit_cost DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Costo de visita para diagnóstico',
    valid_until DATETIME DEFAULT NULL,
    approved_at DATETIME DEFAULT NULL,
    approved_by INT DEFAULT NULL,
    rejection_reason TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES service_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: quote_details (detalle de cotización)
-- Separa mano de obra, materiales y cargos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS quote_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NOT NULL,
    item_type ENUM('mano_obra','material','cargo_adicional') NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: services (servicios contratados)
-- Estados: programado, en_ejecucion, completado, cerrado
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote_id INT NOT NULL UNIQUE,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    status ENUM('programado','en_ejecucion','completado','cerrado') DEFAULT 'programado',
    started_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    closed_at DATETIME DEFAULT NULL,
    progress_notes TEXT DEFAULT NULL COMMENT 'Avance del servicio',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: state_histories (historial de transiciones de estado)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS state_histories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entity_type VARCHAR(50) NOT NULL COMMENT 'service_request, quote, service, complaint',
    entity_id INT NOT NULL,
    old_state VARCHAR(50) DEFAULT NULL,
    new_state VARCHAR(50) NOT NULL,
    changed_by INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reason TEXT DEFAULT NULL,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: ratings (calificaciones)
-- Solo servicios realizados (estado completado/cerrado)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL UNIQUE,
    client_id INT NOT NULL,
    provider_id INT NOT NULL,
    score TINYINT NOT NULL COMMENT '1 a 5',
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT chk_score CHECK (score >= 1 AND score <= 5)
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: complaints (reclamos)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    client_id INT NOT NULL,
    provider_id INT DEFAULT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    evidence TEXT DEFAULT NULL COMMENT 'Evidencia adjunta (ruta de archivo)',
    status ENUM('abierto','en_proceso','resuelto','cerrado') DEFAULT 'abierto',
    resolution TEXT DEFAULT NULL,
    resolved_by INT DEFAULT NULL,
    resolved_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES service_requests(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: payments (pagos — simulado, no es pago real)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pendiente','completado','fallido','reembolsado') DEFAULT 'pendiente',
    payment_method VARCHAR(50) DEFAULT 'simulado',
    transaction_id VARCHAR(100) DEFAULT NULL COMMENT 'Simulado — no hay pasarela real',
    paid_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabla: notifications (notificaciones — simulado)
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(200),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Datos iniciales (seed)
-- -----------------------------------------------------
INSERT IGNORE INTO roles (name, description) VALUES
('cliente', 'Cliente que solicita servicios de oficios'),
('prestador', 'Prestador que ofrece servicios de oficios'),
('administrador', 'Administrador del sistema'),
('soporte', 'Soporte con permisos limitados para reclamos');

INSERT IGNORE INTO trades (name, description) VALUES
('Fontanero', 'Reparación e instalación de tuberías, fugas, sanitarios'),
('Electricista', 'Instalación y reparación eléctrica, fallas eléctricas'),
('Mecánico', 'Reparación mecánica, diagnóstico de fallas'),
('Herrero', 'Trabajos en metal, herrería por presupuesto'),
('Encargado de pinchazos', 'Reparación de pinchazos de llantas'),
('Carpintero', 'Trabajos en madera, carpintería por presupuesto'),
('Albañil', 'Obra ligera, construcción, reparaciones de albañilería'),
('Lavandera', 'Servicios de lavandería programada');

-- No se crean cuentas con contraseñas conocidas. Registrar usuarios desde la aplicación.

-- ============================================================================
-- FIN DEL ESQUEMA
-- ============================================================================
