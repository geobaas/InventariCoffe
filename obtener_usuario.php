<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['cargo'])) {
    echo json_encode(["nombre" => "Invitado", "cargo" => "Sin cargo"]);
    exit();
}

echo json_encode([
    "nombre" => $_SESSION['nombre'],
    "cargo" => $_SESSION['cargo']
]);
?>
