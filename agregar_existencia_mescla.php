<?php
// Validar que todos los parámetros requeridos estén presentes
if (isset($_GET['id']) && isset($_GET['nombre']) && isset($_GET['rendimiento']) && isset($_GET['existencias']) && isset($_GET['pack_mescla_exis'])) {

    $id_mescla = $_GET['id'];
    $nombre = htmlspecialchars($_GET['nombre']);
    $rendimiento = floatval($_GET['rendimiento']);
    $existencias_actuales = floatval($_GET['existencias']);
    $pack_actual = floatval($_GET['pack_mescla_exis']);
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
    <title>Agregar Existencias - Mesclas</title>
    <link rel="stylesheet" href="styles/styles.css">
    <script>
        function mostrarAlerta() {
            alert("Recuerda que estás ingresando un grupo de mesclas. El valor que ingreses se multiplicará por el rendimiento y se sumará a las existencias actuales para calcular el nuevo total de la mescla.");
        }
    </script>
</head>
<body onload="mostrarAlerta()">
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
            <form action="procesar_existencia_mescla.php" method="POST">
                <!-- Pasar el ID de la mescla como campo oculto -->
                <input type="hidden" name="id_mescla" value="<?php echo $id_mescla; ?>">
                <input type="hidden" name="rendimiento" value="<?php echo $rendimiento; ?>">
                <p><strong>Mescla:</strong> <?php echo $nombre; ?></p>
                <p><strong>Existencias actuales:</strong> <?php echo number_format($existencias_actuales, 3); ?> unidades</p>
                <p><strong>Pack actual:</strong> <?php echo number_format($pack_actual, 3); ?></p>
                <label for="cantidad_pack">Cantidad de pack a agregar:</label>
                <input type="number" id="cantidad_pack" name="cantidad_pack" step="0.001" required>
                <button type="submit">Confirmar</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados</p>
    </footer>
</body>
</html>
