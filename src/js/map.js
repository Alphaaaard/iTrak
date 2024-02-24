import * as THREE from "https://cdn.skypack.dev/three@0.129.0/build/three.module.js";
import { OrbitControls } from "https://cdn.skypack.dev/three@0.129.0/examples/jsm/controls/OrbitControls.js";
import { GLTFLoader } from "https://cdn.skypack.dev/three@0.129.0/examples/jsm/loaders/GLTFLoader.js";

let scene,
  camera,
  renderer,
  models = [],
  controls,
  raycaster,
  mouse;

document.addEventListener("DOMContentLoaded", function () {
  init();
  addSidebarEventListeners();
});

function addSidebarEventListeners() {
  const sidebarLinks = document.querySelectorAll("#sidebar a");
  sidebarLinks.forEach((link) => {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      const page = this.getAttribute("href");
      navigateToPage(page);
    });
  });
}

function navigateToPage(page) {
  console.log("Navigating to", page);
  window.location.href = page;
}

function init() {
  createScene();
  onWindowResize();
  createLights();
  createRenderer();
  createControls();
  loadModels();
  setupEventListeners();
}

function createScene() {
  scene = new THREE.Scene();
  camera = new THREE.PerspectiveCamera(
    50,
    window.innerWidth / window.innerHeight,
    0.1,
    1000
  );
  camera.position.set(5, 10, 5);
  camera.lookAt(new THREE.Vector3(0, 0, 0));
}

function onWindowResize() {
  // Get the size of the div container instead of window
  const containerWidth = document.getElementById("model-container").clientWidth;
  const containerHeight =
    document.getElementById("model-container").clientHeight;
  if (renderer) {
    // Update camera aspect ratio and renderer size
    camera.aspect = containerWidth / containerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(containerWidth, containerHeight);
  }
}

function createLights() {
  const frontDirectionalLight = new THREE.DirectionalLight(0xffffff, 1);
  frontDirectionalLight.position.set(0, 1, 1);
  scene.add(frontDirectionalLight);

  const backDirectionalLight = new THREE.DirectionalLight(0xffffff, 1);
  backDirectionalLight.position.set(0, 1, -1);
  scene.add(backDirectionalLight);

  const ambientLight = new THREE.AmbientLight(0x404040);
  scene.add(ambientLight);
}

function createRenderer() {
  renderer = new THREE.WebGLRenderer({ antialias: true });
  renderer.setSize(window.innerWidth, window.innerHeight);
  renderer.setClearColor(0xffffff);
  document.getElementById("model-container").appendChild(renderer.domElement);
}

function createControls() {
  controls = new OrbitControls(camera, renderer.domElement);
  controls.enableDamping = true;

  // Set limits on vertical rotation (in radians)
  controls.minPolarAngle = Math.PI / 100; // Adjust as needed
  controls.maxPolarAngle = Math.PI / 1.9; // Adjust as needed
}

function loadModels() {
  const loader = new GLTFLoader();
  const modelsData = [
    {
      path: "../../src/models/dino/Flooring.glb",
      name: "Model 1",
      description: "Description for Model 1",
      scale: 0.125,
      position: { x: -4.5, y: -1, z: 0 },
      rotation: { x: 0, y: 0, z: 0 },
    },
    {
      path: "../../src/models/dino/TechVoc.glb",
      name: "Model 2",
      description: "Description for Model 2",
      scale: 0.0075,
      position: { x: 1, y: -1.04, z: -2.8 },
      rotation: { x: 0, y: 1.6, z: 0 },
    },
    {
      path: "../../src/models/dino/Yellow.glb",
      name: "Model 3",
      description: "Description for Model 3",
      scale: 0.017,
      position: { x: -2.3, y: -2.2, z: -5 },
      rotation: { x: 0, y: 0, z: 0 },
    },
    {
      path: "../../src/models/dino/Belmonte.glb",
      name: "Model 4",
      description: "Description for Model 4",
      scale: 0.045,
      position: { x: -0.15, y: -1, z: -3.5 },
      rotation: { x: 0, y: 0, z: 0 },
    },
    {
      path: "../../src/models/dino/KorPhil.glb",
      name: "Model 5",
      description: "Description for Model 5",
      scale: 0.23,
      position: { x: -8, y: -0.9, z: 2 },
      rotation: { x: 0, y: 9.5, z: 0 },
    },
    {
      path: "../../src/models/dino/Ballroom.glb",
      name: "Model 6",
      description: "Description for Model 6",
      scale: 0.02,
      position: { x: 0.6, y: -1, z: -1 },
      rotation: { x: 0, y: 0, z: 0 },
    },
    {
      path: "../../src/models/dino/Multipurpose.glb",
      name: "Model 7",
      description: "Description for Model 7",
      scale: 0.007,
      position: { x: 0.6, y: -1.1, z: -4.25 },
      rotation: { x: 0, y: 0, z: 0 },
    },
    {
      path: "../../src/models/dino/Admin.glb",
      name: "Model 8",
      description: "Description for Model 8",
      scale: 0.03,
      position: { x: -4.5, y: -1.1, z: -1.8 },
      rotation: { x: 0, y: 1.55, z: 0 },
    },
    {
      path: "../../src/models/dino/Bautista.glb",
      name: "Model 9",
      description: "Description for Model 9",
      scale: 0.05,
      position: { x: -5.8, y: -1.2, z: -2.1 },
      rotation: { x: 0, y: -4.72, z: 0 },
    },
    {
      path: "../../src/models/dino/Academic.glb",
      name: "Model 10",
      description: "Description for Model 10",
      scale: 0.025,
      position: { x: -1.4, y: -0.9, z: -4.5 },
      rotation: { x: 0, y: -1.575, z: 0 },
    },
  ];

  modelsData.forEach((modelData, index) => {
    loader.load(
      modelData.path,
      (gltf) => {
        const model = gltf.scene;
        model.scale.set(modelData.scale, modelData.scale, modelData.scale);
        model.position.set(
          modelData.position.x,
          modelData.position.y,
          modelData.position.z
        );
        model.rotation.set(
          modelData.rotation.x,
          modelData.rotation.y,
          modelData.rotation.z
        );

        model.userData.name = modelData.name;
        model.userData.description = modelData.description;
        models.push(model);
        scene.add(model);

        setupModalListeners(model, index);
      },
      undefined,
      (error) => {
        console.error(
          `An error happened while loading ${modelData.name}`,
          error
        );
      }
    );
  });
}

