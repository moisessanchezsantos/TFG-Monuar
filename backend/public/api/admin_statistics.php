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
    
    // Obtener estadísticas
    $stats = [];
    
    // Total usuarios
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM usuario");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total países visitados (total de visitas)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM visita_pais");
    $stats['total_visits'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total reseñas
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM resena");
    $stats['total_reviews'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total fotos
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM publicacion");
    $stats['total_photos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
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
