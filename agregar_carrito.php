<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['id'], $data['cantidad'], $data['imagen'], $data['nombre'], $data['precio'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos o inválidos."]);
    exit;
}

$producto_id = intval($data['id']);
$cantidad    = intval($data['cantidad']);
$imagen      = $data['imagen'];
$nombre      = $data['nombre'];
$precio      = floatval($data['precio']);

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

$existe = false;
foreach ($_SESSION["carrito"] as &$item) {
    if ($item["id_producto"] == $producto_id) {
        $item["cantidad"] += $cantidad;
        $item["precio_total"] += $precio * $cantidad;
        $existe = true;
        break;
    }
}
unset($item);

if (!$existe) {
    $_SESSION["carrito"][] = [
        "id_producto"     => $producto_id,
        "nombre"          => $nombre,
        "cantidad"        => $cantidad,
        "precio_unitario" => $precio,
        "precio_total"    => $precio * $cantidad,
        "imagen"          => $imagen
    ];
}

echo json_encode(["success" => true, "carrito" => $_SESSION["carrito"]]);
?>
