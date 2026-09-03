<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

$currentUser = $_SESSION['user'];

// Verificar que sea administrador
require_once __DIR__ . '/../config/monar_database.php';
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT es_admin FROM usuario WHERE id = :id");
$stmt->execute(['id' => $currentUser['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !$user['es_admin']) {
  header('Location: map.php');
  exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin · MONAR</title>
  <link rel="stylesheet" href="assets/css/map.css" />
  <link rel="stylesheet" href="assets/css/dashboard-admin.css" />
</head>

<body>
    <div class="bg">
        <div class="bg__blob bg__blob--a"></div>
        <div class="bg__blob bg__blob--b"></div>
        <div class="bg__grid"></div>
    </div>
    
  <div class="admin-dashboard">
    <header class="topbar">
      <div class="topbar__left">
        <div class="brand">
          <div class="brand__logo"><small>ΜΔΝ</small></div>
          <div>
            <h1 class="brand__name">MONAR Admin</h1>
            <p class="brand__tagline">Panel de administración</p>
          </div>
        </div>
      </div>

      <div class="topbar__right">
        <div class="topbar__user">
          <a href="map.php" class="topbar__logout" style="background: rgba(64, 224, 208, 0.15); border: 1px solid rgba(64, 224, 208, 0.3); margin-right: 10px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
            Mi Mapa
          </a>
          <a href="profile.php" class="topbar__username" title="Mi perfil">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
            </svg>
            <?= htmlspecialchars($currentUser['nombre_usuario']) ?>
          </a>
          <a href="logout.php" class="topbar__logout" title="Cerrar sesión">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Cerrar Sesión
          </a>
        </div>
      </div>
    </header>

    <main class="dashboard-content">
      <!-- Estadísticas generales -->
      <section class="stats-grid">
        <div class="stat-card">
          <div class="stat-card__icon" style="background: rgba(127, 92, 255, 0.15);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-card__content">
            <h3 id="totalUsers">-</h3>
            <p>Usuarios Registrados</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__icon" style="background: rgba(64, 224, 208, 0.15);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
          </div>
          <div class="stat-card__content">
            <h3 id="totalVisits">-</h3>
            <p>Países Visitados</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__icon" style="background: rgba(255, 193, 7, 0.15);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
          </div>
          <div class="stat-card__content">
            <h3 id="totalReviews">-</h3>
            <p>Reseñas Publicadas</p>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-card__icon" style="background: rgba(255, 59, 92, 0.15);">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
          </div>
          <div class="stat-card__content">
            <h3 id="totalPhotos">-</h3>
            <p>Fotos Subidas</p>
          </div>
        </div>
      </section>

      <!-- Tabs de navegación -->
      <section class="tabs">
        <button class="tab-btn active" data-tab="users">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
          Usuarios
        </button>
        <button class="tab-btn" data-tab="reviews">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
          </svg>
          Reseñas
        </button>
        <button class="tab-btn" data-tab="photos">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <circle cx="8.5" cy="8.5" r="1.5"></circle>
            <polyline points="21 15 16 10 5 21"></polyline>
          </svg>
          Fotos
        </button>
      </section>

      <!-- Contenido de tabs -->
      <section class="tab-content active" id="users-tab">
        <div class="content-header">
          <h2>Gestión de Usuarios</h2>
          <div class="search-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" id="searchUsers" placeholder="Buscar usuarios...">
          </div>
        </div>
        <div id="usersContainer" class="data-table">
          <p style="text-align: center; padding: 40px; color: var(--muted);">Cargando usuarios...</p>
        </div>
      </section>

      <section class="tab-content" id="reviews-tab">
        <div class="content-header">
          <h2>Gestión de Reseñas</h2>
          <div class="search-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" id="searchReviews" placeholder="Buscar reseñas...">
          </div>
        </div>
        <div id="reviewsContainer" class="data-grid">
          <p style="text-align: center; padding: 40px; color: var(--muted);">Cargando reseñas...</p>
        </div>
      </section>

      <section class="tab-content" id="photos-tab">
        <div class="content-header">
          <h2>Gestión de Fotos</h2>
          <div class="search-box">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" id="searchPhotos" placeholder="Buscar por usuario o país...">
          </div>
        </div>
        <div id="photosContainer" class="photos-grid">
          <p style="text-align: center; padding: 40px; color: var(--muted);">Cargando fotos...</p>
        </div>
      </section>
    </main>
  </div>

  <script src="assets/js/admin-dashboard.js"></script>
</body>
</html>
