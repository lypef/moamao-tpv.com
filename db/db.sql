-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-08-2020 a las 08:43:34
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.2.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `moamaotp_store`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacen`
--

CREATE TABLE `almacen` (
  `id` int(11) NOT NULL,
  `nombre` varchar(254) NOT NULL,
  `ubicacion` varchar(254) NOT NULL,
  `telefono` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `almacen`
--

INSERT INTO `almacen` (`id`, `nombre`, `ubicacion`, `telefono`) VALUES
(3, 'CENTRAL 101', '101', '544');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `annuities`
--

CREATE TABLE `annuities` (
  `id` int(11) NOT NULL,
  `client` int(11) NOT NULL,
  `concepto` varchar(254) NOT NULL,
  `price` float NOT NULL,
  `date_ini` datetime NOT NULL DEFAULT current_timestamp(),
  `date_last` datetime NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `annuities`
--

INSERT INTO `annuities` (`id`, `client`, `concepto`, `price`, `date_ini`, `date_last`, `active`) VALUES
(4, 210, 'Anualidad CFDI', 1800, '2018-11-01 12:00:00', '2019-11-01 09:38:02', 1),
(6, 212, 'Anualidad CFDI', 1900, '2019-03-29 12:00:00', '2020-06-30 14:40:36', 1),
(7, 213, 'Anualidad CFDI', 1900, '2019-04-17 12:00:00', '2020-05-13 10:40:01', 1),
(8, 214, 'Anualidad CFDI', 1700, '2019-05-30 12:00:00', '2019-05-30 12:00:00', 0),
(9, 215, 'Anualidad CFDI', 1900, '2019-09-01 12:00:00', '2019-09-01 12:00:00', 1),
(10, 199, 'Anualidad CFDI', 1900, '2019-10-30 14:37:47', '2019-10-30 14:37:47', 1),
(12, 154, 'Anualidad SendMAil', 100, '2019-11-14 09:45:02', '2019-11-14 09:45:02', 1),
(13, 277, 'Anualidad CFDI , CABB891111CL8', 1900, '2020-02-21 11:02:15', '2020-02-21 11:02:15', 1),
(14, 154, 'Anualidad SendMAil. fol 120200223223835', 100, '2020-02-24 14:11:01', '2020-02-24 14:11:01', 1),
(15, 282, 'moamao-tpv.com + certificado digital, anualidad. ', 1300, '2020-04-04 16:04:02', '2020-04-04 16:04:02', 1),
(16, 290, 'anualidad rfc GIA100728216 , GIC040830321', 1900, '2020-06-01 12:25:48', '2020-06-01 12:25:48', 1),
(17, 154, 'eNVIOS DE CORREO ELECTRONICA', 100, '2020-07-17 12:13:41', '2020-07-17 12:13:41', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nombre` varchar(254) NOT NULL,
  `direccion` varchar(254) NOT NULL,
  `telefono` varchar(254) NOT NULL,
  `descuento` int(11) NOT NULL,
  `rfc` varchar(254) NOT NULL,
  `razon_social` varchar(254) NOT NULL,
  `correo` varchar(254) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `clients`
--

