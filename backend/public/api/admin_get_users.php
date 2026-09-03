<?php
session_start();
header('Content-Type: application/json');

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
    
    // Obtener todos los usuarios con sus estadísticas
    $stmt = $pdo->query("
        SELECT 
            u.id,
            u.nombre_usuario,
            u.correo_electronico,
            u.es_admin,
            u.fecha_registro,
            COUNT(DISTINCT vp.pais_id) as paises_visitados,
            COUNT(DISTINCT r.id) as total_resenas,
            COUNT(DISTINCT p.id) as total_fotos
        FROM usuario u
        LEFT JOIN visita_pais vp ON u.id = vp.usuario_id
        LEFT JOIN resena r ON u.id = r.usuario_id
        LEFT JOIN publicacion p ON u.id = p.usuario_id
        GROUP BY u.id
        ORDER BY u.fecha_registro DESC
    ");
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener usuarios: ' . $e->getMessage()
    ]);
}
