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

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Método no permitido']);
  exit;
}

$userId = $_SESSION['user']['id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['pais_id'])) {
  http_response_code(400);
  echo json_encode(['error' => 'pais_id es requerido']);
  exit;
}

$paisIso = $data['pais_id'];

try {
  $pdo = getDBConnection();
  
  // Buscar el país por código ISO
  $stmt = $pdo->prepare('SELECT id FROM pais WHERE codigo_iso = :iso');
  $stmt->execute(['iso' => $paisIso]);
  $pais = $stmt->fetch();
  
  if (!$pais) {
    http_response_code(404);
    echo json_encode(['error' => 'País no encontrado en la base de datos']);
    exit;
  }
  
  $paisId = $pais['id'];
  
  // Verificar si el usuario ya visitó este país
  $stmt = $pdo->prepare('SELECT id FROM visita_pais WHERE usuario_id = :user_id AND pais_id = :pais_id');
  $stmt->execute([
    'user_id' => $userId,
    'pais_id' => $paisId
  ]);
  
  if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['error' => 'Este país ya está en tu lista de visitados']);
    exit;
  }
  
  // Insertar la visita
  $stmt = $pdo->prepare('INSERT INTO visita_pais (usuario_id, pais_id, fecha_visita) VALUES (:user_id, :pais_id, :fecha_visita)');
  $stmt->execute([
    'user_id' => $userId,
    'pais_id' => $paisId,
    'fecha_visita' => date('Y-m-d')
  ]);
  
  echo json_encode([
    'success' => true,
    'message' => 'País añadido a tus visitados'
  ]);
  
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'Error al guardar el país',
    'message' => $e->getMessage()
  ]);
}
