<?php
declare(strict_types=1);
// Se ejecuta al arrancar el contenedor: crea las tablas si no existen.
require __DIR__.'/../includes/db.php';

try {
    $sql = (string)file_get_contents(__DIR__.'/../database/schema.sql');
    $sql = preg_replace('/^--.*$/m', '', $sql) ?? $sql;
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $sentencia) {
        db()->exec($sentencia);
    }
    echo "Migración OK\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'Migración fallida: '.$e->getMessage()."\n");
    exit(1);
}
