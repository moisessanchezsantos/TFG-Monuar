<?php
require_once __DIR__ . '/../../config/monar_database.php';

try {
  $pdo = getDBConnection();
  
  echo "📋 Estructura de la tabla resena:\n\n";
  
  $stmt = $pdo->query("DESCRIBE resena");
  $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  foreach ($columns as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
  }
  
} catch (PDOException $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
}
