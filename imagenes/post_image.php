<?php
try {
    //header("Content-Type: application/json");
    //header("Access-Control-Allow-Origin: *");
    //header("Access-Control-Allow-Methods: GET, POST");
    //header("Access-Control-Allow-Headers: Content-Type");

    // Configuración de la base de datos
    $host = "localhost";
    $dbname = "pruebaimagen";
    $username = "root";  // Cambia esto si tienes otro usuario
    $password = "";  // Cambia esto si tienes una contraseña
    $host = "localhost:3306";

    // Crear conexión con MySQL
    $conn = new mysqli($host, $username, $password, $dbname);
    // Leer el cuerpo de la petición (esperando JSON)
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    // Verificar si la URL está presente en el JSON
    if (!$data || !isset($data['url'])) {
        throw new Exception('La URL es obligatoria.');
    }

    $url = $data['url'];
    $image_url = $url;

    // Verificar si la tabla existe
    $table_check = $conn->query("SHOW TABLES LIKE 'imagenes'");
    if ($table_check->num_rows == 0) {
        die(json_encode(["error" => "La tabla 'imagenes' no existe en la base de datos."]));
    }

    // Preparar la consulta para evitar SQL Injection
    $stmt = $conn->prepare("INSERT INTO imagenes (url) VALUES (?)");
    $stmt->bind_param("s", $image_url);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Imagen guardada exitosamente", "id" => $stmt->insert_id]);
    } else {
        echo json_encode(["error" => "Error al guardar la imagen: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    // Simulación de inserción (aquí puedes llamar a Client::post_image($url))
    echo json_encode([
        "success" => true,
        "message" => "URL ingresada correctamente",
        "url" => $url
    ]);
} catch (Exception $e) {
    http_response_code(400); // Código 400 = Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
