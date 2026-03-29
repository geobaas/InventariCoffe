<?php
session_start();
include 'db.php'; // Conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['cargo'])) {
  header("Location: login.php");
  exit();
}

$nombre_usuario = $_SESSION['nombre'];
$cargo_usuario  = $_SESSION['cargo'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear Producto</title>
  <link rel="stylesheet" href="styles/styles.css">
  <style>
    .contenedor-lado-a-lado {
      display: flex;
      gap: 20px;
      max-width: 1200px;
      margin: 20px auto;
    }
    .panel-insumos {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 0px 10px #aaa;
      flex: 0 0 40%;
    }
    .panel-formulario {
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 0px 10px #aaa;
      flex: 0 0 60%;
    }
    .notificacion {
      display: none;
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #00796b;
      color: white;
      padding: 15px;
      border-radius: 5px;
      z-index: 9999;
      box-shadow: 0px 0px 5px rgba(0,0,0,0.5);
    }
  </style>
  <script>
    // Función para mostrar un mensaje flotante por 3 segundos
    function mostrarMensaje(mensaje) {
      const notificacion = document.getElementById("notificacion");
      notificacion.textContent = mensaje;
      notificacion.style.display = "block";
      setTimeout(() => {
        notificacion.style.display = "none";
      }, 3000);
    }

    // Función para mostrar la vista previa de la imagen seleccionada
    function mostrarVistaPrevia(event) {
      const vistaPrevia = document.getElementById("vistaPrevia");
      vistaPrevia.src = URL.createObjectURL(event.target.files[0]);
      vistaPrevia.style.display = "block";
    }
    
    document.addEventListener("DOMContentLoaded", function(){
      // Cargar el listado de insumos desde cargar_insumos.php mediante fetch()
      fetch("cargar_insumos.php")
        .then(response => response.json())
        .then(insumos => {
          const tbody = document.querySelector("#listadoInsumos tbody");
          insumos.forEach(insumo => {
            const fila = document.createElement("tr");
            fila.innerHTML = `<td>${insumo.nombre}</td>
                              <td>${insumo.cantidad_existencias} ${insumo.tipo}</td>
                              <td>$${insumo.precio_unitario}</td>
                              <td>
                                <button type="button" onclick="agregarInsumo(${insumo.id_insumo}, '${insumo.nombre}', '${insumo.tipo}', ${insumo.cantidad_existencias}, ${insumo.precio_unitario})">
                                  Agregar
                                </button>
                              </td>`;
            tbody.appendChild(fila);
          });
        })
        .catch(error => console.error("Error al cargar insumos:", error));

      // Envío del formulario vía Ajax: se recalcula el costo total justo antes del envío
      document.getElementById("productoForm").addEventListener("submit", async function(e) {
        e.preventDefault(); // Prevenir la recarga de página
        actualizarCosto();  // Recalcula el costo total y la ganancia
        const form = this;
        const formData = new FormData(form);
        try {
          const response = await fetch(form.action, {  
            method: form.method,
            body: formData
          });
          const result = await response.text();
          mostrarMensaje(result);
          form.reset();
          document.querySelector("#insumosSeleccionados tbody").innerHTML = "";
          document.getElementById("vistaPrevia").style.display = "none";
        } catch (error) {
          console.error("Error al enviar el formulario:", error);
        }
      });
    });

    // Función para recalcular el costo total y la ganancia total
    function actualizarCosto() {
      let total = 0;
      // Recorre cada fila de la tabla de insumos seleccionados
      document.querySelectorAll("#insumosSeleccionados tbody tr").forEach(fila => {
        // Se obtiene la cantidad del insumo (segundo td)
        let cantidad = parseFloat(fila.querySelector("td:nth-child(2)").textContent);
        // Se obtiene el costo unitario (cuarto td) eliminando el símbolo "$" y espacios
        let costoStr = fila.querySelector("td:nth-child(4)").textContent.replace("$", "").trim();
        let costo = parseFloat(costoStr);
        // Se multiplica la cantidad por el costo unitario para obtener el costo de este insumo
        if (!isNaN(cantidad) && !isNaN(costo)) {
          total += cantidad * costo;
        }
      });
      // Se actualiza el párrafo que muestra el costo total del producto
      document.getElementById("costoTotal").textContent = `Costo Total del Producto: $${total.toFixed(2)}`;
      // Se recupera el precio de venta ingresado y se calcula la ganancia (precio - costo total)
      let precioVenta = parseFloat(document.getElementById("precio").value);
      if (isNaN(precioVenta)) { precioVenta = 0; }
      document.getElementById("gananciaTotal").textContent = `Ganancia Total: $${(precioVenta - total).toFixed(2)}`;
    }

    // Función para agregar un insumo a la tabla (permite cualquier cantidad mayor a 0)
    function agregarInsumo(id, nombre, tipo, cantidadDisponible, precioUnitario) {
      // Se solicita al usuario la cantidad mediante un prompt
      const inputValor = prompt(`Ingrese la cantidad para "${nombre}" (${tipo}) - Disponible: ${cantidadDisponible} ${tipo}`);
      if (inputValor && !isNaN(inputValor) && parseFloat(inputValor) > 0) {
        let cantidadFinal = parseFloat(inputValor);
        // Para insumos de tipo "Kilogramos" o "Litros", se asume que el usuario ingresa en gramos o mililitros y se convierte
        if (tipo === "Kilogramos" || tipo === "Litros") {
          cantidadFinal = (cantidadFinal / 1000).toFixed(3);
        }
        // Se calcula el costo para este insumo multiplicando la cantidad por el precio unitario
        const costo = (cantidadFinal * precioUnitario).toFixed(2);
        // Se agrega una nueva fila a la tabla "insumosSeleccionados"
        const tabla = document.querySelector("#insumosSeleccionados tbody");
        const fila = document.createElement("tr");
        fila.innerHTML = `<td>${nombre}</td>
                          <td>${cantidadFinal}</td>
                          <td>${tipo}</td>
                          <td>$${costo}</td>
                          <td>
                            <button type="button" onclick="eliminarInsumo(this)">Eliminar</button>
                          </td>
                          <input type="hidden" name="insumos[]" value='{"id":${id}, "cantidad":${cantidadFinal}, "costo":${costo}, "tipo":"${tipo}"}'>`;
        tabla.appendChild(fila);
        // Se recalcula el costo total luego de agregar el nuevo insumo
        actualizarCosto();
        // Se muestra un mensaje de confirmación
        mostrarMensaje(`Insumo "${nombre}" agregado correctamente. Favor de editar la receta al finalizar.`);
      } else {
        mostrarMensaje("Por favor, ingrese una cantidad válida para el insumo.");
      }
    }

    // Función para eliminar un insumo de la tabla
    function eliminarInsumo(boton) {
      boton.parentElement.parentElement.remove();
      actualizarCosto();
    }
  </script>
</head>
<body>
  <header>
    <?php include 'menu.php'; ?>
    <h1>Crear Producto</h1>
  </header>
  
  <div class="contenedor-lado-a-lado">
    <!-- Panel izquierdo: Listado de Insumos -->
    <div class="panel-insumos">
      <h3>Listado de Insumos</h3>
      <table id="listadoInsumos">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Disponible</th>
            <th>Precio Unitario</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody><!-- Se llenará dinámicamente desde cargar_insumos.php --></tbody>
      </table>
    </div>
    
    <!-- Panel derecho: Formulario para crear Producto -->
    <div class="panel-formulario">
      <div id="notificacion" class="notificacion"></div>
      <form id="productoForm" action="procesar_producto.php" method="POST" enctype="multipart/form-data">
        <div class="seccion">
          <label for="nombre_producto">Nombre del producto:</label>
          <input type="text" id="nombre_producto" name="nombre_producto" required>
          <label for="descripcion">Descripción:</label>
          <textarea id="descripcion" name="descripcion" required></textarea>
        </div>
        <div class="seccion">
          <h3>Insumos Seleccionados</h3>
          <table id="insumosSeleccionados">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Cantidad</th>
                <th>Tipo</th>
                <th>Costo</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody><!-- Se agregarán insumos seleccionados --></tbody>
          </table>
        </div>
        <div class="seccion">
          <label for="precio">Precio de venta ($):</label>
          <input type="number" id="precio" name="precio" step="0.01" required>
          <p id="costoTotal">Costo Total del Producto: $0.00</p>
          <p id="gananciaTotal">Ganancia Total: $0.00</p>
        </div>
        <div class="seccion">
          <label for="imagen_producto">Imagen del producto:</label>
          <input type="file" id="imagen_producto" name="imagen_producto" accept="image/*" onchange="mostrarVistaPrevia(event)" required>
          <img id="vistaPrevia" style="display:none; width:120px; height:120px; border-radius:5px;" alt="Vista previa">
        </div>
        <button type="submit">Crear Producto</button>
      </form>
    </div>
  </div>
  
  <footer>
    <p>&copy; 2025 Las Abuelitas - Todos los derechos reservados.</p>
  </footer>
</body>
</html>

