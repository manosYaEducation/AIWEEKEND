
const masonry = document.querySelector('.masonry');

async function fetchImages() {
    try {
        const response = await fetch('http://localhost/masonrylayout/backend/get_all.php');
        const images = await response.json();

        images.forEach(imgData => {
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

            // mantiene la relacion de aspecto de la imagen
            frontImg.onload = function () {
                const aspectRatio = this.naturalHeight / this.naturalWidth;
                container.style.height = `${container.offsetWidth * aspectRatio}px`;
            };

            // añade efecto de volteo al hacer clic
            container.addEventListener('click', () => {
                container.classList.toggle('flipped');
            });
        });
    } catch (error) {
        console.error('error fetching images:', error);
    }
}

fetchImages();


window.addEventListener('resize', () => {
  document.querySelectorAll('.flip-container').forEach(container => {
      const img = container.querySelector('.front');
      if (img.naturalWidth) {
          const aspectRatio = img.naturalHeight / img.naturalWidth;
          container.style.height = `${container.offsetWidth * aspectRatio}px`;
      }
  });
});

//API POKEMON////API POKEMON////API POKEMON////API POKEMON////API POKEMON//

///const masonry = document.querySelector('.masonry');

//     const response = await fetch('http://localhost/imagenes/get_all.php');
//      for (const pokemon of data.results) {
//            const pokemonResponse = await fetch(pokemon.url);
//         const pokemonData = await pokemonResponse.json();
//            const imageUrl = pokemonData.sprites.front_default;
//            const imgElement = document.createElement('img');
//            imgElement.src = imageUrl;
//            masonry.appendChild(imgElement);
//        }
//    } catch (error) {
//        console.error('Error fetching Pokémon data:', error);
//    }
//}

///fetchPokemonImages();

