<?php
session_start();
session_destroy(); // Cierra la sesión
header("Location: index.html"); // Redirige al usuario al inicio
exit();
?>
<button id="logoutButton">Cerrar sesión</button>

<script>
    document.getElementById('logoutButton').addEventListener('click', function() {
        window.location.href = 'logout.php';
    });
</script>