function setupModalListeners(model, index) {
  const modalTemplate = document.getElementById(`modalTemplate${index + 1}`);
  const modalTitle = modalTemplate.querySelector(`#modalTitle${index + 1}`);
  const modalDescription = modalTemplate.querySelector(
    `#modalDescription${index + 1}`
  );
  const modal = document.getElementById(`myModal${index + 1}`);
  const closeModal = document.getElementById(`closeModal${index + 1}`);

  model.userData.modal = modal;
  model.userData.modalTitle = modalTitle;
  model.userData.modalDescription = modalDescription;
  model.userData.closeModal = closeModal;

  closeModal.addEventListener("click", () => {
    modal.style.display = "none";
  });

  // Add this event listener to prevent clicks from propagating to the model
  modal.addEventListener("click", (event) => {
    event.stopPropagation();
  });
}

function setupEventListeners() {
  raycaster = new THREE.Raycaster();
  mouse = new THREE.Vector2();

  window.addEventListener("resize", onWindowResize, false);
  window.addEventListener("mousemove", onDocumentMouseMove, false);
  window.addEventListener("click", onDocumentClick, false);
}

function animate() {
  requestAnimationFrame(animate);

  // Check if renderer is defined before calling render
  if (renderer) {
    // Check if controls is defined before calling update
    if (controls) {
      controls.update();
    }

    renderer.render(scene, camera);
  }
}

// function onWindowResize() {
//     camera.aspect = window.innerWidth / window.innerHeight;
//     camera.updateProjectionMatrix();
//     renderer.setSize(window.innerWidth, window.innerHeight);
// }

function onDocumentMouseMove(event) {
  const canvasBounds = renderer.domElement.getBoundingClientRect();
  mouse.x =
    ((event.clientX - canvasBounds.left) /
      (canvasBounds.right - canvasBounds.left)) *
      2 -
    1;
  mouse.y =
    -(
      (event.clientY - canvasBounds.top) /
      (canvasBounds.bottom - canvasBounds.top)
    ) *
      2 +
    1;

  raycaster.setFromCamera(mouse, camera);

  let isClickable = false;

  for (let i = 1; i < models.length; i++) {
    const model = models[i];
    const intersects = raycaster.intersectObject(model, true);

    if (intersects.length > 0) {
      isClickable = true;
      break;
    }
  }

  // Set cursor style based on whether the mouse is over a clickable model or not
  document.body.style.cursor = isClickable ? "pointer" : "auto";
}

function onDocumentClick(event) {
  event.preventDefault();

  raycaster.setFromCamera(mouse, camera);

  for (let i = 1; i < models.length; i++) {
    const model = models[i];
    const intersects = raycaster.intersectObject(model, true);

    if (intersects.length > 0) {
      const modelName = model.userData.name;
      const modelDescription = model.userData.description;

      for (const m of models) {
        m.userData.modal.style.display = "none";
      }

      model.userData.modalTitle.textContent = modelName;
      model.userData.modalDescription.textContent = modelDescription;
      model.userData.modal.style.display = "block";
      break;
    }
  }
}

animate();

// Mobile JS

const canvas = document.getElementById("model-container");
const modelContainer = document.getElementById("model-container");

function handleResize() {
  var currentWidth = window.innerWidth;

  if (currentWidth <= 600) {
    modelContainer.style.display = "none";
  } else {
    modelContainer.style.display = "block"; // or 'flex' or any other appropriate value
  }
}

window.addEventListener("resize", handleResize);

handleResize();