<?php
include 'db.php';

if (isset($_POST['id_insumo'])) {
    $id_insumo = $_POST['id_insumo'];

    if ($_POST['accion'] === 'guardar') {
        $imagen_insumo = $_POST['imagen_insumo'];
        $nombre_insumo = $_POST['nombre_insumo'];
        $tipo = $_POST['tipo'];
        $cantidad_por_presentacion = $_POST['cantidad_por_presentacion'];
        $costo_presentacion = $_POST['costo_presentacion'];
        $precio_por_unidad = $_POST['precio_por_unidad'];
        $cantidad_existencias = $_POST['cantidad_existencias'];

        // Actualizar el insumo en la base de datos
        $sql_update = "UPDATE insumos SET imagen = ?, nombre = ?, tipo = ?, cantidad_por_presentacion = ?, costo_presentacion = ?, precio_por_unidad = ?, cantidad_existencias = ? WHERE id_insumo = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssdssdi", $imagen_insumo, $nombre_insumo, $tipo, $cantidad_por_presentacion, $costo_presentacion, $precio_por_unidad, $cantidad_existencias, $id_insumo);
        $stmt_update->execute();

        if ($stmt_update->affected_rows > 0) {
            echo "<p>✅ Se ha guardado con éxito.</p>";
        } else {
            echo "<p>❌ No se realizaron cambios.</p>";
        }

        $stmt_update->close();
    } elseif ($_POST['accion'] === 'eliminar') {
        // Eliminar el insumo de la base de datos
        $sql_delete = "DELETE FROM insumos WHERE id_insumo = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_insumo);
        $stmt_delete->execute();

        if ($stmt_delete->affected_rows > 0) {
            echo "<p>✅ Se ha eliminado con éxito.</p>";
        } else {
            echo "<p>❌ No se pudo eliminar el insumo.</p>";
        }

        $stmt_delete->close();
    } else {
        echo "<p>Error: Acción no válida.</p>";
    }
} else {
    echo "<p>Error: No se recibieron datos válidos.</p>";
}

$conn->close();
?>
