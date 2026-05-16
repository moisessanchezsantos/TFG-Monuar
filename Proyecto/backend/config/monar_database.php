<?php
/**
 * Configuración de base de datos
 */

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'monuar_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Obtiene una conexión PDO a la base de datos
 * @return PDO
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Asegura que exista la tabla de seguimiento entre usuarios.
 */
function ensureUserFollowTable(PDO $pdo): void {
    static $initialized = false;
    if ($initialized) { return; }

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuario_seguidor (
            id INT AUTO_INCREMENT PRIMARY KEY,
            seguidor_id INT NOT NULL,
            seguido_id  INT NOT NULL,
            fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_us (seguidor_id, seguido_id),
            INDEX idx_seguidor (seguidor_id),
            INDEX idx_seguido  (seguido_id),
            CONSTRAINT fk_us_seguidor FOREIGN KEY (seguidor_id) REFERENCES usuario(id) ON DELETE CASCADE,
            CONSTRAINT fk_us_seguido  FOREIGN KEY (seguido_id)  REFERENCES usuario(id) ON DELETE CASCADE,
            CONSTRAINT chk_us_distinto CHECK (seguidor_id <> seguido_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $initialized = true;
}
