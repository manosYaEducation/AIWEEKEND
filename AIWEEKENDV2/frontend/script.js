const masonry = document.querySelector(".masonry");
let currentPage = 1;
let totalPages = 1;
const IMAGES_PER_PAGE = 6;

// Crear contenedor de paginación
const paginationContainer = document.createElement("div");
paginationContainer.className = "pagination-container";
document.body.insertBefore(paginationContainer, masonry.nextSibling);

// Función para mostrar/ocultar el loader
function showLoading() {
  const loader = document.querySelector(".loading-text");
  loader.classList.add("show");
}

function hideLoading() {
  const loader = document.querySelector(".loading-text");
  loader.classList.remove("show");
}

// Función para mezclar array (algoritmo Fisher-Yates)
function shuffleArray(array) {
  const newArray = [...array];
  for (let i = newArray.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
  }
  return newArray;
}

// Array de áreas del grid que podemos reorganizar
const gridAreas = [
  "main",
  "right-top",
  "right-bottom",
  "left-square",
  "right-rect",
  "bottom-left",
  "big-bottom",
];

// Obtener imágenes
async function fetchImages(page = 1, shouldShuffle = false) {
  try {
    showLoading();
    document.getElementById("result").innerText = "Cargando imágenes...";
    const response = await fetch(
      `http://localhost:3000/AIWEEKENDV2/backend/get_all.php?page=${page}`
    );
    const data = await response.json();
    console.log("Datos recibidos:", data);

    if (data.status === "success" && data.data.length > 0) {
      totalPages = data.totalPages;
      currentPage = data.currentPage;

      // Si shouldShuffle es true, mezclar las imágenes y las áreas del grid
      let imagesToRender = [...data.data];
      let currentGridAreas = [...gridAreas];

      if (shouldShuffle) {
        imagesToRender = shuffleArray(imagesToRender);
        currentGridAreas = shuffleArray(currentGridAreas);
      }

      renderImages(imagesToRender, currentGridAreas);
      updatePagination();
      document.getElementById(
        "result"
      ).innerText = `Mostrando página ${currentPage} de ${totalPages} (Total: ${data.total} imágenes)`;
    } else {
      document.getElementById("result").innerText =
        "No hay imágenes disponibles";
    }
  } catch (error) {
    console.error("Error:", error);
    document.getElementById("result").innerText =
      "Error al cargar las imágenes";
  } finally {
    hideLoading();
  }
}

// Renderizar imágenes
function renderImages(images, currentGridAreas) {
  masonry.innerHTML = "";
  const gridContainer = document.createElement("div");
  gridContainer.className = "grid-container";

  images.forEach((imgData, index) => {
    const container = document.createElement("div");
    container.className = "flip-container";
    container.setAttribute("data-index", index + 1);
    container.style.opacity = "0";
    container.style.animation = `fadeInUp 0.5s ease-out ${
      index * 0.1
    }s forwards`;

    // Asignar área de grid
    if (index < currentGridAreas.length) {
      container.style.gridArea = currentGridAreas[index];
    }

    const flipper = document.createElement("div");
    flipper.className = "flipper";

    // Crear div para el frente
    const front = document.createElement("div");
    front.className = "front";
    const frontImg = document.createElement("img");
    frontImg.src = imgData.url;
    frontImg.alt = "Imagen frontal";
    frontImg.loading = "lazy";
    front.appendChild(frontImg);

    // Crear div para el reverso
    const back = document.createElement("div");
    back.className = "back";
    const backImg = document.createElement("img");
    backImg.src = imgData.url_carta;
    backImg.alt = "Imagen trasera";
    backImg.loading = "lazy";
    back.appendChild(backImg);

    // Añadir mensaje
    const mensaje = document.createElement("div");
    mensaje.className = "mensaje";
    mensaje.textContent = imgData.mensaje;
    back.appendChild(mensaje);

    flipper.appendChild(front);
    flipper.appendChild(back);
    container.appendChild(flipper);

    // Añadir efecto hover
    container.addEventListener("mouseenter", () => {
      container.style.transform = "scale(1.02)";
    });

    container.addEventListener("mouseleave", () => {
      container.style.transform = "scale(1)";
    });

    // Efecto de volteo al hacer click
    container.addEventListener("click", () => {
      container.classList.add("spark");
      container.classList.toggle("flipped");

      // Efecto de vibración táctil si está disponible
      if (window.navigator && window.navigator.vibrate) {
        window.navigator.vibrate(50);
      }

      // Remover la clase spark después de la animación
      setTimeout(() => {
        container.classList.remove("spark");
      }, 800); // Aumentado a 800ms para coincidir con la duración de la animación
    });

    gridContainer.appendChild(container);
  });

  masonry.appendChild(gridContainer);

  // Añadir efecto de aparición escalonada
  const containers = gridContainer.querySelectorAll(".flip-container");
  containers.forEach((container, index) => {
    setTimeout(() => {
      container.style.opacity = "1";
      container.style.transform = "translateY(0)";
    }, index * 100);
  });
}

// Función para actualizar la paginación
function updatePagination() {
  paginationContainer.innerHTML = "";

  if (totalPages > 1) {
    for (let i = 1; i <= totalPages; i++) {
      const pageButton = document.createElement("button");
      pageButton.className = `page-button ${i === currentPage ? "active" : ""}`;
      pageButton.innerText = i;
      pageButton.onclick = () => {
        if (i !== currentPage) {
          fetchImages(i, false);
        }
      };
      paginationContainer.appendChild(pageButton);
    }
  }
}

// Botón de reorganizar
const shuffleButton = document.createElement("button");
shuffleButton.innerText = "Reorganizar";
shuffleButton.className = "page-button shuffle-button";
shuffleButton.onclick = () => fetchImages(currentPage, true);
document.body.insertBefore(shuffleButton, paginationContainer);

// Inicializar la página
fetchImages(1, false);

// Manejar resize de la ventana
window.addEventListener("resize", () => {
  document.querySelectorAll(".front").forEach((img) => {
    if (img.naturalWidth) {
      const container = img.closest(".grid-item");
      if (container) {
        container.style.height = `${container.offsetWidth}px`;
      }
    }
  });
});
