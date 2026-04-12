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
            <div class="country-panel__header">
                <span class="country-panel__eyebrow">Country Name</span>
                <h2 id="countryName">Selecciona un país</h2>
                <p id="countrySubtitle">Aquí aparecerá la información del país seleccionado.</p>
                <div id="countryInfo" class="country-panel__info">
                  <p>Selecciona un destino para ver su información.</p>
                </div>
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
  <script src="https://unpkg.com/topojson-client@3"></script>
  <script src="map.js"></script>
  <div id="ai-chat-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <button onclick="document.getElementById('ai-window').style.display='flex'" style="width:60px; height:60px; border-radius:50%; background:#4f46e5; color:white; border:none; cursor:pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
        🤖
    </button>
    
    <div id="ai-window" style="display:none; position:absolute; bottom:80px; right:0; width:300px; height:400px; background:white; border-radius:15px; flex-direction:column; box-shadow:0 5px 25px rgba(0,0,0,0.2); overflow:hidden; border:1px solid #ddd;">
        <div style="background:#4f46e5; color:white; padding:10px; text-align:center; font-weight:bold;">Asistente de Viajes</div>
        <div id="ai-messages" style="flex:1; padding:15px; overflow-y:auto; color:#333; font-size:14px;">
            ¡Hola! Pregúntame por un destino.
        </div>
        <div style="padding:10px; border-top:1px solid #eee; display:flex;">
            <input type="text" id="ai-input" placeholder="¿A dónde vamos?" style="flex:1; padding:5px; border:1px solid #ddd; border-radius:5px;">
            <button onclick="preguntarIA()" style="margin-left:5px; background:#4f46e5; color:white; border:none; border-radius:5px; padding:5px 10px; cursor:pointer;">-></button>
        </div>
    </div>
</div>

<script>
async function preguntarIA() {
    const input = document.getElementById('ai-input');
    const box = document.getElementById('ai-messages');
    const msg = input.value;
    if(!msg) return;

    box.innerHTML += `<div style="margin-bottom:10px; text-align:right;"><b>Tú:</b> ${msg}</div>`;
    input.value = '';

    try {
        const res = await fetch('api_bot.php', {
            method: 'POST',
            body: JSON.stringify({ mensaje: msg })
        });
        const data = await res.json();
        if(data.status === 'success') {
            box.innerHTML += `<div style="margin-bottom:10px;"><b>Bot:</b> Te sugiero ${data.pais} <br><img src="${data.datos.flags.svg}" style="width:100px;"></div>`;
        }
    } catch(e) { box.innerHTML += "<div>Error de conexión.</div>"; }
    box.scrollTop = box.scrollHeight;
}
</script>
</body>
</html>