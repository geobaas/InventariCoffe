<?php
include 'db.php'; // Conexión a la base de datos

// Obtener el ID del producto desde la URL usando "producto_id"
$producto_id = isset($_GET["producto_id"]) ? intval($_GET["producto_id"]) : null;
if (!$producto_id) {
    die("Producto no especificado.");
}

// **Procesar actualización de cantidad si se envió el formulario**
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_insumo = intval($_POST["id"]);
    $cantidad_nueva = floatval($_POST["cantidad"]);

    if ($id_insumo > 0 && $cantidad_nueva >= 0) {
        $sql_update = "UPDATE productos_insumos SET cantidad = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("di", $cantidad_nueva, $id_insumo);
        
        if ($stmt_update->execute()) {
            echo "<p style='color: green; text-align:center;'>Cantidad actualizada correctamente.</p>";
        } else {
            echo "<p style='color: red; text-align:center;'>Error al actualizar.</p>";
        }
        
        $stmt_update->close();
    } else {
        echo "<p style='color: red; text-align:center;'>Datos inválidos.</p>";
    }
}

// Consultar la información del producto usando producto_id
$sql_producto = "SELECT nombre FROM productos WHERE producto_id = ?";
$stmt_producto = $conn->prepare($sql_producto);
$stmt_producto->bind_param("i", $producto_id);
$stmt_producto->execute();
$result_producto = $stmt_producto->get_result();
$producto = $result_producto->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado.");
}

// Consultar los insumos relacionados con el producto
$sql_insumos = "SELECT productos_insumos.id, insumos.nombre, productos_insumos.cantidad 
                FROM productos_insumos 
                JOIN insumos ON productos_insumos.id_insumos = insumos.id_insumo 
                WHERE productos_insumos.producto_id = ?";
$stmt_insumos = $conn->prepare($sql_insumos);
$stmt_insumos->bind_param("i", $producto_id);
$stmt_insumos->execute();
$result_insumos = $stmt_insumos->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <style>
        .contenedor {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 40px;
        }
        .producto {
            width: 400px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: center;
            background: #f7f1e3;
            font-size: 20px;
            font-weight: bold;
        }
        .insumos {
            width: 500px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #e0f2f1;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f7f1e3;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Editar Producto</h2>

<div class="contenedor">
    <!-- Mostrar el nombre del producto -->
    <div class="producto">
        <p><?php echo htmlspecialchars($producto["nombre"]); ?></p>
    </div>

    <!-- Tabla con insumos y campo de edición -->
    <div class="insumos">
        <h3>Insumos Asociados</h3>

        <?php if ($result_insumos->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Insumo</th>
                    <th>Cantidad</th>
                    <th>Editar</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_insumos->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row["nombre"]); ?></td>
                        <td><?php echo $row["cantidad"]; ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <input type="number" name="cantidad" step="0.001" value="<?php echo $row['cantidad']; ?>" required>
                                <button type="submit">Guardar</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">No se encontraron insumos para este producto.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
