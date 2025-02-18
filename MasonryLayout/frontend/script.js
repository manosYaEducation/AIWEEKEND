// const masonry = document.querySelector('.masonry');

// async function fetchImages() {
//     try {
//         const response = await fetch('http://localhost/MasonryLayout/backend/insertar.php');
//         const images = await response.json();

//         images.forEach(imgData => {
//             const imgElement = document.createElement('img');
//             imgElement.src = imgData.url;
//             masonry.appendChild(imgElement);
//         });
//     } catch (error) {
//         console.error('Error...', error);
//     }
// }

// fetchImages();


//API POKEMON////API POKEMON////API POKEMON////API POKEMON////API POKEMON//

const masonry = document.querySelector('.masonry');

async function fetchPokemonImages() {
    try {
        const response = await fetch('https://pokeapi.co/api/v2/pokemon?limit=20');
        const data = await response.json();

        for (const pokemon of data.results) {
            const pokemonResponse = await fetch(pokemon.url);
            const pokemonData = await pokemonResponse.json();
            const imageUrl = pokemonData.sprites.front_default;
            const imgElement = document.createElement('img');
            imgElement.src = imageUrl;
            masonry.appendChild(imgElement);
        }
    } catch (error) {
        console.error('Error fetching Pokémon data:', error);
    }
}

fetchPokemonImages();

