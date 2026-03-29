<?php
include 'db.php'; // Conexión a la base de datos

// Consulta para obtener los insumos
$sql = "SELECT id_insumo, nombre FROM insumos"; // Ajusta la tabla y columnas según tu base de datos
$result = $conn->query($sql);

$insumos = [];
while ($row = $result->fetch_assoc()) {
    $insumos[] = [
        "id_insumo" => $row["id_insumo"],
        "nombre" => $row["nombre"]
    ];
}

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($insumos);

$conn->close();
?>
