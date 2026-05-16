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

try {
  $pdo = getDBConnection();
  
  // Obtener todos los países
  $stmt = $pdo->query('SELECT id, nombre, continente, codigo_iso FROM pais ORDER BY nombre');
  $countries = $stmt->fetchAll();
  
  echo json_encode([
    'success' => true,
    'data' => $countries
  ]);
  
} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'Error al obtener los países',
    'message' => $e->getMessage()
  ]);
}
