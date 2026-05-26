<?php
session_start();
require_once __DIR__ . '/../config/monar_database.php';

// Solo procesar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validar que los campos no estén vacíos
if (empty($email) || empty($password)) {
  $_SESSION['login_error'] = 'Por favor, completa todos los campos.';
  header('Location: index.php');
  exit;
}

try {
  $pdo = getDBConnection();
  
  // Buscar el usuario por correo electrónico
  $stmt = $pdo->prepare('SELECT id, nombre_usuario, correo_electronico, contraseña_hash FROM usuario WHERE correo_electronico = :email');
  $stmt->execute(['email' => $email]);
  $user = $stmt->fetch();
  
  // Verificar si el usuario existe y la contraseña es correcta
  if ($user && password_verify($password, $user['contraseña_hash'])) {
    // Login exitoso: guardar información del usuario en la sesión
    $_SESSION['user'] = [
      'id' => $user['id'],
      'email' => $user['correo_electronico'],
      'nombre_usuario' => $user['nombre_usuario']
    ];
    
    // Redirigir al mapa
    header('Location: map.php');
    exit;
  } else {
    // Credenciales incorrectas
    $_SESSION['login_error'] = 'Correo electrónico o contraseña incorrectos.';
    header('Location: index.php');
    exit;
  }
} catch (PDOException $e) {
  // Error en la base de datos
  $_SESSION['login_error'] = 'Error al conectar con la base de datos. Inténtalo más tarde.';
  header('Location: index.php');
  exit;
}
