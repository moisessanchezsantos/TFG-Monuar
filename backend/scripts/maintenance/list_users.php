<?php
require_once __DIR__ . '/../../config/monar_database.php';

try {
  $pdo = getDBConnection();
  
  echo "👥 Usuarios en la base de datos:\n\n";
  
  // Primero ver la estructura de la tabla
  $stmt = $pdo->query("DESCRIBE usuario");
  $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo "Columnas de la tabla usuario:\n";
  foreach ($columns as $col) {
    echo "  - {$col['Field']}\n";
  }
  echo "\n";
  
  $stmt = $pdo->query("
    SELECT 
      u.id, 
      u.nombre_usuario,
      COUNT(DISTINCT vp.pais_id) as paises_visitados,
      COUNT(DISTINCT r.id) as total_resenas
    FROM usuario u
    LEFT JOIN visita_pais vp ON u.id = vp.usuario_id
    LEFT JOIN resena r ON u.id = r.usuario_id
    GROUP BY u.id
    ORDER BY u.id
  ");
  
  $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  if (empty($usuarios)) {
    echo "❌ No hay usuarios en la base de datos.\n";
    exit;
  }
  
  foreach ($usuarios as $user) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "ID: {$user['id']}\n";
    echo "Usuario: {$user['nombre_usuario']}\n";
    echo "Países visitados: {$user['paises_visitados']}\n";
    echo "Reseñas: {$user['total_resenas']}\n";
    echo "\n";
  }
  
  echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
  echo "Total usuarios: " . count($usuarios) . "\n";
  
} catch (PDOException $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
}
