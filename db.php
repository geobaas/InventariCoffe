<?php
// Función para cargar las variables del archivo .env
function cargarEnv($ruta) {
    if (!file_exists($ruta)) {
        return false;
    }

    $lineas = file($ruta, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        // Ignorar los comentarios
        if (strpos(trim($linea), '#') === 0) continue;

        // Separar el nombre de la variable y su valor
        list($nombre, $valor) = explode('=', $linea, 2);
        $nombre = trim($nombre);
        $valor = trim($valor);

        // Quitar comillas si el valor las tiene
        $valor = trim($valor, '"\'');

        // Guardar en la superglobal $_ENV
        $_ENV[$nombre] = $valor;
    }
}

// Cargar el archivo .env desde la carpeta config
cargarEnv(__DIR__ . '/config/.env');

// Asignar las variables desde el .env (usando los valores de fallback si algo falla)
$servername = $_ENV["DB_HOST"] ?? "127.0.0.1";
$username   = $_ENV["DB_USER"] ?? "root";
$password   = $_ENV["DB_PASS"] ?? "";
$dbname     = $_ENV["DB_NAME"] ?? "inventariocafe";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer caracteres a UTF-8 para evitar problemas con acentos (muy recomendado)
$conn->set_charset("utf8mb4");
?>