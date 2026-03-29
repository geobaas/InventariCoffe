<?php
include 'db.php';
header('Content-Type: application/json');

if (!isset($_GET['venta_id'])) {
    echo json_encode(["success" => false, "error" => "venta_id no especificado"]);
    exit();
}
$venta_id = intval($_GET['venta_id']);
$errors = array();

// 1. Obtener los productos vendidos para esta venta
$stmt = $conn->prepare("SELECT producto_id, cantidad FROM detalle_ventas WHERE venta_id = ?");
$stmt->bind_param("i", $venta_id);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "error" => $stmt->error]);
    exit();
}
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $producto_id = $row['producto_id'];
    $cantidad_vendida = $row['cantidad'];
    
    // 2. Obtener los insumos requeridos para este producto
    $stmt2 = $conn->prepare("SELECT insumo_id, cantidad FROM productos_insumo WHERE producto_id = ?");
    $stmt2->bind_param("i", $producto_id);
    if (!$stmt2->execute()) {
       $errors[] = "Error al consultar insumos para producto_id $producto_id: " . $stmt2->error;
       continue;
    }
    $res2 = $stmt2->get_result();
    
    // Si no hay insumos definidos para el producto, se continúa sin error
    if ($res2->num_rows == 0) {
        continue;
    }
    
    while ($insumo = $res2->fetch_assoc()) {
         $insumo_id = $insumo['insumo_id'];
         $cantidad_por_producto = $insumo['cantidad']; // cantidad requerida para cada unidad
         $cantidad_total = $cantidad_vendida * $cantidad_por_producto;
         
         // 3. Seleccionar el stock actual del insumo (usando id_insumo)
         $stmtSelect = $conn->prepare("SELECT cantidad_existencias FROM insumos WHERE id_insumo = ?");
         $stmtSelect->bind_param("i", $insumo_id);
         if (!$stmtSelect->execute()) {
             $errors[] = "Error al seleccionar stock para insumo_id $insumo_id: " . $stmtSelect->error;
             continue;
         }
         $stmtSelect->bind_result($stock);
         if (!$stmtSelect->fetch()) {
             $errors[] = "No se encontró stock para insumo_id $insumo_id.";
             $stmtSelect->close();
             continue;
         }
         $stmtSelect->close();
         
         // 4. Si hay stock suficiente, actualizarlo; de lo contrario, acumular error.
         if ($stock < $cantidad_total) {
              $errors[] = "Stock insuficiente para insumo_id $insumo_id: se requieren $cantidad_total, hay $stock.";
         } else {
              $nueva_existencia = $stock - $cantidad_total;
              $stmtUpdate = $conn->prepare("UPDATE insumos SET cantidad_existencias = ? WHERE id_insumo = ?");
              // Usamos "di": d para doble (nueva_existencia) e i para el id_insumo
              $stmtUpdate->bind_param("di", $nueva_existencia, $insumo_id);
              if (!$stmtUpdate->execute()) {
                   $errors[] = "Error al actualizar stock para insumo_id $insumo_id: " . $stmtUpdate->error;
              }
              $stmtUpdate->close();
         }
    }
    $stmt2->close();
}
$stmt->close();

if (count($errors) > 0) {
   echo json_encode(["success" => false, "errors" => $errors]);
} else {
   echo json_encode(["success" => true]);
}
$conn->close();
?>
