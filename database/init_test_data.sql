-- Script SQL para inicializar datos de prueba
-- Crear un usuario de prueba (contraseña: 1234)
INSERT INTO usuario (nombre_usuario, correo_electronico, contraseña_hash, biografia, fecha_registro)
VALUES 
  ('admin', 'admin@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario administrador de prueba', NOW()),
  ('usuario1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Usuario de prueba 1', NOW());

-- Insertar algunos países de ejemplo
-- Nota: El código ISO debe coincidir con los códigos del mapa world-atlas
INSERT INTO pais (nombre, continente, codigo_iso) VALUES
  ('España', 'Europa', '724'),
  ('Francia', 'Europa', '250'),
  ('Italia', 'Europa', '380'),
  ('Alemania', 'Europa', '276'),
  ('Reino Unido', 'Europa', '826'),
  ('Portugal', 'Europa', '620'),
  ('Japón', 'Asia', '392'),
  ('China', 'Asia', '156'),
  ('India', 'Asia', '356'),
  ('Estados Unidos', 'América del Norte', '840'),
  ('México', 'América del Norte', '484'),
  ('Brasil', 'América del Sur', '076'),
  ('Argentina', 'América del Sur', '032'),
  ('Australia', 'Oceanía', '036'),
  ('Nueva Zelanda', 'Oceanía', '554'),
  ('Egipto', 'África', '818'),
  ('Sudáfrica', 'África', '710'),
  ('Marruecos', 'África', '504'),
  ('Grecia', 'Europa', '300'),
  ('Turquía', 'Asia', '792');

-- Insertar algunas visitas de ejemplo para el usuario admin (id = 1)
-- Asumiendo que el usuario admin tiene id = 1
INSERT INTO visita_pais (usuario_id, pais_id, fecha_visita) VALUES
  (1, 1, '2024-01-15'),  -- España
  (1, 2, '2024-03-20'),  -- Francia
  (1, 7, '2024-06-10');  -- Japón
