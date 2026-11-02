-- Tecnoback · versión MySQL/MariaDB (XAMPP). Se puede ejecutar varias veces.
DROP DATABASE IF EXISTS tecnoback;
CREATE DATABASE tecnoback CHARACTER SET utf8mb4;
USE tecnoback;

CREATE TABLE Usuario (            -- RQ-01, RQ-02, RQ-03
  id_usuario VARCHAR(20) PRIMARY KEY,
  nombre_usuario VARCHAR(50) NOT NULL,
  correo VARCHAR(100) NOT NULL,
  contrasena VARCHAR(255) NOT NULL,  -- hash SHA-256
  CONSTRAINT UQ_Usuario_Nombre UNIQUE (nombre_usuario),
  CONSTRAINT UQ_Usuario_Correo UNIQUE (correo)
);
CREATE TABLE Cliente (            -- RQ-04, RQ-05
  documento VARCHAR(20) PRIMARY KEY,
  nombre_cliente VARCHAR(100) NOT NULL,
  edad INT NULL,
  celular VARCHAR(15) NOT NULL,
  CONSTRAINT CK_Cliente_Edad CHECK (edad IS NULL OR edad >= 0)
);
CREATE TABLE Categoria (          -- RQ-06, RQ-07
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(50) NOT NULL,
  seccion VARCHAR(50) NULL,
  nombre VARCHAR(80) NOT NULL,
  id_usuario VARCHAR(20) NOT NULL,
  CONSTRAINT UQ_Categoria_Nombre UNIQUE (nombre),
  CONSTRAINT FK_Categoria_Usuario FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);
CREATE TABLE Producto (           -- RQ-08, RQ-09
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) NULL,
  precio DECIMAL(10,2) NOT NULL,
  cantidad INT NOT NULL DEFAULT 0,
  id_categoria INT NOT NULL,
  CONSTRAINT CK_Producto_Precio CHECK (precio > 0),
  CONSTRAINT CK_Producto_Cantidad CHECK (cantidad >= 0),
  CONSTRAINT FK_Producto_Categoria FOREIGN KEY (id_categoria) REFERENCES Categoria(id_categoria)
);
CREATE TABLE Pedido (             -- RQ-10, RQ-11
  id_pedido INT AUTO_INCREMENT PRIMARY KEY,
  documento_cliente VARCHAR(20) NOT NULL,
  fecha_pedido DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
  valor_a_pagar DECIMAL(10,2) NOT NULL DEFAULT 0,
  direccion_envio VARCHAR(150) NULL,
  CONSTRAINT CK_Pedido_Estado CHECK (estado IN ('pendiente','pagado','cancelado')),
  CONSTRAINT CK_Pedido_Valor CHECK (valor_a_pagar >= 0),
  CONSTRAINT FK_Pedido_Cliente FOREIGN KEY (documento_cliente) REFERENCES Cliente(documento)
);
CREATE TABLE Detalle_pedido (     -- RQ-12, RQ-13
  id_detalle INT AUTO_INCREMENT PRIMARY KEY,
  id_pedido INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(12,2) AS (cantidad * precio_unitario) STORED,
  CONSTRAINT CK_Detalle_Cantidad CHECK (cantidad > 0),
  CONSTRAINT CK_Detalle_Precio CHECK (precio_unitario > 0),
  CONSTRAINT UQ_Detalle_Pedido_Producto UNIQUE (id_pedido, id_producto),
  CONSTRAINT FK_Detalle_Pedido FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido),
  CONSTRAINT FK_Detalle_Producto FOREIGN KEY (id_producto) REFERENCES Producto(id_producto)
);
CREATE TABLE Venta (              -- RQ-14, RQ-15
  id_venta INT AUTO_INCREMENT PRIMARY KEY,
  id_pedido INT NOT NULL,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  total_pago DECIMAL(10,2) NOT NULL,
  metodo_pago VARCHAR(30) NOT NULL DEFAULT 'Pendiente',
  CONSTRAINT UQ_Venta_Pedido UNIQUE (id_pedido),
  CONSTRAINT CK_Venta_Total CHECK (total_pago >= 0),
  CONSTRAINT CK_Venta_MetodoPago CHECK (metodo_pago IN ('Pendiente','Efectivo','Tarjeta','Transferencia')),
  CONSTRAINT FK_Venta_Pedido FOREIGN KEY (id_pedido) REFERENCES Pedido(id_pedido)
);

-- RQ-16: al insertar una línea se descuenta el inventario; si no alcanza, se rechaza
DELIMITER $$
CREATE TRIGGER trg_detalle_descuenta BEFORE INSERT ON Detalle_pedido
FOR EACH ROW
BEGIN
  IF (SELECT cantidad FROM Producto WHERE id_producto = NEW.id_producto) < NEW.cantidad THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Inventario insuficiente';
  END IF;
  UPDATE Producto SET cantidad = cantidad - NEW.cantidad WHERE id_producto = NEW.id_producto;
END$$
DELIMITER ;

-- Datos de prueba
INSERT INTO Usuario VALUES ('USR001','admin_tecnoback','admin@tecnoback.co', SHA2('Admin123',256));
INSERT INTO Categoria (tipo, seccion, nombre, id_usuario) VALUES
 ('Computadores','Portátiles','Portátiles','USR001'),
 ('Perifericos','Teclados y Mouse','Teclados y Mouse','USR001'),
 ('Perifericos','Audio','Audífonos','USR001'),
 ('Componentes','Memoria','Memoria RAM','USR001');
INSERT INTO Producto (nombre, descripcion, precio, cantidad, id_categoria) VALUES
 ('Portátil ASUS TUF Gaming','Portátil orientado a videojuegos',4299900,6,1),
 ('Portátil Lenovo IdeaPad 3','Ligero para estudiantes',2399900,10,1),
 ('Teclado Mecánico RGB','Switches rojos, retroiluminado',249900,25,2),
 ('Mouse Inalámbrico','Sensor óptico 1600 DPI',79900,40,2),
 ('Audífonos Gamer 7.1','Sonido envolvente con micrófono',189900,15,3),
 ('Memoria RAM DDR4 16 GB','Kit 3200 MHz',219900,20,4);
