window.addEventListener('DOMContentLoaded', function () {
  // Variables de configuración (definidas en map.php)
  const viewUserId = typeof VIEW_USER_ID !== 'undefined' ? VIEW_USER_ID : null;
  const isViewMode = typeof IS_VIEW_MODE !== 'undefined' ? IS_VIEW_MODE : false;
  
  let selectedCountry = null;
  let allCountries = []; // Array para almacenar todos los países
  let allCountriesData = []; // Array para almacenar las features del mapa
  let visitedByIso = {}; // Objeto para países visitados
  
  // Mapeo de códigos numéricos ISO 3166-1 a códigos alpha-2 para casos especiales
  const numericToAlpha2 = {
    '010': 'AQ', // Antártida
    '304': 'GL'  // Groenlandia
  };
  
  // Función helper para obtener el código ISO alpha-2 desde el feature
  function getCountryIsoCode(feature) {
    const numericCode = String(feature.id).padStart(3, '0');
    // Si existe un mapeo explícito, usarlo
    if (numericToAlpha2[numericCode]) {
      return numericToAlpha2[numericCode];
    }
    // Si no, usar el ID del feature (que para la mayoría de países es el alpha-2)
    return feature.id;
  }
  
  const el = document.getElementById('mapGlobe');

  if (!el || typeof Globe === 'undefined') return;

  const countryName = document.getElementById('countryName');
  const countrySubtitle = document.getElementById('countrySubtitle');
  const countryInfo = document.getElementById('countryInfo');
  const searchInput = document.getElementById('searchCountry');
  const searchResults = document.getElementById('searchResults');

  const globe = Globe()(el)
    .globeImageUrl('//unpkg.com/three-globe/example/img/earth-blue-marble.jpg')
    .backgroundColor('rgba(0,0,0,0)')
    .showAtmosphere(true)
    .atmosphereColor('#7f5cff')
    .atmosphereAltitude(0.12);

  const controls = globe.controls();
  controls.autoRotate = true;
  controls.autoRotateSpeed = 0.6;
  controls.enablePan = false;
  controls.minDistance = 140;
  controls.maxDistance = 320;

  function resizeGlobe() {
    globe.width(el.clientWidth);
    globe.height(el.clientHeight);
  }

  window.addEventListener('resize', resizeGlobe);
  resizeGlobe();

  globe.pointOfView(
    { lat: 20, lng: 0, altitude: 1.8 },
    0
  );

  function marcarPaisActivo(nombrePais) {
    const items = document.querySelectorAll('.visited__item');

    items.forEach(item => {
      item.classList.remove('visited__item--active');

      const paisItem = item.dataset.pais?.trim().toLowerCase();
      const paisClick = nombrePais?.trim().toLowerCase();

      if (paisItem === paisClick) {
        item.classList.add('visited__item--active');
      }
    });
  }

  // Función para navegar a un país en el mapa
  function navigateToCountry(countryIso, countryNombre) {
    // Esperar a que allCountriesData esté cargado
    if (!allCountriesData || allCountriesData.length === 0) {
      console.warn('Esperando a que se cargue el mapa...');
      setTimeout(() => navigateToCountry(countryIso, countryNombre), 500);
      return;
    }

    const feature = allCountriesData.find(c => c.id === countryIso);
    if (!feature) {
      console.warn('País no encontrado en el mapa:', countryIso);
      return;
    }

    selectedCountry = feature;
    
    // Actualizar el mapa
    globe.polygonsData([...allCountriesData]);

    const center = getFeatureCenter(feature);
    if (center) {
      controls.autoRotate = false;
      globe.pointOfView(
        {
          lat: center.lat,
          lng: center.lng,
          altitude: 1.2
        },
        1200
      );
    }

    marcarPaisActivo(countryNombre);

    // Actualizar panel de información
    const country = visitedByIso[countryIso];
    if (country) {
      if (countryName) countryName.textContent = country.nombre;
      if (countrySubtitle) countrySubtitle.textContent = `Continente: ${country.continente}`;
      
      const fecha = new Date(country.fecha_visita);
      const fechaFormateada = fecha.toLocaleDateString('es-ES');

      if (countryInfo) {
        countryInfo.innerHTML = `
          <div><strong>País:</strong> ${country.nombre}</div>
          <div><strong>Continente:</strong> ${country.continente}</div>
          <div><strong>Código ISO:</strong> ${country.codigo_iso}</div>
          <div><strong>Fecha de visita:</strong> ${fechaFormateada}</div>
          ${!isViewMode ? `<button class="country-panel__button" style="background: #d9534f; margin-top: 10px;" onclick="removeCountryFromVisited('${countryIso}')">
            Eliminar de visitados
          </button>` : ''}
        `;
      }
      
      // Mostrar secciones de fotos y reseñas
      const photosSection = document.getElementById('photosSection');
      const reviewsSection = document.getElementById('reviewsSection');
      if (photosSection) photosSection.style.display = 'block';
      if (reviewsSection) reviewsSection.style.display = 'block';
      
      // Cargar fotos y reseñas del país visitado
      loadCountryPhotos(countryIso);
      loadCountryReviews(countryIso);
      
      // Mostrar botones de añadir (solo si no está en modo visualización)
      if (!isViewMode) {
        const actionButtons = document.getElementById('actionButtons');
        if (actionButtons) {
          actionButtons.style.display = 'flex';
          // Actualizar atributos data para los botones
          const addPhotoBtn = document.getElementById('addPhotoBtn');
          const addReviewBtn = document.getElementById('addReviewBtn');
          if (addPhotoBtn) addPhotoBtn.dataset.countryIso = countryIso;
          if (addReviewBtn) addReviewBtn.dataset.countryIso = countryIso;
        }
      }
    } else {
      // Buscar info del país en allCountries
      const paisInfo = allCountries.find(p => p.codigo_iso === countryIso);
      if (paisInfo) {
        if (countryName) countryName.textContent = paisInfo.nombre;
        if (countrySubtitle) countrySubtitle.textContent = `Continente: ${paisInfo.continente}`;
        if (countryInfo) {
          countryInfo.innerHTML = `
            <div><strong>Estado:</strong> ${isViewMode ? 'No visitado por este usuario' : 'No visitado'}</div>
            <div><strong>Código ISO:</strong> ${countryIso}</div>
            ${!isViewMode ? `<button class="country-panel__button" onclick="addCountryToVisited('${countryIso}')">
              Marcar como visitado
            </button>` : ''}
          `;
        }
      }
      
      // Limpiar fotos y reseñas
      const photosContainer = document.getElementById('photosContainer');
      const reviewsContainer = document.getElementById('reviewsContainer');
      const photosSection = document.getElementById('photosSection');
      const reviewsSection = document.getElementById('reviewsSection');
      
      if (isViewMode) {
        // En modo visualización, ocultar secciones de fotos y reseñas si no está visitado
        if (photosSection) photosSection.style.display = 'none';
        if (reviewsSection) reviewsSection.style.display = 'none';
      } else {
        // En tu propio mapa, mostrar mensaje para añadir contenido
        if (photosSection) photosSection.style.display = 'block';
        if (reviewsSection) reviewsSection.style.display = 'block';
        if (photosContainer) photosContainer.innerHTML = '<p>Selecciona un país visitado para ver o añadir fotos.</p>';
        if (reviewsContainer) reviewsContainer.innerHTML = '<p>Selecciona un país visitado para ver o añadir reseñas.</p>';
      }
      
      // Ocultar botones de añadir
      const actionButtons = document.getElementById('actionButtons');
      if (actionButtons) actionButtons.style.display = 'none';
    }
  }

  // Funcionalidad de búsqueda
  if (searchInput && searchResults) {
    // Cargar todos los países de la base de datos
    fetch('api/get_countries.php')
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          allCountries = data.data;
        }
      })
      .catch(err => console.error('Error al cargar países:', err));

    // Evento de búsqueda en tiempo real
    searchInput.addEventListener('input', function(e) {
      const query = e.target.value.trim().toLowerCase();
      
      if (query.length === 0) {
        searchResults.style.display = 'none';
        return;
      }

      // Filtrar países que coincidan con la búsqueda
      const filtered = allCountries.filter(country => 
        country.nombre.toLowerCase().includes(query) ||
        country.continente.toLowerCase().includes(query)
      ).slice(0, 10); // Limitar a 10 resultados

      if (filtered.length === 0) {
        searchResults.innerHTML = '<div class="search-results__empty">No se encontraron países</div>';
        searchResults.style.display = 'block';
        return;
      }

      searchResults.innerHTML = filtered.map(country => `
        <div class="search-results__item" data-iso="${country.codigo_iso}" data-nombre="${country.nombre}">
          <span class="search-results__name">${country.nombre}</span>
          <span class="search-results__continent">${country.continente}</span>
        </div>
      `).join('');

      searchResults.style.display = 'block';

      // Añadir eventos click a los resultados
      searchResults.querySelectorAll('.search-results__item').forEach(item => {
        item.addEventListener('click', function() {
          const iso = this.dataset.iso;
          const nombre = this.dataset.nombre;
          
          // Cerrar el dropdown inmediatamente
          searchResults.style.display = 'none';
          searchInput.value = nombre;
          
          // Navegar al país
          navigateToCountry(iso, nombre);
        });
      });
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', function(e) {
      if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
      }
    });

    // Mostrar resultados al hacer focus en el input si hay texto
    searchInput.addEventListener('focus', function() {
      if (this.value.trim().length > 0) {
        searchInput.dispatchEvent(new Event('input'));
      }
    });
  }

  Promise.all([
    fetch(viewUserId ? `api/get_visited_countries.php?user_id=${viewUserId}` : 'api/get_visited_countries.php').then(res => res.json()),
    fetch('https://unpkg.com/world-atlas@2/countries-110m.json').then(res => res.json())
  ])
    .then(([response, worldData]) => {
      const visitedCountries = response.success ? response.data : [];
      const countries = topojson.feature(worldData, worldData.objects.countries).features;

      // Guardar para uso global
      allCountriesData = countries;

      // Crear un objeto para búsqueda rápida de países visitados por código ISO
      visitedByIso = {};
      visitedCountries.forEach(country => {
        visitedByIso[country.codigo_iso] = country;
      });

      const visitedList = document.getElementById('visitedList');
        if (visitedList) {
          if (visitedCountries.length > 0) {
            visitedList.innerHTML = '';

            visitedCountries.forEach(destino => {
              const item = document.createElement('article');
              item.className = 'visited__item';
              item.dataset.pais = destino.nombre;
              item.dataset.iso = destino.codigo_iso;
              item.style.cursor = 'pointer';

              const fecha = new Date(destino.fecha_visita);
              const fechaFormateada = fecha.toLocaleDateString('es-ES');

              item.innerHTML = `
                <h3>${destino.nombre}</h3>
                <p>Visitado el ${fechaFormateada}</p>
              `;

              item.addEventListener('click', () => {
                navigateToCountry(destino.codigo_iso, destino.nombre);
              });
              visitedList.appendChild(item);
            });
            
            // Actualizar botones de navegación después de cargar países
            setTimeout(() => {
              if (window.updateCarouselNavButtons) {
                window.updateCarouselNavButtons();
              }
            }, 150);
          } else {
            visitedList.innerHTML = `
              <article class="visited__item">
                <h3>No hay países visitados todavía</h3>
                <p>Haz clic en un país del mapa para añadirlo</p>
              </article>
            `;
            
            // Actualizar botones de navegación para lista vacía
            setTimeout(() => {
              if (window.updateCarouselNavButtons) {
                window.updateCarouselNavButtons();
              }
            }, 150);
          }
        }

      globe
        .polygonsData(countries)
        .polygonCapColor(feature => {
          const countryIso = getCountryIsoCode(feature);
          const isVisited = visitedByIso[countryIso];

          if (feature === selectedCountry) {
            return '#d1d1d1'; // seleccionado
          }

          if (isVisited) {
            return '#4c4a7a'; // visitado
          }

          return '#2a2f3f'; // no visitado
        })
        .polygonSideColor(() => 'rgba(0, 0, 0, 0.04)')
        .polygonStrokeColor(() => 'rgba(255,255,255,0.35)')
        .polygonAltitude(feature => {
          const countryIso = getCountryIsoCode(feature);
          const isVisited = visitedByIso[countryIso];
          return isVisited ? 0.02 : 0.01;
        })
        .polygonsTransitionDuration(300)
        .onPolygonClick(feature => {
          const countryIso = getCountryIsoCode(feature);
          const country = visitedByIso[countryIso];
          
          selectedCountry = feature;
          globe.polygonsData([...countries]);

          if (country) {
            marcarPaisActivo(country.nombre);
          }
          
          if (!country) {
            if (countryName) {
              countryName.textContent = feature.properties.name || 'País desconocido';
            }

            if (countrySubtitle) {
              countrySubtitle.textContent = 'País no visitado';
            }

            if (countryInfo) {
              countryInfo.innerHTML = `
                <div><strong>Estado:</strong> ${isViewMode ? 'No visitado por este usuario' : 'No visitado'}</div>
                <div><strong>Código ISO:</strong> ${countryIso}</div>
                ${!isViewMode ? `<button class="country-panel__button" onclick="addCountryToVisited('${countryIso}')">
                  Marcar como visitado
                </button>` : ''}
              `;
            }
            
            // Limpiar fotos y reseñas
            const photosContainer = document.getElementById('photosContainer');
            const reviewsContainer = document.getElementById('reviewsContainer');
            const photosSection = document.getElementById('photosSection');
            const reviewsSection = document.getElementById('reviewsSection');
            
            if (isViewMode) {
              // En modo visualización, ocultar secciones si no está visitado
              if (photosSection) photosSection.style.display = 'none';
              if (reviewsSection) reviewsSection.style.display = 'none';
            } else {
              // En tu propio mapa, mostrar mensaje
              if (photosSection) photosSection.style.display = 'block';
              if (reviewsSection) reviewsSection.style.display = 'block';
              if (photosContainer) photosContainer.innerHTML = '<p>Selecciona un país visitado para ver o añadir fotos.</p>';
              if (reviewsContainer) reviewsContainer.innerHTML = '<p>Selecciona un país visitado para ver o añadir reseñas.</p>';
            }
            
            // Ocultar botones de añadir
            const actionButtons = document.getElementById('actionButtons');
            if (actionButtons) actionButtons.style.display = 'none';
          } else {
            if (countryName) {
              countryName.textContent = country.nombre;
            }

            if (countrySubtitle) {
              countrySubtitle.textContent = `Continente: ${country.continente}`;
            }

            const fecha = new Date(country.fecha_visita);
            const fechaFormateada = fecha.toLocaleDateString('es-ES');

            if (countryInfo) {
              countryInfo.innerHTML = `
                <div><strong>País:</strong> ${country.nombre}</div>
                <div><strong>Continente:</strong> ${country.continente}</div>
                <div><strong>Código ISO:</strong> ${country.codigo_iso}</div>
                <div><strong>Fecha de visita:</strong> ${fechaFormateada}</div>
                ${!isViewMode ? `<button class="country-panel__button" style="background: #d9534f; margin-top: 10px;" onclick="removeCountryFromVisited('${countryIso}')">
                  Eliminar de visitados
                </button>` : ''}
              `;
            }
            
            // Mostrar secciones de fotos y reseñas
            const photosSection = document.getElementById('photosSection');
            const reviewsSection = document.getElementById('reviewsSection');
            if (photosSection) photosSection.style.display = 'block';
            if (reviewsSection) reviewsSection.style.display = 'block';
            
            // Cargar fotos y reseñas del país visitado
            loadCountryPhotos(countryIso);
            loadCountryReviews(countryIso);
            
            // Mostrar botones de añadir (solo si no está en modo visualización)
            if (!isViewMode) {
              const actionButtons = document.getElementById('actionButtons');
              if (actionButtons) {
                actionButtons.style.display = 'flex';
                // Actualizar atributos data para los botones
                const addPhotoBtn = document.getElementById('addPhotoBtn');
                const addReviewBtn = document.getElementById('addReviewBtn');
                if (addPhotoBtn) addPhotoBtn.dataset.countryIso = countryIso;
                if (addReviewBtn) addReviewBtn.dataset.countryIso = countryIso;
              }
            }
          }

          const center = getFeatureCenter(feature);

          if (center) {
            controls.autoRotate = false;

            globe.pointOfView(
              {
                lat: center.lat,
                lng: center.lng,
                altitude: 1.2
              },
              1200
            );
          }
        });
    })
    .catch(error => {
      console.error('ERROR AL CARGAR DATOS DEL MAPA:', error);
    });

  function getFeatureCenter(feature) {
    try {
      const coords = feature.geometry.coordinates;

      let flatCoords = [];

      if (feature.geometry.type === 'Polygon') {
        flatCoords = coords[0];
      } else if (feature.geometry.type === 'MultiPolygon') {
        coords.forEach(polygon => {
          flatCoords = flatCoords.concat(polygon[0]);
        });
      }

      if (!flatCoords.length) return null;

      let sumLng = 0;
      let sumLat = 0;

      flatCoords.forEach(coord => {
        sumLng += coord[0];
        sumLat += coord[1];
      });

      return {
        lng: sumLng / flatCoords.length,
        lat: sumLat / flatCoords.length
      };
    } catch (e) {
      return null;
    }
  }
});

