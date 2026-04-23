<?php
session_start();

// Simular usuario logueado
$_SESSION['user'] = ['id' => 8, 'nombre_usuario' => 'usuario_test'];

require_once __DIR__ . '/../../config/monar_database.php';

echo "🔍 Probando carga de perfil de usuario...\n\n";

$testUserId = 5; // Usuario moises

try {
  $pdo = getDBConnection();
  
  // Obtener información del usuario
  echo "1. Obteniendo información básica...\n";
  $stmt = $pdo->prepare("
    SELECT id, nombre_usuario, correo_electronico as email, fecha_registro
    FROM usuario 
    WHERE id = :user_id
  ");
  $stmt->execute(['user_id' => $testUserId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$user) {
    echo "❌ Usuario no encontrado\n";
    exit;
  }
  
  echo "✅ Usuario: {$user['nombre_usuario']} | Email: {$user['email']}\n\n";
  
  // Obtener países visitados
  echo "2. Obteniendo países visitados...\n";
  $stmt = $pdo->prepare("
    SELECT 
      p.id,
      p.nombre,
      p.continente,
      p.codigo_iso,
      vp.fecha_visita
    FROM visita_pais vp
    INNER JOIN pais p ON vp.pais_id = p.id
    WHERE vp.usuario_id = :user_id
    ORDER BY vp.fecha_visita DESC
  ");
  $stmt->execute(['user_id' => $testUserId]);
  $visitedCountries = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo "✅ Países visitados: " . count($visitedCountries) . "\n";
  foreach ($visitedCountries as $country) {
    echo "   - {$country['nombre']} (ISO: {$country['codigo_iso']})\n";
  }
  echo "\n";
  
  // Obtener reseñas
  echo "3. Obteniendo reseñas...\n";
  $stmt = $pdo->prepare("
    SELECT 
      r.id,
      r.titulo,
      r.contenido,
      r.puntuacion,
      r.fecha_resena as fecha_creacion,
      p.nombre as pais_nombre,
      p.codigo_iso
    FROM resena r
    INNER JOIN pais p ON r.pais_id = p.id
    WHERE r.usuario_id = :user_id
    ORDER BY r.fecha_resena DESC
    LIMIT 20
  ");
  $stmt->execute(['user_id' => $testUserId]);
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo "✅ Reseñas: " . count($reviews) . "\n\n";
  
  // Contar continentes
  $continentesCounts = [];
  foreach ($visitedCountries as $country) {
    $cont = $country['continente'];
    if (!isset($continentesCounts[$cont])) {
      $continentesCounts[$cont] = 0;
    }
    $continentesCounts[$cont]++;
  }
  
  echo "4. Estadísticas:\n";
  echo "   Total países: " . count($visitedCountries) . "\n";
  echo "   Total reseñas: " . count($reviews) . "\n";
  echo "   Continentes visitados: " . count($continentesCounts) . "\n";
  foreach ($continentesCounts as $cont => $count) {
    echo "     - {$cont}: {$count} países\n";
  }
  
  echo "\n✅ Todo funciona correctamente!\n";
  
} catch (PDOException $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
  echo "Código de error: " . $e->getCode() . "\n";
}
