<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

$host = "localhost:3307";
$dbname = "pruebaimagen";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}
//////////////////////////////
//obtener todas las imágenes//
//////////////////////////////
$result = $conn->query("SELECT * FROM imagenes");
$imagenes = [];
while ($row = $result->fetch_assoc()) {
    $imagenes[] = $row;
}

echo json_encode($imagenes);
$conn->close();
?>
