# Tecnoback · Tienda virtual (PHP + MySQL)

Proyecto integrador de bases de datos · Universidad Santo Tomás, Villavicencio.
Integrantes: Samuel Angulo y Leandro Hernandez.

Tecnologías: HTML, CSS, PHP 8 (PDO), MySQL/MariaDB (XAMPP) y Git/GitHub.

## Qué incluye
- Catálogo con búsqueda por nombre y filtro por categoría (RQ-17).
- Registro de clientes (RQ-04, RQ-05).
- Carrito y pedido en una sola transacción con COMMIT/ROLLBACK (RQ-20).
- Descuento automático de inventario con trigger (RQ-16).

## Paso a paso en tu computador
1. Instala XAMPP, ábrelo e inicia **Apache** y **MySQL**.
2. Copia esta carpeta a `C:\xampp\htdocs\tecnoback`.
3. Abre http://localhost/phpmyadmin → pestaña **Importar** → elige `sql/tecnoback.sql` → **Importar**.
   (También puedes pegar el contenido en la pestaña SQL.)
4. Abre http://localhost/tecnoback/test.php. Debe decir: `Conexión exitosa a tecnoback · Productos: 6`.
5. Abre http://localhost/tecnoback/ y prueba: busca un producto, agrégalo al carrito, regístrate y confirma el pedido.
6. Comprueba en phpMyAdmin que bajó `Producto.cantidad` y que hay filas en `Pedido` y `Detalle_pedido`.

## Subirlo a GitHub
1. Crea cuenta en github.com e instala Git (git-scm.com).
2. En GitHub: **New repository** → nombre `tecnoback` → Create (sin README).
3. En la carpeta del proyecto, en la terminal:
   ```
   git init
   git add .
   git commit -m "Primer avance: sitio PHP + MySQL"
   git branch -M main
   git remote add origin https://github.com/TU_USUARIO/tecnoback.git
   git push -u origin main
   ```
4. Agrega a tu compañero en Settings → Collaborators. Para cada cambio: `git add .`, `git commit -m "mensaje"`, `git push`.

## Publicarlo en internet (opcional)
GitHub Pages **no ejecuta PHP**: sirve solo HTML estático. GitHub guarda y versiona el código; para verlo en línea con base de datos necesitas un hosting con PHP y MySQL (por ejemplo InfinityFree; revisa sus condiciones actuales).
1. Crea la base en el hosting e importa `sql/tecnoback.sql` (quita las dos primeras líneas `DROP DATABASE`/`CREATE DATABASE` y la línea `USE`, y usa el nombre de base que te asignen).
2. Copia `includes/config.example.php` como `includes/config.php` con los datos del hosting. Ese archivo está en `.gitignore` y no se sube a GitHub.
3. Sube los archivos por FTP o por el administrador de archivos del hosting.

## Errores frecuentes
| Mensaje | Solución |
|---|---|
| 2002 Connection refused | Iniciar MySQL en XAMPP |
| 1049 Unknown database | Importar `sql/tecnoback.sql` |
| 1045 Access denied | Revisar usuario y clave en `includes/conexion.php` o `config.php` |
| could not find driver | Activar `extension=pdo_mysql` en php.ini y reiniciar Apache |
