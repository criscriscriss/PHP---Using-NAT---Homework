-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-09-2025 a las 02:20:14
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `redes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dispositivos`
--

CREATE TABLE `dispositivos` (
  `id_dispositivo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ip_privada` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `dispositivos`
--

INSERT INTO `dispositivos` (`id_dispositivo`, `nombre`, `ip_privada`) VALUES
(1, 'Smartphone', '192.168.1.10'),
(2, 'Laptop', '192.168.1.20'),
(3, 'PC Gamer', '192.168.1.30'),
(4, 'Smart TV', '192.168.1.40'),
(5, 'Tablet', '192.168.1.50'),
(6, 'Maus', '192.95.2.1'),
(7, 'Maus', '123.168.2. 1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id_respuesta` int(11) NOT NULL,
  `id_solicitud` int(11) DEFAULT NULL,
  `mensaje` varchar(255) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id_respuesta`, `id_solicitud`, `mensaje`, `fecha`) VALUES
(1, 1, 'Respuesta de Internet para Entrar a WhatsApp Web → entregada a Smartphone (192.168.1.10)', '2025-09-06 16:52:51'),
(2, 2, 'Respuesta de Internet para Abrir YouTube → entregada a Laptop (192.168.1.20)', '2025-09-06 16:52:51'),
(3, 3, 'Respuesta de Internet para Jugar en Steam → entregada a PC Gamer (192.168.1.30)', '2025-09-06 16:52:51'),
(4, 4, 'Respuesta de Internet para Acceder a Netflix → entregada a Smart TV (192.168.1.40)', '2025-09-06 16:52:51'),
(5, 5, 'Respuesta de Internet para Navegar en Wikipedia → entregada a Tablet (192.168.1.50)', '2025-09-06 16:52:51'),
(6, 6, 'Respuesta de Internet para Abrir Instagram → entregada a Smartphone (192.168.1.10)', '2025-09-06 16:52:51'),
(7, 7, 'Respuesta de Internet para Conectar a Google Drive → entregada a Laptop (192.168.1.20)', '2025-09-06 16:52:51'),
(8, 8, 'Respuesta de Internet para Partida en Fortnite → entregada a PC Gamer (192.168.1.30)', '2025-09-06 16:52:51'),
(13, 13, 'Respuesta de Internet para Ver youtube → entregada a Maus (123.168.2. 1)', '2025-09-06 17:18:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

CREATE TABLE `solicitudes` (
  `id_solicitud` int(11) NOT NULL,
  `id_dispositivo` int(11) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  `ip_publica` varchar(15) NOT NULL,
  `puerto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes`
--

INSERT INTO `solicitudes` (`id_solicitud`, `id_dispositivo`, `descripcion`, `ip_publica`, `puerto`) VALUES
(1, 1, 'Entrar a WhatsApp Web', '181.50.23.65', 5001),
(2, 2, 'Abrir YouTube', '142.250.72.206', 443),
(3, 3, 'Jugar en Steam', '192.95.33.35', 27015),
(4, 4, 'Acceder a Netflix', '52.26.15.88', 443),
(5, 5, 'Navegar en Wikipedia', '208.80.154.224', 80),
(6, 1, 'Abrir Instagram', '157.240.20.35', 443),
(7, 2, 'Conectar a Google Drive', '142.250.184.206', 443),
(8, 3, 'Partida en Fortnite', '35.198.123.77', 5222),
(13, 7, 'Ver youtube', '157.89.12.12', 1322);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  ADD PRIMARY KEY (`id_dispositivo`),
  ADD UNIQUE KEY `ip_privada` (`ip_privada`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id_respuesta`),
  ADD KEY `id_solicitud` (`id_solicitud`);

--
-- Indices de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `id_dispositivo` (`id_dispositivo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `dispositivos`
--
ALTER TABLE `dispositivos`
  MODIFY `id_dispositivo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id_respuesta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitudes` (`id_solicitud`);

--
-- Filtros para la tabla `solicitudes`
--
ALTER TABLE `solicitudes`
  ADD CONSTRAINT `solicitudes_ibfk_1` FOREIGN KEY (`id_dispositivo`) REFERENCES `dispositivos` (`id_dispositivo`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
