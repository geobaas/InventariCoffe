<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Obtener los cargos disponibles en la base de datos
$cargos = [];
$sql = "SELECT id, descripcion FROM cargo";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $cargos[] = $row;
}

// Procesar registro cuando se envíe el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $usuario = $_POST["usuario"];
    $contrasena = password_hash($_POST["contrasena"], PASSWORD_DEFAULT); // Encriptar contraseña
    $id_cargo = $_POST["cargo"];

    // Insertar usuario en la base de datos
    $sql = "INSERT INTO usuarios (nombre, usuario, contraseña, id_cargo) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $usuario, $contrasena, $id_cargo);

    if ($stmt->execute()) {
        echo "<script>alert('Usuario registrado correctamente'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error al registrar usuario');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="styles/styles.css">
    <script>
        function validarFormulario() {
            let nombre = document.getElementById("nombre").value;
            let usuario = document.getElementById("usuario").value;
            let contrasena = document.getElementById("contrasena").value;
            let cargo = document.getElementById("cargo").value;

            if (nombre === "" || usuario === "" || contrasena === "" || cargo === "") {
                alert("Todos los campos son obligatorios");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

    <div class="contenedor">
        <h2>Registro de Usuario</h2>
        <form action="registro.php" method="POST" onsubmit="return validarFormulario()">
            <label>Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label>Usuario:</label>
            <input type="text" id="usuario" name="usuario" required>

            <label>Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>

            <label>Cargo:</label>
            <select name="cargo" id="cargo">
                <option value="">Selecciona un cargo</option>
                <?php foreach ($cargos as $cargo) { ?>
                    <option value="<?= $cargo['id'] ?>"><?= htmlspecialchars($cargo['descripcion']) ?></option>
                <?php } ?>
            </select>

            <button type="submit">Registrar</button>
        </form>
    </div>

</body>
</html>
