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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Costos</title>
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .contenedor {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .mensaje {
            background-color: #d7ccc8;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
            max-width: 600px;
            color: #5d4037;
        }
        .boton {
            display: inline-block;
            background: #795548;
            color: white;
            padding: 12px 20px;
            margin-top: 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .boton:hover {
            background: #5d4037;
        }
    </style>
</head>
<body>

    <div class="contenedor">
        <div class="mensaje">
            <h2>Acceso Restringido</h2>
            <p>Por el momento, el administrador no ha dado autorización para usar esta parte del programa. Apenas nos den permiso, la habilitaremos.</p>
            <a href="productos.php" class="boton">Regresar al listado de productos</a>
        </div>
    </div>

</body>
</html>
