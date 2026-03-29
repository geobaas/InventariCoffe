<?php
// Validar que todos los parámetros requeridos estén presentes
if (isset($_GET['id']) && isset($_GET['nombre']) && isset($_GET['cantidad'])) {
    $id_insumo = $_GET['id'];
    $nombre = htmlspecialchars($_GET['nombre']);
    $cantidad_actual = number_format($_GET['cantidad'], 3);
} else {
    echo "<p>Error: No se recibieron datos válidos. Revisa los parámetros.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Existencias</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul class="menu">
                <li><a href="index.html">Inicio</a></li>
                <li><a href="inventario.html">Inventario</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="contenedor">
            <h2>Agregar Existencias</h2>
            <form action="procesar_existencia.php" method="POST">
                <!-- Pasar el ID del insumo como campo oculto -->
                <input type="hidden" name="id_insumo" value="<?php echo $id_insumo; ?>">
                <p><strong>Insumo:</strong> <?php echo $nombre; ?></p>
                <p><strong>Existencias actuales:</strong> <?php echo $cantidad_actual; ?></p>
                <label for="cantidad_agregar">Cantidad a agregar:</label>
                <input type="number" id="cantidad_agregar" name="cantidad_agregar" step="0.001" required>
                <button type="submit">Confirmar</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados</p>
    </footer>
</body>
</html>
