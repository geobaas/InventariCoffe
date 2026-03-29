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
    <title>Inventario - Las Abuelitas</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
     <header>
    <!-- Incluir el menú desde menu.php -->
    <?php include 'menu.php'; ?>
    
    <h1>Listado de Insumos</h1>
    <p>Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong> (<?= htmlspecialchars($cargo_usuario) ?>)</p>
  </header>

    <main>
        <section class="contenedor">
            <h2></h2>
            <div id="insumosContainer" class="grid">
                <!-- Aquí se cargará dinámicamente el contenido de listar_insumos.php -->
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados</p>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("listar_insumos.php")
                .then(response => response.text())
                .then(data => {
                    document.getElementById("insumosContainer").innerHTML = data;
                })
                .catch(error => console.error("Error cargando los insumos:", error));
        });
    </script>
</body>
</html>
<?php
mysqli_close($conn); // Cerrar conexión a la base de datos
?>