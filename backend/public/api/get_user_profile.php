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
  ensureUserFollowTable($pdo);
  $currentUserId = (int) $_SESSION['user']['id'];
  
  // Obtener información del usuario incluyendo estado de seguimiento
  $stmt = $pdo->prepare("
    SELECT
      u.id,
      u.nombre_usuario,
      u.correo_electronico AS email,
      u.fecha_registro,
      EXISTS(
        SELECT 1 FROM usuario_seguidor us
        WHERE us.seguidor_id = :cuid1 AND us.seguido_id = u.id
      ) AS is_following,
      EXISTS(
        SELECT 1 FROM usuario_seguidor us
        WHERE us.seguidor_id = u.id AND us.seguido_id = :cuid2
      ) AS follows_you
    FROM usuario u
    WHERE u.id = :user_id
  ");
  $stmt->execute(['user_id' => $userId, 'cuid1' => $currentUserId, 'cuid2' => $currentUserId]);
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
  
  // Contar total real de reseñas (sin limite)
  $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM resena WHERE usuario_id = :user_id");
  $stmt->execute(['user_id' => $userId]);
  $totalResenas = (int) ($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

  // Contadores de seguidores/seguidos
  $stmt = $pdo->prepare("
    SELECT
      SUM(CASE WHEN seguido_id  = :uid1 THEN 1 ELSE 0 END) AS followers_count,
      SUM(CASE WHEN seguidor_id = :uid2 THEN 1 ELSE 0 END) AS following_count
    FROM usuario_seguidor
    WHERE seguido_id = :uid3 OR seguidor_id = :uid4
  ");
  $stmt->execute(['uid1' => $userId, 'uid2' => $userId, 'uid3' => $userId, 'uid4' => $userId]);
  $followCounts = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

  // Obtener estadísticas
  $stats = [
    'total_paises'    => count($visitedCountries),
    'total_resenas'   => $totalResenas,
    'continentes'     => [],
    'followers_count' => (int) ($followCounts['followers_count'] ?? 0),
    'following_count' => (int) ($followCounts['following_count'] ?? 0),
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
