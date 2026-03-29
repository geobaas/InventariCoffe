<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar si el usuario ha iniciado sesión correctamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['cargo'])) {
    header("Location: login.php"); // Redirigir si no hay sesión activa
    exit();
}

// Recuperar datos del usuario desde la sesión
$nombre_usuario = $_SESSION['nombre'];
$cargo_usuario = $_SESSION['cargo']; // Cargo del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Insumo</title>
    <link rel="stylesheet" href="styles/styles.css"> <!-- Carga de estilos -->
    <script>
        /**
         * Muestra una vista previa de la imagen seleccionada por el usuario
         */
        function mostrarVistaPrevia(event) {
            let vistaPrevia = document.getElementById("vistaPrevia");
            let archivo = event.target.files[0];

            if (archivo) {
                let lector = new FileReader();
                lector.onload = function(e) {
                    vistaPrevia.src = e.target.result;
                    vistaPrevia.style.display = "block";
                };
                lector.readAsDataURL(archivo);
            } else {
                vistaPrevia.src = "img/no-image.png"; // Imagen por defecto si no se selecciona ninguna
            }
        }

        /**
         * Calcula el precio unitario basado en el costo total y la cantidad por presentación
         */
        function calcularPrecioUnitario() {
            let costo = parseFloat(document.getElementById("costo").value) || 0;
            let cantidad = parseFloat(document.getElementById("cantidad_presentacion").value) || 1;
            let tipo = document.getElementById("tipo").value;

            // Convertir a gramos o mililitros si es necesario
            if (tipo === "Kilogramo" || tipo === "Litro") {
                cantidad *= 1000;
            }

            // Calcular el precio por unidad
            document.getElementById("precio_unitario").value = cantidad > 0 ? (costo / cantidad).toFixed(4) : 0;
        }

        /**
         * Muestra una notificación de éxito al agregar el insumo
         */
        function mostrarNotificacion() {
            let mensaje = document.getElementById("notificacion");
            mensaje.style.display = "block";

            // Ocultar el mensaje después de 3 segundos
            setTimeout(() => {
                mensaje.style.display = "none";
                document.getElementById("formInsumo").reset();
                document.getElementById("vistaPrevia").src = "img/no-image.png"; // Restablecer la imagen a la predeterminada
            }, 3000);
        }
    </script>
</head>
<body>
   <header>
        <!-- Incluir el menú desde menu.php -->
        <?php include 'menu.php'; ?>
    
        <h1>Agregar Insumo</h1>
        <p>Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong> (<?= htmlspecialchars($cargo_usuario) ?>)</p>
    </header>
  
    <main>
        <section class="contenedor">
            <h2></h2>
            <form id="formInsumo" action="agregar_insumo.php" method="POST" enctype="multipart/form-data">
                <label>Imagen:</label>
                <input type="file" name="imagen" accept="image/*" onchange="mostrarVistaPrevia(event)">
                <img id="vistaPrevia" src="img/no-image.png" style="width: 120px; height: 120px; margin-top: 10px;">

                <label>Nombre del insumo:</label>
                <input type="text" name="nombre" placeholder="Ejemplo: Harina de trigo" required>

                <label>Tipo:</label>
                <select name="tipo" id="tipo" onchange="calcularPrecioUnitario()">
                    <option value="Kilogramo">Kilogramo (Se convertirá a gramos)</option>
                    <option value="Unidad">Unidad (Sin conversión)</option>
                    <option value="Litro">Litro (Se convertirá a mililitros)</option>
                </select>

                <label>Cantidad por presentación:</label>
                <input type="number" step="0.001" id="cantidad_presentacion" name="cantidad_presentacion" placeholder="Ejemplo: 5" oninput="calcularPrecioUnitario()" required>

                <label>Costo presentación ($):</label>
                <input type="number" step="0.01" id="costo" name="costo" placeholder="Ejemplo: 150.00" oninput="calcularPrecioUnitario()" required>

                <label>Precio por unidad ($):</label>
                <input type="number" step="0.0001" id="precio_unitario" name="precio_unitario" readonly>

                <label>Cantidad a ingresar:</label>
                <input type="number" step="0.001" name="cantidad_ingresar" placeholder="Ejemplo: 50" required>

                <button type="submit" onclick="mostrarNotificacion()">Agregar Insumo</button>
            </form>

            <div id="notificacion" class="notificacion">✅ Insumo agregado correctamente</div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados</p>
    </footer>
</body>
</html>

<?php
mysqli_close($conn); // Cerrar conexión a la base de datos
?>
