<?php
session_start();
$error = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar sesión · MONAR</title>
  <link rel="stylesheet" href="styles.css" />
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
        <!-- OJO: Globe.gl renderiza dentro de un DIV -->
        <div id="globeViz" class="globe__canvas"></div>

        <div class="globe__overlay">
          <div class="globe__badge">Mapa 3D</div>
          <div class="globe__hint">Arrastra para rotar · Scroll para zoom</div>
        </div>
      </div>
    </section>

    <!-- Login -->
    <section class="card" aria-label="Formulario de inicio de sesión">
      <header class="card__header">
        <div class="brand">
          <div class="brand__logo" aria-hidden="true"><small>ΜΔΝ</small></div>
          <div>
            <h1 class="brand__name">MONAR</h1>
            <p class="brand__tagline">Tu web de viajes. Entra y mira tu mundo en 3D.</p>
          </div>
        </div>
      </header>

      <?php if ($error): ?>
        <div class="alert" role="alert">
          <strong>Ups:</strong> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form class="form" method="post" action="login.php" autocomplete="on">
        <div class="field">
          <label class="field__label" for="email">Email</label>
          <div class="field__control">
            <input id="email" name="email" type="email" placeholder="tuemail@ejemplo.com" required autofocus />
          </div>
        </div>

        <div class="field">
          <label class="field__label" for="password">Contraseña</label>
          <div class="field__control">
            <input id="password" name="password" type="password" placeholder="••••••••" required />
          </div>
        </div>

        <div class="row">
          <label class="check">
            <input type="checkbox" name="remember" />
            <span>Recordarme</span>
          </label>
          <a class="link" href="#" onclick="return false;">¿Olvidaste tu contraseña?</a>
        </div>

        <button class="btn" type="submit">
          Entrar
          <span class="btn__icon" aria-hidden="true">→</span>
        </button>

        <p class="hint">
          ¿Aún no tienes cuenta?
          <a class="link link--strong" href="#" onclick="return false;">Crear cuenta</a>
        </p>

        <p class="legal">
          Al continuar aceptas <a class="link" href="#" onclick="return false;">Términos</a> y
          <a class="link" href="#" onclick="return false;">Privacidad</a>.
        </p>
      </form>
    </section>
  </main>

  <!-- Globe.gl (trae Three dentro, cero líos con módulos) -->
  <script src="https://unpkg.com/globe.gl"></script>
  <script src="app.js"></script>
</body>
</html>
