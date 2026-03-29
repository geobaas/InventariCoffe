<?php
session_start();
include 'db.php';

// Verifica que el usuario esté autenticado y tenga el rol de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['cargo'] != 1) {
    header("Location: login.php");
    exit();
}

$nombre_usuario = $_SESSION['nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrador - Las Abuelitas</title>
    <link rel="stylesheet" href="styles/styles.css"> <!-- Carga de estilos -->
</head>
<body>
    <header>
        <h1>Panel de Administración</h1>
        <p>Bienvenido, <strong><?= htmlspecialchars($nombre_usuario) ?></strong> (Administrador)</p>

        <!-- Carga el menú dinámico basado en el rol del usuario -->
        <?php include 'menu.php'; ?>
        
    </header>

    <main>
        <h2>Gestión de Inventario</h2>
        <p>Aquí puedes administrar los insumos del sistema.</p>
    </main>

    <footer>
        <a href="logout.php">Cerrar sesión</a> <!-- Opción para cerrar sesión -->
    </footer>
</body>
</html>
