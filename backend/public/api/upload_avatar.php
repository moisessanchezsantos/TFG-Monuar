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
    // Verificar que se haya subido un archivo
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Error al subir el archivo']);
        exit;
    }
    
    $file = $_FILES['avatar'];
    
    // Validar tamaño (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'El archivo no puede superar 5MB']);
        exit;
    }
    
    // Validar tipo de archivo
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Formato de imagen no válido. Usa JPG, PNG o WEBP']);
        exit;
    }
    
    // Crear directorio de avatares si no existe
    $uploadDir = __DIR__ . '/../uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generar nombre único para el archivo
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $_SESSION['user']['id'] . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // Mover archivo
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo']);
        exit;
    }
    
    // Actualizar base de datos
    $pdo = getDBConnection();
    
    // Obtener avatar anterior para eliminarlo
    $stmt = $pdo->prepare("SELECT avatar_url FROM usuario WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    $oldAvatar = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Actualizar con nuevo avatar
    $avatarUrl = 'uploads/avatars/' . $filename;
    $stmt = $pdo->prepare("UPDATE usuario SET avatar_url = :avatar_url WHERE id = :id");
    $stmt->execute([
        'avatar_url' => $avatarUrl,
        'id' => $_SESSION['user']['id']
    ]);
    
    // Eliminar avatar anterior si existe
    if ($oldAvatar && $oldAvatar['avatar_url']) {
        $oldPath = __DIR__ . '/../' . $oldAvatar['avatar_url'];
        if (file_exists($oldPath)) {
            @unlink($oldPath);
        }
    }
    
    // Actualizar sesión
    $_SESSION['user']['avatar_url'] = $avatarUrl;
    
    echo json_encode([
        'success' => true,
        'message' => 'Avatar actualizado correctamente',
        'avatar_url' => $avatarUrl
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al subir el avatar: ' . $e->getMessage()
    ]);
}