// Funciones globales para agregar y eliminar países visitados
window.addCountryToVisited = async function(isoCode) {
  try {
    const response = await fetch('api/add_visited_country.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ pais_id: isoCode })
    });

    const result = await response.json();

    if (result.success) {
      alert('País añadido a tus visitados. Recargando mapa...');
      location.reload();
    } else {
      alert('Error: ' + (result.error || 'No se pudo añadir el país'));
    }
  } catch (error) {
    console.error('Error al añadir país:', error);
    alert('Error al conectar con el servidor');
  }
};

window.removeCountryFromVisited = async function(isoCode) {
  if (!confirm('¿Estás seguro de que quieres eliminar este país de tus visitados?')) {
    return;
  }

  try {
    const response = await fetch('api/remove_visited_country.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ pais_id: isoCode })
    });

    const result = await response.json();

    if (result.success) {
      alert('País eliminado de tus visitados. Recargando mapa...');
      location.reload();
    } else {
      alert('Error: ' + (result.error || 'No se pudo eliminar el país'));
    }
  } catch (error) {
    console.error('Error al eliminar país:', error);
    alert('Error al conectar con el servidor');
  }
};

// ==========================================
// BÚSQUEDA DE USUARIOS Y MODAL DE PERFIL
// ==========================================

// Variables para el buscador de usuarios
const searchUserInput = document.getElementById('searchUser');
const userSearchResults = document.getElementById('userSearchResults');
let searchUserTimeout = null;
let currentViewingUserId = null; // ID del usuario cuyo perfil se está visualizando
let currentViewingFollowsYou = false;

