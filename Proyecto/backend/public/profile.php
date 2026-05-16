<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

// Obtener información actualizada del usuario
require_once __DIR__ . '/../config/monar_database.php';
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT id, nombre_usuario, correo_electronico, avatar_url, fecha_registro FROM usuario WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user']['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  header('Location: logout.php');
  exit;
}

// Actualizar sesión con datos recientes
$_SESSION['user'] = $user;

$userInitial = strtoupper(substr($user['nombre_usuario'], 0, 1));
$avatarUrl = $user['avatar_url'] ?? null;
$registrationDate = date('d/m/Y', strtotime($user['fecha_registro']));
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mi Perfil · MONAR</title>
  <link rel="stylesheet" href="assets/css/map.css" />
  <link rel="stylesheet" href="assets/css/profile.css" />
</head>

<body>
    <div class="bg">
        <div class="bg__blob bg__blob--a"></div>
        <div class="bg__blob bg__blob--b"></div>
        <div class="bg__grid"></div>
    </div>
    
  <div class="profile-page">
    <header class="topbar">
      <div class="topbar__left">
        <div class="brand">
          <div class="brand__logo"><small>ΜΔΝ</small></div>
          <div>
            <h1 class="brand__name">Mi Perfil</h1>
            <p class="brand__tagline">Gestiona tu información personal</p>
          </div>
        </div>
      </div>

      <div class="topbar__right">
        <a href="map.php" class="topbar__back-btn">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7"></path>
          </svg>
          Volver al mapa
        </a>
      </div>
    </header>

    <main class="profile-content">
      <!-- Avatar y información básica -->
      <section class="profile-header">
        <div class="profile-avatar-section">
          <div class="profile-avatar-wrapper">
            <?php if ($avatarUrl): ?>
              <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="Avatar" class="profile-avatar-img" id="currentAvatar">
            <?php else: ?>
              <div class="profile-avatar-placeholder" id="currentAvatar"><?= $userInitial ?></div>
            <?php endif; ?>
            <button class="profile-avatar-edit" id="editAvatarBtn" title="Cambiar foto">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </button>
          </div>
          <input type="file" id="avatarInput" accept="image/jpeg,image/png,image/jpg,image/webp" style="display: none;">
          <h2 class="profile-username"><?= htmlspecialchars($user['nombre_usuario']) ?></h2>
          <p class="profile-email"><?= htmlspecialchars($user['correo_electronico']) ?></p>
          <p class="profile-member-since">Miembro desde <?= $registrationDate ?></p>
        </div>
      </section>

      <!-- Formularios de configuración -->
      <div class="profile-sections">
        
        <!-- Cambiar contraseña -->
        <section class="profile-card">
          <div class="profile-card__header">
            <div>
              <h3 class="profile-card__title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                  <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Cambiar Contraseña
              </h3>
              <p class="profile-card__subtitle">Actualiza tu contraseña para mantener tu cuenta segura</p>
            </div>
          </div>
          <form id="changePasswordForm" class="profile-form">
            <div class="form-group">
              <label for="currentPassword" class="form-label">Contraseña actual</label>
              <input type="password" id="currentPassword" name="currentPassword" class="form-input" required>
            </div>
            <div class="form-group">
              <label for="newPassword" class="form-label">Nueva contraseña</label>
              <input type="password" id="newPassword" name="newPassword" class="form-input" required minlength="6">
              <div id="passwordStrength" class="password-strength"></div>
            </div>
            <div class="form-group">
              <label for="confirmPassword" class="form-label">Confirmar nueva contraseña</label>
              <input type="password" id="confirmPassword" name="confirmPassword" class="form-input" required>
            </div>
            <button type="submit" class="btn btn--primary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              Cambiar contraseña
            </button>
          </form>
        </section>

        <!-- Actualizar información del perfil -->
        <section class="profile-card">
          <div class="profile-card__header">
            <div>
              <h3 class="profile-card__title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Información Personal
              </h3>
              <p class="profile-card__subtitle">Actualiza tu nombre de usuario y correo electrónico</p>
            </div>
          </div>
          <form id="updateProfileForm" class="profile-form">
            <div class="form-group">
              <label for="username" class="form-label">Nombre de usuario</label>
              <input type="text" id="username" name="username" class="form-input" value="<?= htmlspecialchars($user['nombre_usuario']) ?>" required minlength="3" maxlength="50">
            </div>
            <div class="form-group">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($user['correo_electronico']) ?>" required>
            </div>
            <button type="submit" class="btn btn--primary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
              </svg>
              Guardar cambios
            </button>
          </form>
        </section>

        <!-- Estadísticas del usuario -->
        <section class="profile-card">
          <div class="profile-card__header">
            <div>
              <h3 class="profile-card__title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="1" x2="12" y2="23"></line>
                  <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                Mis Estadísticas
              </h3>
              <p class="profile-card__subtitle">Tu actividad en MONAR</p>
            </div>
          </div>
          <div class="profile-stats" id="userStats">
            <p style="text-align: center; padding: 20px; color: var(--muted);">Cargando estadísticas...</p>
          </div>
        </section>

        <!-- Red social -->
        <section class="profile-card profile-card--wide">
          <div class="profile-card__header">
            <div>
              <h3 class="profile-card__title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Mi Red
              </h3>
              <p class="profile-card__subtitle">Consulta a quién sigues y quién sigue tu perfil</p>
            </div>
          </div>
          <div class="profile-social-grid">
            <div class="profile-social-panel">
              <div class="profile-social-panel__header">
                <h4>Usuarios que sigues</h4>
                <span id="followingCountPill" class="profile-social-panel__pill">0</span>
              </div>
              <div id="followingList" class="profile-social-list">
                <p class="profile-social-empty">Cargando...</p>
              </div>
            </div>
            <div class="profile-social-panel">
              <div class="profile-social-panel__header">
                <h4>Usuarios que te siguen</h4>
                <span id="followersCountPill" class="profile-social-panel__pill">0</span>
              </div>
              <div id="followersList" class="profile-social-list">
                <p class="profile-social-empty">Cargando...</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Eliminar cuenta -->
        <section class="profile-card profile-card--danger">
          <div class="profile-card__header">
            <div>
              <h3 class="profile-card__title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                  <line x1="12" y1="9" x2="12" y2="13"></line>
                  <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                Zona Peligrosa
              </h3>
              <p class="profile-card__subtitle">Esta acción no se puede deshacer</p>
            </div>
          </div>
          <div class="profile-form">
            <p style="color: var(--muted); margin-bottom: 16px; line-height: 1.6;">
              Si eliminas tu cuenta, perderás permanentemente todos tus datos: países visitados, reseñas, fotos y toda tu información personal. Esta acción es irreversible.
            </p>
            <button type="button" class="btn btn--danger" id="deleteAccountBtn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
              </svg>
              Eliminar mi cuenta
            </button>
          </div>
        </section>

      </div>
    </main>
  </div>

  <!-- Modal de confirmación para eliminar cuenta -->
  <div id="deleteAccountModal" class="confirm-modal" style="display: none;">
    <div class="confirm-modal__overlay"></div>
    <div class="confirm-modal__content">
      <div class="confirm-modal__icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
          <line x1="12" y1="9" x2="12" y2="13"></line>
          <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
      </div>
      <h3 class="confirm-modal__title">¿Eliminar cuenta permanentemente?</h3>
      <p class="confirm-modal__message">
        Esta acción no se puede deshacer. Todos tus datos serán eliminados permanentemente.
        <br><br>
        Por favor, escribe tu contraseña para confirmar:
      </p>
      <input type="password" id="deletePasswordConfirm" class="form-input" placeholder="Tu contraseña" style="margin-bottom: 20px;">
      <div class="confirm-modal__actions">
        <button class="confirm-modal__btn confirm-modal__btn--cancel" onclick="closeDeleteModal()">Cancelar</button>
        <button class="confirm-modal__btn confirm-modal__btn--confirm" id="confirmDeleteBtn">Eliminar cuenta</button>
      </div>
    </div>
  </div>

  <script src="assets/js/profile.js"></script>
</body>
</html>
