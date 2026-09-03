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
  ensureUserFollowTable($pdo);
  
  $query = isset($_GET['q']) ? trim($_GET['q']) : '';
  
  if (empty($query)) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
  }
  
  // Buscar usuarios por nombre o correo (excluyendo al usuario actual)
  $stmt = $pdo->prepare("
    SELECT
      u.id,
      u.nombre_usuario,
      u.correo_electronico AS email,
      u.fecha_registro,
      EXISTS(
        SELECT 1 FROM usuario_seguidor us
        WHERE us.seguidor_id = :cuid_follow AND us.seguido_id = u.id
      ) AS is_following
    FROM usuario u
    WHERE (u.nombre_usuario LIKE :query OR u.correo_electronico LIKE :query2)
      AND u.id != :cuid_exclude
    ORDER BY is_following DESC, u.nombre_usuario
    LIMIT 15
  ");
  
  $stmt->execute([
    'query'        => '%' . $query . '%',
    'query2'       => '%' . $query . '%',
    'cuid_follow'  => $currentUserId,
    'cuid_exclude' => $currentUserId,
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