// Función para buscar usuarios
async function searchUsers(query) {
  if (!query || query.length < 2) {
    userSearchResults.style.display = 'none';
    return;
  }

  try {
    const response = await fetch(`api/search_users.php?q=${encodeURIComponent(query)}`);
    const result = await response.json();

    if (result.success && result.data.length > 0) {
      displayUserSearchResults(result.data);
    } else {
      userSearchResults.innerHTML = '<div class="search-results__empty">No se encontraron usuarios</div>';
      userSearchResults.style.display = 'block';
    }
  } catch (error) {
    console.error('Error al buscar usuarios:', error);
    userSearchResults.style.display = 'none';
  }
}

// Función para mostrar resultados de búsqueda de usuarios
function displayUserSearchResults(users) {
  userSearchResults.innerHTML = users.map(user => `
    <div class="search-results__item" data-user-id="${user.id}">
      <div style="flex: 1;">
        <div class="search-results__name">${user.nombre_usuario}</div>
        <div style="display:flex;align-items:center;gap:8px;">
          <div class="search-results__continent">${user.email}</div>
          ${Number(user.is_following) ? '<span class="search-results__follow-badge">Siguiendo</span>' : ''}
        </div>
      </div>
    </div>
  `).join('');

  userSearchResults.style.display = 'block';

  // Agregar eventos click a los resultados
  userSearchResults.querySelectorAll('.search-results__item').forEach(item => {
    item.addEventListener('click', function() {
      const userId = this.dataset.userId;
      userSearchResults.style.display = 'none';
      searchUserInput.value = '';
      openUserProfile(userId);
    });
  });
}

