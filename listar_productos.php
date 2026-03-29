<?php
include 'db.php'; // Conexión a la base de datos

// Consulta para obtener los productos
$sql = "SELECT producto_id, nombre, descripcion, precio, imagen FROM productos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";

        // Verificar si hay imagen, sino mostrar una imagen por defecto
        $imagen = !empty($row["imagen"]) ? htmlspecialchars($row["imagen"]) : "img/default.png";
        echo "<img src='$imagen' alt='Imagen del producto'>";

        echo "<h3>" . htmlspecialchars($row["nombre"]) . "</h3>";
        echo "<p>" . htmlspecialchars($row["descripcion"]) . "</p>";
        echo "<p><strong>Precio:</strong> $" . number_format($row["precio"], 2) . "</p>";

        // Sección de acciones
        echo "<div class='acciones'>";
        echo "<a href='editar_receta.php?producto_id=" . $row["producto_id"] . "' class='boton editar'>Editar Receta</a>";
        echo "<a href='acceso_restringido.html' class='boton bloquear'>Editar Costos</a>";
        echo "</div>";

        echo "</div>";
    }
} else {
    echo "<p>No hay productos registrados.</p>";
}

// Manejo de posibles errores en la consulta
if ($conn->error) {
    echo "<p>Error en la consulta: " . htmlspecialchars($conn->error) . "</p>";
}

$conn->close();
?>
