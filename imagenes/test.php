<?php
$host = "localhost";
$dbname = "pruebaimagen";
$username = "root";
$password = "";
$host = "localhost:3307";


$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

echo "Conexión exitosa";
$conn->close();
?>
