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
    $pdo = getDBConnection();
    
    // Obtener datos del POST
    $data = json_decode(file_get_contents('php://input'), true);
    $currentPassword = $data['current_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    
    // Validaciones
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }
    
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'Las contraseñas nuevas no coinciden']);
        exit;
    }
    
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'La nueva contraseña debe tener al menos 6 caracteres']);
        exit;
    }
    
    // Verificar contraseña actual
    $stmt = $pdo->prepare("SELECT contrasena FROM usuario WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($currentPassword, $user['contrasena'])) {
        echo json_encode(['success' => false, 'message' => 'La contraseña actual es incorrecta']);
        exit;
    }
    
    // Actualizar contraseña
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE usuario SET contrasena = :password WHERE id = :id");
    $stmt->execute([
        'password' => $hashedPassword,
        'id' => $_SESSION['user']['id']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Contraseña actualizada correctamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()
    ]);
}
