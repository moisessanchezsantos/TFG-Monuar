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
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    
    // Validaciones
    if (empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }
    
    if (strlen($username) < 3 || strlen($username) > 50) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario debe tener entre 3 y 50 caracteres']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Formato de correo electrónico inválido']);
        exit;
    }
    
    // Verificar que el nombre de usuario no esté en uso por otro usuario
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE nombre_usuario = :username AND id != :id");
    $stmt->execute([
        'username' => $username,
        'id' => $_SESSION['user']['id']
    ]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso']);
        exit;
    }
    
    // Verificar que el email no esté en uso por otro usuario
    $stmt = $pdo->prepare("SELECT id FROM usuario WHERE correo_electronico = :email AND id != :id");
    $stmt->execute([
        'email' => $email,
        'id' => $_SESSION['user']['id']
    ]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El correo electrónico ya está en uso']);
        exit;
    }
    
    // Actualizar información
    $stmt = $pdo->prepare("UPDATE usuario SET nombre_usuario = :username, correo_electronico = :email WHERE id = :id");
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'id' => $_SESSION['user']['id']
    ]);
    
    // Actualizar sesión
    $_SESSION['user']['nombre_usuario'] = $username;
    $_SESSION['user']['correo_electronico'] = $email;
    
    echo json_encode([
        'success' => true,
        'message' => 'Perfil actualizado correctamente'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar el perfil: ' . $e->getMessage()
    ]);
}
