<?php
session_start();
header('Content-Type: application/json');

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
    
    // Obtener estadísticas del usuario
    $stats = [];
    
    // Países visitados
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM visita_pais WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $stats['paises_visitados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total reseñas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM resena WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $stats['total_resenas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total fotos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM publicacion WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $stats['total_fotos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total likes recibidos en sus fotos
    $stmt = $pdo->prepare("
        SELECT COUNT(l.id) as total
        FROM likes l
        INNER JOIN publicacion p ON l.publicacion_id = p.id
        WHERE p.usuario_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);
    $stats['total_likes_recibidos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Seguidores y seguidos
    $stmt = $pdo->prepare("
        SELECT
            SUM(CASE WHEN seguido_id  = :uid1 THEN 1 ELSE 0 END) AS total_seguidores,
            SUM(CASE WHEN seguidor_id = :uid2 THEN 1 ELSE 0 END) AS total_siguiendo
        FROM usuario_seguidor
        WHERE seguido_id = :uid3 OR seguidor_id = :uid4
    ");
    $stmt->execute(['uid1' => $userId, 'uid2' => $userId, 'uid3' => $userId, 'uid4' => $userId]);
    $ft = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    $stats['total_seguidores'] = (int) ($ft['total_seguidores'] ?? 0);
    $stats['total_siguiendo']  = (int) ($ft['total_siguiendo']  ?? 0);

    $stmt = $pdo->prepare("
        SELECT u.id, u.nombre_usuario, u.correo_electronico AS email, us.fecha_creacion
        FROM usuario_seguidor us
        INNER JOIN usuario u ON u.id = us.seguido_id
        WHERE us.seguidor_id = :user_id
        ORDER BY us.fecha_creacion DESC, u.nombre_usuario ASC
        LIMIT 24
    ");
    $stmt->execute(['user_id' => $userId]);
    $stats['usuarios_siguiendo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT u.id, u.nombre_usuario, u.correo_electronico AS email, us.fecha_creacion
        FROM usuario_seguidor us
        INNER JOIN usuario u ON u.id = us.seguidor_id
        WHERE us.seguido_id = :user_id
        ORDER BY us.fecha_creacion DESC, u.nombre_usuario ASC
        LIMIT 24
    ");
    $stmt->execute(['user_id' => $userId]);
    $stats['seguidores'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}
