<?php
session_start();
require_once __DIR__ . '/../config/monar_database.php';

// Solo procesar si es una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombreUsuario = trim($_POST['nombre_usuario'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $passwordConfirm = $_POST['password_confirm'] ?? '';

  // Validaciones
  $errors = [];

  if (empty($nombreUsuario)) {
    $errors[] = 'El nombre de usuario es obligatorio.';
  } elseif (strlen($nombreUsuario) < 3) {
    $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres.';
  }

  if (empty($email)) {
    $errors[] = 'El correo electrónico es obligatorio.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El correo electrónico no es válido.';
  }

  if (empty($password)) {
    $errors[] = 'La contraseña es obligatoria.';
  } elseif (strlen($password) < 4) {
    $errors[] = 'La contraseña debe tener al menos 4 caracteres.';
  }

  if ($password !== $passwordConfirm) {
    $errors[] = 'Las contraseñas no coinciden.';
  }

  if (empty($errors)) {
    try {
      $pdo = getDBConnection();

      // Verificar si el correo ya existe
      $stmt = $pdo->prepare('SELECT id FROM usuario WHERE correo_electronico = :email');
      $stmt->execute(['email' => $email]);
      
      if ($stmt->fetch()) {
        $errors[] = 'Este correo electrónico ya está registrado.';
      } else {
        // Crear el usuario
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare('
          INSERT INTO usuario (nombre_usuario, correo_electronico, contraseña_hash, fecha_registro) 
          VALUES (:nombre_usuario, :email, :password_hash, NOW())
        ');
        
        $stmt->execute([
          'nombre_usuario' => $nombreUsuario,
          'email' => $email,
          'password_hash' => $passwordHash
        ]);

        // Registro exitoso
        $_SESSION['register_success'] = 'Registro exitoso. Ya puedes iniciar sesión.';
        header('Location: index.php');
        exit;
      }
    } catch (PDOException $e) {
      $errors[] = 'Error al conectar con la base de datos.';
    }
  }

  $_SESSION['register_errors'] = $errors;
  $_SESSION['register_data'] = [
    'nombre_usuario' => $nombreUsuario,
    'email' => $email
  ];
  header('Location: register_form.php');
  exit;
}

// Si no es POST, redirigir al formulario
header('Location: register_form.php');
exit;