// Event listener para búsqueda de usuarios
if (searchUserInput) {
  searchUserInput.addEventListener('input', function(e) {
    clearTimeout(searchUserTimeout);
    const query = e.target.value.trim();

    if (query.length < 2) {
      userSearchResults.style.display = 'none';
      return;
    }

    searchUserTimeout = setTimeout(() => {
      searchUsers(query);
    }, 300);
  });

  // Cerrar resultados al hacer clic fuera
  document.addEventListener('click', function(e) {
    if (!searchUserInput.contains(e.target) && !userSearchResults.contains(e.target)) {
      userSearchResults.style.display = 'none';
    }
  });
}

// Función para abrir el modal con el perfil del usuario
async function openUserProfile(userId) {
  const modal = document.getElementById('userProfileModal');
  modal.style.display = 'flex';

  // Mostrar loading
  document.getElementById('modalUserName').textContent = 'Cargando...';
  document.getElementById('modalUserEmail').textContent = '';
  document.getElementById('modalUserDate').textContent = '';
  document.getElementById('modalTotalCountries').textContent = '0';
  document.getElementById('modalTotalReviews').textContent = '0';
  document.getElementById('modalTotalContinents').textContent = '0';
  // Resetear barra de seguimiento
  const followBtn = document.getElementById('modalFollowBtn');
  if (followBtn) { followBtn.disabled = true; followBtn.textContent = 'Cargando...'; }
  const followStatus = document.getElementById('modalFollowStatus');
  if (followStatus) followStatus.textContent = 'Cargando...';
  const followMeta = document.getElementById('modalFollowMeta');
  if (followMeta) followMeta.textContent = '0 seguidores · 0 seguidos';
  document.getElementById('modalCountriesList').innerHTML = '<p class="user-modal__empty">Cargando países...</p>';
  document.getElementById('modalReviewsList').innerHTML = '<p class="user-modal__empty">Cargando reseñas...</p>';

  try {
    const response = await fetch(`api/get_user_profile.php?user_id=${userId}`);
    const result = await response.json();

    if (result.success) {
      displayUserProfile(result.data);
    } else {
      alert('Error al cargar el perfil del usuario');
      modal.style.display = 'none';
    }
  } catch (error) {
    console.error('Error al cargar perfil:', error);
    alert('Error al conectar con el servidor');
    modal.style.display = 'none';
  }
}

