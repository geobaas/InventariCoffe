<?php
// procesar_venta.php
error_reporting(E_ALL);
ini_set("display_errors", 1);
session_start();
include 'db.php'; // Archivo que configura la conexión en $conn

// Verificar que el carrito tenga productos
if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
    echo json_encode(["success" => false, "message" => "Carrito vacío."]);
    exit();
}

// Calcular el total de la venta
$total = 0;
foreach ($_SESSION["carrito"] as $item) {
    // Se asume que cada ítem tiene: producto_id, cantidad y precio
    $total += ($item["cantidad"] * $item["precio"]);
}

// Insertar la venta en la tabla "ventas"
$stmt = $conn->prepare("INSERT INTO ventas (total) VALUES (?)");
if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Error preparando venta: " . $conn->error]);
    exit();
}
$stmt->bind_param("d", $total);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Error ejecutando venta: " . $stmt->error]);
    exit();
}
$venta_id = $conn->insert_id;
$stmt->close();

echo json_encode(["success" => true, "venta_id" => $venta_id]);
$conn->close();
?>
