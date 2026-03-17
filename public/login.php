<?php
session_start();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$demoUserEmail = 'admin@demo.com';
$demoUserPass  = '1234';

if ($email === $demoUserEmail && $password === $demoUserPass) {
  $_SESSION['user'] = [
    'email' => $email,
    'role' => 'admin',
  ];
  header('Location: ok.php');
  exit;
}

$_SESSION['login_error'] = 'Credenciales incorrectas. Prueba admin@demo.com / 1234';
header('Location: index.php');
exit;
