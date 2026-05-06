<?php
session_start();
$errors = $_SESSION['register_errors'] ?? [];
$data = $_SESSION['register_data'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_data']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro · MONAR</title>
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>

<body>
  <div class="bg">
    <div class="bg__blob bg__blob--a"></div>
    <div class="bg__blob bg__blob--b"></div>
    <div class="bg__grid"></div>
  </div>

  <main class="layout">
    <!-- Globo 3D -->
    <section class="globe" aria-label="Globo 3D">
      <div class="globe__frame">
        <div id="globeViz" class="globe__canvas"></div>

        <div class="globe__overlay">
          <div class="globe__badge">Mapa 3D</div>
          <div class="globe__hint">Arrastra para rotar · Scroll para zoom</div>
        </div>
      </div>
    </section>

    <!-- Formulario de Registro -->
    <section class="card" aria-label="Formulario de registro">
      <header class="card__header">
        <div class="brand">
          <div class="brand__logo" aria-hidden="true"><small>ΜΔΝ</small></div>
          <div>
            <h1 class="brand__name">MONAR</h1>
            <p class="brand__tagline">Crea tu cuenta y empieza a explorar el mundo.</p>
          </div>
        </div>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert" role="alert" style="margin-bottom: 20px;">
          <strong>Errores:</strong>
          <ul style="margin: 10px 0 0 20px; padding: 0;">
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="card__form" method="POST" action="register.php">
        <div class="form-group">
          <label for="nombre_usuario" class="form-label">Nombre de usuario</label>
          <input
            type="text"
            id="nombre_usuario"
            name="nombre_usuario"
            class="form-input"
            placeholder="Tu nombre de usuario"
            value="<?= htmlspecialchars($data['nombre_usuario'] ?? '') ?>"
            required
          />
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Correo electrónico</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="tu@correo.com"
            value="<?= htmlspecialchars($data['email'] ?? '') ?>"
            required
          />
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Contraseña</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            placeholder="Mínimo 4 caracteres"
            required
          />
        </div>

        <div class="form-group">
          <label for="password_confirm" class="form-label">Confirmar contraseña</label>
          <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            class="form-input"
            placeholder="Repite tu contraseña"
            required
          />
        </div>

        <button type="submit" class="card__button">
          Crear cuenta
        </button>

        <p style="text-align: center; margin-top: 15px; color: #a0a0b0;">
          ¿Ya tienes cuenta? <a href="index.php" style="color: #7f5cff; text-decoration: none;">Inicia sesión</a>
        </p>
      </form>
    </section>
  </main>

  <script src="https://unpkg.com/globe.gl"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
