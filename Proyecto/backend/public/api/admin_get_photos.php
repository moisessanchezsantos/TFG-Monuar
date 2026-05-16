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
    
    // Obtener todas las fotos
    $stmt = $pdo->query("
        SELECT 
            pub.id,
            pub.descripcion,
            pub.imagen_url,
            pub.fecha_publicacion,
            u.nombre_usuario,
            p.nombre as pais_nombre,
            COUNT(l.id) as total_likes
        FROM publicacion pub
        INNER JOIN usuario u ON pub.usuario_id = u.id
        INNER JOIN pais p ON pub.pais_id = p.id
        LEFT JOIN likes l ON pub.id = l.publicacion_id
        GROUP BY pub.id
        ORDER BY pub.fecha_publicacion DESC
    ");
    
    $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear fechas
    foreach ($photos as &$photo) {
        $date = new DateTime($photo['fecha_publicacion']);
        $photo['fecha_formateada'] = $date->format('d/m/Y');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $photos
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener fotos: ' . $e->getMessage()
    ]);
}
