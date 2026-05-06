<?php
session_start();

// Verificar que el usuario esté logueado
if (!isset($_SESSION['user'])) {
  header('Location: index.php');
  exit;
}

$currentUser = $_SESSION['user'];

// Verificar si estamos en modo de visualización de otro usuario
$viewUserId = isset($_GET['view_user']) ? intval($_GET['view_user']) : 0;
$isViewMode = $viewUserId > 0 && $viewUserId != $currentUser['id'];

if ($isViewMode) {
  // Obtener información del usuario que estamos visualizando
  require_once __DIR__ . '/../config/monar_database.php';
  $pdo = getDBConnection();
  $stmt = $pdo->prepare("SELECT id, nombre_usuario FROM usuario WHERE id = :user_id");
  $stmt->execute(['user_id' => $viewUserId]);
  $viewUser = $stmt->fetch(PDO::FETCH_ASSOC);
  
  if (!$viewUser) {
    // Si el usuario no existe, volver al modo normal
    header('Location: map.php');
    exit;
  }
  
  $user = $viewUser; // Usar datos del usuario visualizado
  $displayName = $viewUser['nombre_usuario'];
} else {
  $user = $currentUser;
  $displayName = $currentUser['nombre_usuario'];
  $viewUserId = $currentUser['id'];
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mapa de <?= htmlspecialchars($displayName) ?> · MONAR</title>
  <link rel="stylesheet" href="assets/css/map.css" />
  <link rel="stylesheet" href="assets/css/chatbot.css" />
</head>

<body>
    <div class="bg">
        <div class="bg__blob bg__blob--a"></div>
        <div class="bg__blob bg__blob--b"></div>
        <div class="bg__grid"></div>
    </div>
  <div class="map-page">
    <header class="topbar">
      <div class="topbar__left">
        <div class="brand">
          <div class="brand__logo"><small>ΜΔΝ</small></div>
          <div>
            <h1 class="brand__name">MONAR</h1>
            <p class="brand__tagline"><?= $isViewMode ? 'Explorando el mapa de ' . htmlspecialchars($displayName) : 'Explora tu mapa de viajes' ?></p>
          </div>
        </div>
      </div>

      <?php if (!$isViewMode): ?>
      <div class="search-wrapper search-wrapper--centered">
        <input
          type="text"
          id="searchUser"
          class="topbar__search topbar__search--user"
          placeholder="Buscar usuario..."
          autocomplete="off"
        />
        <div id="userSearchResults" class="search-results search-results--users" style="display: none;"></div>
      </div>
      <?php endif; ?>

      <div class="topbar__right">
        <div class="topbar__user">
          <?php if ($isViewMode): ?>
          <span class="topbar__username" style="background: rgba(92, 184, 92, 0.15); border: 1px solid rgba(92, 184, 92, 0.3);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M13.8 12H3"></path>
            </svg>
            Modo Lectura
          </span>
          <a href="map.php" class="topbar__logout" style="background: rgba(92, 184, 92, 0.15); border: 1px solid rgba(92, 184, 92, 0.3);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M19 12H5M12 19l-7-7 7-7"></path>
            </svg>
            Volver a mi mapa
          </a>
          <?php else: ?>
          <span class="topbar__username">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <?= htmlspecialchars($user['nombre_usuario']) ?>
          </span>
          <a href="logout.php" class="topbar__logout" title="Cerrar sesión">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            Cerrar Sesión
          </a>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <main class="dashboard">
        <section class="dashboard__globe">
            <div class="globe-card">
            <div id="mapGlobe" class="globe-card__canvas"></div>

            <div class="globe-card__overlay">
                <div class="globe-card__badge"><?= $isViewMode ? 'Mapa de ' . htmlspecialchars($displayName) : 'Mapa 3D interactivo' ?></div>
                <div class="globe-card__hint">Arrastra para rotar · Scroll para zoom</div>
            </div>
            </div>

            <section class="visited">
            <div class="visited__header">
                <h2>Países visitados</h2>
                <p><?= $isViewMode ? 'Destinos de ' . htmlspecialchars($displayName) : 'Tus destinos guardados aparecerán aquí' ?></p>
            </div>

            <div class="visited__list" id="visitedList">
                <article class="visited__item">
                    <h3>No hay países visitados todavía</h3>
                    <p>Se cargarán automáticamente</p>
                </article>
            </div>
            </section>
        </section>

        <aside class="dashboard__panel">
            <div class="country-panel">
            <?php if (!$isViewMode): ?>
            <div class="search-wrapper search-wrapper--panel">
              <input
                type="text"
                id="searchCountry"
                class="topbar__search country-panel__search"
                placeholder="Buscar país..."
                autocomplete="off"
              />
              <div id="searchResults" class="search-results" style="display: none;"></div>
            </div>
            <?php endif; ?>
            <div class="country-panel__header">
                <span class="country-panel__eyebrow">Nombre del país</span>
                <h2 id="countryName">Selecciona un país</h2>
                <p id="countrySubtitle">Aquí aparecerá la información del país seleccionado.</p>
                <div id="countryInfo" class="country-panel__info">
                  <p>Selecciona un destino para ver su información.</p>
                </div>
              </div>

            <section class="country-panel__section" id="photosSection">
                <h3>Fotos</h3>
                <div id="photosContainer" class="country-panel__photos-grid">
                <p>Selecciona un país visitado para ver o añadir fotos.</p>
                </div>
            </section>

            <section class="country-panel__section" id="reviewsSection">
                <h3>Reseñas</h3>
                <div id="reviewsContainer" class="country-panel__reviews-list">
                <p>Selecciona un país visitado para ver o añadir reseñas.</p>
                </div>
            </section>

            <div id="actionButtons" style="display: none;">
                <button id="addPhotoBtn" class="country-panel__button" type="button" style="margin-bottom: 10px;">
                    Añadir foto
                </button>
                <button id="addReviewBtn" class="country-panel__button" type="button">
                    Añadir reseña
                </button>
            </div>
            </div>
        </aside>
    </main>
  </div>

  <!-- Modal de perfil de usuario -->
  <div id="userProfileModal" class="user-modal" style="display: none;">
    <div class="user-modal__overlay"></div>
    <div class="user-modal__content">
      <button class="user-modal__close" onclick="closeUserModal()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>

      <div class="user-modal__header">
        <div class="user-modal__avatar">
          <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
          </svg>
        </div>
        <div class="user-modal__info">
          <h2 id="modalUserName">Nombre Usuario</h2>
          <p id="modalUserEmail">email@example.com</p>
          <p id="modalUserDate" class="user-modal__date">Miembro desde: 2026</p>
        </div>
      </div>

      <div class="user-modal__stats">
        <div class="user-modal__stat">
          <span class="user-modal__stat-value" id="modalTotalCountries">0</span>
          <span class="user-modal__stat-label">Países visitados</span>
        </div>
        <div class="user-modal__stat">
          <span class="user-modal__stat-value" id="modalTotalReviews">0</span>
          <span class="user-modal__stat-label">Reseñas</span>
        </div>
        <div class="user-modal__stat">
          <span class="user-modal__stat-value" id="modalTotalContinents">0</span>
          <span class="user-modal__stat-label">Continentes</span>
        </div>
      </div>

      <div style="margin-bottom: 24px; text-align: center;">
        <button id="visitProfileBtn" class="user-modal__visit-button" onclick="visitUserProfile()">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
          </svg>
          Visitar su mapa
        </button>
      </div>

      <div class="user-modal__section">
        <h3>Países visitados</h3>
        <div id="modalCountriesList" class="user-modal__countries">
          <p>Cargando países...</p>
        </div>
      </div>

      <div class="user-modal__section">
        <h3>Reseñas</h3>
        <div id="modalReviewsList" class="user-modal__reviews">
          <p>Cargando reseñas...</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para añadir foto -->
  <div id="addPhotoModal" class="add-modal" style="display: none;">
    <div class="add-modal__overlay" onclick="closeAddPhotoModal()"></div>
    <div class="add-modal__content">
      <button class="add-modal__close" onclick="closeAddPhotoModal()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <h2 class="add-modal__title">Añadir Foto</h2>
      <form id="addPhotoForm" class="add-modal__form" enctype="multipart/form-data">
        <div>
          <label class="add-modal__label">Seleccionar imagen</label>
          <input type="file" id="photoFile" class="add-modal__input" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
          <small style="color: var(--muted); font-size: 12px; margin-top: 4px; display: block;">Formatos: JPG, PNG, GIF, WEBP (máx. 5MB)</small>
        </div>
        <div>
          <label class="add-modal__label">Descripción (opcional)</label>
          <textarea id="photoDescription" class="add-modal__textarea" placeholder="Descripción de la foto"></textarea>
        </div>
        <button type="submit" class="add-modal__submit">Guardar Foto</button>
      </form>
    </div>
  </div>

  <!-- Modal para añadir reseña -->
  <div id="addReviewModal" class="add-modal" style="display: none;">
    <div class="add-modal__overlay" onclick="closeAddReviewModal()"></div>
    <div class="add-modal__content">
      <button class="add-modal__close" onclick="closeAddReviewModal()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <h2 class="add-modal__title">Añadir Reseña</h2>
      <form id="addReviewForm" class="add-modal__form">
        <div>
          <label class="add-modal__label">Título</label>
          <input type="text" id="reviewTitle" class="add-modal__input" placeholder="Título de tu reseña" required maxlength="150">
        </div>
        <div>
          <label class="add-modal__label">Puntuación</label>
          <div class="add-modal__rating" id="ratingStars">
            <span class="add-modal__star" data-rating="1">★</span>
            <span class="add-modal__star" data-rating="2">★</span>
            <span class="add-modal__star" data-rating="3">★</span>
            <span class="add-modal__star" data-rating="4">★</span>
            <span class="add-modal__star" data-rating="5">★</span>
          </div>
          <input type="hidden" id="reviewRating" value="0" required>
        </div>
        <div>
          <label class="add-modal__label">Contenido</label>
          <textarea id="reviewContent" class="add-modal__textarea" placeholder="Cuéntanos sobre tu experiencia en este país" required></textarea>
        </div>
        <button type="submit" class="add-modal__submit">Guardar Reseña</button>
      </form>
    </div>
  </div>

  <script src="https://unpkg.com/globe.gl"></script>
  <script src="https://unpkg.com/topojson-client@3"></script>
  <script>
    // Configuración del modo de visualización
    const VIEW_USER_ID = <?= $viewUserId ?>;
    const IS_VIEW_MODE = <?= $isViewMode ? 'true' : 'false' ?>;
    const CURRENT_USER_ID = <?= $currentUser['id'] ?>;
  </script>
  <script src="assets/js/map.js"></script>
  <script src="assets/js/chatbot.js"></script>
</body>
</html>