const masonry = document.querySelector(".masonry");
let currentPage = 1;
let totalPages = 1;
const IMAGES_PER_PAGE = 6;

// Crear contenedor de paginación
const paginationContainer = document.createElement("div");
paginationContainer.className = "pagination-container";
document.body.insertBefore(paginationContainer, masonry.nextSibling);

// Función para mezclar array aleatoriamente
function shuffleArray(array) {
  for (let i = array.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [array[i], array[j]] = [array[j], array[i]];
  }
  return array;
}

// Función para obtener imágenes
async function fetchImages(page = 1) {
  try {
    document.getElementById("result").innerText = "Cargando imágenes...";

    const response = await fetch(
      `http://localhost:3000/MasonryLayoutFuncional/backend/get_all.php?page=${page}`
    );
    const data = await response.json();

    if (data.images && data.images.length > 0) {
      // Mezclar las imágenes aleatoriamente
      const shuffledImages = shuffleArray([...data.images]);
      renderImages(shuffledImages);
      totalPages = data.totalPages;
      currentPage = data.currentPage;
      updatePagination();
      document.getElementById(
        "result"
      ).innerText = `Mostrando página ${currentPage} de ${totalPages} (Total: ${data.total} imágenes)`;
    } else {
      document.getElementById("result").innerText =
        "No hay imágenes para mostrar en esta página";
    }
  } catch (error) {
    console.error("Error al obtener imágenes:", error);
    document.getElementById("result").innerText =
      "Error al cargar las imágenes";
  }
}

// Función para renderizar imágenes
function renderImages(images) {
  masonry.innerHTML = "";

  const gridContainer = document.createElement("div");
  gridContainer.className = "grid-container";

  // Asignar aleatoriamente cuáles serán las imágenes destacadas
  const featuredIndices = shuffleArray([...Array(images.length).keys()]).slice(
    0,
    2
  );

  images.forEach((imgData, index) => {
    const container = document.createElement("div");
    container.className = "flip-container grid-item";

    // Asignar featured aleatoriamente
    if (featuredIndices.includes(index)) {
      container.classList.add("featured");
    }

    const flipper = document.createElement("div");
    flipper.className = "flipper";

    const frontImg = document.createElement("img");
    frontImg.className = "front";
    frontImg.src = imgData.url;

    // Imagen de carga mientras se carga la imagen real
    frontImg.style.opacity = "0";
    frontImg.onload = () => {
      frontImg.style.opacity = "1";
      container.style.transform = "translateY(0)";
    };

    const backImg = document.createElement("img");
    backImg.className = "back";
    backImg.src = imgData.url_ia;

    flipper.appendChild(frontImg);
    flipper.appendChild(backImg);
    container.appendChild(flipper);
    gridContainer.appendChild(container);

    // Evento de click
    container.addEventListener("click", () => {
      container.classList.toggle("flipped");
      const resultDisplay = document.getElementById("result");
      resultDisplay.innerText = imgData.observacion;
    });
  });

  masonry.appendChild(gridContainer);
}

// Función para actualizar la paginación
function updatePagination() {
  paginationContainer.innerHTML = "";

  // Solo mostrar botones si hay más de una página
  if (totalPages > 1) {
    for (let i = 1; i <= totalPages; i++) {
      const pageButton = document.createElement("button");
      pageButton.className = `page-button ${i === currentPage ? "active" : ""}`;
      pageButton.innerText = i;
      pageButton.onclick = () => {
        if (i !== currentPage) {
          fetchImages(i);
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
shuffleButton.onclick = () => fetchImages(currentPage);
document.body.insertBefore(shuffleButton, paginationContainer);

// Inicializar la página
fetchImages();

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
