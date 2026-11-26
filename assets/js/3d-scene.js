/**
 * ScalePixel - Interactive 3D Canvas Scene
 * Uses Three.js for lightweight, interactive floating polyhedra & particle stars
 */

(function () {
  const canvas = document.getElementById('threejs-canvas');
  if (!canvas || typeof THREE === 'undefined') return;

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
  camera.position.z = 25;

  const renderer = new THREE.WebGLRenderer({
    canvas: canvas,
    alpha: true,
    antialias: true,
  });
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

  // Ambient & Directional Lighting
  const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
  scene.add(ambientLight);

  const pointLightCyan = new THREE.PointLight(0x00f2fe, 2, 50);
  pointLightCyan.position.set(15, 15, 15);
  scene.add(pointLightCyan);

  const pointLightPurple = new THREE.PointLight(0x7f00ff, 2, 50);
  pointLightPurple.position.set(-15, -15, 10);
  scene.add(pointLightPurple);

  // 1. Central Floating 3D Geometric Icosahedron
  const icoGeometry = new THREE.IcosahedronGeometry(7, 1);
  const icoMaterial = new THREE.MeshStandardMaterial({
    color: 0x0a192f,
    wireframe: true,
    transparent: true,
    opacity: 0.35,
    roughness: 0.2,
    metalness: 0.8,
  });
  const icosahedron = new THREE.Mesh(icoGeometry, icoMaterial);
  icosahedron.position.set(12, 2, -5);
  scene.add(icosahedron);

  // 2. Floating Torus Knot (Wireframe Cyan)
  const knotGeometry = new THREE.TorusKnotGeometry(4.5, 0.8, 80, 16);
  const knotMaterial = new THREE.MeshBasicMaterial({
    color: 0x00f2fe,
    wireframe: true,
    transparent: true,
    opacity: 0.15,
  });
  const torusKnot = new THREE.Mesh(knotGeometry, knotMaterial);
  torusKnot.position.set(-14, -5, -8);
  scene.add(torusKnot);

  // 3. Particle Starfield
  const particleCount = 600;
  const positions = new Float32Array(particleCount * 3);
  const colors = new Float32Array(particleCount * 3);

  const cyanColor = new THREE.Color(0x00f2fe);
  const emeraldColor = new THREE.Color(0x00f5a0);
  const purpleColor = new THREE.Color(0x7f00ff);

  for (let i = 0; i < particleCount * 3; i += 3) {
    positions[i] = (Math.random() - 0.5) * 80;
    positions[i + 1] = (Math.random() - 0.5) * 80;
    positions[i + 2] = (Math.random() - 0.5) * 60;

    const chosenColor = Math.random() > 0.6 ? cyanColor : Math.random() > 0.3 ? emeraldColor : purpleColor;
    colors[i] = chosenColor.r;
    colors[i + 1] = chosenColor.g;
    colors[i + 2] = chosenColor.b;
  }

  const particleGeometry = new THREE.BufferGeometry();
  particleGeometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
  particleGeometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

  const particleMaterial = new THREE.PointsMaterial({
    size: 0.45,
    vertexColors: true,
    transparent: true,
    opacity: 0.75,
  });

  const particleSystem = new THREE.Points(particleGeometry, particleMaterial);
  scene.add(particleSystem);

  // Mouse Move Interactive Tilting
  let mouseX = 0;
  let mouseY = 0;
  let targetX = 0;
  let targetY = 0;

  window.addEventListener('mousemove', (e) => {
    mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
    mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
  });

  // Resize handler
  window.addEventListener('resize', () => {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
  });

  // Animation Loop
  function animate() {
    requestAnimationFrame(animate);

    targetX += (mouseX - targetX) * 0.05;
    targetY += (mouseY - targetY) * 0.05;

    // Smooth rotations
    icosahedron.rotation.x += 0.003;
    icosahedron.rotation.y += 0.005;
    icosahedron.position.x = 12 + targetX * 3;
    icosahedron.position.y = 2 - targetY * 3;

    torusKnot.rotation.x -= 0.002;
    torusKnot.rotation.y += 0.003;
    torusKnot.position.x = -14 + targetX * 2;
    torusKnot.position.y = -5 - targetY * 2;

    particleSystem.rotation.y += 0.0008;
    particleSystem.rotation.x += 0.0004;

    camera.position.x = targetX * 1.5;
    camera.position.y = -targetY * 1.5;
    camera.lookAt(0, 0, 0);

    renderer.render(scene, camera);
  }

  animate();
})();
