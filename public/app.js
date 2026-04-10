window.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('globeViz');

  if (!el) {
    console.error('No se encontró #globeViz');
    return;
  }

  const width = el.clientWidth || 700;
  const height = el.clientHeight || 700;

  console.log('globeViz encontrado', { width, height });

  const globe = Globe()(el)
    .width(width)
    .height(height)
    .globeImageUrl('https://unpkg.com/three-globe/example/img/earth-blue-marble.jpg')
    .backgroundColor('rgba(0,0,0,0)');

  const controls = globe.controls();
  controls.autoRotate = true;
  controls.autoRotateSpeed = 0.5;
  controls.enableDamping = true;
  controls.minDistance = 180;
  controls.maxDistance = 500;

  window.addEventListener('resize', () => {
    globe
      .width(el.clientWidth || 700)
      .height(el.clientHeight || 700);
  });

  console.log('Globo cargado correctamente');
});

fetch('http://127.0.0.1:8000/api/destinos')
  .then(response => response.json())
  .then(data => {
    console.log('DATOS DESDE SYMFONY:', data);
  })
  .catch(error => {
    console.error('ERROR FETCH DESTINOS:', error);
});