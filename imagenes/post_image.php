<?php
//header("Content-Type: application/json");
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Methods: GET, POST");
//header("Access-Control-Allow-Headers: Content-Type");


if($_SERVER['REQUEST_METHOD'] === 'POST' 
&& isset($_GET['url'])) {
//Client::post_image($_GET['url']);
$url = $_GET['url'];
echo json_encode(["message" => "Imagen creada correctamente"].$url);
}
else{
echo json_encode(["message" => "fallado correctamente"]);}
?>
