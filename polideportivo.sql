-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-11-2025 a las 15:32:48
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
-- Base de datos: `polideportivo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `idactividad` int(11) NOT NULL,
  `actividad` varchar(100) DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT NULL,
  `horario` varchar(10) DEFAULT NULL,
  `lugar` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`idactividad`, `actividad`, `costo`, `horario`, `lugar`) VALUES
(1, 'Futbol', 1000.00, 'M3', 'Cancha A'),
(2, 'Natacion', 800.00, 'M4', 'Alberca A'),
(3, 'Basketball', 900.00, 'V5', 'Cancha B'),
(4, 'Volibol', 900.00, 'V6', 'Cancha C'),
(5, 'Tenis', 1000.00, 'V1', 'Cancha D'),
(6, 'Futbol', 1000.00, 'N1', 'Cancha A'),
(7, 'Natacion', 800.00, 'V2', 'Alberca B');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estudiantes`
--

CREATE TABLE `estudiantes` (
  `matricula` int(11) NOT NULL,
  `nombre_completo` varchar(100) DEFAULT NULL,
  `carrera` varchar(50) DEFAULT NULL,
  `semestre` int(11) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estudiantes`
--

INSERT INTO `estudiantes` (`matricula`, `nombre_completo`, `carrera`, `semestre`, `edad`) VALUES
(1, 'Estudiante A', 'IAS', 5, 20),
(2, 'Estudiante B', 'ITS', 4, 19),
(3, 'Estudiante C', 'IME', 6, 21),
(4, 'Estudiante D', 'IMA', 3, 19),
(5, 'Estudiante E', 'ITS', 7, 22),
(6, 'Estudiante F', 'IAS', 6, 21),
(7, 'Estudiante G', 'IB', 5, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
--

CREATE TABLE `registros` (
  `id` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `matricula` int(11) DEFAULT NULL,
  `idactividad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registros`
--

INSERT INTO `registros` (`id`, `fecha`, `matricula`, `idactividad`) VALUES
(1, '2024-01-17', 1, 1),
(2, '2024-01-18', 2, 2),
(3, '2024-01-19', 3, 3),
(4, '2024-01-19', 4, 2),
(5, '2024-01-22', 2, 4),
(6, '2024-01-22', 1, 5),
(7, '2024-01-23', 5, 6),
(8, '2024-01-23', 6, 7),
(9, '2024-01-25', 5, 3),
(10, '2024-02-01', 7, 4),
(11, '2024-02-01', 7, 6);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`idactividad`);

--
-- Indices de la tabla `estudiantes`
--
ALTER TABLE `estudiantes`
  ADD PRIMARY KEY (`matricula`);

--
-- Indices de la tabla `registros`
--
ALTER TABLE `registros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matricula` (`matricula`),
  ADD KEY `idactividad` (`idactividad`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `registros`
--
ALTER TABLE `registros`
  ADD CONSTRAINT `registros_ibfk_1` FOREIGN KEY (`matricula`) REFERENCES `estudiantes` (`matricula`),
  ADD CONSTRAINT `registros_ibfk_2` FOREIGN KEY (`idactividad`) REFERENCES `actividades` (`idactividad`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
