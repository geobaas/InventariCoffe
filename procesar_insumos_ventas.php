<?php
// procesar_insumos_ventas.php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include 'db.php';

// Se espera recibir el ID de la venta por POST
if (!isset($_POST["venta_id"]) || empty($_POST["venta_id"])) {
    echo json_encode(["success" => false, "message" => "Falta el ID de la venta."]);
    exit();
}
$venta_id = intval($_POST["venta_id"]);

// Recuperar de la tabla detalle_ventas todos los productos vendidos en esta venta
$stmt_det = $conn->prepare("SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?");
if (!$stmt_det) {
    echo json_encode(["success" => false, "message" => "Error preparando consulta de detalle: " . $conn->error]);
    exit();
}
$stmt_det->bind_param("i", $venta_id);
$stmt_det->execute();
$result_det = $stmt_det->get_result();

while ($detalle = $result_det->fetch_assoc()) {
    $producto_id = $detalle["producto_id"];
    $cantidad_vendida = $detalle["cantidad"];
    
    // Para cada producto vendido, obtener en productos_insumos los insumos asociados y la cantidad usada por producto
    $stmt_pi = $conn->prepare("SELECT id_insumos, cantidad FROM productos_insumos WHERE producto_id = ?");
    if (!$stmt_pi) {
        echo json_encode(["success" => false, "message" => "Error preparando consulta de insumos: " . $conn->error]);
        exit();
    }
    $stmt_pi->bind_param("i", $producto_id);
    $stmt_pi->execute();
    $result_pi = $stmt_pi->get_result();
    
    while ($pi = $result_pi->fetch_assoc()) {
        $id_insumo = $pi["id_insumos"];
        $cantidad_por_producto = $pi["cantidad"];
        
        // Calcular la cantidad total a descontar: cantidad_vendida * cantidad de insumo usado por producto
        $deduccion_total = $cantidad_vendida * $cantidad_por_producto;
        
        // Actualizar la tabla insumos: restar la deducción al stock actual
        $stmt_update = $conn->prepare("UPDATE insumos SET cantidad = cantidad - ? WHERE id_insumo = ?");
        if (!$stmt_update) {
            echo json_encode(["success" => false, "message" => "Error preparando actualización de insumo: " . $conn->error]);
            exit();
        }
        $stmt_update->bind_param("di", $deduccion_total, $id_insumo);
        if (!$stmt_update->execute()) {
            echo json_encode(["success" => false, "message" => "Error actualizando insumo: " . $stmt_update->error]);
            exit();
        }
        $stmt_update->close();
    }
    $stmt_pi->close();
}
$stmt_det->close();

echo json_encode(["success" => true, "message" => "Insumos deducidos correctamente para la venta " . $venta_id]);
$conn->close();
?>