// Función para mostrar el perfil del usuario en el modal
function displayUserProfile(data) {
  const { user, visited_countries, reviews, stats } = data;

  // Guardar el ID del usuario actual
  currentViewingUserId = user.id;
  currentViewingFollowsYou = Boolean(Number(user.follows_you));

  // Actualizar barra de seguimiento
  const isFollowing = Boolean(Number(user.is_following));
  updateFollowBar({
    isFollowing,
    followsYou: currentViewingFollowsYou,
    followersCount: Number(stats.followers_count || 0),
    followingCount: Number(stats.following_count || 0),
  });
  document.getElementById('modalUserName').textContent = user.nombre_usuario;
  document.getElementById('modalUserEmail').textContent = user.email;
  
  const fechaRegistro = new Date(user.fecha_registro);
  document.getElementById('modalUserDate').textContent = `Miembro desde: ${fechaRegistro.toLocaleDateString('es-ES', { year: 'numeric', month: 'long' })}`;

  // Estadísticas
  document.getElementById('modalTotalCountries').textContent = stats.total_paises;
  document.getElementById('modalTotalReviews').textContent = stats.total_resenas;
  document.getElementById('modalTotalContinents').textContent = Object.keys(stats.continentes).length;

  // Países visitados
  const countriesList = document.getElementById('modalCountriesList');
  if (visited_countries.length > 0) {
    countriesList.innerHTML = visited_countries.map(country => {
      const fecha = new Date(country.fecha_visita);
      return `
        <div class="user-modal__country-item" data-iso="${country.codigo_iso}">
          <div class="user-modal__country-name">${country.nombre}</div>
          <div class="user-modal__country-date">${fecha.toLocaleDateString('es-ES')}</div>
        </div>
      `;
    }).join('');

    // Agregar evento click para navegar al país en el mapa
    countriesList.querySelectorAll('.user-modal__country-item').forEach(item => {
      item.addEventListener('click', function() {
        const iso = this.dataset.iso;
        const nombre = this.querySelector('.user-modal__country-name').textContent;
        closeUserModal();
        navigateToCountry(iso, nombre);
      });
    });
  } else {
    countriesList.innerHTML = '<p class="user-modal__empty">Este usuario no ha visitado ningún país todavía.</p>';
  }

  // Reseñas
  const reviewsList = document.getElementById('modalReviewsList');
  if (reviews.length > 0) {
    reviewsList.innerHTML = reviews.map(review => {
      const fecha = new Date(review.fecha_creacion);
      const stars = '⭐'.repeat(Math.round(review.puntuacion));
      return `
        <div class="user-modal__review">
          <div class="user-modal__review-header">
            <div>
              <div class="user-modal__review-title">${review.titulo}</div>
              <div class="user-modal__review-country">${review.pais_nombre}</div>
            </div>
            <div class="user-modal__review-rating">${stars}</div>
          </div>
          <div class="user-modal__review-content">${review.contenido}</div>
          <div class="user-modal__review-date">${fecha.toLocaleDateString('es-ES')}</div>
        </div>
      `;
    }).join('');
  } else {
    reviewsList.innerHTML = '<p class="user-modal__empty">Este usuario no ha escrito ninguna reseña todavía.</p>';
  }
}

// Función para cerrar el modal
window.closeUserModal = function() {
  const modal = document.getElementById('userProfileModal');
  modal.style.display = 'none';
};

function updateFollowBar({ isFollowing, followsYou, followersCount, followingCount }) {
  const btn    = document.getElementById('modalFollowBtn');
  const status = document.getElementById('modalFollowStatus');
  const meta   = document.getElementById('modalFollowMeta');
  if (!btn || !status || !meta) return;

  btn.disabled = false;
  btn.textContent = isFollowing ? 'Dejar de seguir' : 'Seguir';
  btn.classList.toggle('user-modal__follow-btn--active', isFollowing);

  status.textContent = isFollowing ? 'Ya sigues a este usuario' : 'Aún no sigues a este usuario';

  const parts = [`${followersCount} seguidores`, `${followingCount} seguidos`];
  if (followsYou) parts.push('Te sigue');
  meta.textContent = parts.join(' · ');
}

window.toggleFollowCurrentUser = async function() {
  if (!currentViewingUserId) return;
  const btn = document.getElementById('modalFollowBtn');
  const wasFollowing = btn.textContent.trim() === 'Dejar de seguir';
  btn.disabled = true;
  btn.textContent = wasFollowing ? 'Actualizando...' : 'Siguiendo...';

  try {
    const response = await fetch('api/toggle_follow_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ target_user_id: currentViewingUserId })
    });
    const result = await response.json();
    if (!result.success) {
      alert(result.message || 'No se pudo actualizar el seguimiento');
      btn.disabled = false;
      btn.textContent = wasFollowing ? 'Dejar de seguir' : 'Seguir';
      return;
    }
    updateFollowBar({
      isFollowing:    Boolean(result.data.is_following),
      followsYou:     currentViewingFollowsYou,
      followersCount: Number(result.data.followers_count || 0),
      followingCount: Number(result.data.following_count || 0),
    });
  } catch (error) {
    console.error('Error al actualizar seguimiento:', error);
    alert('Error al conectar con el servidor');
    btn.disabled = false;
    btn.textContent = wasFollowing ? 'Dejar de seguir' : 'Seguir';
  }
};

// Cerrar modal al hacer clic en el overlay
document.querySelector('.user-modal__overlay')?.addEventListener('click', closeUserModal);

// Cerrar modal con tecla ESC
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeUserModal();
  }
});

