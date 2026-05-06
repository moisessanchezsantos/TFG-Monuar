<?php
require __DIR__ . '/../config/monar_database.php';

$pdo = getDBConnection();
$tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

echo "Tablas en la base de datos:\n";
foreach ($tables as $table) {
    echo "- $table\n";
}

// Mostrar estructura de las tablas resena y publicacion
echo "\n\nEstructura de la tabla 'resena':\n";
try {
    $result = $pdo->query("DESCRIBE resena");
    foreach ($result as $row) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
} catch (Exception $e) {
    echo "  Tabla no existe\n";
}

echo "\n\nEstructura de la tabla 'publicacion':\n";
try {
    $result = $pdo->query("DESCRIBE publicacion");
    foreach ($result as $row) {
        echo "  {$row['Field']} ({$row['Type']}) - {$row['Key']}\n";
    }
} catch (Exception $e) {
    echo "  Tabla no existe\n";
}
