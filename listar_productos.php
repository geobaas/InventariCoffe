<?php
// listar_productos.php
header("Content-Type: application/json");
include 'db.php';

$sql = "SELECT producto_id, nombre, precio FROM productos";
$result = $conn->query($sql);
$productos = [];

while ($row = $result->fetch_assoc()) {
    // Convertir precio a número (opcional)
    $row['precio'] = floatval($row['precio']);
    $row['id_producto'] = intval($row['producto_id']); // para usarlo en el front
    $productos[] = $row;
}

echo json_encode($productos);
$conn->close();
?>
