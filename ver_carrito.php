<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php';

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

$sql = "SELECT producto_id, nombre, descripcion, precio, imagen FROM productos";
$result = $conn->query($sql);
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito y Productos - Las Abuelitas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="styles/styles.css">
  <style>
    .shopping-container { display: flex; justify-content: space-between; gap: 20px; padding: 20px; }
    .left-column { width: 60%; }
    .right-column { width: 38%; background-color: #fff; border-radius: 8px; padding: 15px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
    .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; padding: 10px; }
    .card { padding: 10px; text-align: center; border-radius: 8px; box-shadow: 0px 0px 8px #aaa; }
    .card img { width: 100px; height: 100px; border-radius: 5px; margin-bottom: 10px; object-fit: cover; }
    .cart-item { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #ccc; padding: 5px 0; }
    .cart-item img { width: 50px; height: 50px; border-radius: 5px; margin-right: 10px; object-fit: cover; }
    .cart-item button { background: #d32f2f; color: white; padding: 3px 6px; border: none; cursor: pointer; border-radius: 5px; }
    .cart-total { text-align: right; font-size: 16px; font-weight: bold; margin-top: 10px; }
    .notificacion { display: none; background-color: #00796b; color: white; text-align: center; padding: 10px; margin-top: 15px; border-radius: 5px; }
  </style>
</head>
<body>
  <header>
    <h1>Las Abuelitas - Carrito y Productos</h1>
  </header>
  <div class="shopping-container">
    <!-- Sección de productos disponibles -->
    <div class="left-column">
      <h2>Productos Disponibles</h2>
      <div class="grid">
        <?php 
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            $img = htmlspecialchars($row["imagen"]);
            $nombre = htmlspecialchars($row["nombre"]);
            $precio = number_format($row["precio"], 2);
            $producto_id = $row["producto_id"];
            echo "<div class='card'>";
            echo "<img src='{$img}' alt='Imagen de {$nombre}'>";
            echo "<h3>{$nombre}</h3>";
            echo "<p>\${$precio}</p>";
            echo "<form onsubmit='agregarAlCarrito(event, {$producto_id}, \"{$img}\", \"" . addslashes($nombre) . "\", {$row["precio"]})'>";
            echo "<input type='number' id='cantidad_{$producto_id}' min='1' value='1' required>";
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
    <!-- Sección del carrito -->
    <div class="right-column">
      <h2>Carrito de Compras</h2>
      <div id="cart-list">
        <?php
          $total = 0;
          if (!empty($_SESSION["carrito"])) {
              foreach ($_SESSION["carrito"] as $index => $item) {
                  $subtotal = $item["cantidad"] * $item["precio"];
                  echo "<div class='cart-item' id='producto_{$index}'>";
                  echo "<img src='" . htmlspecialchars($item["imagen"]) . "' alt='Imagen'>";
                  echo "<div>";
                  echo "<strong>" . htmlspecialchars($item["nombre"]) . "</strong><br>";
                  echo "\$" . number_format($item["precio"], 2) . " x " . $item["cantidad"] . "<br>";
                  echo "Subtotal: \$" . number_format($subtotal, 2);
                  echo "</div>";
                  echo "<button onclick='eliminarDelCarrito({$index})'>Eliminar</button>";
                  echo "</div>";
                  $total += $subtotal;
              }
              echo "<p class='cart-total' id='cart-total'>Total: \$" . number_format($total, 2) . "</p>";
          } else {
              echo "<p id='cart-total'>No hay productos en el carrito.</p>";
          }
        ?>
      </div>
      <button class="boton" onclick="finalizarCompra()">Finalizar Compra</button>
      <p class="notificacion" id="notificacion">Pedido en proceso</p>
    </div>
  </div>
  <script>
    // Función para agregar productos al carrito
    function agregarAlCarrito(event, producto_id, imagen, nombre, precio) {
      event.preventDefault();
      let cantidad = document.getElementById(`cantidad_${producto_id}`).value;
      fetch('agregar_carrito.php', {
          method: 'POST',
          body: JSON.stringify({ id: producto_id, cantidad: cantidad, imagen: imagen, nombre: nombre, precio: precio }),
          headers: { 'Content-Type': 'application/json' },
          cache: 'no-store'
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              actualizarCarrito(data.carrito);
          } else {
              alert(data.error);
          }
      });
    }

    // Función para eliminar productos del carrito
    function eliminarDelCarrito(index) {
      fetch('eliminar_carrito.php', {
          method: 'POST',
          body: JSON.stringify({ index: index }),
          headers: { 'Content-Type': 'application/json' },
          cache: 'no-store'
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              actualizarCarrito(data.carrito);
          } else {
              alert(data.error);
          }
      });
    }

    // Actualiza la vista del carrito
    function actualizarCarrito(carrito) {
      let cartContainer = document.getElementById("cart-list");
      cartContainer.innerHTML = "";
      let totalCompra = 0;
      carrito.forEach((item, index) => {
          let subtotal = item.cantidad * item.precio;
          totalCompra += subtotal;
          cartContainer.innerHTML += `
            <div class="cart-item" id="producto_${index}">
              <img src="${item.imagen}" alt="Imagen">
              <div>
                <strong>${item.nombre}</strong><br>
                \$${item.precio.toFixed(2)} x ${item.cantidad}<br>
                Subtotal: \$${subtotal.toFixed(2)}
              </div>
              <button onclick="eliminarDelCarrito(${index})">Eliminar</button>
            </div>
          `;
      });
      if (totalCompra > 0) {
          cartContainer.innerHTML += `<p class="cart-total" id="cart-total">Total: \$${totalCompra.toFixed(2)}</p>`;
      } else {
          cartContainer.innerHTML = "<p id='cart-total'>No hay productos en el carrito.</p>";
      }
    }

    // Finaliza la compra: envía la solicitud a la base de datos, vacía el carrito y muestra la notificación
    function finalizarCompra() {
      fetch('finalizar_compra.php', { 
          method: 'POST',
          cache: 'no-store'
      })
      .then(response => response.json())
      .then(data => {
          if(data.success) {
              // Actualiza la vista para vaciar el carrito
              actualizarCarrito([]);
              // Muestra la notificación "Pedido en proceso"
              let notif = document.getElementById("notificacion");
              notif.style.display = "block";
              setTimeout(() => { notif.style.display = "none"; }, 3000);
          } else {
              alert(data.error);
          }
      })
      .catch(err => {
          console.error("Error al finalizar la compra:", err);
      });
    }
  </script>
  <footer>
    <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados</p>
  </footer>
</body>
</html>
