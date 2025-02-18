 const masonry = document.querySelector('.masonry');

async function fetchImages() {
    try {
        const response = await fetch('http://localhost/MasonryLayout/backend/get_all.php');
        const images = await response.json();

        images.forEach(imgData => {
            const imgElement = document.createElement('img');
             imgElement.src = imgData.url;
           masonry.appendChild(imgElement);
      });
    } catch (error) {
       console.error('Error...', error);
    }
}

fetchImages();


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

