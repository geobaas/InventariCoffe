<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar que los datos fueron enviados
if (!isset($_POST['usuario']) || !isset($_POST['contraseña'])) {
    die("Error: Datos no recibidos correctamente.");
}

$usuario = trim($_POST['usuario']);
$contraseña = trim($_POST['contraseña']);
$_SESSION['usuario'] = $usuario;

// Nueva consulta: comparación directa
$consulta = "SELECT * FROM usuarios WHERE usuario='$usuario' AND contraseña='$contraseña'";
$resultado = mysqli_query($conn, $consulta);
$filas = mysqli_fetch_array($resultado);

// Verificar credenciales
if ($filas) {
    $_SESSION["cargo"] = $filas["id_cargo"];
    $_SESSION["nombre"] = $filas["nombre"];

    // Redirigir según el cargo
    if ($filas['id_cargo'] == 1) { // **Administrador**
        header("Location: admin.php");
    } elseif ($filas['id_cargo'] == 2) { // **Vendedor**
        header("Location: vendedor.php");
    } elseif ($filas['id_cargo'] == 3) { // **Superusuario**
        header("Location: superusuario.php");
    } else {
        header("Location: login.php"); // Si el cargo no es válido, vuelve al login
    }
    exit();
} else {
    ?>
    <?php include("index.html"); ?>
    <h1 class="bad">ERROR EN LA AUTENTIFICACIÓN</h1>
    <?php
}

// Liberar recursos y cerrar conexión
mysqli_free_result($resultado);
mysqli_close($conn);
?>
