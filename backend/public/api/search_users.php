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

$currentUserId = $_SESSION['user']['id'];

try {
  $pdo = getDBConnection();
  
  $query = isset($_GET['q']) ? trim($_GET['q']) : '';
  
  if (empty($query)) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
  }
  
  // Buscar usuarios por nombre o correo (excluyendo al usuario actual)
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
    'current_user_id' => $currentUserId
  ]);
  
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  echo json_encode([
    'success' => true,
    'data' => $users
  ]);
  
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'Error al buscar usuarios',
    'message' => $e->getMessage()
  ]);
}
