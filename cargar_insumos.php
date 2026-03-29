<?php
include 'db.php';

$sql = "SELECT id_insumo, nombre, cantidad_existencias, precio_unitario, tipo FROM insumos";
$result = $conn->query($sql);

$insumos = [];
while ($row = $result->fetch_assoc()) {
    $insumos[] = $row;
}

echo json_encode($insumos); // Devuelve insumos con tipo incluido
?>
