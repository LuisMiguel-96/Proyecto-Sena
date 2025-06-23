-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS ama_chocolates;

-- Usar la base de datos
USE ama_chocolates;

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE,
    genero VARCHAR(20),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(200),
    ciudad VARCHAR(50),
    municipio VARCHAR(50),
    codigo_postal VARCHAR(10),
    razon_social VARCHAR(100),
    identificacion_fiscal VARCHAR(20),
    cargo VARCHAR(50),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de inventario (productos)
CREATE TABLE IF NOT EXISTS inventario (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL,
    descripcion VARCHAR(200),
    categoria VARCHAR(50),
    stock_actual INT DEFAULT 0,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de ingresos de mercancía
CREATE TABLE IF NOT EXISTS ingresos_mercancia (
    id_ingreso INT AUTO_INCREMENT PRIMARY KEY,
    numero_pedido VARCHAR(20) NOT NULL,
    nit_proveedor VARCHAR(20),
    codigo_producto VARCHAR(20) NOT NULL,
    cantidad INT NOT NULL,
    lote VARCHAR(50),
    categoria VARCHAR(50),
    fecha_ingreso TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    fecha_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'Pendiente',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

-- Tabla de detalles de pedido
CREATE TABLE IF NOT EXISTS detalles_pedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    codigo_producto VARCHAR(20),
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (id_pedido) REFERENCES pedidos(id_pedido)
);

-- Tabla de devoluciones
CREATE TABLE IF NOT EXISTS devoluciones (
    id_devolucion INT AUTO_INCREMENT PRIMARY KEY,
    tipo_devolucion VARCHAR(20) NOT NULL,
    numero_pedido_factura VARCHAR(20) NOT NULL,
    codigo_producto VARCHAR(20) NOT NULL,
    lote VARCHAR(50),
    cantidad INT NOT NULL,
    unidad_medida VARCHAR(20),
    novedades TEXT,
    fecha_devolucion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla para registrar mercancía facturada
CREATE TABLE IF NOT EXISTS mercancia_facturada (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(20) NOT NULL,
    id_cliente INT,
    fecha_factura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'Completo',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

-- Tabla de detalles de factura
CREATE TABLE IF NOT EXISTS detalles_factura (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT,
    codigo_producto VARCHAR(20),
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    FOREIGN KEY (id_factura) REFERENCES mercancia_facturada(id_factura)
);