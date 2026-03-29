<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['index'])) {
    echo json_encode(["success" => false, "error" => "Datos inválidos."]);
    exit;
}

$index = intval($data['index']);
if (isset($_SESSION["carrito"]) && array_key_exists($index, $_SESSION["carrito"])) {
    array_splice($_SESSION["carrito"], $index, 1);
    echo json_encode(["success" => true, "carrito" => $_SESSION["carrito"]]);
} else {
    echo json_encode(["success" => false, "error" => "Índice no válido."]);
}
?>