INSERT INTO `clients` (`id`, `nombre`, `direccion`, `telefono`, `descuento`, `rfc`, `razon_social`, `correo`) VALUES
(1, 'PUBLICO EN GENERAL', 'Dirección de cliente demo ', '923120050', 0, 'XAXX010101000', 'PUBLICO EN GENERAL', 'ventas@cyberchoapas.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credits`
--

CREATE TABLE `credits` (
  `id` int(11) NOT NULL,
  `client` int(11) NOT NULL,
  `f_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `factura` varchar(254) NOT NULL,
  `adeudo` decimal(65,4) NOT NULL,
  `abono` decimal(65,4) NOT NULL,
  `dias_credit` int(11) NOT NULL,
  `pay` tinyint(1) NOT NULL DEFAULT 0,
  `sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `credits`
--

INSERT INTO `credits` (`id`, `client`, `f_registro`, `factura`, `adeudo`, `abono`, `dias_credit`, `pay`, `sucursal`) VALUES
(1, 213, '2020-06-15 15:57:33', '120200615120441', '186.7600', '0.0000', 3, 1, 10),
(8, 1, '2020-06-19 18:49:44', '120200619184843', '219.2400', '219.2400', 7, 1, 10),
(9, 295, '2020-06-20 16:20:35', '120200620161732', '90.0000', '90.0000', 7, 1, 10),
(10, 296, '2020-06-20 16:20:39', '120200620161632', '620.0000', '620.0000', 7, 1, 10),
(11, 1, '2020-06-20 16:33:00', '120200620163250', '10.0000', '10.0000', 7, 1, 10),
(14, 296, '2020-06-27 15:00:28', '120200627145955', '90.0000', '0.0000', 7, 1, 10),
(20, 298, '2020-07-01 16:17:53', '120200701161718', '672.8000', '0.0000', 7, 0, 10),
(23, 295, '2020-07-04 22:26:30', '120200704194345', '270.0000', '270.0000', 7, 1, 10),
(24, 296, '2020-07-04 22:26:33', '120200704194244', '90.0000', '90.0000', 7, 1, 10),
(26, 296, '2020-07-11 12:04:29', '120200711120307', '180.0000', '0.0000', 7, 1, 10),
(27, 295, '2020-07-11 12:06:16', '120200711120531', '180.0000', '180.0000', 7, 1, 10),
(32, 294, '2020-07-20 13:54:00', '120200720134513', '280.0000', '0.0000', 7, 0, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `credit_pay`
--

CREATE TABLE `credit_pay` (
  `id` int(11) NOT NULL,
  `credito` int(11) NOT NULL,
  `monto` decimal(65,4) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `credit_pay`
--

INSERT INTO `credit_pay` (`id`, `credito`, `monto`, `fecha`) VALUES
(10, 14, '90.0000', '2020-06-27 18:30:46'),
(11, 1, '186.7600', '2020-07-08 12:34:05'),
(12, 26, '180.0000', '2020-07-11 12:54:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE `departamentos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(254) NOT NULL,
  `descripcion` varchar(254) NOT NULL,
  `view_index` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`id`, `nombre`, `descripcion`, `view_index`) VALUES
(33, 'DEPARTAMENTO', 'ARTICULOS VARIOS', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id` int(11) NOT NULL,
  `nombre` varchar(254) NOT NULL,
  `nombre_corto` varchar(254) NOT NULL,
  `direccion` varchar(254) NOT NULL,
  `correo` varchar(254) NOT NULL,
  `telefono` varchar(254) NOT NULL,
  `mision` longtext NOT NULL,
  `vision` longtext NOT NULL,
  `contacto` longtext NOT NULL,
  `facebook` varchar(254) NOT NULL,
  `twitter` varchar(254) NOT NULL,
  `youtube` varchar(254) NOT NULL,
  `iva` int(11) NOT NULL,
  `footer` longtext NOT NULL,
  `cfdi_lugare_expedicion` varchar(254) NOT NULL,
  `cfdi_rfc` varchar(254) NOT NULL,
  `cfdi_regimen` varchar(254) NOT NULL,
  `cfdi_cer` varchar(254) NOT NULL,
  `cfdi_key` varchar(254) NOT NULL,
  `cfdi_pass` varchar(254) NOT NULL,
  `token` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id`, `nombre`, `nombre_corto`, `direccion`, `correo`, `telefono`, `mision`, `vision`, `contacto`, `facebook`, `twitter`, `youtube`, `iva`, `footer`, `cfdi_lugare_expedicion`, `cfdi_rfc`, `cfdi_regimen`, `cfdi_cer`, `cfdi_key`, `cfdi_pass`, `token`) VALUES
(1, 'GRUPO ASCGAR', 'GA', 'Veracruz, Mexico', 'contacto@cyberchoapa.scom', '+52 55 4163 0891 ', 'Somos una empresa fundada físicamente el 29 de mayo del año 2013 en el estado de Veracruz, México. Dedicada al desarrollo, distribución y venta de software, soluciones en Internet, venta de equipos (Hardware) y servicios varios. Ofreciendo una solución global a empresas, profesionales, administraciones, usuarios particulares y al publico en general, en todo el territorio nacional e internacional .', 'Pretendemos ser un referente en el mercado nacional en el sector de las TIC, y para ello abarcaremos todos los servicios que ofrecemos actualmente incrementando los que vayan surgiendo debido a la necesidad de cambio provocado por los avances tecnológicos. Esto es así ya que somos una empresa en constante innovación ya que el sector de la tecnología así lo requiere.', 'Telefono\r\n<br>\r\n+52 55 4163 0891\r\n<br><br>\r\nVentas | Informacion \r\n<br>\r\nventas@cyberchoapas.com', '', '', '', 16, '<h5 style=\"background-color: #1a4f7d; text-align: center;\"><span style=\"background-color: #1a4f7d; color: #ffffff;\"><em><strong>| www.cyberchoapas.com | ::: GRUPO ASCGAR ::: | www.ascgar.com |</strong></em></span><span style=\"background-color: #1a4f7d; color: #ffffff;\"><em><strong><br /></strong></em></span></h5>', '96980', 'AEDF9201245G3', '621', 'SDK2/certificados/CER.cer  ', 'SDK2/certificados/KEY.key', 'AEDF9201', 'g4UW4c0gIkyX2yH90bOHlCx8ivt0lD3/Eyh7AnLuSrmVeBiyFbjEmdlFBs0uaaeVOxQRjz5DPTmXzuZrWdVZs/bsVoQ8Tc4BWo/XDDG+EvA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas`
--

CREATE TABLE `facturas` (
  `serie` varchar(254) NOT NULL,
  `folio` varchar(254) NOT NULL,
  `estatus` varchar(254) NOT NULL,
  `cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `facturas`
--

INSERT INTO `facturas` (`serie`, `folio`, `estatus`, `cliente`) VALUES
('A', '120190819143328', 'Vigente', 154),
('A', '120190819153303', 'Proceso cancelar', 1),
('A', '120190819153435', 'Proceso cancelar', 1),
('A', '120190820121431', 'Vigente', 155),
('A', '120190913132203', 'Proceso cancelar', 1),
('A', '120190916221358', 'Vigente', 180),
('A', '120190925093148', 'Vigente', 184),
('A', '120190927195056', 'Vigente', 187),
('A', '120190930201754', 'Vigente', 1),
('A', '120190930202456', 'Vigente', 1),
('A', '120191004195727', 'Vigente', 198),
('A', '120191011135643', 'Vigente', 199),
('A', '120191014113426', 'Vigente', 184),
('A', '120191014165302', 'Vigente', 200),
('A', '120191029181000', 'Vigente', 208),
('A', '120191122200257', 'Vigente', 198),
('A', '120191123103040', 'Vigente', 228),
('A', '120191128191557', 'Vigente', 198),
('A', '120191211172238', 'Vigente', 240),
('A', '120191212203154', 'Vigente', 198),
('A', '120191219190531', 'Vigente', 198),
('A', '120191223121908', 'Vigente', 244),
('A', '120191223191852', 'Vigente', 245),
('A', '120191231095013', 'Vigente', 240),
('A', '120200103144631', 'Vigente', 252),
('A', '120200105121047', 'Vigente', 253),
('A', '120200105140748', 'Vigente', 254),
('A', '120200111110951', 'Vigente', 198),
('A', '120200114134955', 'Vigente', 155),
('A', '120200124105728', 'Vigente', 263),
('A', '120200205122840', 'Vigente', 270),
('A', '120200211003703', 'Vigente', 273),
('B', '120200214191541', 'Vigente', 275),
('A', '120200219111106', 'Vigente', 278),
('A', '120200219164131', 'Vigente', 200),
('A', '120200223223835', 'Vigente', 154),
('A', '120200228101706', 'Vigente', 270),
('A', '120200301183321', 'Vigente', 282),
('A', '120200311102623', 'Vigente', 285),
('A', '120200314104810', 'Vigente', 198),
('A', '120200325181657', 'Vigente', 198),
('A', '120200404153703', 'Vigente', 282),
('A', '120200404160402', 'Vigente', 282),
('A', '120200422094312', 'Vigente', 289),
('B', '120200430141134', 'Vigente', 290),
('A', '120200513104001', 'Vigente', 213),
('A', '120200514232903', 'Vigente', 293),
('A', '120200521115213', 'Vigente', 198),
('A', '120200525125754', 'Vigente', 290),
('A', '120200526153134', 'Vigente', 290),
('A', '120200526153239', 'Vigente', 290),
('A', '120200527092213', 'Vigente', 290),
('A', '120200527092332', 'Vigente', 290),
('A', '120200527092511', 'Vigente', 290),
('A', '120200527092727', 'Vigente', 290),
('A', '120200602141246', 'Vigente', 294),
('A', '120200603154543', 'Vigente', 213),
('B', '120200622193924', 'Vigente', 282),
('B', '120200630124124', 'Vigente', 244),
('B', '120200630144036', 'Vigente', 212),
('B', '120200705162232', 'Vigente', 301),
('A', '120200708192655', 'Vigente', 302),
('B', '3620200115190647', 'Vigente', 258),
('A', '3720191023123718', 'Proceso cancelar', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `folio_venta`
--

CREATE TABLE `folio_venta` (
  `folio` varchar(254) NOT NULL,
  `vendedor` int(11) NOT NULL,
  `client` int(11) NOT NULL,
  `descuento` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `open` tinyint(1) NOT NULL,
  `cobrado` float DEFAULT NULL,
  `fecha_venta` datetime DEFAULT NULL,
  `cut` tinyint(1) DEFAULT 0,
  `sucursal` int(11) NOT NULL,
  `cut_global` int(11) NOT NULL DEFAULT 0,
  `iva` int(11) NOT NULL DEFAULT 0,
  `t_pago` varchar(254) NOT NULL DEFAULT 'Ninguno',
  `pedido` tinyint(1) NOT NULL DEFAULT 0,
  `folio_venta_ini` varchar(254) DEFAULT NULL,
  `cotizacion` tinyint(1) NOT NULL DEFAULT 0,
  `concepto` varchar(254) DEFAULT NULL,
  `comision_pagada` tinyint(1) NOT NULL DEFAULT 0,
  `oxxo_pay` varchar(254) NOT NULL DEFAULT '0',
  `titulo` varchar(254) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `no. De parte` varchar(254) NOT NULL,
  `nombre` varchar(254) NOT NULL,
  `descripcion` longtext NOT NULL,
  `almacen` int(11) NOT NULL,
  `departamento` int(11) NOT NULL,
  `loc_almacen` varchar(254) NOT NULL,
  `marca` varchar(254) NOT NULL,
  `proveedor` varchar(254) NOT NULL,
  `foto0` varchar(254) NOT NULL,
  `foto1` varchar(254) NOT NULL,
  `foto2` varchar(254) NOT NULL,
  `foto3` varchar(254) NOT NULL,
  `oferta` tinyint(1) NOT NULL DEFAULT 0,
  `precio_normal` float NOT NULL DEFAULT 0,
  `precio_oferta` float NOT NULL DEFAULT 0,
  `stock` int(11) NOT NULL,
  `tiempo de entrega` varchar(254) NOT NULL,
  `stock_min` int(11) NOT NULL,
  `stock_max` int(11) NOT NULL,
  `precio_costo` float NOT NULL DEFAULT 0,
  `cv` varchar(254) NOT NULL DEFAULT '01010101',
  `um` varchar(254) NOT NULL DEFAULT 'H87',
  `um_des` varchar(254) NOT NULL DEFAULT 'NA'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `no. De parte`, `nombre`, `descripcion`, `almacen`, `departamento`, `loc_almacen`, `marca`, `proveedor`, `foto0`, `foto1`, `foto2`, `foto3`, `oferta`, `precio_normal`, `precio_oferta`, `stock`, `tiempo de entrega`, `stock_min`, `stock_max`, `precio_costo`, `cv`, `um`, `um_des`) VALUES
(47, 'mag-350', 'IMAN MAGNETICO PARA PUERTA', '', 3, 33, 'RSELL', 'VARIOS', 'DESCONOCIDO', 'product/product_img120200305095158.jpg', '', '', '', 0, 2800, 2700, 2, '1 DIA HABIL', 1, 1, 1600, '01010101', 'H87', 'NA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_sub`
--

CREATE TABLE `productos_sub` (
  `id` int(11) NOT NULL,
  `padre` int(11) NOT NULL,
  `almacen` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `ubicacion` varchar(254) NOT NULL,
  `max` int(11) NOT NULL DEFAULT 0,
  `min` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_pedido`
--

CREATE TABLE `product_pedido` (
  `id` int(11) NOT NULL,
  `folio_venta` varchar(254) NOT NULL,
  `product` int(11) DEFAULT NULL,
  `unidades` int(11) NOT NULL,
  `precio` float NOT NULL,
  `p_generico` varchar(254) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product_venta`
--

CREATE TABLE `product_venta` (
  `id` int(11) NOT NULL,
  `folio_venta` varchar(254) NOT NULL,
  `product` int(11) DEFAULT NULL,
  `unidades` int(11) NOT NULL,
  `precio` float NOT NULL,
  `product_sub` int(11) DEFAULT NULL,
  `p_generico` varchar(254) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `product_venta`
--

INSERT INTO `product_venta` (`id`, `folio_venta`, `product`, `unidades`, `precio`, `product_sub`, `p_generico`) VALUES
(842, '120200620163250', NULL, 1, 10, NULL, 'aa'),
(846, '120200622193924', NULL, 1, 200, NULL, 'Eliminar Abonos de caja'),
(848, '120200622193924', NULL, 1, 324, NULL, 'Cierre de caja en modo corte x & z'),
(849, '120200622193924', NULL, 1, 1700, NULL, 'Informtes (Listado)'),
(850, '120200622193924', NULL, 1, 1948, NULL, 'Control de pedidos'),
(855, '120200626165105', NULL, 1, 580, NULL, 'Restauración. '),
(861, '120200630124124', NULL, 1, 672.8, NULL, 'Reinicie licencia manual '),
(864, '120200630144036', NULL, 1, 1900, NULL, 'Anualidad CFDI'),
(867, '120200701161718', NULL, 1, 672.8, NULL, 'Recontruccion de usuario administrador'),
(868, '120200701175124', NULL, 64, 300, NULL, 'Modulo vehiculos multiples'),
(1415, '120200703143244', NULL, 1, 650, NULL, 'AJUSTE BOTONES POR SEMANA'),
(1421, '120200705162232', NULL, 1, 800, NULL, 'LECTOR RFID'),
(1423, '120200705162232', NULL, 100, 17, NULL, 'Tarjetas Proximidad'),
(1424, '120200705162232', NULL, 50, 29, NULL, 'Llaveros rfid'),
(1442, '120200710135209', NULL, 1, 464, NULL, 'Reinstalación sistema en blanco '),
(1449, '120200715194200', NULL, 1, 800, NULL, 'Lector RFID'),
(1450, '120200715194200', NULL, 100, 17, NULL, 'Tarjetas RFID');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `soporte`
--

CREATE TABLE `soporte` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(254) NOT NULL,
  `costo` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `soporte`
--

INSERT INTO `soporte` (`id`, `descripcion`, `costo`) VALUES
(1, 'REINSTALACION SISTEMA SIN RESPALDO', 150),
(2, 'REINSTALACION DE SISTEMA CON RESPALDO', 400),
(3, 'CONFIGURACION DE WINDOWS PARA TRABAJO EN RED', 400),
(4, 'AGREGAR COMPUTADORA ADICIONAL', 100),
(5, 'INSTALACION Y CONFIGURACION DE IMPRESORAS', 150),
(6, 'RECUPERACION DE CONTRASEÑA', 350),
(7, 'ERROR EN CONFIGURACION DE SISTEMA', 180),
(8, 'ERROR EN CONFIGURACION DE SISTEMA OPERATIVO', 220),
(9, 'OPTIMIZACION DE SISTEMA OPERATIVO', 250),
(10, 'LIMPIEZA DE VIRUS Y AMENAZAS DIGITALES', 201),
(11, 'ACTUALIZACION DE SISTEMAS', 580);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursales`
--

CREATE TABLE `sucursales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(254) NOT NULL,
  `direccion` varchar(254) NOT NULL,
  `telefono` varchar(254) NOT NULL,
  `cfdi_serie` varchar(254) NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sucursales`
--

INSERT INTO `sucursales` (`id`, `nombre`, `direccion`, `telefono`, `cfdi_serie`) VALUES
(10, 'SUCURSAL 1', 'AVENIDA 20 DE NOVIEMBRE 306, CENTRO', '+52 55 4163 0891', 'B');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal_almacen`
--

CREATE TABLE `sucursal_almacen` (
  `id` int(11) NOT NULL,
  `sucursal` int(11) NOT NULL,
  `almacen` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sucursal_almacen`
--

INSERT INTO `sucursal_almacen` (`id`, `sucursal`, `almacen`) VALUES
(9, 10, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(254) NOT NULL,
  `password` varchar(254) NOT NULL,
  `nombre` varchar(254) NOT NULL,
  `imagen` varchar(254) NOT NULL,
  `product_add` tinyint(1) NOT NULL DEFAULT 0,
  `product_gest` tinyint(1) NOT NULL DEFAULT 0,
  `gen_orden_compra` tinyint(1) NOT NULL DEFAULT 0,
  `client_add` tinyint(1) NOT NULL DEFAULT 0,
  `client_guest` tinyint(1) NOT NULL DEFAULT 0,
  `almacen_add` tinyint(1) NOT NULL DEFAULT 0,
  `almacen_guest` tinyint(1) NOT NULL DEFAULT 0,
  `depa_add` tinyint(1) NOT NULL DEFAULT 0,
  `depa_guest` tinyint(1) NOT NULL DEFAULT 0,
  `propiedades` tinyint(1) NOT NULL DEFAULT 0,
  `usuarios` tinyint(1) NOT NULL DEFAULT 0,
  `finanzas` tinyint(1) NOT NULL DEFAULT 0,
  `descripcion` longtext NOT NULL,
  `sucursal` int(11) NOT NULL,
  `change_suc` tinyint(1) NOT NULL,
  `sucursal_gest` tinyint(1) NOT NULL DEFAULT 0,
  `caja` tinyint(1) NOT NULL DEFAULT 0,
  `super_pedidos` tinyint(1) NOT NULL DEFAULT 0,
  `comision` int(11) DEFAULT 5,
  `sueldo` float NOT NULL DEFAULT 0,
  `vtd_pg` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nombre`, `imagen`, `product_add`, `product_gest`, `gen_orden_compra`, `client_add`, `client_guest`, `almacen_add`, `almacen_guest`, `depa_add`, `depa_guest`, `propiedades`, `usuarios`, `finanzas`, `descripcion`, `sucursal`, `change_suc`, `sucursal_gest`, `caja`, `super_pedidos`, `comision`, `sueldo`, `vtd_pg`) VALUES
(1, 'root', '6990149e5865432c7061b4b1376b7283', 'ISC. FRANCISCO E. ASCENCIO DOMINGUEZ', 'users/usuario20200624223146.jpg', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'CEO', 10, 1, 1, 1, 1, 5, 1800, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacen`
--
ALTER TABLE `almacen`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `annuities`
--
ALTER TABLE `annuities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annuity_client` (`client`);

--
-- Indices de la tabla `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `credits`
--
ALTER TABLE `credits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `factura` (`factura`),
  ADD KEY `credit_client` (`client`),
  ADD KEY `credit_sucursal` (`sucursal`);

--
-- Indices de la tabla `credit_pay`
--
ALTER TABLE `credit_pay`
  ADD PRIMARY KEY (`id`),
  ADD KEY `credit` (`credito`);

--
-- Indices de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD PRIMARY KEY (`folio`),
  ADD KEY `cliente_cliente` (`cliente`);

--
-- Indices de la tabla `folio_venta`
--
ALTER TABLE `folio_venta`
  ADD PRIMARY KEY (`folio`),
  ADD KEY `client_folio` (`client`),
  ADD KEY `vendedor_folio` (`vendedor`),
  ADD KEY `sale_sucursal` (`sucursal`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_almacen` (`almacen`),
  ADD KEY `producto_departamento` (`departamento`);

--
-- Indices de la tabla `productos_sub`
--
ALTER TABLE `productos_sub`
  ADD PRIMARY KEY (`id`),
  ADD KEY `padre_hijo` (`padre`),
  ADD KEY `hijo_almacen` (`almacen`);

--
-- Indices de la tabla `product_pedido`
--
ALTER TABLE `product_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto` (`product`),
  ADD KEY `folio` (`folio_venta`);

--
-- Indices de la tabla `product_venta`
--
ALTER TABLE `product_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `folio_venta` (`folio_venta`),
  ADD KEY `sale_product` (`product`),
  ADD KEY `hijo` (`product_sub`);

--
-- Indices de la tabla `soporte`
--
ALTER TABLE `soporte`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sucursal_almacen`
--
ALTER TABLE `sucursal_almacen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sucursal` (`sucursal`),
  ADD KEY `almacen` (`almacen`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendedor_sucursal` (`sucursal`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacen`
--
ALTER TABLE `almacen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `annuities`
--
ALTER TABLE `annuities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT de la tabla `credits`
--
ALTER TABLE `credits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `credit_pay`
--
ALTER TABLE `credit_pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `departamentos`
--
ALTER TABLE `departamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT de la tabla `productos_sub`
--
ALTER TABLE `productos_sub`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `product_pedido`
--
ALTER TABLE `product_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `product_venta`
--
ALTER TABLE `product_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1472;

--
-- AUTO_INCREMENT de la tabla `soporte`
--
ALTER TABLE `soporte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `sucursales`
--
ALTER TABLE `sucursales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `sucursal_almacen`
--
ALTER TABLE `sucursal_almacen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `annuities`
--
ALTER TABLE `annuities`
  ADD CONSTRAINT `annuity_client` FOREIGN KEY (`client`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `credits`
--
ALTER TABLE `credits`
  ADD CONSTRAINT `credit_client` FOREIGN KEY (`client`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `credit_sucursal` FOREIGN KEY (`sucursal`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `credit_pay`
--
ALTER TABLE `credit_pay`
  ADD CONSTRAINT `credit` FOREIGN KEY (`credito`) REFERENCES `credits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `facturas`
--
ALTER TABLE `facturas`
  ADD CONSTRAINT `cliente_cliente` FOREIGN KEY (`cliente`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `folio_venta`
--
ALTER TABLE `folio_venta`
  ADD CONSTRAINT `client_folio` FOREIGN KEY (`client`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_sucursal` FOREIGN KEY (`sucursal`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vendedor_folio` FOREIGN KEY (`vendedor`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `producto_almacen` FOREIGN KEY (`almacen`) REFERENCES `almacen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `producto_departamento` FOREIGN KEY (`departamento`) REFERENCES `departamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `productos_sub`
--
ALTER TABLE `productos_sub`
  ADD CONSTRAINT `hijo_almacen` FOREIGN KEY (`almacen`) REFERENCES `almacen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `padre_hijo` FOREIGN KEY (`padre`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `product_pedido`
--
ALTER TABLE `product_pedido`
  ADD CONSTRAINT `folio` FOREIGN KEY (`folio_venta`) REFERENCES `folio_venta` (`folio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `producto` FOREIGN KEY (`product`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `product_venta`
--
ALTER TABLE `product_venta`
  ADD CONSTRAINT `folio_venta` FOREIGN KEY (`folio_venta`) REFERENCES `folio_venta` (`folio`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `hijo` FOREIGN KEY (`product_sub`) REFERENCES `productos_sub` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sale_product` FOREIGN KEY (`product`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sucursal_almacen`
--
ALTER TABLE `sucursal_almacen`
  ADD CONSTRAINT `almacen` FOREIGN KEY (`almacen`) REFERENCES `almacen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sucursal` FOREIGN KEY (`sucursal`) REFERENCES `sucursales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `vendedor_sucursal` FOREIGN KEY (`sucursal`) REFERENCES `sucursales` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
