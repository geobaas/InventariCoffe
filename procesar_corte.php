<?php
include 'db.php';

// Obtener la fecha actual del sistema automáticamente
$fecha_corte = date("Y-m-d");

// Obtener total de ventas del día actual
$sql_ventas = "SELECT SUM(total) AS total_ventas FROM ventas WHERE fecha = ?";
$stmt_ventas = $conn->prepare($sql_ventas);
$stmt_ventas->bind_param("s", $fecha_corte);
$stmt_ventas->execute();
$resultado_ventas = $stmt_ventas->get_result();
$fila_ventas = $resultado_ventas->fetch_assoc();
$total_ventas = $fila_ventas["total_ventas"] ?? 0;

// Obtener efectivo en caja ingresado por el usuario
$efectivo_en_caja = $_POST["efectivo_en_caja"];
$observaciones = $_POST["observaciones"];

// Calcular diferencia entre ventas y efectivo en caja
$diferencia = $efectivo_en_caja - $total_ventas;

// Insertar el corte de caja en la base de datos
$sql_corte = "INSERT INTO corte_caja (fecha, total_ventas, efectivo_en_caja, diferencia, observaciones) VALUES (?, ?, ?, ?, ?)";
$stmt_corte = $conn->prepare($sql_corte);
$stmt_corte->bind_param("sddds", $fecha_corte, $total_ventas, $efectivo_en_caja, $diferencia, $observaciones);

if ($stmt_corte->execute()) {
    echo "<script>alert('Corte de caja registrado correctamente'); window.location.href='corte_caja.php';</script>";
} else {
    echo "<script>alert('Error al registrar el corte de caja');</script>";
}
?>
