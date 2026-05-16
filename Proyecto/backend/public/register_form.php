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
  <link rel="stylesheet" href="assets/css/chatbot.css" />
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
        <div class="alert" role="alert">
          <strong>⚠️ Ups, hay algunos errores:</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="card__form" method="POST" action="register.php">
        <div class="form-group">
          <label for="nombre_usuario" class="form-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            Nombre de usuario
          </label>
          <input
            type="text"
            id="nombre_usuario"
            name="nombre_usuario"
            class="form-input"
            placeholder="Elige tu nombre de usuario"
            value="<?= htmlspecialchars($data['nombre_usuario'] ?? '') ?>"
            required
            autocomplete="username"
          />
        </div>

        <div class="form-group">
          <label for="email" class="form-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
              <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            Correo electrónico
          </label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="tu@correo.com"
            value="<?= htmlspecialchars($data['email'] ?? '') ?>"
            required
            autocomplete="email"
          />
        </div>

        <div class="form-group">
          <label for="password" class="form-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Contraseña
          </label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            placeholder="Mínimo 4 caracteres"
            required
            autocomplete="new-password"
          />
          <div id="passwordStrength" style="display: none; margin-top: 6px; padding: 6px 10px; border-radius: 8px; font-size: 12px; text-align: center; transition: all 0.3s ease;"></div>
        </div>

        <div class="form-group">
          <label for="password_confirm" class="form-label">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 4px;">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
              <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Confirmar contraseña
          </label>
          <input
            type="password"
            id="password_confirm"
            name="password_confirm"
            class="form-input"
            placeholder="Repite tu contraseña"
            required
            autocomplete="new-password"
          />
        </div>

        <button type="submit" class="card__button">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 6px;">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <line x1="20" y1="8" x2="20" y2="14"></line>
            <line x1="23" y1="11" x2="17" y2="11"></line>
          </svg>
          Crear cuenta
        </button>

        <p class="hint" style="margin-top: 8px;">
          ¿Ya tienes cuenta? <a href="index.php" class="link link--strong">Inicia sesión aquí</a>
        </p>
      </form>
    </section>
  </main>

  <script src="https://unpkg.com/globe.gl"></script>
  <script src="assets/js/app.js"></script>
  <script src="assets/js/chatbot.js"></script>
  <script>
    // Validación en tiempo real de contraseñas
    const password = document.getElementById('password');
    const passwordConfirm = document.getElementById('password_confirm');
    const passwordStrength = document.getElementById('passwordStrength');
    const submitBtn = document.querySelector('.card__button');

    // Indicador de fortaleza de contraseña
    function checkPasswordStrength() {
      const pwd = password.value;
      
      if (pwd.length === 0) {
        passwordStrength.style.display = 'none';
        return;
      }

      passwordStrength.style.display = 'block';
      let strength = 0;
      let message = '';
      let color = '';

      if (pwd.length >= 4) strength++;
      if (pwd.length >= 8) strength++;
      if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
      if (/\d/.test(pwd)) strength++;
      if (/[^a-zA-Z0-9]/.test(pwd)) strength++;

      if (strength <= 2) {
        message = '🔴 Contraseña débil';
        color = 'rgba(255, 90, 115, 0.15)';
        passwordStrength.style.color = 'rgba(255, 170, 180, 0.95)';
      } else if (strength <= 3) {
        message = '🟡 Contraseña media';
        color = 'rgba(255, 193, 7, 0.15)';
        passwordStrength.style.color = 'rgba(255, 220, 120, 0.95)';
      } else {
        message = '🟢 Contraseña fuerte';
        color = 'rgba(76, 175, 80, 0.15)';
        passwordStrength.style.color = 'rgba(150, 255, 150, 0.95)';
      }

      passwordStrength.textContent = message;
      passwordStrength.style.background = color;
    }

    function validatePasswords() {
      if (passwordConfirm.value === '') {
        passwordConfirm.style.borderColor = 'rgba(255,255,255,.16)';
        passwordConfirm.style.boxShadow = 'none';
        return;
      }

      if (password.value === passwordConfirm.value) {
        passwordConfirm.style.borderColor = 'rgba(76, 175, 80, 0.6)';
        passwordConfirm.style.boxShadow = '0 0 0 4px rgba(76, 175, 80, 0.12)';
      } else {
        passwordConfirm.style.borderColor = 'rgba(255, 90, 115, 0.6)';
        passwordConfirm.style.boxShadow = '0 0 0 4px rgba(255, 90, 115, 0.12)';
      }
    }

    password.addEventListener('input', function() {
      checkPasswordStrength();
      validatePasswords();
    });
    passwordConfirm.addEventListener('input', validatePasswords);
    passwordConfirm.addEventListener('blur', validatePasswords);

    // Animación del botón al hacer submit
    document.querySelector('.card__form').addEventListener('submit', function(e) {
      if (password.value !== passwordConfirm.value) {
        e.preventDefault();
        passwordConfirm.focus();
        
        // Shake animation
        passwordConfirm.style.animation = 'shake 0.3s';
        setTimeout(() => {
          passwordConfirm.style.animation = '';
        }, 300);
        return;
      }

      submitBtn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite;"><circle cx="12" cy="12" r="10"></circle></svg> Creando cuenta...';
      submitBtn.disabled = true;
    });

    // Añadir animaciones al CSS
    const style = document.createElement('style');
    style.textContent = `
      @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
      }
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>
