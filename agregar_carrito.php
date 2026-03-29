<?php
session_start();
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$producto_id = isset($data['id']) ? intval($data['id']) : 0;
$cantidad = isset($data['cantidad']) ? intval($data['cantidad']) : 1;
$imagen = isset($data['imagen']) ? $data['imagen'] : '';
$nombre = isset($data['nombre']) ? $data['nombre'] : '';
$precio = isset($data['precio']) ? floatval($data['precio']) : 0;

if ($producto_id > 0) {
    if (!isset($_SESSION["carrito"])) {
        $_SESSION["carrito"] = [];
    }
    $found = false;
    foreach ($_SESSION["carrito"] as &$item) {
        if ($item["producto_id"] == $producto_id) {
            $item["cantidad"] += $cantidad;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION["carrito"][] = [
            "producto_id" => $producto_id,
            "imagen" => $imagen,
            "nombre" => $nombre,
            "cantidad" => $cantidad,
            "precio" => $precio
        ];
    }
    echo json_encode(["success" => true, "carrito" => $_SESSION["carrito"]]);
} else {
    echo json_encode(["success" => false, "error" => "ID de producto inválido"]);
}
?>
