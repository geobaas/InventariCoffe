<?php
require_once "db.php"; // Conexión a la base de datos

// RECIBIR DATOS DEL PRODUCTO
$nombre_producto = trim($_POST["nombre_producto"] ?? "");
$descripcion     = trim($_POST["descripcion"] ?? "");
$precio          = floatval($_POST["precio"] ?? 0.00);

// RECIBIR ID DEL PRODUCTO (para saber si se edita o crea uno nuevo)
$id_producto = intval($_POST["id_producto"] ?? 0);

// RECIBIR INSUMOS (formulario envía un array de JSON en 'insumos[]')
$insumosArray = isset($_POST["insumos"]) && is_array($_POST["insumos"]) ? $_POST["insumos"] : [];

// INICIALIZAR ACUMULADORES PARA INSUMOS
$costo_total = 0.00;
$productos_insumos = [];

// PROCESAR CADA INSUMO
foreach ($insumosArray as $insumoJson) {
    $insumo = json_decode($insumoJson, true);
    if ($insumo) {
        $id_insumo = intval($insumo['id']);
        $cantidad  = floatval($insumo['cantidad']);
        $costo     = floatval($insumo['costo']);
        
        $costo_total += $costo;
        
        $productos_insumos[] = [
            "id_insumos" => $id_insumo,
            "cantidad"   => $cantidad,
            "costo"      => $costo
        ];
    }
}

// CALCULAR GANANCIA TOTAL (precio de venta - costo_total)
$ganancia_total = $precio - $costo_total;

// PROCESAR LA FOTO CON EL CÓDIGO DE REFERENCIA
$target_dir = "img/";
$imagen = (!empty($_FILES["imagen"]["name"])) ? $target_dir . basename($_FILES["imagen"]["name"]) : "img/no-image.png";
if (!empty($_FILES["imagen"]["name"])) {
    move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen);
}

// **CREAR O ACTUALIZAR PRODUCTO**
if ($id_producto > 0) {
    // Si se edita el producto; si no se sube una nueva imagen, se conserva la anterior.
    if (!empty($_FILES["imagen"]["name"])) {
        // Actualiza incluyendo la columna de imagen
        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, costo_total = ?, ganancia_total = ?, imagen = ? WHERE id = ?");
        if (!$stmt) {
            die("Error en la actualización del producto: " . $conn->error);
        }
        $stmt->bind_param("ssddssi", $nombre_producto, $descripcion, $precio, $costo_total, $ganancia_total, $imagen, $id_producto);
    } else {
        // Actualiza sin modificar la imagen
        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, costo_total = ?, ganancia_total = ? WHERE id = ?");
        if (!$stmt) {
            die("Error en la actualización del producto: " . $conn->error);
        }
        $stmt->bind_param("ssdddi", $nombre_producto, $descripcion, $precio, $costo_total, $ganancia_total, $id_producto);
    }
} else {
    // Insertar nuevo producto (se guarda la imagen, aunque sea "img/no-image.png")
    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, costo_total, ganancia_total, imagen) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la inserción del producto: " . $conn->error);
    }
    $stmt->bind_param("ssddds", $nombre_producto, $descripcion, $precio, $costo_total, $ganancia_total, $imagen);
}

// Ejecutar la operación sobre el producto
if (!$stmt->execute()) {
    die("Error al guardar producto: " . $stmt->error);
}

if ($id_producto == 0) {
    $id_producto = $conn->insert_id; // Obtener el ID si se creó uno nuevo
}
$stmt->close();

// **ELIMINAR INSUMOS ANTERIORES PARA EVITAR DUPLICADOS**
$stmt_delete = $conn->prepare("DELETE FROM productos_insumos WHERE producto_id = ?");
if (!$stmt_delete) {
    die("Error al limpiar insumos previos: " . $conn->error);
}
$stmt_delete->bind_param("i", $id_producto);
$stmt_delete->execute();
$stmt_delete->close();

// **INSERTAR INSUMOS ACTUALIZADOS EN productos_insumos**
$stmt2 = $conn->prepare("INSERT INTO productos_insumos (producto_id, id_insumos, cantidad, costo) VALUES (?, ?, ?, ?)");
if (!$stmt2) {
    die("Error en la preparación del statement para insumos: " . $conn->error);
}

foreach ($productos_insumos as $insumo_item) {
    $producto_id_bind = $id_producto;
    $id_insumos = $insumo_item["id_insumos"];
    $cantidad   = $insumo_item["cantidad"];
    $costo      = $insumo_item["costo"];

    $stmt2->bind_param("iidd", $producto_id_bind, $id_insumos, $cantidad, $costo);
    if (!$stmt2->execute()) {
        die("Error al insertar insumo (ID: $id_insumos): " . $stmt2->error);
    }
}

$stmt2->close();
$conn->close();

echo "Producto guardado con éxito. La imagen se guardó en: " . $imagen;
?>