// Función para visitar el mapa del usuario
window.visitUserProfile = function() {
  if (!currentViewingUserId) {
    alert('Error: No se ha seleccionado ningún usuario');
    return;
  }
  
  // Redirigir a map.php con parámetro view_user
  window.location.href = `map.php?view_user=${currentViewingUserId}`;
};

// ==================== FUNCIONES PARA FOTOS Y RESEÑAS ====================

// Función para cargar fotos del país
async function loadCountryPhotos(countryIso) {
  const photosContainer = document.getElementById('photosContainer');
  if (!photosContainer) return;
  
  // Guardar el countryIso en el contenedor para uso posterior
  photosContainer.dataset.countryIso = countryIso;
  
  photosContainer.innerHTML = '<p>Cargando fotos...</p>';
  
  try {
    // Si estamos en modo visualización, cargar fotos del usuario visualizado
    const userParam = IS_VIEW_MODE ? `&user_id=${VIEW_USER_ID}` : '';
    const response = await fetch(`api/get_country_photos.php?codigo_iso=${countryIso}${userParam}`);
    const result = await response.json();
    
    if (result.success && result.data.length > 0) {
      photosContainer.innerHTML = result.data.map(photo => {
        // Verificar si la foto pertenece al usuario actual
        const isOwner = photo.usuario_id == CURRENT_USER_ID;
        const deleteButton = isOwner && !IS_VIEW_MODE ? `
          <button class="country-panel__photo-delete" 
                  onclick="deleteOwnPhoto(${photo.id}, event)"
                  title="Eliminar foto">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
          </button>
        ` : '';
        
        return `
        <div class="country-panel__photo-item" data-photo-id="${photo.id}">
          <img src="${photo.imagen_url}" 
               alt="${photo.descripcion || 'Foto del país'}" 
               title="${photo.descripcion || ''}" 
               loading="lazy"
               class="country-panel__photo-img"
               onclick="openPhotoViewer('${photo.imagen_url}', '${(photo.descripcion || '').replace(/'/g, "\\'")}', ${photo.total_likes}, ${photo.id})">
          <div class="country-panel__photo-actions">
            <button class="country-panel__photo-like ${photo.user_liked ? 'liked' : ''}" 
                    onclick="togglePhotoLike(${photo.id}, event)"
                    title="${photo.user_liked ? 'Quitar me gusta' : 'Me gusta'}">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="${photo.user_liked ? 'currentColor' : 'none'}" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
              <span class="country-panel__photo-like-count">${photo.total_likes}</span>
            </button>
            ${deleteButton}
          </div>
        </div>
        `;
      }).join('');
    } else {
      // Mensaje diferente según si estás viendo tu mapa o el de otro usuario
      const emptyMessage = IS_VIEW_MODE 
        ? '<p style="color: var(--muted); font-style: italic;">Este usuario aún no ha compartido fotos de este país</p>'
        : '<p>No hay fotos añadidas. ¡Sé el primero en añadir una!</p>';
      photosContainer.innerHTML = emptyMessage;
    }
  } catch (error) {
    console.error('Error al cargar fotos:', error);
    photosContainer.innerHTML = '<p>Error al cargar las fotos.</p>';
  }
}

// Función para abrir el visor de fotos ampliado
window.openPhotoViewer = function(imageUrl, description, totalLikes, photoId) {
  const modal = document.getElementById('photoViewerModal');
  const image = document.getElementById('photoViewerImage');
  const desc = document.getElementById('photoViewerDescription');
  const likesCount = document.getElementById('photoViewerLikesCount');
  
  if (modal && image && desc && likesCount) {
    image.src = imageUrl;
    desc.textContent = description || 'Sin descripción';
    likesCount.textContent = totalLikes;
    modal.dataset.photoId = photoId;
    modal.style.display = 'flex';
  }
};

// Función para cerrar el visor de fotos
window.closePhotoViewer = function() {
  const modal = document.getElementById('photoViewerModal');
  if (modal) {
    modal.style.display = 'none';
  }
};

// Función para dar/quitar like a una foto
window.togglePhotoLike = async function(photoId, event) {
  // Prevenir que se abra el modal de foto al hacer clic en el botón de like
  if (event) {
    event.stopPropagation();
  }
  
  try {
    const response = await fetch('api/toggle_photo_like.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ publicacion_id: photoId })
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Actualizar el botón de like en la galería
      const photoItem = document.querySelector(`[data-photo-id="${photoId}"]`);
      if (photoItem) {
        const likeBtn = photoItem.querySelector('.country-panel__photo-like');
        const likeCount = photoItem.querySelector('.country-panel__photo-like-count');
        const likeSvg = likeBtn.querySelector('svg');
        
        if (result.user_liked) {
          likeBtn.classList.add('liked');
          likeSvg.setAttribute('fill', 'currentColor');
        } else {
          likeBtn.classList.remove('liked');
          likeSvg.setAttribute('fill', 'none');
        }
        
        likeCount.textContent = result.total_likes;
        likeBtn.title = result.user_liked ? 'Quitar me gusta' : 'Me gusta';
      }
      
      // Actualizar el modal si está abierto
      const modal = document.getElementById('photoViewerModal');
      if (modal && modal.style.display === 'flex' && modal.dataset.photoId == photoId) {
        const modalLikesCount = document.getElementById('photoViewerLikesCount');
        if (modalLikesCount) {
          modalLikesCount.textContent = result.total_likes;
        }
      }
    } else {
      alert('Error al procesar like: ' + result.message);
    }
  } catch (error) {
    console.error('Error al procesar like:', error);
    alert('Error al conectar con el servidor');
  }
};

