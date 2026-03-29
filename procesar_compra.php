<?php
session_start();
include 'db.php';

if (!empty($_SESSION["carrito"])) {
    $total_venta = 0;

    // Calcular el total de la venta
    foreach ($_SESSION["carrito"] as $item) {
        $total_venta += ($item['cantidad'] * $item['precio']);
    }

    // Registrar la venta en la base de datos
    $sql_venta = "INSERT INTO ventas (total, fecha) VALUES (?, NOW())";
    $stmt_venta = $conn->prepare($sql_venta);
    $stmt_venta->bind_param("d", $total_venta);
    if (!$stmt_venta->execute()) {
        echo json_encode(["success" => false, "error" => "Error al insertar venta: " . $stmt_venta->error]);
        exit();
    }
    $venta_id = $conn->insert_id;
    $stmt_venta->close();

    // Guardar los productos en `detalle_ventas`
    $stmt_detalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION["carrito"] as $item) {
        $producto_id = $item["producto_id"];
        $cantidad = $item["cantidad"];
        $precio = $item["precio"];
        $stmt_detalle->bind_param("iiid", $venta_id, $producto_id, $cantidad, $precio);
        if (!$stmt_detalle->execute()) {
            echo json_encode(["success" => false, "error" => "Error al insertar producto en detalle_ventas: " . $stmt_detalle->error]);
            exit();
        }
    }
    $stmt_detalle->close();

    // Vaciar el carrito
    unset($_SESSION["carrito"]);

    echo json_encode(["success" => true, "message" => "Compra realizada con éxito", "venta_id" => $venta_id]);
} else {
    echo json_encode(["success" => false, "message" => "No hay productos en el carrito"]);
}

$conn->close();
?>
