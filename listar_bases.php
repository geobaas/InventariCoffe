<?php
include 'db.php'; // Conexión a la base de datos

try {
    // Consulta para obtener las bases
    $sql = "SELECT id_base, nombre, exis_base FROM bases";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card'>";

            // Mostrar el nombre de la base
            echo "<h3>" . htmlspecialchars($row["nombre"]) . "</h3>";

            // Mostrar la cantidad de existencias
            echo "<p><strong>Existencias:</strong> " . number_format($row["exis_base"], 2) . " unidades</p>";

            // Botones de acción
            echo "<div class='acciones'>";
            echo "<a href='editar_base.php?id=" . $row["id_base"] . "' class='boton editar'>Editar</a>"; // Botón Editar
            echo "<a href='agregar_existencia_base.php?id=" . $row["id_base"] . "&nombre=" . urlencode($row["nombre"]) . "&existencias=" . $row["exis_base"] . "' class='boton agregar'>Agregar</a>"; // Botón Agregar
            echo "</div>";

            echo "</div>"; // Fin de la tarjeta
        }
    } else {
        // Mostrar mensaje si no hay bases registradas
        echo "<p>No hay bases registradas.</p>";
    }
} catch (mysqli_sql_exception $e) {
    // Manejo de errores
    echo "<p>Error al cargar las bases: " . htmlspecialchars($e->getMessage()) . "</p>";
}

$conn->close();
?>
