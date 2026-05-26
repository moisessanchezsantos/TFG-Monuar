<?php
session_start();
require_once __DIR__ . '/../../config/monar_database.php';

header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
  http_response_code(401);
  echo json_encode(['error' => 'No autorizado']);
  exit;
}

// Permitir ver países de otro usuario si se proporciona user_id
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : $_SESSION['user']['id'];

try {
  $pdo = getDBConnection();
  
  // Obtener países visitados por el usuario
  $stmt = $pdo->prepare('
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
  ');
  
  $stmt->execute(['user_id' => $userId]);
  $countries = $stmt->fetchAll();
  
  echo json_encode([
    'success' => true,
    'data' => $countries
  ]);
  
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'Error al obtener los países visitados',
    'message' => $e->getMessage()
  ]);
}
