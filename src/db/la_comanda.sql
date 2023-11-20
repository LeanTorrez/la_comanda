-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-11-2023 a las 18:20:03
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `la_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `rol` varchar(50) NOT NULL,
  `es_eliminado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `email`, `clave`, `rol`, `es_eliminado`) VALUES
(3, 'susana', 'susy@yahoo.com', 'pe2345', 'cocinero', 0),
(4, 'leandro', 'lean@yahoo.com', 'so123', 'socio', 0),
(5, 'leila', 'lei@yahoo.com', 'mo123', 'mozo', 0),
(6, 'sergio', 'ser@yahoo.com', 'bar123', 'bartender', 0),
(7, 'macarena', 'maca@yahoo.com', 'so2345', 'socio', 0),
(8, 'luis', 'luis@yahoo.com', 'co123', 'cocinero', 0),
(9, 'micaela', 'mica@yahoo.com', 'mo123', 'mozo', 0),
(10, 'roberto', 'rober@yahoo.com', 'bar123', 'bartender', 0),
(11, 'Camilo', 'cam@yahoo.com', 'cer123', 'cervecero', 0),
(12, 'Federico', 'fede@yahoo.com', 'cer124', 'cervecero', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id_mesa` int(11) NOT NULL,
  `alfanumerico` varchar(10) NOT NULL,
  `puntuacion` int(11) NOT NULL,
  `comentario` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encuesta`
--

INSERT INTO `encuesta` (`id_mesa`, `alfanumerico`, `puntuacion`, `comentario`) VALUES
(2, 'N6Y1K', 80, 'Excelente la comida y el servicio a la mesa.'),
(5, 'W4J3R', 60, 'La comida estubo bien, pero la atencion al cliente dejo que desear');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesas`
--

CREATE TABLE `mesas` (
  `id` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `id_mozo` int(11) NOT NULL,
  `id_pedido` varchar(10) DEFAULT '0',
  `es_eliminado` int(11) NOT NULL DEFAULT 0,
  `cantidad_usos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesas`
--

INSERT INTO `mesas` (`id`, `estado`, `id_mozo`, `id_pedido`, `es_eliminado`, `cantidad_usos`) VALUES
(2, 'cliente esperando pedido', 1, 'N6Y1K', 0, 0),
(3, 'con cliente comiendo', 5, 'T8C1O', 0, 5),
(4, 'cliente esperando pedido', 5, 'H1S7F', 0, 1),
(5, 'cerrada', 9, 'W4J3R', 0, 1),
(6, 'cerrada', 0, '0', 0, 0),
(7, 'cerrada', 0, '0', 0, 0),
(8, 'cerrada', 0, '0', 0, 0),
(9, 'cerrada', 0, '0', 0, 0),
(10, 'cerrada', 0, '0', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `alfanumerico` varchar(10) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `mozo_id` int(11) NOT NULL,
  `horario_estimado` datetime DEFAULT NULL,
  `horario_entrega` datetime DEFAULT NULL,
  `es_eliminado` int(11) NOT NULL DEFAULT 0,
  `ruta_foto` varchar(100) NOT NULL DEFAULT 'sin ruta'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`alfanumerico`, `nombre`, `mozo_id`, `horario_estimado`, `horario_entrega`, `es_eliminado`, `ruta_foto`) VALUES
('H1S7F', 'Sergio', 5, '2023-11-19 07:31:59', NULL, 0, 'sin ruta'),
('J4H6Q', 'Susana', 1, '2023-11-17 07:52:10', '0000-00-00 00:00:00', 0, 'sin ruta'),
('N6Y1K', 'Leandro', 5, '2023-11-17 07:50:15', '0000-00-00 00:00:00', 0, '/../imagenes/mesas/2-N6Y1K.jpg'),
('T8C1O', 'Susana', 5, '2023-11-19 05:56:53', '2023-11-20 05:06:35', 0, '/../imagenes/mesas/3-T8C1O.jpg'),
('W4J3R', 'Ludmila', 9, '2023-11-20 07:24:26', '2023-11-20 07:42:56', 0, '/../imagenes/mesas/5-W4J3R.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `tiempoPreparacion` int(11) NOT NULL,
  `cantidadVendida` int(11) NOT NULL,
  `es_eliminado` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `tipo`, `precio`, `tiempoPreparacion`, `cantidadVendida`, `es_eliminado`) VALUES
(1, 'Daikiri', 'coctel', 1000.00, 5, 3, 0),
(2, 'Milanesa de pollo', 'comida', 1500.00, 15, 3, 0),
(3, 'corona', 'cerveza', 400.00, 3, 3, 0),
(4, 'tarta de jamon', 'comida', 1500.00, 15, 3, 0),
(5, 'Margarita', 'coctel', 100.00, 4, 1, 0),
(6, 'asado', 'comida', 2000.00, 30, 1, 0),
(7, 'milanesa de carne', 'comida', 700.00, 10, 1, 0),
(8, 'stella artois', 'cerveza', 400.00, 3, 1, 0),
(10, 'brahma', 'cerveza', 650.00, 2, 0, 0),
(11, 'sopa de verduras', 'comida', 950.00, 10, 0, 0),
(12, 'mojito', 'coctel', 700.00, 10, 0, 0),
(13, 'Heineken', 'coctel', 800.00, 3, 0, 0),
(14, 'chinculines', 'comida', 1150.00, 2, 0, 0),
(15, 'milanesa a caballo', 'comida', 2000.00, 20, 0, 0),
(16, 'hamburguesa de garbanzo', 'comida', 900.00, 11, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_pedidos`
--

CREATE TABLE `productos_pedidos` (
  `id` int(11) NOT NULL,
  `alfanumerico` varchar(50) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos_pedidos`
--

INSERT INTO `productos_pedidos` (`id`, `alfanumerico`, `id_producto`, `tipo_producto`, `nombre_producto`, `estado`) VALUES
(2, 'N6Y1K', 10, 'cerveza', 'brahma', 'pendiente'),
(3, 'N6Y1K', 2, 'comida', 'Milanesa de pollo', 'en preparacion'),
(4, 'N6Y1K', 3, 'cerveza', 'corona', 'pendiente'),
(5, 'N6Y1K', 4, 'comida', 'tarta de jamon', 'pendiente'),
(6, 'N6Y1K', 8, 'cerveza', 'stella artois', 'pendiente'),
(7, 'J4H6Q', 1, 'cerveza', 'stella artois', 'pendiente'),
(8, 'J4H6Q', 2, 'comida', 'Milanesa de pollo', 'pendiente'),
(9, 'J4H6Q', 3, 'cerveza', 'corona', 'pendiente'),
(10, 'J4H6Q', 4, 'comida', 'tarta de jamon', 'pendiente'),
(11, 'J4H6Q', 8, 'cerveza', 'stella artois', 'pendiente'),
(12, 'T8C1O', 1, 'coctel', 'Daikiri', 'pendiente'),
(13, 'T8C1O', 2, 'comida', 'Milanesa de pollo', 'pendiente'),
(14, 'T8C1O', 3, 'cerveza', 'corona', 'pendiente'),
(15, 'T8C1O', 4, 'comida', 'tarta de jamon', 'pendiente'),
(16, 'T8C1O', 8, 'cerveza', 'stella artois', 'pendiente'),
(17, 'H1S7F', 1, 'coctel', 'Daikiri', 'pendiente'),
(18, 'H1S7F', 2, 'comida', 'Milanesa de pollo', 'pendiente'),
(19, 'H1S7F', 3, 'cerveza', 'corona', 'pendiente'),
(20, 'H1S7F', 4, 'comida', 'tarta de jamon', 'pendiente'),
(21, 'H1S7F', 8, 'cerveza', 'stella artois', 'pendiente'),
(22, 'W4J3R', 1, 'coctel', 'Daikiri', 'listo para servir'),
(23, 'W4J3R', 3, 'cerveza', 'corona', 'listo para servir'),
(24, 'W4J3R', 15, 'comida', 'milanesa a caballo', 'listo para servir'),
(25, 'W4J3R', 16, 'comida', 'hamburguesa de garbanzo', 'listo para servir');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD UNIQUE KEY `alfanumerico` (`alfanumerico`);

--
-- Indices de la tabla `mesas`
--
ALTER TABLE `mesas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD UNIQUE KEY `alfanumerico` (`alfanumerico`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos_pedidos`
--
ALTER TABLE `productos_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `mesas`
--
ALTER TABLE `mesas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `productos_pedidos`
--
ALTER TABLE `productos_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
