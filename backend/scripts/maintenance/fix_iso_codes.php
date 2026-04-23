<?php
require_once __DIR__ . '/../../config/monar_database.php';

try {
  $pdo = getDBConnection();
  
  echo "🔧 Corrigiendo códigos ISO...\n\n";
  
  $correcciones = [
    ['nombre' => 'España', 'codigo_correcto' => '724', 'continente' => 'Europa'],
    ['nombre' => 'Japón', 'codigo_correcto' => '392', 'continente' => 'Asia'],
    ['nombre' => 'México', 'codigo_correcto' => '484', 'continente' => 'América del Norte']
  ];
  
  foreach ($correcciones as $correccion) {
    $stmt = $pdo->prepare("
      UPDATE pais 
      SET codigo_iso = :codigo_iso, continente = :continente
      WHERE nombre = :nombre
    ");
    
    $stmt->execute([
      'codigo_iso' => $correccion['codigo_correcto'],
      'continente' => $correccion['continente'],
      'nombre' => $correccion['nombre']
    ]);
    
    echo "✅ {$correccion['nombre']}: Código ISO actualizado a {$correccion['codigo_correcto']}\n";
  }
  
  echo "\n🎯 Verificando cambios...\n\n";
  
  foreach ($correcciones as $correccion) {
    $stmt = $pdo->prepare("SELECT id, nombre, codigo_iso, continente FROM pais WHERE nombre = :nombre");
    $stmt->execute(['nombre' => $correccion['nombre']]);
    $pais = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($pais) {
      echo "{$pais['nombre']}:\n";
      echo "  ID: {$pais['id']}\n";
      echo "  Código ISO: {$pais['codigo_iso']}\n";
      echo "  Continente: {$pais['continente']}\n\n";
    }
  }
  
  echo "✅ Corrección completada.\n";
  
} catch (PDOException $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
}
