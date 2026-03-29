<?php
$servername = "127.0.0.1"; // Cambiado para coincidir con tu conexión local
$username = "root";        // Ajusta según el usuario que uses
$password = "";            // Agrega la contraseña si tienes una
$dbname = "inventariocafe"; // Actualizado a minúsculas exactas

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>