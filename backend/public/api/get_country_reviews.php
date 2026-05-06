<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

require_once __DIR__ . '/../../config/monar_database.php';

// Permitir ver reseñas de otro usuario si se pasa user_id (para modo visualización)
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : $_SESSION['user']['id'];
$pais_codigo_iso = $_GET['codigo_iso'] ?? '';

if (empty($pais_codigo_iso)) {
    echo json_encode(['success' => false, 'message' => 'Código ISO requerido']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Primero obtener el ID del país a partir del código ISO
    $stmt = $pdo->prepare("SELECT id FROM pais WHERE codigo_iso = :codigo_iso");
    $stmt->execute(['codigo_iso' => $pais_codigo_iso]);
    $pais = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pais) {
        echo json_encode(['success' => false, 'message' => 'País no encontrado']);
        exit;
    }
    
    $pais_id = $pais['id'];
    
    // Obtener las reseñas del usuario para este país
    $stmt = $pdo->prepare("
        SELECT id, titulo, contenido, puntuacion, fecha_resena
        FROM resena
        WHERE usuario_id = :usuario_id AND pais_id = :pais_id
        ORDER BY fecha_resena DESC
    ");
    
    $stmt->execute([
        'usuario_id' => $user_id,
        'pais_id' => $pais_id
    ]);
    
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear las fechas
    foreach ($reviews as &$review) {
        $date = new DateTime($review['fecha_resena']);
        $review['fecha_formateada'] = $date->format('d/m/Y');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $reviews
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener reseñas: ' . $e->getMessage()
    ]);
}
