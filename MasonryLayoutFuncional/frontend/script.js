const masonry = document.querySelector('.masonry');

// Crear botones de navegación
const prevPageButton = document.createElement('button');
prevPageButton.innerText = 'Página Anterior';
prevPageButton.style.display = 'none'; // Se oculta al inicio

const nextPageButton = document.createElement('button');
nextPageButton.innerText = 'Siguiente Página';
nextPageButton.style.display = 'none'; // Se oculta al inicio

// Agregar los botones al DOM
document.body.appendChild(prevPageButton);
document.body.appendChild(nextPageButton);

let images = [];
let currentPage = 0;
const imagesPerPage = 4;

// Función para obtener las imágenes del backend
async function fetchImages() {
    try {
        const response = await fetch('http://localhost/MansoryLayoutGit/MasonryLayoutFuncional/backend/get_all.php');
        images = await response.json();
        if (images.length > 0) {
            renderImages();
            updateButtons();
        }
    } catch (error) {
        console.error('Error al obtener imágenes:', error);
    }
}

// Función para renderizar imágenes por página
function renderImages() {
    // Limpiamos el contenedor antes de agregar nuevas imágenes
    masonry.innerHTML = '';

    // Obtener las imágenes de la página actual
    const start = currentPage * imagesPerPage;
    const end = start + imagesPerPage;
    const imagesToShow = images.slice(start, end);

    imagesToShow.forEach(imgData => {
        const container = document.createElement('div');
        container.className = 'flip-container';

        const flipper = document.createElement('div');
        flipper.className = 'flipper';

        const frontImg = document.createElement('img');
        frontImg.className = 'front';
        frontImg.src = imgData.url;

        const backImg = document.createElement('img');
        backImg.className = 'back';
        backImg.src = imgData.url_ia;

        flipper.appendChild(frontImg);
        flipper.appendChild(backImg);
        container.appendChild(flipper);
        masonry.appendChild(container);

        // Ajustar el tamaño de la imagen
        frontImg.onload = function () {
            const aspectRatio = this.naturalHeight / this.naturalWidth;
            container.style.height = `${container.offsetWidth * aspectRatio}px`;
        };

        // Agregar efecto de volteo y mostrar observación
        container.addEventListener('click', () => {
            container.classList.toggle('flipped');
            document.getElementById('result').innerText = imgData.observacion;
        });
    });

    // Actualizar estado de los botones
    updateButtons();
}

// Función para actualizar la visibilidad de los botones
function updateButtons() {
    prevPageButton.style.display = currentPage > 0 ? 'block' : 'none';
    nextPageButton.style.display = (currentPage + 1) * imagesPerPage < images.length ? 'block' : 'none';
}

// Evento del botón "Siguiente Página"
nextPageButton.addEventListener('click', () => {
    if ((currentPage + 1) * imagesPerPage < images.length) {
        currentPage++;
        renderImages();
    }
});

// Evento del botón "Página Anterior"
prevPageButton.addEventListener('click', () => {
    if (currentPage > 0) {
        currentPage--;
        renderImages();
    }
});

// Cargar las imágenes al inicio
fetchImages();

// Ajustar tamaño de imagen cuando se cambia el tamaño de la ventana
window.addEventListener('resize', () => {
    document.querySelectorAll('.flip-container').forEach(container => {
        const img = container.querySelector('.front');
        if (img.naturalWidth) {
            const aspectRatio = img.naturalHeight / img.naturalWidth;
            container.style.height = `${container.offsetWidth * aspectRatio}px`;
        }
    });
});
