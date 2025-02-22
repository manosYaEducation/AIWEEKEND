<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "startup_selfie";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para obtener todos los registros
    $sql = "SELECT id, url, url_carta, mensaje, created_at FROM imagenes ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Obtener todos los resultados
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si hay resultados
    if (count($resultados) > 0) {
        echo json_encode([
            "status" => "success",
            "data" => $resultados  // Ya no necesitamos procesar las URLs
        ]);
    } else {
        echo json_encode([
            "status" => "empty",
            "message" => "No hay registros en la base de datos"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error de conexión: " . $e->getMessage()
    ]);
}
