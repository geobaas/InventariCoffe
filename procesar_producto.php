<?php
require_once "db.php"; // Conexión a la base de datos

// RECIBIR DATOS DEL PRODUCTO
$nombre_producto = trim($_POST["nombre_producto"] ?? "");
$descripcion     = trim($_POST["descripcion"] ?? "");
$precio          = floatval($_POST["precio"] ?? 0.00);

// RECIBIR ID DEL PRODUCTO (0 para nuevo, >0 para edición)
$id_producto = intval($_POST["id_producto"] ?? 0);

// RECIBIR INSUMOS (cada "insumos[]" es una cadena JSON)
$insumosArray = isset($_POST["insumos"]) && is_array($_POST["insumos"]) ? $_POST["insumos"] : [];

// INICIALIZAR ACUMULADOR Y ARREGLO DE INSUMOS
$costo_total = 0.00;
$productos_insumos = [];

// Si no se han recibido insumos y el producto ya existe, los recupera de la BD
if (empty($insumosArray)) {
  if ($id_producto > 0) {
    $consulta = "SELECT id_insumos, cantidad, costo FROM productos_insumos WHERE producto_id = ?";
    $stmt = $conn->prepare($consulta);
    if (!$stmt) { die("Error en prepare: " . $conn->error); }
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()){
      $productos_insumos[] = [
        "id_insumos" => intval($fila["id_insumos"]),
        "cantidad"   => floatval($fila["cantidad"]),
        "costo"      => floatval($fila["costo"])
      ];
      $costo_total += floatval($fila["costo"]) * floatval($fila["cantidad"]);
    }
    $stmt->close();
  } else {
    die("Error: No se han recibido insumos para el producto.");
  }
} else {
  foreach ($insumosArray as $insumoJson) {
    $insumo = json_decode($insumoJson, true);
    if ($insumo) {
      $id_insumo = intval($insumo['id']);
      $cantidad  = floatval($insumo['cantidad']);
      $costo     = floatval($insumo['costo']);
      $costo_total += $cantidad * $costo;
      $productos_insumos[] = [
        "id_insumos" => $id_insumo,
        "cantidad"   => $cantidad,
        "costo"      => $costo
      ];
    }
  }
}

// CALCULAR GANANCIA TOTAL: precio de venta - costo_total
$ganancia_total = $precio - $costo_total;

// Evitar guardar si el costo_total es 0 o menor
if ($costo_total <= 0) {
  die("Error: El costo total no puede ser 0.");
}

// PROCESAR LA IMAGEN DEL PRODUCTO (campo: imagen_producto)
$target_dir = "img/";
$imagen = (!empty($_FILES["imagen_producto"]["name"])) 
          ? $target_dir . basename($_FILES["imagen_producto"]["name"]) 
          : "img/no-image.png";
if (!empty($_FILES["imagen_producto"]["name"])) {
  move_uploaded_file($_FILES["imagen_producto"]["tmp_name"], $imagen);
}

// CREAR O ACTUALIZAR PRODUCTO EN LA BD
if ($id_producto > 0) {
  if (!empty($_FILES["imagen_producto"]["name"])) {
    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, costo_total = ?, ganancia_total = ?, imagen = ? WHERE id = ?");
    if (!$stmt) { die("Error en la actualización del producto: " . $conn->error); }
    $stmt->bind_param("ssddssi", $nombre_producto, $descripcion, $precio, $costo_total, $ganancia_total, $imagen, $id_producto);
  } else {
    $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, costo_total = ?, ganancia_total = ? WHERE id = ?");
    if (!$stmt) { die("Error en la actualización del producto: " . $conn->error); }
    $stmt->bind_param("ssdddi", $nombre_producto, $descripcion, $precio, $costo_total, $ganancia_total, $id_producto);
  }
} else {
  $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, costo_total, ganancia_total, imagen) VALUES (?, ?, ?, ?, ?, ?)");
  if (!$stmt) { die("Error en la inserción del producto: " . $conn->error); }
  $stmt->bind_param("ssddds", $nombre_producto, $descripcion, $precio, $costo_total, $ganancia_total, $imagen);
}

if (!$stmt->execute()) {
  die("Error al guardar producto: " . $stmt->error);
}
if ($id_producto == 0) {
  $id_producto = $conn->insert_id;
}
$stmt->close();

// ELIMINAR INSUMOS PREVIOS PARA EVITAR DUPLICADOS
$stmt_delete = $conn->prepare("DELETE FROM productos_insumos WHERE producto_id = ?");
if (!$stmt_delete) { die("Error al limpiar insumos previos: " . $conn->error); }
$stmt_delete->bind_param("i", $id_producto);
$stmt_delete->execute();
$stmt_delete->close();

// INSERTAR LOS INSUMOS ACTUALIZADOS EN productos_insumos
$stmt2 = $conn->prepare("INSERT INTO productos_insumos (producto_id, id_insumos, cantidad, costo) VALUES (?, ?, ?, ?)");
if (!$stmt2) { die("Error en la preparación del statement para insumos: " . $conn->error); }
foreach ($productos_insumos as $insumo_item) {
  $producto_id_bind = $id_producto;
  $id_insumos = intval($insumo_item["id_insumos"]);
  $cantidad   = floatval($insumo_item["cantidad"]);
  $costo      = floatval($insumo_item["costo"]);
  
  $stmt2->bind_param("iidd", $producto_id_bind, $id_insumos, $cantidad, $costo);
  if (!$stmt2->execute()) {
    die("Error al insertar insumo (ID: $id_insumos): " . $stmt2->error);
  }
}
$stmt2->close();
$conn->close();

echo "Se han guardado los datos con éxito. Favor de editar la receta si es necesario.";
?>
