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
    ensureUserFollowTable($pdo);
    $userId = $_SESSION['user']['id'];
    
    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    $password = $data['password'] ?? '';
    
    // Validar contraseña
    if (empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Debes proporcionar tu contraseña']);
        exit;
    }
    
    // Verificar contraseña
    $stmt = $pdo->prepare("SELECT contrasena, es_admin FROM usuario WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($password, $user['contrasena'])) {
        echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta']);
        exit;
    }
    
    // No permitir eliminar cuenta de admin
    if ($user['es_admin']) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar una cuenta de administrador']);
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
    
    // Obtener avatar para eliminarlo
    $stmt = $pdo->prepare("SELECT avatar_url FROM usuario WHERE id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $avatar = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
    
    // Eliminar avatar
    if ($avatar && $avatar['avatar_url']) {
        $avatarPath = __DIR__ . '/../' . $avatar['avatar_url'];
        if (file_exists($avatarPath)) {
            @unlink($avatarPath);
        }
    }
    
    // Destruir sesión
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Cuenta eliminada correctamente'
    ]);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar la cuenta: ' . $e->getMessage()
    ]);
}
