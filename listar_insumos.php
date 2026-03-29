<?php
include 'db.php'; // Conexión a la base de datos

// Consulta para obtener los insumos
$sql = "SELECT id_insumo, nombre, cantidad_existencias, imagen FROM insumos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<img src='" . htmlspecialchars($row["imagen"]) . "' alt='Imagen del insumo'>";
        echo "<h3>" . htmlspecialchars($row["nombre"]) . "</h3>";
        echo "<p><strong>Existencias:</strong> " . number_format($row["cantidad_existencias"], 3) . "</p>";
        echo "<div class='acciones'>";
        echo "<a href='editar_insumo.php?id=" . $row["id_insumo"] . "' class='boton editar'>Editar</a>"; // Botón Editar
        echo "<a href='agregar_existencia.php?id=" . $row["id_insumo"] . "&nombre=" . urlencode($row["nombre"]) . "&cantidad=" . $row["cantidad_existencias"] . "' class='boton agregar'>Agregar</a>"; // Botón Agregar
        echo "</div>";
        echo "</div>";
    }
} else {
    echo "<p>No hay insumos registrados.</p>";
}

$conn->close();
?>
