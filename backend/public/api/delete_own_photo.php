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
    $photoId = $data['photo_id'] ?? null;
    
    if (!$photoId) {
        echo json_encode(['success' => false, 'message' => 'ID de foto no proporcionado']);
        exit;
    }
    
    // Verificar que la foto pertenece al usuario
    $stmt = $pdo->prepare("SELECT usuario_id, imagen_url FROM publicacion WHERE id = :id");
    $stmt->execute(['id' => $photoId]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$photo) {
        echo json_encode(['success' => false, 'message' => 'Foto no encontrada']);
        exit;
    }
    
    if ($photo['usuario_id'] != $userId) {
        echo json_encode(['success' => false, 'message' => 'No tienes permiso para eliminar esta foto']);
        exit;
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Eliminar likes de la foto
    $stmt = $pdo->prepare("DELETE FROM likes WHERE publicacion_id = :id");
    $stmt->execute(['id' => $photoId]);
    
    // Eliminar la publicación
    $stmt = $pdo->prepare("DELETE FROM publicacion WHERE id = :id");
    $stmt->execute(['id' => $photoId]);
    
    // Commit
    $pdo->commit();
    
    // Eliminar archivo físico
    $imagePath = __DIR__ . '/../' . $photo['imagen_url'];
    if (file_exists($imagePath)) {
        @unlink($imagePath);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Foto eliminada correctamente'
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar foto: ' . $e->getMessage()
    ]);
}
