<?php
// procesar_detalle_ventas.php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
include 'db.php';

// Se espera recibir mediante POST el ID de la venta obtenido en el proceso anterior
if (!isset($_POST["venta_id"]) || empty($_POST["venta_id"])) {
    echo json_encode(["success" => false, "message" => "Falta el ID de la venta."]);
    exit();
}
$venta_id = intval($_POST["venta_id"]);

// Verificar que el carrito contenga productos
if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
    echo json_encode(["success" => false, "message" => "Carrito vacío."]);
    exit();
}

// Insertar cada ítem del carrito en la tabla "detalle_ventas"
$stmt = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error preparando detalle: " . $conn->error]);
    exit();
}

foreach ($_SESSION["carrito"] as $item) {
    $producto_id = $item["producto_id"];
    $cantidad = $item["cantidad"];
    $precio = $item["precio"];
    
    $stmt->bind_param("iiid", $venta_id, $producto_id, $cantidad, $precio);
    if (!$stmt->execute()) {
        echo json_encode(["success" => false, "message" => "Error al insertar detalle: " . $stmt->error]);
        exit();
    }
}
$stmt->close();

// (Opcional) Si deseas vaciar el carrito luego de registrar el detalle, descomenta la siguiente línea:
// $_SESSION["carrito"] = [];

echo json_encode(["success" => true, "venta_id" => $venta_id]);
$conn->close();
?>
