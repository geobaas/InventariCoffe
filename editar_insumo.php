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

if (isset($_GET['id'])) {
    $id_insumo = $_GET['id'];

    // Consulta para obtener los datos del insumo
    $sql = "SELECT nombre, tipo, cantidad_presentacion, cantidad_existencias, costo, precio_unitario, imagen FROM insumos WHERE id_insumo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_insumo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $tipo = $row['tipo'];
        $cantidad_presentacion = $row['cantidad_presentacion'];
        $cantidad_existencias = $row['cantidad_existencias'];
        $costo = $row['costo'];
        $precio_unitario = $row['precio_unitario'];
        $imagen = $row['imagen'];
    } else {
        echo "<p>Error: No se encontró el insumo solicitado.</p>";
        exit();
    }

    $stmt->close();
} else {
    echo "<p>Error: No se proporcionó un ID válido.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Insumo</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
   <header>
    <!-- Incluir el menú desde menu.php -->
    <?php include 'menu.php'; ?>
    
    <h1>Crear Producto</h1>
    <p>Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong> (<?= htmlspecialchars($cargo_usuario) ?>)</p>
  </header>

    <main>
        <section class="contenedor">
            <h2>Detalles del Insumo</h2>
            <form action="procesar_editar_insumo.php" method="POST">
                <input type="hidden" name="id_insumo" value="<?php echo $id_insumo; ?>">

                <label for="imagen">Imagen del insumo:</label>
                <input type="text" id="imagen" name="imagen" value="<?php echo htmlspecialchars($imagen); ?>" placeholder="URL de la imagen" required>

                <label for="nombre">Nombre del insumo:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>

                <label for="tipo">Tipo (Selecciona):</label>
                <select id="tipo" name="tipo" onchange="calcularPrecio()">
                    <option value="Unidad" <?php echo ($tipo == 'Unidad') ? 'selected' : ''; ?>>Unidad</option>
                    <option value="Kilogramo" <?php echo ($tipo == 'Kilogramo') ? 'selected' : ''; ?>>Kilogramo</option>
                    <option value="Litro" <?php echo ($tipo == 'Litro') ? 'selected' : ''; ?>>Litro</option>
                </select>

                <label for="cantidad_presentacion">Cantidad por presentación:</label>
                <input type="number" id="cantidad_presentacion" name="cantidad_presentacion" step="0.001" value="<?php echo $cantidad_presentacion; ?>" required oninput="calcularPrecio()">

                <label for="costo">Costo de presentación ($):</label>
                <input type="number" id="costo" name="costo" step="0.01" value="<?php echo $costo; ?>" required oninput="calcularPrecio()">

                <label for="precio_unitario">Precio por unidad ($):</label>
                <input type="number" id="precio_unitario" name="precio_unitario" step="0.01" value="<?php echo $precio_unitario; ?>" readonly>

                <label for="cantidad_existencias">Cantidad en existencias:</label>
                <input type="number" id="cantidad_existencias" name="cantidad_existencias" step="0.001" value="<?php echo $cantidad_existencias; ?>" required>

                <div class="acciones">
                    <button type="submit" name="accion" value="guardar">Guardar Cambios</button>
                    <button type="submit" name="accion" value="eliminar" class="eliminar">Eliminar Insumo</button>
                </div>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados.</p>
    </footer>

    <script>
        function calcularPrecio() {
            const tipo = document.getElementById("tipo").value;
            const cantidadPresentacion = parseFloat(document.getElementById("cantidad_presentacion").value) || 0;
            const costo = parseFloat(document.getElementById("costo").value) || 0;

            let precioPorUnidad = 0;

            if (tipo === "Unidad") {
                precioPorUnidad = costo / cantidadPresentacion;
            } else if (tipo === "Kilogramo" || tipo === "Litro") {
                precioPorUnidad = costo / (cantidadPresentacion * 1000); // Convertir a gramos o mililitros
            }

            document.getElementById("precio_unitario").value = precioPorUnidad.toFixed(2);
        }
    </script>
</body>
</html>
