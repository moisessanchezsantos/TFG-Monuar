<?php
/**
 * Script rápido para crear un usuario de prueba
 * 
 * Ejecutar desde terminal:
 * php create_test_user.php
 */

require_once __DIR__ . '/../../config/monar_database.php';

echo "═══════════════════════════════════════\n";
echo "  Crear Usuario de Prueba - MONAR\n";
echo "═══════════════════════════════════════\n\n";

try {
    $pdo = getDBConnection();
    
    // Datos del usuario de prueba
    $usuarios = [
        [
            'nombre_usuario' => 'admin',
            'email' => 'admin@demo.com',
            'password' => '1234',
            'biografia' => 'Usuario administrador de prueba'
        ],
        [
            'nombre_usuario' => 'usuario_test',
            'email' => 'test@example.com',
            'password' => '1234',
            'biografia' => 'Usuario de prueba para testing'
        ]
    ];
    
    foreach ($usuarios as $userData) {
        // Verificar si el usuario ya existe
        $stmt = $pdo->prepare('SELECT id FROM usuario WHERE correo_electronico = :email');
        $stmt->execute(['email' => $userData['email']]);
        
        if ($stmt->fetch()) {
            echo "⏭️  Usuario '{$userData['nombre_usuario']}' ({$userData['email']}) ya existe\n";
            continue;
        }
        
        // Crear el hash de la contraseña
        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        // Insertar el usuario
        $stmt = $pdo->prepare('
            INSERT INTO usuario (nombre_usuario, correo_electronico, contraseña_hash, biografia, fecha_registro) 
            VALUES (:nombre_usuario, :email, :password_hash, :biografia, NOW())
        ');
        
        $stmt->execute([
            'nombre_usuario' => $userData['nombre_usuario'],
            'email' => $userData['email'],
            'password_hash' => $passwordHash,
            'biografia' => $userData['biografia']
        ]);
        
        echo "✅ Usuario '{$userData['nombre_usuario']}' creado exitosamente\n";
        echo "   📧 Email: {$userData['email']}\n";
        echo "   🔑 Contraseña: {$userData['password']}\n\n";
    }
    
    echo "═══════════════════════════════════════\n";
    echo "✨ Proceso completado\n";
    echo "═══════════════════════════════════════\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
