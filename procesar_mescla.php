<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_mescla = $_POST['nombre_mescla'];
    $rendimiento = $_POST['rendimiento'];
    $insumo_nombres = $_POST['insumo_nombre'];
    $insumo_cantidades = $_POST['insumo_cantidad'];

    // Insertar la mescla en la base de datos
    $sql_mescla = "INSERT INTO mesclas (nombre, rendimiento) VALUES (?, ?)";
    $stmt_mescla = $conn->prepare($sql_mescla);
    $stmt_mescla->bind_param("sd", $nombre_mescla, $rendimiento);
    $stmt_mescla->execute();
    $id_mescla = $conn->insert_id;

    // Insertar insumos de la mescla
    for ($i = 0; $i < count($insumo_nombres); $i++) {
        $sql_insumo = "INSERT INTO mescla_insumos (id_mescla, nombre_insumo, cantidad) VALUES (?, ?, ?)";
        $stmt_insumo = $conn->prepare($sql_insumo);
        $stmt_insumo->bind_param("isd", $id_mescla, $insumo_nombres[$i], $insumo_cantidades[$i]);
        $stmt_insumo->execute();
    }

    echo "<script>alert('✅ Mescla creada correctamente.'); window.location.href='mesclas.html';</script>";

    $stmt_mescla->close();
    $conn->close();
}
?>
