<?php
include 'db.php'; // Conexión a la base de datos

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar datos del formulario
    $nombre_base = $_POST['nombre_base'];
    $insumos_nombres = $_POST['insumo_nombre']; // Array con nombres de insumos
    $insumos_cantidades = $_POST['insumo_cantidad']; // Array con cantidades de insumos
    $cantidad_dividir = $_POST['cantidad_dividir']; // Cantidad ingresada para dividir

    // Calcular rendimiento automáticamente
    $rendimiento_base = array_sum($insumos_cantidades) / $cantidad_dividir;

    // Insertar la base en la tabla `bases`
    $sql_base = "INSERT INTO bases (nombre, rendimiento) VALUES (?, ?)";
    $stmt_base = $conn->prepare($sql_base);
    $stmt_base->bind_param("sd", $nombre_base, $rendimiento_base);
    $stmt_base->execute();
    $id_base = $conn->insert_id; // Obtener el ID de la base recién creada

    // Insertar insumos utilizados en la tabla `base_insumos`
    for ($i = 0; $i < count($insumos_nombres); $i++) {
        $sql_insumo = "INSERT INTO base_insumos (id_base, nombre_insumo, cantidad) VALUES (?, ?, ?)";
        $stmt_insumo = $conn->prepare($sql_insumo);
        $stmt_insumo->bind_param("isd", $id_base, $insumos_nombres[$i], $insumos_cantidades[$i]);
        $stmt_insumo->execute();
    }

    // Confirmar éxito o mostrar error
    if ($stmt_base->affected_rows > 0) {
        echo "<script>alert('✅ Base creada correctamente.'); window.location.href='bases.html';</script>";
    } else {
        echo "<script>alert('❌ Error al crear la base.'); window.location.href='crear_base.html';</script>";
    }

    // Cerrar conexiones
    $stmt_base->close();
    $conn->close();
}
?>
