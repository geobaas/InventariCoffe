<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['cargo'])) {
    header("Location: login.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];
$cargo_usuario = $_SESSION['cargo']; // Cargo del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Productos - Las Abuelitas</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
   <header>
    <!-- Incluir el menú desde menu.php -->
    <?php include 'menu.php'; ?>
    
    <h1>Listado de Productos</h1>
    <p>Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong> (<?= htmlspecialchars($cargo_usuario) ?>)</p>
  </header>
    <main>
        <section class="contenedor">
            <h2></h2>
            <div id="productosContainer" class="grid">
                <!-- Aquí se cargará dinámicamente el contenido de listado_productos.php -->
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("listado_productos.php")
                .then(response => response.text())
                .then(data => {
                    document.getElementById("productosContainer").innerHTML = data;
                })
                .catch(error => console.error("Error cargando los productos:", error));
        });

        function editarReceta(productoId) {
            window.location.href = `editar_receta.php?producto_id=${productoId}`;
        }

        function editarPrecio() {
            window.location.href = "acceso_restringido.php";
        }
    </script>
</body>
</html>
