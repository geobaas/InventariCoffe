<?php
include 'db.php'; // Conexión a la base de datos

try {
    // Consulta para obtener las mesclas
    $sql = "SELECT id_mescla, nombre, exis_mescla, rendimiento FROM mesclas";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='card'>";

            // Mostrar el nombre de la mescla
            echo "<h3>" . htmlspecialchars($row["nombre"]) . "</h3>";

            // Mostrar la cantidad de existencias actuales
            echo "<p><strong>Existencias:</strong> " . number_format($row["exis_mescla"], 3) . " unidades</p>";

            // Mostrar el rendimiento por pack
            echo "<p><strong>Rendimiento:</strong> " . number_format($row["rendimiento"], 3) . " unidades por pack</p>";

            // Botones de acción
            echo "<div class='acciones'>";
            echo "<button onclick=\"actualizarExistencias(" . $row["id_mescla"] . ", " . $row["rendimiento"] . ")\">Agregar Existencias</button>";
            echo "</div>";

            echo "</div>"; // Fin de la tarjeta
        }
    } else {
        echo "<p>No hay mesclas registradas.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error al cargar las mesclas: " . htmlspecialchars($e->getMessage()) . "</p>";
}

$conn->close();
?>
