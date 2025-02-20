<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Cargar variables de entorno usando ruta relativa
$env_file = '../.env';  // Subimos dos niveles desde backend hasta AIWEEKEND
if (!file_exists($env_file)) {
    die(json_encode(["error" => "Archivo .env no encontrado en: " . $env_file]));
}

$env = parse_ini_file($env_file);
$host = $env['DB_HOST'];
$username = $env['DB_USER'];
$password = $env['DB_PASSWORD'];
$dbname = $env['DB_NAME'];

// Conectar con MySQL
$conn = new mysqli($host, $username, $password, $dbname);



// Obtener el método HTTP
$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        obtenerImagenes($conn);
        break;
    case "POST":
        guardarOActualizarImagen($conn);
        break;
    case "DELETE":
        eliminarImagen($conn);
        break;
    default:
        echo json_encode(["error" => "Método no permitido"]);
        break;
}

$conn->close();

// ✅ Obtener todas las imágenes
function obtenerImagenes($conn)
{
    $result = $conn->query("SELECT * FROM imagenes ORDER BY id DESC");
    $imagenes = [];
    while ($row = $result->fetch_assoc()) {
        $imagenes[] = $row;
    }

    echo json_encode($imagenes);
}

// ✅ Guardar una nueva imagen o actualizar la última registrada
function guardarOActualizarImagen($conn)
{
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["url"]) || empty($data["url"])) {
        echo json_encode(["error" => "URL de imagen requerida"]);
        exit;
    }

    $url = $conn->real_escape_string($data["url"]);

    // Obtener la última imagen insertada
    $result = $conn->query("SELECT id FROM imagenes ORDER BY id DESC LIMIT 1");

    if ($result->num_rows > 0) {
        // Si hay imágenes, actualizar la última
        $row = $result->fetch_assoc();
        $id = $row["id"];
        $sql = "UPDATE imagenes SET url='$url' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Última imagen actualizada", "id" => $id, "new_url" => $url]);
        } else {
            echo json_encode(["error" => "Error al actualizar la imagen"]);
        }
    } else {
        // Si no hay imágenes, insertar la nueva
        $sql = "INSERT INTO imagenes (url) VALUES ('$url')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["message" => "Imagen guardada exitosamente", "id" => $conn->insert_id, "new_url" => $url]);
        } else {
            echo json_encode(["error" => "Error al guardar la imagen"]);
        }
    }
}

// ✅ Eliminar una imagen
function eliminarImagen($conn)
{
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["id"])) {
        echo json_encode(["error" => "ID requerido"]);
        exit;
    }

    $id = intval($data["id"]);
    $sql = "DELETE FROM imagenes WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Imagen eliminada correctamente"]);
    } else {
        echo json_encode(["error" => "Error al eliminar la imagen"]);
    }
}
