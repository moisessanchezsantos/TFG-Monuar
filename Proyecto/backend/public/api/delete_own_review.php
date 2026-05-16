<?php
session_start();
header('Content-Type: application/json');

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

require_once __DIR__ . '/../../config/monar_database.php';

try {
    $pdo = getDBConnection();
    $userId = $_SESSION['user']['id'];
    
    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    $reviewId = $data['review_id'] ?? null;
    
    if (!$reviewId) {
        echo json_encode(['success' => false, 'message' => 'ID de reseña no proporcionado']);
        exit;
    }
    
    // Verificar que la reseña pertenece al usuario
    $stmt = $pdo->prepare("SELECT usuario_id FROM resena WHERE id = :id");
    $stmt->execute(['id' => $reviewId]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$review) {
        echo json_encode(['success' => false, 'message' => 'Reseña no encontrada']);
        exit;
    }
    
    if ($review['usuario_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta reseña']);
        exit;
    }
    
    // Eliminar la reseña
    $stmt = $pdo->prepare("DELETE FROM resena WHERE id = :id");
    $stmt->execute(['id' => $reviewId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Reseña eliminada correctamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar reseña: ' . $e->getMessage()
    ]);
}
