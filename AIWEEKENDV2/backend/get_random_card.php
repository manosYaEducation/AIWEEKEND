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

    // Consulta SQL para obtener una carta aleatoria
    $sql = "SELECT id, url, url_carta, mensaje FROM imagenes ORDER BY RAND() LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        // Ya no necesitamos convertir a base64, devolvemos las URLs directamente
        echo json_encode([
            "status" => "success",
            "data" => [
                "id" => $resultado['id'],
                "url" => $resultado['url'],
                "url_carta" => $resultado['url_carta'],
                "mensaje" => $resultado['mensaje']
            ]
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No hay cartas disponibles"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Error de conexión: " . $e->getMessage()
    ]);
}
