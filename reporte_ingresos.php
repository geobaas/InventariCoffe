<?php
// reporte_ingresos.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php'; // Conexión a la base de datos

// Obtenemos los registros uniendo movimientos_insumos e insumos, usando el id_insumo para localizar el nombre del insumo
$query = "SELECT insumos.nombre AS nombre_insumo, movimientos_insumos.cantidad_agregada, movimientos_insumos.fecha 
          FROM movimientos_insumos 
          INNER JOIN insumos ON movimientos_insumos.id_insumo = insumos.id_insumo 
          ORDER BY movimientos_insumos.fecha DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de ingresos de inventarios - Las Abuelitas</title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        /* Estilos adicionales compatibles con tu CSS general */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f1e3; /* Beige claro */
            color: #5d4037; /* Café oscuro */
            margin: 0;
            padding: 0;
        }
        
        header {
            background-color: #795548;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        footer {
            background-color: #4e342e;
            color: white;
            text-align: center;
            padding: 10px;
        }
        
        main {
            padding: 20px;
        }
        
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        
        table {
            width: 90%;
            margin: 0 auto 20px auto;
            border-collapse: collapse;
            background-color: white;
        }
        
        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        
        table th {
            background-color: #6d4c41; /* Café medio */
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Reporte de ingresos de inventarios</h1>
    </header>
    <main>
        <h2>Listado de Ingresos</h2>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Insumo</th>
                    <th>Cantidad Agregada</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nombre_insumo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['cantidad_agregada']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fecha']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay registros disponibles.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados</p>
    </footer>
</body>
</html>
