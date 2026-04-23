<?php
session_start();

// Simular sesión
$_SESSION['user'] = ['id' => 8, 'nombre_usuario' => 'usuario_test'];

// Simular parámetro GET (vacío para modo normal)
$_GET['view_user'] = null;

$currentUser = $_SESSION['user'];
$viewUserId = isset($_GET['view_user']) ? intval($_GET['view_user']) : 0;
$isViewMode = $viewUserId > 0 && $viewUserId != $currentUser['id'];

echo "DEBUG - Estado de la página:\n";
echo "viewUserId: " . $viewUserId . "\n";
echo "isViewMode: " . ($isViewMode ? 'true' : 'false') . "\n";
echo "currentUser ID: " . $currentUser['id'] . "\n";
echo "\n";

if ($isViewMode) {
  echo "❌ Modo LECTURA - Los buscadores NO deberían aparecer\n";
} else {
  echo "✅ Modo NORMAL - Los buscadores DEBERÍAN aparecer\n";
}

echo "\nHTML que se generaría:\n";
if (!$isViewMode) {
  echo "  <input id=\"searchCountry\" /> ✅ Buscador de países\n";
  echo "  <input id=\"searchUser\" /> ✅ Buscador de usuarios\n";
} else {
  echo "  (Sin buscadores - modo lectura)\n";
}
