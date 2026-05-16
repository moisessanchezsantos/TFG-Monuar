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
    
    // Obtener todas las reseñas
    $stmt = $pdo->query("
        SELECT 
            r.id,
            r.titulo,
            r.contenido,
            r.puntuacion,
            r.fecha_resena,
            u.nombre_usuario,
            p.nombre as pais_nombre
        FROM resena r
        INNER JOIN usuario u ON r.usuario_id = u.id
        INNER JOIN pais p ON r.pais_id = p.id
        ORDER BY r.fecha_resena DESC
    ");
    
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear fechas
    foreach ($reviews as &$review) {
        $date = new DateTime($review['fecha_resena']);
        $review['fecha_formateada'] = $date->format('d/m/Y H:i');
    }
    
    echo json_encode([
        'success' => true,
        'data' => $reviews
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener reseñas: ' . $e->getMessage()
    ]);
}
