window.addEventListener('DOMContentLoaded', function () {
  const el = document.getElementById('mapGlobe');

  if (!el || typeof Globe === 'undefined') return;

  const globe = Globe()(el)
    .globeImageUrl('https://unpkg.com/three-globe/example/img/earth-night.jpg')
    .bumpImageUrl('https://unpkg.com/three-globe/example/img/earth-topology.png')
    .backgroundColor('rgba(0,0,0,0)')
    .atmosphereColor('#7f5cff')
    .atmosphereAltitude(0.16)
    .showAtmosphere(true);

  const controls = globe.controls();
  controls.autoRotate = true;
  controls.autoRotateSpeed = 0.7;
  controls.enablePan = false;
  controls.minDistance = 140;
  controls.maxDistance = 360;

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
});