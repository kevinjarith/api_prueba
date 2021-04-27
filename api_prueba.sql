-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 27-04-2021 a las 20:21:00
-- Versión del servidor: 10.4.17-MariaDB
-- Versión de PHP: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `api_prueba`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recharge`
--

CREATE TABLE `recharge` (
  `rechargeid` int(11) NOT NULL,
  `rechargevalor` decimal(10,2) NOT NULL,
  `rechargeuserid` varchar(50) NOT NULL,
  `rechargefecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transfer`
--

CREATE TABLE `transfer` (
  `transferid` int(11) NOT NULL,
  `transfervalor` decimal(10,2) NOT NULL,
  `transferuser` varchar(50) NOT NULL,
  `receiveuser` varchar(50) NOT NULL,
  `transferfecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `userid` varchar(50) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `saldo` decimal(10,2) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `fecharegistro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `recharge`
--
ALTER TABLE `recharge`
  ADD PRIMARY KEY (`rechargeid`);

--
-- Indices de la tabla `transfer`
--
ALTER TABLE `transfer`
  ADD PRIMARY KEY (`transferid`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD UNIQUE KEY `userid` (`userid`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `recharge`
--
ALTER TABLE `recharge`
  MODIFY `rechargeid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transfer`
--
ALTER TABLE `transfer`
  MODIFY `transferid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
