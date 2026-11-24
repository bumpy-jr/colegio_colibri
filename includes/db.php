<?php
declare(strict_types=1);

/** Conexión PDO a MySQL (Aiven). Se crea solo cuando se necesita. */
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;

    $host    = getenv('DB_HOST') ?: '127.0.0.1';
    $puerto  = getenv('DB_PORT') ?: '3306';
    $nombre  = getenv('DB_NAME') ?: 'defaultdb';
    $usuario = getenv('DB_USER') ?: 'root';
    $clave   = getenv('DB_PASS') ?: '';
    // Certificado CA de Aiven (en Render: Secret File llamado ca.pem)
    $ca      = getenv('DB_SSL_CA') ?: '/etc/secrets/ca.pem';

    $opciones = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 10,
    ];
    if (is_readable($ca)) {
        $opciones[PDO::MYSQL_ATTR_SSL_CA] = $ca;
        if (getenv('DB_SSL_VERIFY') === '0') {
            $opciones[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
    }

    $dsn = "mysql:host={$host};port={$puerto};dbname={$nombre};charset=utf8mb4";
    $pdo = new PDO($dsn, $usuario, $clave, $opciones);
    return $pdo;
}
