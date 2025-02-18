<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Configuración de la base de datos
$host = "localhost";
$dbname = "pruebaimagen";
$username = "root";  // Cambia esto si tienes otro usuario
$password = "";  // Cambia esto si tienes una contraseña
$host = "localhost:3307";

// Crear conexión con MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $conn->connect_error]));
}

// URL de la imagen a insertar
$image_url = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBwr_zZjgvmu4BccwDNIHic8K5dyehw7cSYA&s";

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
?>
