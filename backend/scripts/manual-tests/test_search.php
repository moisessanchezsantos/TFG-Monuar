<?php
session_start();

// Simular usuario logueado
$_SESSION['user'] = ['id' => 8, 'nombre_usuario' => 'usuario_test'];

require_once __DIR__ . '/../../config/monar_database.php';

try {
  $pdo = getDBConnection();
  
  echo "🔍 Probando búsqueda de usuarios...\n\n";
  
  $testQueries = ['moises', 'admin', 'usuario'];
  
  foreach ($testQueries as $query) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Búsqueda: '{$query}'\n\n";
    
    $stmt = $pdo->prepare("
      SELECT id, nombre_usuario, correo_electronico as email, fecha_registro
      FROM usuario 
      WHERE (nombre_usuario LIKE :query OR correo_electronico LIKE :query2)
      AND id != :current_user_id
      ORDER BY nombre_usuario
      LIMIT 15
    ");
    
    $stmt->execute([
      'query' => '%' . $query . '%',
      'query2' => '%' . $query . '%',
      'current_user_id' => 8
    ]);
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
      echo "No se encontraron resultados\n";
    } else {
      foreach ($users as $user) {
        echo "✅ ID: {$user['id']} | Usuario: {$user['nombre_usuario']} | Email: {$user['email']}\n";
      }
    }
    echo "\n";
  }
  
} catch (PDOException $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
}
