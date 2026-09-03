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
    ensureUserFollowTable($pdo);
    
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
    $userId = $data['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
        exit;
    }
    
    // No permitir que el admin se elimine a sí mismo
    if ($userId == $_SESSION['user']['id']) {
        echo json_encode(['success' => false, 'message' => 'No puedes eliminarte a ti mismo']);
        exit;
    }
    
    // Verificar que el usuario a eliminar no sea admin
    $stmt = $pdo->prepare("SELECT es_admin FROM usuario WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $targetUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($targetUser && $targetUser['es_admin']) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar a otro administrador']);
        exit;
    }
    
    // Iniciar transacción
    $pdo->beginTransaction();
    
    // Eliminar likes del usuario
    $stmt = $pdo->prepare("DELETE FROM likes WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Eliminar likes en publicaciones del usuario
    $stmt = $pdo->prepare("
        DELETE l FROM likes l
        INNER JOIN publicacion p ON l.publicacion_id = p.id
        WHERE p.usuario_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);

    // Eliminar relaciones de seguimiento
    $stmt = $pdo->prepare("DELETE FROM usuario_seguidor WHERE seguidor_id = :user_id OR seguido_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Obtener rutas de imágenes para eliminar archivos físicos
    $stmt = $pdo->prepare("SELECT imagen_url FROM publicacion WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Eliminar publicaciones
    $stmt = $pdo->prepare("DELETE FROM publicacion WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Eliminar reseñas
    $stmt = $pdo->prepare("DELETE FROM resena WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Eliminar países visitados
    $stmt = $pdo->prepare("DELETE FROM visita_pais WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Eliminar mensajes de chat
    $stmt = $pdo->prepare("DELETE FROM mensaje_chat WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Eliminar conversaciones
    $stmt = $pdo->prepare("DELETE FROM conversacion_chat WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Eliminar mapas de usuario
    $stmt = $pdo->prepare("DELETE FROM mapa_usuario WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Eliminar el usuario
    $stmt = $pdo->prepare("DELETE FROM usuario WHERE id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    
    // Commit
    $pdo->commit();
    
    // Eliminar archivos físicos de imágenes
    foreach ($images as $image) {
        $imagePath = __DIR__ . '/../' . $image['imagen_url'];
        if (file_exists($imagePath)) {
            @unlink($imagePath);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Usuario eliminado correctamente'
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar usuario: ' . $e->getMessage()
    ]);
}
