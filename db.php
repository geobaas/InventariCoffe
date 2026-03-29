<?php
$servername = "localhost";  // Cambia si tu servidor es diferente
$username = "root";  // Ajusta según el usuario que uses
$password = "";  // Agrega la contraseña si tienes una
$dbname = "InventarioCafe";  // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
