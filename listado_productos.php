<?php
include 'db.php';

$sql = "SELECT producto_id, nombre, precio, imagen FROM productos";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div class='card'>";
    echo "<img src='" . htmlspecialchars($row['imagen']) . "' alt='Imagen de " . htmlspecialchars($row['nombre']) . "'>";
    echo "<h3>" . htmlspecialchars($row['nombre']) . "</h3>";
    echo "<p>Precio: $" . number_format($row['precio'], 2) . "</p>";
    echo "<div class='acciones'>";
    echo "<button class='boton editar' onclick='editarReceta(" . $row['producto_id'] . ")'>Editar Receta</button>";
    echo "<button class='boton ajustar' onclick='editarPrecio()'>Editar Precio</button>";
    echo "</div>";
    echo "</div>";
}
?>
