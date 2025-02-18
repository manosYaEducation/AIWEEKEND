<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$dbname = "pruebaimagen";
$username = "root";  // Cambia esto si tienes otro usuario
$password = "";  // Cambia esto si tienes una contraseña
$host = "localhost:3307";


$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos"]));
}

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data["url"]) || empty($data["url"])) {
        echo json_encode(["error" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQVkz3sMnUJmvkNyaaxkgVpEIp0SRzaZQWITw&s"]);
        exit;
    }

    $url = $conn->real_escape_string($data["url"]);
    $sql = "INSERT INTO imagenes (url) VALUES ('$url')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Imagen guardada exitosamente", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["error" => "Error al guardar la imagen"]);
    }
}

elseif ($method === "GET") {
    $result = $conn->query("SELECT * FROM imagenes");
    $imagenes = [];

    while ($row = $result->fetch_assoc()) {
        $imagenes[] = $row;
    }

    echo json_encode($imagenes);
}

else {
    echo json_encode(["error" => "Método no permitido"]);
}

$conn->close();
?>
