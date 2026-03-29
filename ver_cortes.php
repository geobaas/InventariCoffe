<?php
include 'db.php';

$sql = "SELECT * FROM corte_caja ORDER BY fecha DESC";
$resultado = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cortes de Caja</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h2>Historial de Cortes de Caja</h2>
    <table border="1">
        <tr>
            <th>Fecha</th>
            <th>Total Ventas</th>
            <th>Efectivo en Caja</th>
            <th>Diferencia</th>
            <th>Observaciones</th>
        </tr>

        <?php while ($fila = $resultado->fetch_assoc()) { ?>
        <tr>
            <td><?= $fila['fecha'] ?></td>
            <td><?= $fila['total_ventas'] ?></td>
            <td><?= $fila['efectivo_en_caja'] ?></td>
            <td><?= $fila['diferencia'] ?></td>
            <td><?= $fila['observaciones'] ?></td>
        </tr>
        <?php } ?>
    </table>

    <br>
    <a href="corte_caja.php">Registrar nuevo corte</a>

</body>
</html>
