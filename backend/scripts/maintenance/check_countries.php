<?php
require_once __DIR__ . '/../../config/monar_database.php';

try {
  $pdo = getDBConnection();
  
  echo "🔍 Verificando países problemáticos...\n\n";
  
  $problematicos = ['España', 'Japón', 'México'];
  
  foreach ($problematicos as $nombre) {
    $stmt = $pdo->prepare("SELECT id, nombre, continente, codigo_iso FROM pais WHERE nombre = :nombre");
    $stmt->execute(['nombre' => $nombre]);
    $pais = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pais) {
      echo "País: {$pais['nombre']}\n";
      echo "  ID: {$pais['id']}\n";
      echo "  Continente: {$pais['continente']}\n";
      echo "  Código ISO: {$pais['codigo_iso']}\n";
      echo "\n";
    } else {
      echo "❌ No se encontró: {$nombre}\n\n";
    }
  }
  
  // Ver los primeros 10 países para referencia
  echo "📋 Primeros 10 países en la base de datos:\n";
  $stmt = $pdo->query("SELECT id, nombre, codigo_iso FROM pais ORDER BY id LIMIT 10");
  $paises = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  foreach ($paises as $p) {
    echo "  ID {$p['id']}: {$p['nombre']} (ISO: {$p['codigo_iso']})\n";
  }
  
} catch (PDOException $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
}
