# InventariCoffe

Sistema web para el control de inventario, insumos, mezclas, ventas y cortes de caja de una cafetería, desarrollado en PHP con conexión a MySQL.

## Versión

**1.0.0**

## Requisitos

- PHP 7.4 o superior (con la extensión `mysqli` habilitada)
- MySQL o MariaDB 5.7+
- Servidor web con soporte para PHP (Apache recomendado, por ejemplo mediante XAMPP o WAMP)
- Navegador web moderno (Chrome, Firefox, Edge)

## Instalación

1. Clona o descarga este repositorio dentro de la carpeta de tu servidor local (por ejemplo `htdocs` si usas XAMPP).
2. Crea la base de datos y sus tablas ejecutando `crear_base.php` (o importando el script SQL correspondiente si lo tienes).
3. Copia el archivo de ejemplo de configuración y renómbralo:
   ```
   config/.env.example → config/.env
   ```
4. Edita `config/.env` con los datos reales de tu base de datos:
   ```
   DB_HOST=127.0.0.1
   DB_USER=root
   DB_PASS=tu_contraseña
   DB_NAME=inventariocafe
   ```
5. Inicia tu servidor (Apache/MySQL) y accede al proyecto desde el navegador, por ejemplo:
   ```
   http://localhost/InventariCoffe/index.html
   ```

## Notas

- El archivo `config/.env` contiene credenciales reales y **no se sube al repositorio** (está protegido en `.gitignore`).
- Usa `config/.env.example` como plantilla de referencia para crear tu propio `.env`.
