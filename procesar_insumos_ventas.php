<?php
// procesar_insumos_ventas.php

error_reporting(E_ALL);
ini_set("display_errors", 1);
include 'db.php';

// Verificar que se reciba el ID de la venta por POST
if (!isset($_POST["venta_id"]) || empty($_POST["venta_id"])) {
    echo json_encode(["success" => false, "message" => "Falta el ID de la venta."]);
    exit();
}
$venta_id = intval($_POST["venta_id"]);

// Obtener los productos vendidos en esta venta
$stmtDetalle = $conn->prepare("SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?");
if (!$stmtDetalle) {
    echo json_encode(["success" => false, "message" => "Error preparando consulta del detalle: " . $conn->error]);
    exit();
}
$stmtDetalle->bind_param("i", $venta_id);
$stmtDetalle->execute();
$resultDetalle = $stmtDetalle->get_result();

while ($detalle = $resultDetalle->fetch_assoc()) {
    $producto_id = $detalle["producto_id"];
    $cantidad_vendida = $detalle["cantidad"];
    
    // Obtener de "productos_insumos" los insumos asociados a este producto
    $stmtPI = $conn->prepare("SELECT id_insumo, cantidad FROM productos_insumos WHERE id_producto = ?");
    if (!$stmtPI) {
         echo json_encode(["success" => false, "message" => "Error preparando consulta de insumos: " . $conn->error]);
         exit();
    }
    $stmtPI->bind_param("i", $producto_id);
    $stmtPI->execute();
    $resultPI = $stmtPI->get_result();
    
    while ($pi = $resultPI->fetch_assoc()) {
         $id_insumo = $pi["id_insumo"];
         $cantidad_insumo_por_producto = $pi["cantidad"];
         // Calcular total a descuenta: cantidad vendida x cantidad de insumo por producto
         $deduccion_total = $cantidad_vendida * $cantidad_insumo_por_producto;
         
         // Actualizar el stock en la tabla "insumos"
         $stmtUpd = $conn->prepare("UPDATE insumos SET cantidad_existencias = cantidad_existencias - ? WHERE id_insumo = ?");
         if (!$stmtUpd) {
             echo json_encode(["success" => false, "message" => "Error preparando actualización en insumos: " . $conn->error]);
             exit();
         }
         $stmtUpd->bind_param("di", $deduccion_total, $id_insumo);
         if (!$stmtUpd->execute()) {
             echo json_encode(["success" => false, "message" => "Error actualizando insumo: " . $stmtUpd->error]);
             exit();
         }
         $stmtUpd->close();
    }
    $stmtPI->close();
}
$stmtDetalle->close();
$conn->close();

echo json_encode(["success" => true, "message" => "Insumos actualizados correctamente para la venta " . $venta_id]);
?>
