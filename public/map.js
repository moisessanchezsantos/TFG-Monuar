window.addEventListener('DOMContentLoaded', function () {
  let selectedCountry = null;
  
  const el = document.getElementById('mapGlobe');

  if (!el || typeof Globe === 'undefined') return;

  const countryName = document.getElementById('countryName');
  const countrySubtitle = document.getElementById('countrySubtitle');
  const countryInfo = document.getElementById('countryInfo');

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

  Promise.all([
    fetch('http://127.0.0.1:8000/api/destinos').then(res => res.json()),
    fetch('https://unpkg.com/world-atlas@2/countries-110m.json').then(res => res.json())
  ])
    .then(([destinos, worldData]) => {
      const countries = topojson.feature(worldData, worldData.objects.countries).features;

      const visitedList = document.getElementById('visitedList');
        if (visitedList) {
          const visitados = destinos.filter(destino => destino.visitado);

          if (visitados.length > 0) {
            visitedList.innerHTML = '';

            visitados.forEach(destino => {
              const item = document.createElement('article');
              item.className = 'visited__item';
              item.dataset.pais = destino.pais;
              item.style.cursor = 'pointer';

              item.innerHTML = `
                <h3>${destino.pais}</h3>
                <p>Visitado</p>
              `;

              item.addEventListener('click', () => {
                marcarPaisActivo(destino.pais);
                const feature = countries.find(
                  country => destinosPorPais[country.properties.name]?.pais === destino.pais
                );
                if (!feature) return;
                selectedCountry = feature;
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
                if (countryName) {
                  countryName.textContent = destino.nombre;
                }
                if (countrySubtitle) {
                  countrySubtitle.textContent = `País: ${destino.pais}`;
                }
                if (countryInfo) {
                  countryInfo.innerHTML = `
                    <div><strong>Clima:</strong> ${destino.clima}</div>
                    <div><strong>Mejor época:</strong> ${destino.mejor_epoca}</div>
                    <div><strong>Descripción:</strong> ${destino.descripcion}</div>
                    <div><strong>Visitado:</strong> ${destino.visitado ? 'Sí' : 'No'}</div>
                  `;
                }
                globe.polygonsData([...countries]);
              });
              visitedList.appendChild(item);
            });
          } else {
            visitedList.innerHTML = `
              <article class="visited__item">
                <h3>No hay países visitados todavía</h3>
                <p>Aún no se han añadido destinos</p>
              </article>
            `;
          }
        }

      const destinosPorPais = {
        France: {
          nombre: 'Francia',
          pais: 'Francia',
          clima: 'Templado',
          mejor_epoca: 'Primavera',
          descripcion: 'País conocido por su patrimonio, gastronomía y ciudades históricas.',
          visitado: false
        },
        Japan: {
          nombre: 'Japón',
          pais: 'Japón',
          clima: 'Húmedo subtropical',
          mejor_epoca: 'Primavera y otoño',
          descripcion: 'País moderno y tradicional a la vez, con gran riqueza cultural y tecnológica.',
          visitado: false
        },
        Spain: {
          nombre: 'España',
          pais: 'España',
          clima: 'Mediterráneo',
          mejor_epoca: 'Primavera y otoño',
          descripcion: 'País con gran diversidad cultural, gastronómica y paisajística.',
          visitado: false
        }
      };

      destinos.forEach(destino => {
        if (destino.pais === 'Francia') {
          destinosPorPais.France = destino;
        }
        if (destino.pais === 'Japón') {
          destinosPorPais.Japan = destino;
        }
        if (destino.pais === 'España') {
          destinosPorPais.Spain = destino;
        }
      });

      globe
        .polygonsData(countries)
        .polygonCapColor(feature => {
          const country = destinosPorPais[feature.properties.name];

          if (feature === selectedCountry) {
            return '#d1d1d1'; // seleccionado
          }

          if (country) {
            return country.visitado ? '#4c4a7a' : '#2a2f3f';
          }

          return '#2a2f3f';
        })
        .polygonSideColor(() => 'rgba(0, 0, 0, 0.04)')
        .polygonStrokeColor(() => 'rgba(255,255,255,0.35)')
        .polygonAltitude(feature => {
          const country = destinosPorPais[feature.properties.name];
          return country && country.visitado ? 0.02 : 0.01;
        })
        .polygonsTransitionDuration(300)
        .onPolygonClick(feature => {
          const country = destinosPorPais[feature.properties.name];
          
          selectedCountry = feature; // ✔ AQUÍ sí

          globe.polygonsData([...countries]);

          if (country) {
            marcarPaisActivo(country.pais);
          }
          if (!country) {
            if (countryName) {
              countryName.textContent = feature.properties.name;
            }

            if (countrySubtitle) {
              countrySubtitle.textContent = 'País no visitado o sin información adicional.';
            }

            if (countryInfo) {
              countryInfo.innerHTML = `
                <div><strong>Estado:</strong> No visitado</div>
              `;
            }
          } else {
            if (countryName) {
              countryName.textContent = country.nombre;
            }

            if (countrySubtitle) {
              countrySubtitle.textContent = `País: ${country.pais}`;
            }

            if (countryInfo) {
              countryInfo.innerHTML = `
                <div><strong>Clima:</strong> ${country.clima}</div>
                <div><strong>Mejor época:</strong> ${country.mejor_epoca}</div>
                <div><strong>Descripción:</strong> ${country.descripcion}</div>
                <div><strong>Visitado:</strong> ${country.visitado ? 'Sí' : 'No'}</div>
              `;
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