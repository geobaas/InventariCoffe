<?php
// procesar_venta.php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set("display_errors", 1);

include 'db.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
    exit;
}

// Obtener el JSON enviado (carrito) y decodificarlo
$json = file_get_contents("php://input");
$carrito = json_decode($json, true);

if (!$carrito || empty($carrito)) {
    echo json_encode(["success" => false, "message" => "El carrito está vacío o el formato no es válido."]);
    exit;
}

// Iniciar transacción (asegúrate de que las tablas involucradas sean InnoDB)
$conn->begin_transaction();

// Calcular el total de venta
$total = 0;
foreach ($carrito as $item) {
    // Se asume que cada objeto tiene el campo 'precio_total'
    $total += $item['precio_total'];
}

// Insertar registro en la tabla "ventas"
$stmtVenta = $conn->prepare("INSERT INTO ventas (total) VALUES (?)");
if (!$stmtVenta) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error preparando venta: " . $conn->error]);
    exit;
}
$stmtVenta->bind_param("d", $total);
if (!$stmtVenta->execute()) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error ejecutando venta: " . $stmtVenta->error]);
    exit;
}
$venta_id = $conn->insert_id;
$stmtVenta->close();

// Insertar el detalle en la tabla "detalle_ventas"
// Se asume que esta tabla tiene los campos: venta_id, producto_id, cantidad, precio
$stmtDetalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
if (!$stmtDetalle) {
    $conn->rollback();
    echo json_encode(["success" => false, "message" => "Error preparando detalle de venta: " . $conn->error]);
    exit;
}
foreach ($carrito as $item) {
    // Usamos 'id_producto', 'cantidad' y 'precio_unitario' del objeto.
    $producto_id = $item['id_producto'];
    $cantidad    = $item['cantidad'];
    $precio      = $item['precio_unitario'];
    
    $stmtDetalle->bind_param("iiid", $venta_id, $producto_id, $cantidad, $precio);
    if (!$stmtDetalle->execute()){
        $conn->rollback();
        echo json_encode(["success" => false, "message" => "Error insertando el detalle de venta: " . $stmtDetalle->error]);
        exit;
    }
}
$stmtDetalle->close();

// (Opcional) Agregar aquí la lógica para actualizar insumos, si utilizas la relación en "productos_insumos"
// Por cada producto vendido, consulta la tabla 'productos_insumos' y actualiza el stock en la tabla 'insumos'.

// Si todo fue bien, confirmar transacción
$conn->commit();
$conn->close();

echo json_encode(["success" => true, "venta_id" => $venta_id]);
exit;
?>