// Función para cargar reseñas del país
async function loadCountryReviews(countryIso) {
  const reviewsContainer = document.getElementById('reviewsContainer');
  if (!reviewsContainer) return;
  
  // Guardar el countryIso en el contenedor para uso posterior
  reviewsContainer.dataset.countryIso = countryIso;
  
  reviewsContainer.innerHTML = '<p>Cargando reseñas...</p>';
  
  try {
    // Si estamos en modo visualización, cargar reseñas del usuario visualizado
    const userParam = IS_VIEW_MODE ? `&user_id=${VIEW_USER_ID}` : '';
    const response = await fetch(`api/get_country_reviews.php?codigo_iso=${countryIso}${userParam}`);
    const result = await response.json();
    
    if (result.success && result.data.length > 0) {
      reviewsContainer.innerHTML = result.data.map(review => {
        // Verificar si la reseña pertenece al usuario actual
        const isOwner = review.usuario_id == CURRENT_USER_ID;
        const deleteButton = isOwner && !IS_VIEW_MODE ? `
          <button class="country-panel__review-delete" 
                  onclick="deleteOwnReview(${review.id}, event)"
                  title="Eliminar reseña">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
          </button>
        ` : '';
        
        return `
        <div class="country-panel__review-item" data-review-id="${review.id}">
          <div class="country-panel__review-header">
            <h4 class="country-panel__review-title">${review.titulo}</h4>
            <div style="display: flex; align-items: center; gap: 8px;">
              <div class="country-panel__review-rating">${'★'.repeat(review.puntuacion)}${'☆'.repeat(5 - review.puntuacion)}</div>
              ${deleteButton}
            </div>
          </div>
          <p class="country-panel__review-content">${review.contenido}</p>
          <div class="country-panel__review-date">${review.fecha_formateada}</div>
        </div>
        `;
      }).join('');
    } else {
      // Mensaje diferente según si estás viendo tu mapa o el de otro usuario
      const emptyMessage = IS_VIEW_MODE 
        ? '<p style="color: var(--muted); font-style: italic;">Este usuario aún no ha escrito reseñas de este país</p>'
        : '<p>No hay reseñas añadidas. ¡Sé el primero en añadir una!</p>';
      reviewsContainer.innerHTML = emptyMessage;
    }
  } catch (error) {
    console.error('Error al cargar reseñas:', error);
    reviewsContainer.innerHTML = '<p>Error al cargar las reseñas.</p>';
  }
}

// Event listeners para los botones de añadir
document.getElementById('addPhotoBtn')?.addEventListener('click', function() {
  const countryIso = this.dataset.countryIso;
  if (countryIso) {
    openAddPhotoModal(countryIso);
  }
});

document.getElementById('addReviewBtn')?.addEventListener('click', function() {
  const countryIso = this.dataset.countryIso;
  if (countryIso) {
    openAddReviewModal(countryIso);
  }
});

// Funciones para abrir/cerrar modal de foto
function openAddPhotoModal(countryIso) {
  const modal = document.getElementById('addPhotoModal');
  if (modal) {
    modal.style.display = 'flex';
    modal.dataset.countryIso = countryIso;
    
    // Limpiar formulario
    document.getElementById('photoFile').value = '';
    document.getElementById('photoDescription').value = '';
  }
}

window.closeAddPhotoModal = function() {
  const modal = document.getElementById('addPhotoModal');
  if (modal) {
    modal.style.display = 'none';
  }
};

// Funciones para abrir/cerrar modal de reseña
function openAddReviewModal(countryIso) {
  const modal = document.getElementById('addReviewModal');
  if (modal) {
    modal.style.display = 'flex';
    modal.dataset.countryIso = countryIso;
    
    // Limpiar formulario
    document.getElementById('reviewTitle').value = '';
    document.getElementById('reviewContent').value = '';
    document.getElementById('reviewRating').value = '0';
    
    // Resetear estrellas
    document.querySelectorAll('.add-modal__star').forEach(star => {
      star.classList.remove('active');
    });
  }
}

window.closeAddReviewModal = function() {
  const modal = document.getElementById('addReviewModal');
  if (modal) {
    modal.style.display = 'none';
  }
};

// Sistema de rating con estrellas
document.querySelectorAll('.add-modal__star').forEach(star => {
  star.addEventListener('click', function() {
    const rating = parseInt(this.dataset.rating);
    document.getElementById('reviewRating').value = rating;
    
    // Actualizar visualización de estrellas
    document.querySelectorAll('.add-modal__star').forEach((s, index) => {
      if (index < rating) {
        s.classList.add('active');
      } else {
        s.classList.remove('active');
      }
    });
  });
  
  // Hover effect
  star.addEventListener('mouseenter', function() {
    const rating = parseInt(this.dataset.rating);
    document.querySelectorAll('.add-modal__star').forEach((s, index) => {
      if (index < rating) {
        s.style.color = '#ffd700';
      }
    });
  });
  
  star.addEventListener('mouseleave', function() {
    const currentRating = parseInt(document.getElementById('reviewRating').value);
    document.querySelectorAll('.add-modal__star').forEach((s, index) => {
      if (index >= currentRating) {
        s.style.color = '';
      }
    });
  });
});

// Manejar envío de formulario de foto
document.getElementById('addPhotoForm')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const modal = document.getElementById('addPhotoModal');
  const countryIso = modal.dataset.countryIso;
  const photoFile = document.getElementById('photoFile').files[0];
  const photoDescription = document.getElementById('photoDescription').value;
  
  if (!photoFile) {
    alert('Por favor, selecciona una imagen');
    return;
  }
  
  // Validar tamaño del archivo (máximo 5MB)
  if (photoFile.size > 5 * 1024 * 1024) {
    alert('La imagen es demasiado grande. Máximo 5MB.');
    return;
  }
  
  const submitBtn = this.querySelector('.add-modal__submit');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Subiendo...';
  
  try {
    // Crear FormData para enviar el archivo
    const formData = new FormData();
    formData.append('codigo_iso', countryIso);
    formData.append('photo', photoFile);
    formData.append('descripcion', photoDescription);
    
    const response = await fetch('api/add_country_photo.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Foto añadida correctamente');
      closeAddPhotoModal();
      loadCountryPhotos(countryIso);
    } else {
      alert('Error al añadir foto: ' + result.message);
    }
  } catch (error) {
    console.error('Error al añadir foto:', error);
    alert('Error al conectar con el servidor');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Guardar Foto';
  }
});

