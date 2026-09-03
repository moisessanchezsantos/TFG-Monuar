<?php
session_start();
header('Content-Type: application/json');

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar que el usuario esté logueado y sea admin
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

require_once __DIR__ . '/../../config/monar_database.php';

try {
    $pdo = getDBConnection();
    
    // Verificar que sea administrador
    $stmt = $pdo->prepare("SELECT es_admin FROM usuario WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !$user['es_admin']) {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }
    
    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    $photoId = $data['photo_id'] ?? null;
    
    if (!$photoId) {
        echo json_encode(['success' => false, 'message' => 'ID de foto no proporcionado']);
        exit;
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Obtener ruta de la imagen antes de eliminar
    $stmt = $pdo->prepare("SELECT imagen_url FROM publicacion WHERE id = :id");
    $stmt->execute(['id' => $photoId]);
    $photo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$photo) {
        echo json_encode(['success' => false, 'message' => 'Foto no encontrada']);
        exit;
    }
    
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
