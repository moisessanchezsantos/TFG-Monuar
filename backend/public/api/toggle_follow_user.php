<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

require_once __DIR__ . '/../../config/monar_database.php';

try {
    $pdo = getDBConnection();
    ensureUserFollowTable($pdo);

    $currentUserId = (int) $_SESSION['user']['id'];
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $targetUserId = isset($data['target_user_id']) ? (int) $data['target_user_id'] : 0;

    if ($targetUserId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Usuario objetivo no válido']);
        exit;
    }

    if ($targetUserId === $currentUserId) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No puedes seguirte a ti mismo']);
        exit;
    }

    // Verificar que el usuario destino existe
    $stmt = $pdo->prepare('SELECT id FROM usuario WHERE id = :user_id');
    $stmt->execute(['user_id' => $targetUserId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }

    // Comprobar si ya existe la relación
    $stmt = $pdo->prepare('
        SELECT id FROM usuario_seguidor
        WHERE seguidor_id = :seguidor_id AND seguido_id = :seguido_id
    ');
    $stmt->execute(['seguidor_id' => $currentUserId, 'seguido_id' => $targetUserId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare('
            DELETE FROM usuario_seguidor
            WHERE seguidor_id = :seguidor_id AND seguido_id = :seguido_id
        ');
        $stmt->execute(['seguidor_id' => $currentUserId, 'seguido_id' => $targetUserId]);
        $isFollowing = false;
    } else {
        $stmt = $pdo->prepare('
            INSERT INTO usuario_seguidor (seguidor_id, seguido_id)
            VALUES (:seguidor_id, :seguido_id)
        ');
        $stmt->execute(['seguidor_id' => $currentUserId, 'seguido_id' => $targetUserId]);
        $isFollowing = true;
    }

    // Devolver contadores actualizados
    $stmt = $pdo->prepare('
        SELECT
            SUM(CASE WHEN seguido_id  = :uid1 THEN 1 ELSE 0 END) AS followers_count,
            SUM(CASE WHEN seguidor_id = :uid2 THEN 1 ELSE 0 END) AS following_count
        FROM usuario_seguidor
        WHERE seguido_id = :uid3 OR seguidor_id = :uid4
    ');
    $stmt->execute(['uid1' => $targetUserId, 'uid2' => $targetUserId, 'uid3' => $targetUserId, 'uid4' => $targetUserId]);
    $counts = $stmt->fetch() ?: [];

    echo json_encode([
        'success' => true,
        'data' => [
            'is_following'    => $isFollowing,
            'followers_count' => (int) ($counts['followers_count'] ?? 0),
            'following_count' => (int) ($counts['following_count'] ?? 0),
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar el seguimiento']);
}