// Manejar envío de formulario de reseña
document.getElementById('addReviewForm')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const modal = document.getElementById('addReviewModal');
  const countryIso = modal.dataset.countryIso;
  const reviewTitle = document.getElementById('reviewTitle').value;
  const reviewContent = document.getElementById('reviewContent').value;
  const reviewRating = parseInt(document.getElementById('reviewRating').value);
  
  if (reviewRating < 1 || reviewRating > 5) {
    alert('Por favor, selecciona una puntuación (1-5 estrellas)');
    return;
  }
  
  const submitBtn = this.querySelector('.add-modal__submit');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Guardando...';
  
  try {
    const response = await fetch('api/add_country_review.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        codigo_iso: countryIso,
        titulo: reviewTitle,
        contenido: reviewContent,
        puntuacion: reviewRating
      })
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert('Reseña añadida correctamente');
      closeAddReviewModal();
      loadCountryReviews(countryIso);
    } else {
      alert('Error al añadir reseña: ' + result.message);
    }
  } catch (error) {
    console.error('Error al añadir reseña:', error);
    alert('Error al conectar con el servidor');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Guardar Reseña';
  }
});

// Cerrar modales con ESC
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeAddPhotoModal();
    closeAddReviewModal();
  }
});

// Función para eliminar foto propia
window.deleteOwnPhoto = async function(photoId, event) {
  if (event) {
    event.stopPropagation();
  }
  
  if (!confirm('¿Estás seguro de que deseas eliminar esta foto? Esta acción no se puede deshacer.')) {
    return;
  }
  
  try {
    const response = await fetch('api/delete_own_photo.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ photo_id: photoId })
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Recargar las fotos del país actual
      const photosContainer = document.getElementById('photosContainer');
      if (photosContainer && photosContainer.dataset.countryIso) {
        loadCountryPhotos(photosContainer.dataset.countryIso);
      }
      
      // Mostrar mensaje de éxito
      showNotification('Foto eliminada correctamente', 'success');
    } else {
      alert('Error al eliminar foto: ' + result.message);
    }
  } catch (error) {
    console.error('Error al eliminar foto:', error);
    alert('Error al conectar con el servidor');
  }
};

// Función para eliminar reseña propia
window.deleteOwnReview = async function(reviewId, event) {
  if (event) {
    event.stopPropagation();
  }
  
  if (!confirm('¿Estás seguro de que deseas eliminar esta reseña? Esta acción no se puede deshacer.')) {
    return;
  }
  
  try {
    const response = await fetch('api/delete_own_review.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ review_id: reviewId })
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Recargar las reseñas del país actual
      const reviewsContainer = document.getElementById('reviewsContainer');
      if (reviewsContainer && reviewsContainer.dataset.countryIso) {
        loadCountryReviews(reviewsContainer.dataset.countryIso);
      }
      
      // Mostrar mensaje de éxito
      showNotification('Reseña eliminada correctamente', 'success');
    } else {
      alert('Error al eliminar reseña: ' + result.message);
    }
  } catch (error) {
    console.error('Error al eliminar reseña:', error);
    alert('Error al conectar con el servidor');
  }
};

// Función para mostrar notificaciones
function showNotification(message, type = 'info') {
  const notification = document.createElement('div');
  notification.className = `notification notification--${type}`;
  notification.textContent = message;
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.opacity = '0';
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, 3000);
}

// Navegación del carrusel de países visitados
const carousel = document.getElementById('visitedList');
const prevBtn = document.getElementById('carouselPrev');
const nextBtn = document.getElementById('carouselNext');

if (carousel && prevBtn && nextBtn) {
  const scrollAmount = 236; // 220px de ancho + 16px de gap
  
  prevBtn.addEventListener('click', () => {
    carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
  });
  
  nextBtn.addEventListener('click', () => {
    carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
  });
  
  // Actualizar visibilidad de botones según posición
  window.updateCarouselNavButtons = function() {
    // Esperar a que el DOM se actualice completamente
    requestAnimationFrame(() => {
      const scrollLeft = carousel.scrollLeft;
      const scrollWidth = carousel.scrollWidth;
      const clientWidth = carousel.clientWidth;
      const maxScroll = scrollWidth - clientWidth;
      
      const atStart = scrollLeft <= 1;
      const atEnd = scrollLeft >= maxScroll - 1;
      
      // Solo deshabilitar si realmente no hay scroll disponible
      const hasScroll = scrollWidth > clientWidth;
      
      if (!hasScroll) {
        // Si no hay scroll, deshabilitar ambos botones
        prevBtn.style.opacity = '0.3';
        prevBtn.style.pointerEvents = 'none';
        nextBtn.style.opacity = '0.3';
        nextBtn.style.pointerEvents = 'none';
      } else {
        // Si hay scroll, habilitar según posición
        prevBtn.style.opacity = atStart ? '0.3' : '1';
        prevBtn.style.pointerEvents = atStart ? 'none' : 'auto';
        
        nextBtn.style.opacity = atEnd ? '0.3' : '1';
        nextBtn.style.pointerEvents = atEnd ? 'none' : 'auto';
      }
    });
  };
  
  carousel.addEventListener('scroll', window.updateCarouselNavButtons);
  
  // Actualización inicial después de cargar la página
  setTimeout(window.updateCarouselNavButtons, 200);
}