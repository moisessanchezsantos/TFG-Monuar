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

// Obtener datos del formulario
$user_id = $_SESSION['user']['id'];
$pais_codigo_iso = $_POST['codigo_iso'] ?? '';
$descripcion = $_POST['descripcion'] ?? '';

// Validar que se haya enviado un archivo
if (empty($pais_codigo_iso) || !isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o archivo no válido']);
    exit;
}

$file = $_FILES['photo'];

// Validar tipo de archivo
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF y WEBP.']);
    exit;
}

// Validar tamaño (máximo 5MB)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 5MB.']);
    exit;
}

// Crear directorio si no existe
$upload_dir = __DIR__ . '/../uploads/photos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generar nombre único para el archivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('photo_' . $user_id . '_') . '.' . $extension;
$upload_path = $upload_dir . $filename;

// Mover archivo a la carpeta de uploads
if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo']);
    exit;
}

// La URL relativa para guardar en BD y mostrar en el cliente
$imagen_url = 'uploads/photos/' . $filename;

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
        echo json_encode(['success' => false, 'message' => 'Debes haber visitado el país para añadir fotos']);
        exit;
    }
    
    // Insertar la foto (publicación)
    $stmt = $pdo->prepare("
        INSERT INTO publicacion (descripcion, imagen_url, fecha_publicacion, usuario_id, pais_id)
        VALUES (:descripcion, :imagen_url, NOW(), :usuario_id, :pais_id)
    ");
    
    $stmt->execute([
        'descripcion' => $descripcion,
        'imagen_url' => $imagen_url,
        'usuario_id' => $user_id,
        'pais_id' => $pais_id
    ]);
    
    $photo_id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Foto añadida correctamente',
        'data' => [
            'id' => $photo_id,
            'descripcion' => $descripcion,
            'imagen_url' => $imagen_url,
            'fecha_formateada' => date('d/m/Y')
        ]
    ]);
    
} catch (Exception $e) {
    // Si hay error, eliminar el archivo subido
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al añadir foto: ' . $e->getMessage()
    ]);
}
