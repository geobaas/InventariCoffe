<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$index = isset($data['index']) ? intval($data['index']) : -1;

if ($index >= 0 && isset($_SESSION["carrito"][$index])) {
    unset($_SESSION["carrito"][$index]);
    $_SESSION["carrito"] = array_values($_SESSION["carrito"]);
    echo json_encode(["success" => true, "carrito" => $_SESSION["carrito"]]);
} else {
    echo json_encode(["success" => false, "error" => "Índice inválido o producto no encontrado"]);
}
?>
