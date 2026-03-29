<?php
include 'db.php'; // Conexión a la base de datos

$data = json_decode(file_get_contents("php://input"), true); // Datos del carrito

if (!empty($data)) {
    // Insertar nueva venta
    $sql_venta = "INSERT INTO ventas (total) VALUES (?)";
    $stmt_venta = $conn->prepare($sql_venta);

    $total = array_reduce($data, function ($carry, $item) {
        return $carry + $item['precio_total'];
    }, 0);

    $stmt_venta->bind_param("d", $total);
    $stmt_venta->execute();
    $id_venta = $stmt_venta->insert_id;

    // Insertar productos vendidos
    foreach ($data as $item) {
        $sql_producto = "INSERT INTO venta_productos (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
        $stmt_producto = $conn->prepare($sql_producto);
        $stmt_producto->bind_param("iiid", $id_venta, $item['id_producto'], $item['cantidad'], $item['precio_unitario']);
        $stmt_producto->execute();

        // Actualizar insumos utilizados
        $sql_insumos = "SELECT id_insumo, cantidad FROM producto_insumos WHERE id_producto = ?";
        $stmt_insumos = $conn->prepare($sql_insumos);
        $stmt_insumos->bind_param("i", $item['id_producto']);
        $stmt_insumos->execute();
        $result_insumos = $stmt_insumos->get_result();

        while ($row = $result_insumos->fetch_assoc()) {
            $sql_movimiento = "INSERT INTO movi_insu_ven (id_venta, id_insumo, cantidad) VALUES (?, ?, ?)";
            $stmt_movimiento = $conn->prepare($sql_movimiento);
            $stmt_movimiento->bind_param("iid", $id_venta, $row['id_insumo'], $row['cantidad'] * $item['cantidad']);
            $stmt_movimiento->execute();

            $sql_actualizar_insumo = "UPDATE insumos SET cantidad_existencias = cantidad_existencias - ? WHERE id_insumo = ?";
            $stmt_actualizar = $conn->prepare($sql_actualizar_insumo);
            $cantidad_total = $row['cantidad'] * $item['cantidad'];
            $stmt_actualizar->bind_param("di", $cantidad_total, $row['id_insumo']);
            $stmt_actualizar->execute();
        }
    }

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}

$conn->close();
?>
