<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php'; // Asegúrate de que este archivo configure $conn correctamente.

if (!isset($_SESSION['usuario']) || !isset($_SESSION['cargo'])) {
    header("Location: login.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];
$cargo_usuario = $_SESSION['cargo']; // Cargo del usuario

  
if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = array();
}

/* ============================
   Proceso de solicitudes POST
   ============================ */

// 1. Agregar producto al carrito (cuando se envía "producto_id" y "cantidad")
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["producto_id"]) && !isset($_POST["finalizar_compra"]) && !isset($_POST["eliminar"])) {
    $producto_id = intval($_POST["producto_id"]);
    $cantidad = intval($_POST["cantidad"]);

    // Obtener datos del producto desde la tabla productos
    $stmt = $conn->prepare("SELECT nombre, precio, imagen FROM productos WHERE producto_id = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $producto = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($producto) {
        // Buscar insumos asociados al producto desde productos_insumos
        // NOTA: Aquí usamos "id_insumos" ya que ese es el nombre real en esa tabla.
        $stmtInsumos = $conn->prepare("SELECT id_insumos, cantidad FROM productos_insumos WHERE producto_id = ?");
        $stmtInsumos->bind_param("i", $producto_id);
        $stmtInsumos->execute();
        $resultInsumos = $stmtInsumos->get_result();
        $insumos = array();
        while ($row = $resultInsumos->fetch_assoc()) {
            $insumos[] = $row;
        }
        $stmtInsumos->close();

        // Verificar si el producto ya está en el carrito
        $existe = false;
        foreach ($_SESSION["carrito"] as &$item) {
            if ($item["id_producto"] == $producto_id) {
                $item["cantidad"] += $cantidad;
                $item["precio_total"] += $producto["precio"] * $cantidad;
                $existe = true;
                break;
            }
        }
        unset($item);

        if (!$existe) {
            $_SESSION["carrito"][] = array(
                "id_producto"     => $producto_id,
                "nombre"          => $producto["nombre"],
                "cantidad"        => $cantidad,
                "precio_unitario" => $producto["precio"],
                "precio_total"    => $producto["precio"] * $cantidad,
                "imagen"          => $producto["imagen"],
                "insumos"         => $insumos    // Guardamos aquí los insumos asociados
            );
        }
// Mostrar un mensaje emergente de éxito
echo "<script>alert('El producto se ha agregado con éxito al carrito');</script>";

    }
}

// 2. Eliminar producto del carrito (cuando se envía "eliminar")
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["eliminar"])) {
    $index = intval($_POST["eliminar"]);
    if (isset($_SESSION["carrito"][$index])) {
        array_splice($_SESSION["carrito"], $index, 1);
    }
}

