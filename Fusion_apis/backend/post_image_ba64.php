<?php
try {
    // Configuración de la base de datos local
    $host = "localhost";
    $dbname = "alphadocere_aiweekend_salfi_card";
    $username = "root";
    $password = ""; // Vacío en XAMPP por defecto

    // Crear conexión con MySQL
    $conn = new mysqli($host, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        throw new Exception("Error de conexión a la base de datos: " . $conn->connect_error);
    }

    // Leer el JSON del POST
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    // Validar datos recibidos
    if (!$data || !isset($data['url'])) {
        throw new Exception('La URL es obligatoria.');
    }

    $url = $data['url'];

    // Obtener datos de la API externa
    $api_url = "https://www.alphadocere.cl/API/cartas-random/";
    $api_response = file_get_contents($api_url);
    $card_data = json_decode($api_response, true);

    if (!$card_data || !isset($card_data['carta_front']) || !isset($card_data['frase'])) {
        throw new Exception("Error al obtener datos de la API de cartas.");
    }

    // Extraer datos de la carta
    $carta_front = $card_data['carta_front'];
    $frase = $card_data['frase'];

    // Verificar si la tabla existe
    $table_check = $conn->query("SHOW TABLES LIKE 'imagenes_base64'");
    if ($table_check->num_rows == 0) {
        throw new Exception("La tabla 'imagenes_base64' no existe en la base de datos.");
    }

    // Preparar consulta para insertar datos
    $stmt = $conn->prepare("INSERT INTO imagenes_base64 (url, carta_front, frase) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $url, $carta_front, $frase);

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar la información: " . $stmt->error);
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();

    // Responder con datos de la API junto con mensaje de éxito
    echo json_encode([
        "carta_front" => $carta_front,
        "frase" => $frase
    ]);

} catch (Exception $e) {
    http_response_code(400); // Código 400 = Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>