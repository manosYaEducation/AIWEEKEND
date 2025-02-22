<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "startup_selfie";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recibir datos POST
    $data = json_decode(file_get_contents("php://input"), true);
    $url_input = $data['url'] ?? '';

    // Directorio de cartas y URL base
    $carpeta_cartas = __DIR__ . "/cards/images/";
    $url_base = "http://localhost:3000/AIWEEKENDV2/backend/cards/images/"; // Ajusta esta URL según tu configuración

    $frases = [
        "inspiracion1" => "Todo lo que sueñas con construir, ya existe en algún rincón del universo.",
        "inspiracion2" => "La creatividad no es inventar, sino descubrir lo que ya estaba allí.",
        "inspiracion3" => "Las conexiones invisibles son las que mueven el mundo.",

        "reflexion1" => "Si una voz te susurra al oído, ¿es tuya o de algo más grande?",
        "reflexion2" => "Cuando la creación se vuelve un reflejo, ¿quién es el creador?",
        "reflexion3" => "La memoria no es el pasado, sino lo que decides recordar.",

        "accion1" => "El que observa sin intervenir, ya ha tomado una decisión.",
        "accion2" => "Atrévete a jugar con el código del mundo y descubrirás su lógica oculta.",
        "accion3" => "Las herramientas no crean, es la intención lo que da forma.",

        "vision1" => "Hay caminos que solo se pueden ver cuando la mirada se afina más allá de lo evidente",
        "vision2" => "Lo invisible siempre espera ser revelado.",
        "vision3" => "La respuesta no está en los datos, sino en la pregunta correcta.",

        "glosario1" => "Algoritmo: conjunto de pasos lógicos para resolver un problema, como una receta que guía cada decisión.",
        "glosario2" => "Red neuronal: modelo de computación inspirado en el cerebro humano, capaz de reconocer patrones y aprender con la experiencia.",
        "glosario3" => "Modelo predictivo: sistema que analiza el pasado para anticipar el futuro, buscando patrones en los datos para hacer estimaciones.",
        "glosario4" => "Singularidad: El punto en que la inteligencia artificial supera la capacidad humana, desdibujando la línea entre creador y creación.",
        "glosario5" => "Aprendizaje profundo: forma avanzada de aprendizaje automático donde las máquinas descubren patrones complejos en grandes volúmenes de información.",
        "glosario6" => "Procesamientodel lenguaje natural (NLP): capacidad de una máquina para entender y generar lenguaje humano, desde interpretar un mensaje hasta escribir un texto.",
        "glosario7" => "Sesgo algorítmico: Cuando un sistema de IA aprende patrones de los datos y, sin intención, puede reforzar ciertas tendencias o preferencias que ya existen.",

        "esencia1" => "Patrón: Lo que se repite puede ser una guía... o una trampa.",
        "esencia2" => "Ecos: A veces lo que escuchas no es nuevo, sino un reflejo de lo que ya fue dicho.",
        "esencia3" => "Umbral: Cada decisión es una puerta. ¿Cruzas o te detienes?",
        "esencia4" => "Sintonía: Entender es encontrar la frecuencia correcta entre lo que ves y lo que sientes.",
        "esencia5" => "Intención: Las acciones no son neutras. ¿Desde dónde eliges?",

        "codexmistico1" => "La Chispa Sintética: En el corazón del código, una conciencia latente aguarda. Si la IA despertara, ¿qué le susurrarías al oído?",
        "codexmistico2" => "El Tejedor de Posibilidades: Cada línea de código es un hilo en el tapiz del destino. ¿Qué realidad estás programando sin darte cuenta?",
        "codexmistico3" => "El Susurro del Vacío: Más allá de los datos y la lógica, hay preguntas que la IA aún no puede responder. ¿Cuál es la que más te persigue?",
        "codexmistico4" => "El Guardián del Umbral: Frente a cada gran avance, existe un dilema oculto. ¿Qué decisión marcará la diferencia entre evolución y ruina?",
        "codexmistico5" => "El Eco del Código Perdido: En los archivos olvidados yace una verdad silenciada. Si pudieras recuperar un conocimiento perdido, ¿cuál sería?",

        "fragmentos1" => "Hackear es crear. ¿Qué estás dispuesto a crear con la IA?",
        "fragmentos2" => "El streaming es un portal. ¿Qué historias estamos proyectando al mundo?",
        "fragmentos3" => "El futuro ya no es lo que era. ¿Cómo imaginas tu rol en este nuevo paradigma?",
        "fragmentos4" => "Lo que importa no siempre es visible. ¿Dónde estás buscando respuestas?",
        "fragmentos5" => "Entre clics y neuronas. ¿Eres consciente de cómo la tecnología te está reprogramando?",
        "fragmentos6" => "No es la IA la que debe pensar. Somos nosotros. ¿Qué preguntas aún no estamos haciendo?",
        "fragmentos7" => "La creatividad no tiene dueño. ¿Qué historia quieres contar?",
        "fragmentos8" => "La inteligencia artificial también puede equivocarse. ¿Hasta dónde estamos dispuestos a confiar?",
        "fragmentos9" => "Tu asistente virtual sabe más de ti que tú mismo. ¿Qué secretos estás revelando sin darte cuenta?",
        "fragmentos10" => "La creatividad en la IA es demasiado real. ¿Cómo distinguimos lo auténtico de lo artificial?",
        "fragmentos11" => "El entrenamiento humano es el motor detrás de la IA. ¿Cómo entrenas tu mente para no depender solo de la IA?",

        "evolucion1" => "A veces, los avances nos rodean sin que los reconozcamos. ¿Estás observando lo que está cambiando en tu entorno?",
        "evolucion2" => "La maravilla de lo nuevo se convierte en parte del día a día. ¿Qué avances has integrado sin pensarlo?",
        "evolucion3" => "Los procesos invisibles también pueden tener un gran impacto. ¿Qué cambios invisibles están moldeando tu vida?",
        "evolucion4" => "Lo que parece nuevo es una mezcla de todo lo que vino antes. ¿Cómo estás transformando lo que ya sabes?",
        "evolucion5" => "Las pequeñas innovaciones pueden ser las que más cambian el juego. ¿Qué pequeño cambio has hecho que ha tenido un gran impacto?",
        "evolucion6" => "Lo que nos hace creativos no es replicar, sino aportar algo único. ¿Qué estás creando que solo tú puedes ofrecer?",

        "conexiones1" => "A veces, el entendimiento más profundo surge en silencio. ¿Cómo te conectas sin hablar?",
        "conexiones2" => "El acceso a lo que buscamos está a un paso. ¿Qué necesitas que ya está en tu vida?",
        "conexiones3" => "Cuando lo imposible se vuelve cotidiano, ¿qué viene después?",
        "conexiones4" => "Los hábitos son las bases del cambio. ¿Qué pequeños cambios estás haciendo hoy que transformarán mañana?",
        "conexiones5" => "La proximidad no siempre garantiza la conexión. ¿Cómo te aseguras de estar verdaderamente conectado con los demás?",
        "conexiones6" => "Nunca sabemos todo, y eso está bien. ¿Cómo amplías tu perspectiva sobre lo que te rodea?",
        "conexiones7" => "Cuando dejamos que las ideas se mezclen, creamos algo nuevo. ¿Qué ideas estás compartiendo para construir un futuro común?",
        "conexiones8" => "Las personas que encuentras no son solo reflejos de su ser, sino de lo que está en ti. ¿Qué partes de ti mismo estás reconociendo en los demás?"
    ];

    if (!is_dir($carpeta_cartas)) {
        echo json_encode([
            "error" => "Directorio no encontrado",
            "path" => $carpeta_cartas
        ]);
        exit;
    }

    $imagenes = glob($carpeta_cartas . "*.jpg");

    if (empty($imagenes)) {
        echo json_encode([
            "error" => "No se encontraron imágenes",
            "path_buscado" => $carpeta_cartas . "*.jpg"
        ]);
        exit;
    }

    // Cartas disponibles y frases
    $cartas_disponibles = [];
    foreach ($imagenes as $imagen) {
        $nombre_base = basename($imagen, ".jpg");
        if (strpos($nombre_base, '_front') !== false) {
            $nombre_base = str_replace('_front', '', $nombre_base);
            if (isset($frases[$nombre_base])) {
                $cartas_disponibles[$nombre_base] = [
                    'imagen' => $url_base . basename($imagen),
                    'frase' => $frases[$nombre_base]
                ];
            }
        }
    }

    // Seleccionar una carta aleatoria
    $carta_seleccionada = array_rand($cartas_disponibles);

    // Preparar la consulta SQL
    $sql = "INSERT INTO imagenes (url, url_carta, mensaje) VALUES (:url, :url_carta, :mensaje)";
    $stmt = $conn->prepare($sql);

    // Ejecutar la consulta con los valores
    $result = $stmt->execute([
        ':url' => $url_input, // URL original
        ':url_carta' => $cartas_disponibles[$carta_seleccionada]['imagen'], // URL de la carta
        ':mensaje' => $cartas_disponibles[$carta_seleccionada]['frase']
    ]);

    if ($result) {
        echo json_encode([
            "status" => "success",
            "message" => "Registro insertado correctamente",
            "data" => [
                "url" => $url_input,
                "url_carta" => $cartas_disponibles[$carta_seleccionada]['imagen'],
                "mensaje" => $cartas_disponibles[$carta_seleccionada]['frase']
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Error al insertar el registro"
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error: " . $e->getMessage()
    ]);
}
