<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

require_once __DIR__ . '/../../config/monar_database.php';

// Obtener datos
$user_id = $_SESSION['user']['id'];
$input = json_decode(file_get_contents('php://input'), true);
$publicacion_id = $input['publicacion_id'] ?? null;

if (empty($publicacion_id)) {
    echo json_encode(['success' => false, 'message' => 'ID de publicación requerido']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Verificar si la publicación existe
    $stmt = $pdo->prepare("SELECT id FROM publicacion WHERE id = :publicacion_id");
    $stmt->execute(['publicacion_id' => $publicacion_id]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Publicación no encontrada']);
        exit;
    }
    
    // Verificar si el usuario ya le dio like a esta publicación
    $stmt = $pdo->prepare("
        SELECT id FROM likes 
        WHERE usuario_id = :usuario_id AND publicacion_id = :publicacion_id
    ");
    $stmt->execute([
        'usuario_id' => $user_id,
        'publicacion_id' => $publicacion_id
    ]);
    
    $existing_like = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_like) {
        // Ya le dio like, entonces quitarlo
        $stmt = $pdo->prepare("
            DELETE FROM likes 
            WHERE usuario_id = :usuario_id AND publicacion_id = :publicacion_id
        ");
        $stmt->execute([
            'usuario_id' => $user_id,
            'publicacion_id' => $publicacion_id
        ]);
        
        $action = 'removed';
    } else {
        // No le dio like, entonces agregarlo
        $stmt = $pdo->prepare("
            INSERT INTO likes (usuario_id, publicacion_id, fecha)
            VALUES (:usuario_id, :publicacion_id, NOW())
        ");
        $stmt->execute([
            'usuario_id' => $user_id,
            'publicacion_id' => $publicacion_id
        ]);
        
        $action = 'added';
    }
    
    // Obtener el nuevo total de likes
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM likes 
        WHERE publicacion_id = :publicacion_id
    ");
    $stmt->execute(['publicacion_id' => $publicacion_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_likes = intval($result['total']);
    
    echo json_encode([
        'success' => true,
        'action' => $action,
        'total_likes' => $total_likes,
        'user_liked' => $action === 'added'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar like: ' . $e->getMessage()
    ]);
}
