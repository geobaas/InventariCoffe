<?php
include 'db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = htmlspecialchars($_POST["nombre"]);
    $division = floatval($_POST["division"]);
    $insumosSeleccionados = $_POST["insumos"]; // Recibe los insumos seleccionados como un array JSON

    try {
        if ($division <= 0) {
            throw new Exception("La división debe ser mayor a cero.");
        }

        $sumaTotalInsumos = 0;
        foreach ($insumosSeleccionados as $insumo) {
            $insumoData = json_decode($insumo, true);
            $sumaTotalInsumos += floatval($insumoData["cantidad"]);
        }

        $rendimiento = $sumaTotalInsumos / $division;

        $sql = "INSERT INTO bases (nombre, division, rendimiento) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdd", $nombre, $division, $rendimiento);
        $stmt->execute();
        $baseId = $stmt->insert_id;
        $stmt->close();

        foreach ($insumosSeleccionados as $insumo) {
            $insumoData = json_decode($insumo, true);
            $id_insumo = intval($insumoData["id"]);
            $cantidad = floatval($insumoData["cantidad"]);

            $sql_insumo = "INSERT INTO insumos_mescla (id_base, id_insumo, cantidad_requerida) VALUES (?, ?, ?)";
            $stmt_insumo = $conn->prepare($sql_insumo);
            $stmt_insumo->bind_param("iid", $baseId, $id_insumo, $cantidad);
            $stmt_insumo->execute();
            $stmt_insumo->close();

            $sql_descuento = "UPDATE insumos SET cantidad_existencias = cantidad_existencias - ? WHERE id_insumo = ?";
            $stmt_descuento = $conn->prepare($sql_descuento);
            $stmt_descuento->bind_param("di", $cantidad, $id_insumo);
            $stmt_descuento->execute();
            $stmt_descuento->close();
        }

        echo "<p>Base creada correctamente con rendimiento: " . number_format($rendimiento, 3) . " porciones.</p>";
    } catch (Exception $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

$conn->close();
?>
