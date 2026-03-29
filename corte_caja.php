<?php
require "db.php";

// Obtener productos vendidos en el día actual, agrupados por nombre
$sql_productos = "SELECT producto, SUM(cantidad) AS cantidad_total, SUM(total) AS total_vendido 
                  FROM detalles_venta WHERE fecha = CURDATE() GROUP BY producto";
$resultado_productos = $conn->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

    <h2>Registrar Corte de Caja</h2>

    <!-- Tabla para mostrar productos vendidos en el día actual -->
    <h3>Ventas del día <?= date("Y-m-d") ?></h3>
    <table border="1">
        <tr>
            <th>Producto</th>
            <th>Cantidad Vendida</th>
            <th>Total Vendido</th>
        </tr>
        <?php 
        $total_ventas = 0;
        while ($fila = $resultado_productos->fetch_assoc()) { 
            $total_ventas += $fila['total_vendido'];
        ?>
        <tr>
            <td><?= htmlspecialchars($fila['producto']) ?></td>
            <td><?= $fila['cantidad_total'] ?></td>
            <td><?= number_format($fila['total_vendido'], 2) ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td><strong>Total del día:</strong></td>
            <td colspan="2"><strong><?= number_format($total_ventas, 2) ?></strong></td>
        </tr>
    </table>

    <!-- Formulario para registrar el corte de caja -->
    <form action="procesar_corte.php" method="POST">
        <label>Efectivo en caja:</label>
        <input type="number" step="0.01" name="efectivo_en_caja" required>

        <label>Observaciones:</label>
        <textarea name="observaciones"></textarea>

        <!-- Se envía el total de ventas del día -->
        <input type="hidden" name="total_ventas" value="<?= $total_ventas ?>">
        <button type="submit">Registrar corte</button>
    </form>

    <br>
    <a href="ver_cortes.php">Ver cortes anteriores</a>

</body>
</html>
