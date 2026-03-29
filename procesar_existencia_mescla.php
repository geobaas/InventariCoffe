<?php
include 'db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mescla = intval($_POST["id_mescla"]);
    $rendimiento = floatval($_POST["rendimiento"]);
    $cantidad_pack = floatval($_POST["cantidad_pack"]);

    try {
        // Obtener las existencias actuales de la mescla
        $sql_mescla = "SELECT exis_mescla FROM mesclas WHERE id_mescla = ?";
        $stmt_mescla = $conn->prepare($sql_mescla);
        $stmt_mescla->bind_param("i", $id_mescla);
        $stmt_mescla->execute();
        $stmt_mescla->bind_result($existencias_actuales);
        $stmt_mescla->fetch();
        $stmt_mescla->close();

        // Calcular las nuevas existencias de la mescla
        $total_existencias_nuevas = $cantidad_pack * $rendimiento;
        $existencias_totales = $existencias_actuales + $total_existencias_nuevas;

        // Actualizar la tabla de mesclas
        $sql_update_mescla = "UPDATE mesclas SET exis_mescla = ?, pack_mescla_exis = ? WHERE id_mescla = ?";
        $stmt_update_mescla = $conn->prepare($sql_update_mescla);
        $stmt_update_mescla->bind_param("dii", $existencias_totales, $cantidad_pack, $id_mescla);
        $stmt_update_mescla->execute();

        // Obtener la lista de insumos necesarios para esta mescla
        $sql_insumos = "SELECT id_insumo, cantidad_requerida FROM insumos_mescla WHERE id_mescla = ?";
        $stmt_insumos = $conn->prepare($sql_insumos);
        $stmt_insumos->bind_param("i", $id_mescla);
        $stmt_insumos->execute();
        $result_insumos = $stmt_insumos->get_result();

        // Actualizar existencias de cada insumo
        while ($row_insumo = $result_insumos->fetch_assoc()) {
            $id_insumo = $row_insumo["id_insumo"];
            $cantidad_requerida = floatval($row_insumo["cantidad_requerida"]);
            $cantidad_total_descontar = $cantidad_pack * $cantidad_requerida;

            // Descontar insumos
            $sql_update_insumo = "UPDATE insumos SET cantidad_existencias = cantidad_existencias - ? WHERE id_insumo = ?";
            $stmt_update_insumo = $conn->prepare($sql_update_insumo);
            $stmt_update_insumo->bind_param("di", $cantidad_total_descontar, $id_insumo);
            $stmt_update_insumo->execute();
        }

        echo json_encode(["status" => "success", "message" => "Existencias de mescla e insumos actualizadas correctamente."]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error al procesar los datos: " . htmlspecialchars($e->getMessage())]);
    }
}

$conn->close();
?>
