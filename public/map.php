<?php
session_start();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mapa 3D · MONAR</title>
  <link rel="stylesheet" href="map.css" />
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
            <p class="brand__tagline">Explora tu mapa de viajes</p>
          </div>
        </div>
      </div>

      <div class="topbar__right">
        <input
          type="text"
          class="topbar__search"
          placeholder="Buscar país"
        />

        <select class="topbar__select">
          <option value="">Continente</option>
          <option value="Europe">Europa</option>
          <option value="Asia">Asia</option>
          <option value="Africa">África</option>
          <option value="North America">Norteamérica</option>
          <option value="South America">Sudamérica</option>
          <option value="Oceania">Oceanía</option>
        </select>
      </div>
    </header>

    <main class="dashboard">
        <section class="dashboard__globe">
            <div class="globe-card">
            <div id="mapGlobe" class="globe-card__canvas"></div>

            <div class="globe-card__overlay">
                <div class="globe-card__badge">Mapa 3D interactivo</div>
                <div class="globe-card__hint">Arrastra para rotar · Scroll para zoom</div>
            </div>
            </div>

            <section class="visited">
            <div class="visited__header">
                <h2>Países visitados</h2>
                <p>Tus destinos guardados aparecerán aquí</p>
            </div>

            <div class="visited__list">
                <article class="visited__item">
                <h3>España</h3>
                <p>3 reseñas</p>
                </article>

                <article class="visited__item">
                <h3>Francia</h3>
                <p>1 reseña</p>
                </article>

                <article class="visited__item">
                <h3>Japón</h3>
                <p>5 reseñas</p>
                </article>

                <article class="visited__item">
                <h3>Estados Unidos</h3>
                <p>2 reseñas</p>
                </article>
            </div>
            </section>
        </section>

        <aside class="dashboard__panel">
            <div class="country-panel">
            <div class="country-panel__header">
                <span class="country-panel__eyebrow">Country Name</span>
                <h2 id="countryName">Selecciona un país</h2>
                <p id="countrySubtitle">Aquí aparecerá la información del país seleccionado.</p>
            </div>

            <section class="country-panel__section">
                <h3>Reviews</h3>
                <div class="country-panel__box">
                <p>Las reseñas del país aparecerán aquí.</p>
                </div>
            </section>

            <section class="country-panel__section">
                <h3>Photos</h3>
                <div class="country-panel__photo">
                <span>Imagen del país</span>
                </div>
            </section>

            <button class="country-panel__button" type="button">
                Añadir reseña
            </button>
            </div>
        </aside>
    </main>
  </div>

  <script src="https://unpkg.com/globe.gl"></script>
  <script src="map.js"></script>
</body>
</html>