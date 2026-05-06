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

// Leer datos JSON del body
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user']['id'];
$pais_codigo_iso = $data['codigo_iso'] ?? '';
$titulo = $data['titulo'] ?? '';
$contenido = $data['contenido'] ?? '';
$puntuacion = intval($data['puntuacion'] ?? 0);

if (empty($pais_codigo_iso) || empty($titulo) || empty($contenido) || $puntuacion < 1 || $puntuacion > 5) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Obtener el ID del país a partir del código ISO
    $stmt = $pdo->prepare("SELECT id FROM pais WHERE codigo_iso = :codigo_iso");
    $stmt->execute(['codigo_iso' => $pais_codigo_iso]);
    $pais = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pais) {
        echo json_encode(['success' => false, 'message' => 'País no encontrado']);
        exit;
    }
    
    $pais_id = $pais['id'];
    
    // Verificar que el usuario ha visitado este país
    $stmt = $pdo->prepare("
        SELECT id FROM visita_pais 
        WHERE usuario_id = :usuario_id AND pais_id = :pais_id
    ");
    $stmt->execute([
        'usuario_id' => $user_id,
        'pais_id' => $pais_id
    ]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Debes haber visitado el país para añadir reseñas']);
        exit;
    }
    
    // Insertar la reseña
    $stmt = $pdo->prepare("
        INSERT INTO resena (titulo, contenido, puntuacion, fecha_resena, usuario_id, pais_id)
        VALUES (:titulo, :contenido, :puntuacion, NOW(), :usuario_id, :pais_id)
    ");
    
    $stmt->execute([
        'titulo' => $titulo,
        'contenido' => $contenido,
        'puntuacion' => $puntuacion,
        'usuario_id' => $user_id,
        'pais_id' => $pais_id
    ]);
    
    $review_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Reseña añadida correctamente',
        'data' => [
            'id' => $review_id,
            'titulo' => $titulo,
            'contenido' => $contenido,
            'puntuacion' => $puntuacion,
            'fecha_formateada' => date('d/m/Y')
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al añadir reseña: ' . $e->getMessage()
    ]);
}
