<?php
session_start();
include 'db.php';

if (!isset($_SESSION['usuario']) || $_SESSION['cargo'] != 2) {
    header("Location: login.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vendedor - Las Abuelitas</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header>
        <h1>Panel de Ventas</h1>
        <p>Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong> (Vendedor)</p>
        
        <!-- Incluir el menú sin mostrar datos del usuario -->
        <?php include 'menu.php'; ?>
        
    </header>

    <main>
        <h2>Panel de Ventas</h2>
        <p>Aquí puedes gestionar las ventas y cortes de caja.</p>
    </main>

    <footer>
        <a href="logout.php">Cerrar sesión</a>
    </footer>
</body>
</html>
