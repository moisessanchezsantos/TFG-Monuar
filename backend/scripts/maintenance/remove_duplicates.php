<?php
require_once __DIR__ . '/../../config/monar_database.php';

try {
  $pdo = getDBConnection();
  
  echo "🔍 Buscando países duplicados...\n\n";
  
  // Encontrar duplicados por nombre
  $stmt = $pdo->query("
    SELECT nombre, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids
    FROM pais
    GROUP BY nombre
    HAVING count > 1
    ORDER BY nombre
  ");
  
  $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  if (empty($duplicates)) {
    echo "✅ No se encontraron duplicados.\n";
    exit;
  }
  
  echo "❌ Se encontraron " . count($duplicates) . " países duplicados:\n\n";
  
  $totalDeleted = 0;
  
  foreach ($duplicates as $duplicate) {
    $nombre = $duplicate['nombre'];
    $ids = explode(',', $duplicate['ids']);
    
    echo "País: {$nombre} (aparece {$duplicate['count']} veces)\n";
    echo "IDs: " . implode(', ', $ids) . "\n";
    
    // Mantener el primer ID y eliminar los demás
    $keepId = $ids[0];
    $deleteIds = array_slice($ids, 1);
    
    echo "  ➡️  Manteniendo ID: {$keepId}\n";
    echo "  🗑️  Eliminando IDs: " . implode(', ', $deleteIds) . "\n";
    
    foreach ($deleteIds as $deleteId) {
      // Primero, eliminar referencias en visita_pais
      $stmtDeleteVisits = $pdo->prepare("DELETE FROM visita_pais WHERE pais_id = :pais_id");
      $stmtDeleteVisits->execute(['pais_id' => $deleteId]);
      $deletedVisits = $stmtDeleteVisits->rowCount();
      
      if ($deletedVisits > 0) {
        echo "    • Eliminadas {$deletedVisits} visitas asociadas al ID {$deleteId}\n";
        
        // Actualizar las visitas para que apunten al ID que mantenemos
        // Primero verificar si ya existe una visita para el usuario con el ID correcto
        $stmtCheckVisit = $pdo->prepare("
          SELECT usuario_id FROM visita_pais WHERE pais_id = :delete_id
        ");
        $stmtCheckVisit->execute(['delete_id' => $deleteId]);
        $affectedUsers = $stmtCheckVisit->fetchAll(PDO::FETCH_COLUMN);
        
        // Para cada usuario afectado, asegurar que tiene la visita con el ID correcto
        foreach ($affectedUsers as $userId) {
          $stmtInsertCorrect = $pdo->prepare("
            INSERT IGNORE INTO visita_pais (usuario_id, pais_id, fecha_visita)
            SELECT :user_id, :keep_id, MIN(fecha_visita)
            FROM visita_pais
            WHERE usuario_id = :user_id2 AND pais_id = :delete_id
          ");
          $stmtInsertCorrect->execute([
            'user_id' => $userId,
            'keep_id' => $keepId,
            'user_id2' => $userId,
            'delete_id' => $deleteId
          ]);
        }
      }
      
      // Ahora eliminar el país duplicado
      $stmtDeletePais = $pdo->prepare("DELETE FROM pais WHERE id = :id");
      $stmtDeletePais->execute(['id' => $deleteId]);
      $totalDeleted++;
    }
    
    echo "\n";
  }
  
  echo "✅ Proceso completado. Se eliminaron {$totalDeleted} países duplicados.\n";
  
  // Verificar resultado final
  $stmt = $pdo->query("SELECT COUNT(*) FROM pais");
  $totalPaises = $stmt->fetchColumn();
  
  echo "\n📊 Total de países en la base de datos: {$totalPaises}\n";
  
} catch (PDOException $e) {
  echo "❌ Error: " . $e->getMessage() . "\n";
}
