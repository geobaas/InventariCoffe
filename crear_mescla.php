<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre_mescla = $_POST['nombre_mescla'];
    $rendimiento = $_POST['rendimiento'];
    $insumos_seleccionados = $_POST['insumos_seleccionados'];
    $insumo_cantidad = $_POST['insumo_cantidad'];

    // Calcular existencias iniciales basadas en el rendimiento
    $pack_mescla_exis = 1; // Inicializado con valor predeterminado
    $exis_mescla = $pack_mescla_exis * $rendimiento;

    // Insertar la nueva mescla
    $sql_mescla = "INSERT INTO mesclas (nombre, rendimiento, pack_mescla_exis, exis_mescla) VALUES (?, ?, ?, ?)";
    $stmt_mescla = $conn->prepare($sql_mescla);
    $stmt_mescla->bind_param("sddd", $nombre_mescla, $rendimiento, $pack_mescla_exis, $exis_mescla);
    $stmt_mescla->execute();
    $id_mescla = $stmt_mescla->insert_id;

    // Insertar insumos relacionados
    for ($i = 0; $i < count($insumos_seleccionados); $i++) {
        $id_insumo = $insumos_seleccionados[$i];
        $cantidad_usada = $insumo_cantidad[$i];

        $sql_insumos = "INSERT INTO mesclas_insumos (id_mescla, id_insumo, cantidad) VALUES (?, ?, ?)";
        $stmt_insumos = $conn->prepare($sql_insumos);
        $stmt_insumos->bind_param("iid", $id_mescla, $id_insumo, $cantidad_usada);
        $stmt_insumos->execute();
    }

    echo "<script>alert('✅ Mescla creada correctamente con rendimiento registrado.'); window.location.href='crear_mescla.html';</script>";

    $stmt_mescla->close();
    $conn->close();
}
?>
