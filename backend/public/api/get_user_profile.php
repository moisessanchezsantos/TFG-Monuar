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

$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($userId <= 0) {
  http_response_code(400);
  echo json_encode(['error' => 'ID de usuario inválido']);
  exit;
}

try {
  $pdo = getDBConnection();
  
  // Obtener información del usuario
  $stmt = $pdo->prepare("
    SELECT id, nombre_usuario, correo_electronico as email, fecha_registro
    FROM usuario 
    WHERE id = :user_id
  ");
  $stmt->execute(['user_id' => $userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'Usuario no encontrado']);
    exit;
  }
  
  // Obtener países visitados por el usuario
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
  $stmt->execute(['user_id' => $userId]);
  $visitedCountries = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Obtener reseñas del usuario
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
  $stmt->execute(['user_id' => $userId]);
  $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  // Obtener estadísticas
  $stats = [
    'total_paises' => count($visitedCountries),
    'total_resenas' => count($reviews),
    'continentes' => []
  ];
  
  // Contar países por continente
  $continentesCounts = [];
  foreach ($visitedCountries as $country) {
    $cont = $country['continente'];
    if (!isset($continentesCounts[$cont])) {
      $continentesCounts[$cont] = 0;
    }
    $continentesCounts[$cont]++;
  }
  $stats['continentes'] = $continentesCounts;
  
  echo json_encode([
    'success' => true,
    'data' => [
      'user' => $user,
      'visited_countries' => $visitedCountries,
      'reviews' => $reviews,
      'stats' => $stats
    ]
  ]);
  
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'Error al obtener perfil de usuario',
    'message' => $e->getMessage()
  ]);
}
