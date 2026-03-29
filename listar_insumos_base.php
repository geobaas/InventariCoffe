<?php
include 'db.php'; // Conexión a la base de datos

$sql = "SELECT id_insumo, nombre FROM insumos"; // Consulta básica para obtener los insumos
$result = $conn->query($sql);

$insumos = [];
while ($row = $result->fetch_assoc()) {
    $insumos[] = [
        "id_insumo" => $row["id_insumo"],
        "nombre" => $row["nombre"]
    ];
}

header('Content-Type: application/json');
echo json_encode($insumos);

$conn->close();
?>
