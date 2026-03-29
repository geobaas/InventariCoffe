<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
header("Content-Type: application/json");

include 'db.php';

if (!isset($_SESSION["carrito"]) || empty($_SESSION["carrito"])) {
    echo json_encode(["success" => false, "error" => "El carrito está vacío."]);
    exit;
}

$conn->begin_transaction();

try {
    $total = 0;
    foreach ($_SESSION["carrito"] as $item) {
        $total += ($item["cantidad"] * $item["precio_unitario"]);
    }
    
    $stmtVenta = $conn->prepare("INSERT INTO ventas (total) VALUES (?)");
    $stmtVenta->bind_param("d", $total);
    $stmtVenta->execute();
    $venta_id = $conn->insert_id;
    $stmtVenta->close();
    
    $stmtDetalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION["carrito"] as $item) {
        $stmtDetalle->bind_param("iiid", $venta_id, $item["id_producto"], $item["cantidad"], $item["precio_unitario"]);
        $stmtDetalle->execute();
    }
    $stmtDetalle->close();
    
    $conn->commit();
    $_SESSION["carrito"] = [];
    echo json_encode(["success" => true, "venta_id" => $venta_id]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
?>
