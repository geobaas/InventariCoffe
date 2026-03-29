<?php
// procesar_detalle_ventas.php

error_reporting(E_ALL);
ini_set("display_errors", 1);

session_start();
include 'db.php';

// Verificar que se reciba el ID de la venta
if (!isset($_POST["venta_id"]) || empty($_POST["venta_id"])) {
    echo json_encode(["success" => false, "message" => "Falta el ID de la venta."]);
    exit();
}
$venta_id = intval($_POST["venta_id"]);

// Verificar que el carrito tenga productos
if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
    echo json_encode(["success" => false, "message" => "Carrito vacío."]);
    exit();
}

// Insertar cada producto del carrito en "detalle_ventas"
$stmtDetalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
if (!$stmtDetalle) {
    echo json_encode(["success" => false, "message" => "Error en preparación del detalle: " . $conn->error]);
    exit();
}

foreach ($_SESSION["carrito"] as $item) {
    $stmtDetalle->bind_param("iiid", $venta_id, $item["producto_id"], $item["cantidad"], $item["precio"]);
    if (!$stmtDetalle->execute()) {
        echo json_encode(["success" => false, "message" => "Error insertando el detalle: " . $stmtDetalle->error]);
        exit();
    }
}
$stmtDetalle->close();
$conn->close();

echo json_encode(["success" => true, "venta_id" => $venta_id]);
?>
