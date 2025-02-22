<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "startup_selfie";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la página actual
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 6;
    $offset = ($page - 1) * $per_page;

    // Obtener total de registros
    $total = $conn->query("SELECT COUNT(*) FROM imagenes")->fetchColumn();
    $totalPages = ceil($total / $per_page);

    // Consulta paginada
    $sql = "SELECT id, url, url_carta, mensaje FROM imagenes ORDER BY id DESC LIMIT :offset, :per_page";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    $stmt->execute();

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($resultados) {
        echo json_encode([
            "status" => "success",
            "currentPage" => $page,
            "totalPages" => $totalPages,
            "total" => $total,
            "data" => $resultados
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
