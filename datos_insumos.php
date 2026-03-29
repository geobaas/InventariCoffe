<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id_producto = $_GET['id'];

    $sql = "SELECT insumos.nombre, productos_insumos.cantidad 
            FROM productos_insumos 
            JOIN insumos ON productos_insumos.id_insumos = insumos.id_insumo 
            WHERE productos_insumos.producto_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $result = $stmt->get_result();

    $nombres = [];
    $cantidades = [];

    while ($row = $result->fetch_assoc()) {
        $nombres[] = htmlspecialchars($row['nombre']);
        $cantidades[] = htmlspecialchars($row['cantidad']);
    }

    $stmt->close();
    $conn->close();
}
?>
