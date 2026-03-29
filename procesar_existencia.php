<?php
include 'db.php';

if (isset($_POST['id_insumo']) && isset($_POST['cantidad_agregar'])) {
    $id_insumo = $_POST['id_insumo'];
    $cantidad_agregar = $_POST['cantidad_agregar'];

    // Actualizar existencias en la tabla `insumos`
    $sql_update = "UPDATE insumos SET cantidad_existencias = cantidad_existencias + ? WHERE id_insumo = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("di", $cantidad_agregar, $id_insumo);
    $stmt_update->execute();

    // Registrar el movimiento en la tabla `movimientos_insumos`
    $sql_insert = "INSERT INTO movimientos_insumos (id_insumo, cantidad_agregada, fecha) VALUES (?, ?, CURRENT_TIMESTAMP)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("id", $id_insumo, $cantidad_agregar);
    $stmt_insert->execute();

    if ($stmt_update->affected_rows > 0 && $stmt_insert->affected_rows > 0) {
        header("Location: listar_insumos.html"); // Redirige al listado después de confirmar
    } else {
        echo "<p>Error al actualizar las existencias o registrar el movimiento.</p>";
    }

    $stmt_update->close();
    $stmt_insert->close();
}

$conn->close();
?>