// 3. Finalizar compra (cuando se envía "finalizar_compra")
// Registra la venta en la tabla ventas, inserta cada ítem en detalle_ventas y descuenta los insumos
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["finalizar_compra"])) {
    if (empty($_SESSION["carrito"])) {
        echo "<script>alert('El carrito está vacío. No se puede finalizar la compra.');</script>";
    } else {
        // Iniciar transacción
        $conn->begin_transaction();
        try {
            // Calcular total
            $total = 0;
            foreach ($_SESSION["carrito"] as $item) {
                $total += ($item["cantidad"] * $item["precio_unitario"]);
            }

            // Insertar en ventas (se asume que la columna fecha se genera automáticamente)
            $stmtVenta = $conn->prepare("INSERT INTO ventas (total) VALUES (?)");
            $stmtVenta->bind_param("d", $total);
            if (!$stmtVenta->execute()) {
                throw new Exception("Error al registrar la venta.");
            }
            $venta_id = $conn->insert_id;
            $stmtVenta->close();

            // Insertar cada producto en detalle_ventas
            $stmtDetalle = $conn->prepare("INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
            foreach ($_SESSION["carrito"] as $item) {
                $stmtDetalle->bind_param("iiid", $venta_id, $item["id_producto"], $item["cantidad"], $item["precio_unitario"]);
                if (!$stmtDetalle->execute()) {
                    throw new Exception("Error al registrar el detalle de venta.");
                }
            }
            $stmtDetalle->close();

            // Descontar insumos desde la tabla insumos en base a productos_insumos
            foreach ($_SESSION["carrito"] as $item) {
                $producto_id = $item["id_producto"];
                $cantidad_vendida = $item["cantidad"];

                // Obtener insumos asociados
                $stmtPI = $conn->prepare("SELECT id_insumos, cantidad FROM productos_insumos WHERE producto_id = ?");
                $stmtPI->bind_param("i", $producto_id);
                $stmtPI->execute();
                $resultPI = $stmtPI->get_result();
                while ($pi = $resultPI->fetch_assoc()) {
                    $id_insumos = $pi["id_insumos"];
                    $cantidad_por_producto = $pi["cantidad"];
                    $deduccion_total = $cantidad_vendida * $cantidad_por_producto;

                    // Actualizar la tabla insumos: se descuenta de cantidad_existencias
                    $stmtUpd = $conn->prepare("UPDATE insumos SET cantidad_existencias = cantidad_existencias - ? WHERE id_insumo = ?");
                    $stmtUpd->bind_param("di", $deduccion_total, $id_insumos);
                    if (!$stmtUpd->execute()) {
                        throw new Exception("Error al actualizar insumos.");
                    }
                    $stmtUpd->close();
                }
                $stmtPI->close();
            }

            // Confirmar la transacción
            $conn->commit();
            $_SESSION["carrito"] = array();
            echo "<script>alert('Compra realizada con éxito. ID de venta: $venta_id. Insumos descontados correctamente.');</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('".$e->getMessage()."');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito y Productos - Las Abuelitas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Puedes enlazar tu archivo externo de estilos; aquí también se incluyen algunas reglas inline -->
  <link rel="stylesheet" href="styles/styles.css">
  <style>
    .shopping-container { display: flex; gap: 20px; padding: 20px; }
    .left-column { width: 70%; }
    .right-column { width: 30%; background-color: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
    .card { padding: 10px; text-align: center; border-radius: 8px; box-shadow: 0 0 8px #aaa; }
    .card img { width: 100px; height: 100px; border-radius: 5px; margin-bottom: 10px; object-fit: cover; }
    .cart-item { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; padding: 5px; border-bottom: 1px solid #ccc; }
    .cart-item img { width: 50px; height: 50px; border-radius: 5px; margin-right: 10px; object-fit: cover; }
    .cart-total { font-weight: bold; text-align: right; margin-top: 10px; }
  </style>
</head>
<body>
   <header>
    <!-- Incluir el menú desde menu.php -->
    <?php include 'menu.php'; ?>
    
    <h1>Carrito de venta</h1>
    <p>Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong> (<?= htmlspecialchars($cargo_usuario) ?>)</p>
  </header>

  <div class="shopping-container">
    <!-- Lista de productos: columna izquierda -->
    <div class="left-column">
      <h2>Productos Disponibles</h2>
      <div class="grid">
        <?php
         $sql = "SELECT producto_id, nombre, descripcion, precio, imagen FROM productos";
         $result = $conn->query($sql);
         if ($result) {
             while ($row = $result->fetch_assoc()) {
                 echo "<div class='card'>";
                 echo "<img src='" . htmlspecialchars($row["imagen"]) . "' alt='Imagen'>";
                 echo "<h3>" . htmlspecialchars($row["nombre"]) . "</h3>";
                 echo "<p>$" . number_format($row["precio"], 2) . "</p>";
                 echo "<form method='POST'>";
                 echo "<input type='hidden' name='producto_id' value='" . $row["producto_id"] . "'>";
                 echo "<input type='number' name='cantidad' min='1' value='1' required>";
                 echo "<button type='submit' class='agregar'>Agregar</button>";
                 echo "</form>";
                 echo "</div>";
             }
         } else {
             echo "<p>No hay productos disponibles.</p>";
         }
        ?>
      </div>
    </div>
    <!-- Carrito: columna derecha -->
    <div class="right-column">
      <h2>Carrito de Compras</h2>
      <?php 
         $total = 0;
         if (!empty($_SESSION["carrito"])) {
             foreach ($_SESSION["carrito"] as $index => $item) {
                 $subtotal = $item["cantidad"] * $item["precio_unitario"];
                 echo "<div class='cart-item'>";
                 echo "<img src='" . htmlspecialchars($item["imagen"]) . "' alt='Imagen'>";
                 echo "<div>";
                 echo "<strong>" . htmlspecialchars($item["nombre"]) . "</strong><br>";
                 echo "$" . number_format($item["precio_unitario"], 2) . " x " . $item["cantidad"] . "<br>";
                 echo "Subtotal: $" . number_format($subtotal, 2);
                 echo "</div>";
                 echo "<form method='POST' style='margin:0;'>";
                 echo "<input type='hidden' name='eliminar' value='" . $index . "'>";
                 echo "<button type='submit'>Eliminar</button>";
                 echo "</form>";
                 echo "</div>";
                 $total += $subtotal;
             }
             echo "<p class='cart-total'>Total: $" . number_format($total,2) . "</p>";
         } else {
             echo "<p>No hay productos en el carrito.</p>";
         }
      ?>
      <form method="POST">
         <input type="hidden" name="finalizar_compra" value="1">
         <button type="submit" class="boton">Finalizar Compra</button>
      </form>
    </div>
  </div>
</body>
</html>
