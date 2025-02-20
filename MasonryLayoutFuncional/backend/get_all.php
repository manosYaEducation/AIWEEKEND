<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Cargar variables de entorno usando ruta relativa
$env_file = '../.env';
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

function obtenerImagenes($conn)
{
    // Verificar la conexión
    if ($conn->connect_error) {
        die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
    }

    try {
        // Obtener el total de registros
        $totalQuery = $conn->query("SELECT COUNT(*) as total FROM imagenes");
        if (!$totalQuery) {
            throw new Exception("Error al contar registros: " . $conn->error);
        }
        $totalRow = $totalQuery->fetch_assoc();
        $total = $totalRow['total'];

        // Configurar la paginación
        $imagesPerPage = 6;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $totalPages = ceil($total / $imagesPerPage);

        // Asegurarse de que la página solicitada no exceda el total de páginas
        $page = min($page, $totalPages);

        // Calcular el offset
        $offset = ($page - 1) * $imagesPerPage;

        // Obtener las imágenes para la página actual
        $sql = "SELECT id, url, url_ia, observacion, created_at 
                FROM imagenes 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param("ii", $imagesPerPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        $imagenes = [];
        while ($row = $result->fetch_assoc()) {
            $imagenes[] = $row;
        }

        // Devolver la respuesta
        echo json_encode([
            'images' => $imagenes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'imagesPerPage' => $imagesPerPage,
            'showing' => count($imagenes)
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "error" => $e->getMessage(),
            "trace" => $e->getTraceAsString()
        ]);
    }
}

// ✅ Guardar una nueva imagen o actualizar la última registrada
function guardarOActualizarImagen($conn)
{
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar que todos los campos requeridos estén presentes
    if (
        !isset($data["url"]) || empty($data["url"]) ||
        !isset($data["url_ia"]) || empty($data["url_ia"]) ||
        !isset($data["observacion"]) || empty($data["observacion"])
    ) {
        echo json_encode(["error" => "Todos los campos son requeridos (url, url_ia, observacion)"]);
        exit;
    }

    // Escapar todos los valores para prevenir SQL injection
    $url = $conn->real_escape_string($data["url"]);
    $url_ia = $conn->real_escape_string($data["url_ia"]);
    $observacion = $conn->real_escape_string($data["observacion"]);

    // Insertar nueva imagen
    $sql = "INSERT INTO imagenes (url, url_ia, observacion) VALUES ('$url', '$url_ia', '$observacion')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            "message" => "Imagen guardada exitosamente",
            "id" => $conn->insert_id,
            "data" => [
                "url" => $url,
                "url_ia" => $url_ia,
                "observacion" => $observacion
            ]
        ]);
    } else {
        echo json_encode(["error" => "Error al guardar la imagen: " . $conn->error]);
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
