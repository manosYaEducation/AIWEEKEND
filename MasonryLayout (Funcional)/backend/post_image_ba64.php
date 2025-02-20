<?php
try {
    //header("Content-Type: application/json");
    //header("Access-Control-Allow-Origin: *");
    //header("Access-Control-Allow-Methods: GET, POST");
    //header("Access-Control-Allow-Headers: Content-Type");

// Configuración de la base de datos
$host = "localhost";
$dbname = "pruebaimagen2";
$username = "root";  // Cambia esto si tienes otro usuario
$password = "";  // Cambia esto si tienes una contraseña
$host = "localhost:3307";

// Crear conexión con MySQL
$conn = new mysqli($host, $username, $password, $dbname);
    // Leer el cuerpo de la petición (esperando JSON)
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    // Verificar si la URL está presente en el JSON
    if (!$data || !isset($data['url'] )) {
        throw new Exception('La URL es obligatoria.');
    }
    // Verificar si la URL está presente en el JSON
    if (!$data || !isset($data['url_ia'] )) {
        throw new Exception('La url_ia es obligatoria.');
    }
    //throw new Exception('La url_ia es obligatoria.'.$data['url_ia']);

    $url = $data['url'];
    $image_url = $url;
    $url_ia = $data['url_ia'];
    $image_url_ia = $url_ia;
    $observacion = $data['observacion'];
    $obser = $observacion;

        
    // Función para convertir imagen de URL a Base64
    function imageToBase64($image_url) {
        $image_data = @file_get_contents($image_url);
        if ($image_data === false) {
            return $image_url; // Si no se puede obtener la imagen, retorna la URL sin cambios
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->buffer($image_data);
        return 'data:' . $mime_type . ';base64,' . base64_encode($image_data);
    }

    // Convertir imágenes a Base64 si es posible
    $image_base64 = imageToBase64($url);
    $image_base64_ia = imageToBase64($url_ia);
    
    // Verificar si la tabla existe
    $table_check = $conn->query("SHOW TABLES LIKE 'imagenes'");
    if ($table_check->num_rows == 0) {
        die(json_encode(["error" => "La tabla 'imagenes' no existe en la base de datos."]));
    }
   

// Verificar si la tabla existe
$table_check = $conn->query("SHOW TABLES LIKE 'imagenes'");
if ($table_check->num_rows == 0) {
    die(json_encode(["error" => "La tabla 'imagenes' no existe en la base de datos."]));
}

// Preparar la consulta para evitar SQL Injection
$stmt = $conn->prepare("INSERT INTO imagenes (url, url_ia, observacion) VALUES (?,?,?)");
$stmt->bind_param("sss", $image_url,$image_url_ia,$obser);


if ($stmt->execute()) {
    echo json_encode(["message" => "informacion guardada exitosamente", "id" => $stmt->insert_id]);
} else {
    echo json_encode(["error" => "Error al guardar la información: " . $stmt->error]);
}

$stmt->close();
$conn->close();
    // Simulación de inserción (aquí puedes llamar a Client::post_image($url))
    echo json_encode([
        "success" => true,
        "message" => "información ingresada correctamente",
        "url" => $url,
        "url_ia" => $image_url_ia,
        "observacion"=> $obser
    ]);

} catch (Exception $e) {
    http_response_code(400); // Código 400 = Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}





?>