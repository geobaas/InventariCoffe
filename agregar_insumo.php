<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $tipo = $_POST["tipo"];
    $cantidad_presentacion = $_POST["cantidad_presentacion"];
    $costo = $_POST["costo"];
    $precio_unitario = $_POST["precio_unitario"];
    $cantidad_ingresar = $_POST["cantidad_ingresar"];

    // Manejo de imagen
    $target_dir = "img/";
    $imagen = (!empty($_FILES["imagen"]["name"])) ? $target_dir . basename($_FILES["imagen"]["name"]) : "img/no-image.png";

    if (!empty($_FILES["imagen"]["name"])) {
        move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen);
    }

    // Insertar insumo en la base de datos
    $sql = "INSERT INTO insumos (nombre, tipo, cantidad_presentacion, cantidad_existencias, costo, precio_unitario, imagen)
            VALUES ('$nombre', '$tipo', '$cantidad_presentacion', '$cantidad_ingresar', '$costo', '$precio_unitario', '$imagen')";

    if ($conn->query($sql) === TRUE) {
        $id_insumo = $conn->insert_id;

        // Registrar en movimientos_insumos la cantidad ingresada
        $sql_mov = "INSERT INTO movimientos_insumos (id_insumo, cantidad_agregada) VALUES ('$id_insumo', '$cantidad_ingresar')";
        $conn->query($sql_mov);

        echo "<script>alert('✅ Insumo agregado correctamente'); window.location.href='insumos.php';</script>";
    } else {
        echo "<script>alert('❌ Error al guardar en la base de datos: " . $conn->error . "');</script>";
    }

    $conn->close();
}
?>
