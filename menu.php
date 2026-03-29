<?php
$rol = $_SESSION['cargo']; // Cargo del usuario
?>
<nav>
    <ul class="menu">
        <li><a href="index.HTML">Inicio</a></li>

        <?php if ($rol == 1) { ?>
            <li class="dropdown">
                <a href="#">Inventario</a>
                <ul class="submenu">
                    <li><a href="insumos.php">Insumos</a></li>
                    <li><a href="listado_insumos.php">Listado Insumos</a></li>
                </ul>
            </li>
        <?php } elseif ($rol == 2) { ?>
            <li class="dropdown">
                <a href="#">Venta</a>
                <ul class="submenu">
                    <li><a href="ver_carrito.php">Vender</a></li>
                    <li><a href="corte.php">Corte</a></li>
                </ul>
            </li>
        <?php } elseif ($rol == 3) { ?>
            <li class="dropdown">
                <a href="#">Inventario</a>
                <ul class="submenu">
                    <li><a href="insumos.php">Insumos</a></li>
                    <li><a href="listado_insumos.php">Listado Insumos</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Productos</a>
                <ul class="submenu">
                    <li><a href="crear_producto.php">Crear Producto</a></li>
                    <li><a href="productos.php">Listado Producto</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Reportes</a>
                <ul class="submenu">
                    <li><a href="reporte_ingresos.php">Reportes Ingresos</a></li>
                    <li><a href="acceso_restringido2.php">Reportes Ventas</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Venta</a>
                <ul class="submenu">
                    <li><a href="ver_carrito.php">Vender</a></li>
                    <li><a href="acceso_restringido2.php">Corte</a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#">Usuarios</a>
                <ul class="submenu">
                    <li><a href="registro.php">Reguistrar Usuario</a></li>
                    <li><a href="acceso_restringido2.php">Gestión de Usuarios</a></li>
                </ul>
            </li>
        <?php } ?>

        <li><a href="logout.php">Cerrar sesión</a></li>
    </ul>
</nav>
