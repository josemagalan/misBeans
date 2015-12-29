-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-12-2015 a las 15:06:56
-- Versión del servidor: 5.6.17
-- Versión de PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `misbeans`
--
CREATE DATABASE IF NOT EXISTS `misbeans` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `misbeans`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `actionId` tinyint(4) NOT NULL,
  `actionData` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas`
--

DROP TABLE IF EXISTS `ofertas`;
CREATE TABLE IF NOT EXISTS `ofertas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPartida` int(11) NOT NULL,
  `idCreador` int(11) NOT NULL,
  `idDestinatario` int(11) NOT NULL,
  `creado` datetime NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `estado` tinyint(4) NOT NULL,
  `aluBlancaIn` int(11) NOT NULL,
  `aluRojaIn` int(11) NOT NULL,
  `aluBlancaOut` int(11) NOT NULL,
  `aluRojaOut` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idCreador` (`idCreador`),
  KEY `idDestinatario` (`idDestinatario`),
  KEY `idPartida` (`idPartida`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida`
--

DROP TABLE IF EXISTS `partida`;
CREATE TABLE IF NOT EXISTS `partida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `creado` datetime NOT NULL,
  `id_creador` int(11) NOT NULL,
  `fin` datetime NOT NULL,
  `max_jugadores` int(11) NOT NULL,
  `max_ofertas` int(11) NOT NULL,
  `tiempo_oferta` int(11) NOT NULL,
  `ratio` float NOT NULL,
  `alu_por_usuario` int(11) NOT NULL,
  `exp_y` int(11) NOT NULL,
  `exp_z` int(11) NOT NULL,
  `empezado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_creador` (`id_creador`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `username_canonical`, `full_name`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`) VALUES
(2, 'sergyzen', 'sergyzen', 'Sergio Martín Marquina', 'sergy_zen@hotmail.com', 'sergy_zen@hotmail.com', 1, 'hgog7ax1j8088ggo8ssws4k48socks8', '$2y$13$hgog7ax1j8088ggo8sswsu9xY68yVh7vsxUvDSRnvfTBKY0uHLPFe', '2015-12-13 20:20:47', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}', 0, NULL),
(7, 'userTest', 'usertest', 'prueba', 'petet2e@petete.com', 'petet2e@petete.com', 1, 'so4fiwqr1dcos084wwks444g04ws0gk', '$2y$13$so4fiwqr1dcos084wwks4u6Ua.is9K0OC3bwVolwodVu435UoOVU2', '2015-12-13 15:35:31', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL),
(8, 'petete', 'petete', 'Sergio Martín Marquina', 'smm0063@alu.ubu.es', 'smm0063@alu.ubu.es', 1, '5e6as8om72sc0044k4wgwc80cwog00c', '$2y$13$5e6as8om72sc0044k4wgwORQ0LzI4yiwGyrv5HlQwZR9LI4mkBWVS', '2015-12-13 15:35:51', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `userpartida`
--

DROP TABLE IF EXISTS `userpartida`;
CREATE TABLE IF NOT EXISTS `userpartida` (
  `id_partida` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `alu_roja_inicial` int(11) NOT NULL DEFAULT '0',
  `alu_blanca_inicial` int(11) NOT NULL DEFAULT '0',
  `alu_roja_actual` int(11) NOT NULL DEFAULT '0',
  `alu_blanca_actual` int(11) NOT NULL DEFAULT '0',
  `f_utilidad` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_partida`,`id_user`),
  KEY `id_partida` (`id_partida`),
  KEY `id_jugador` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`idCreador`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `ofertas_ibfk_2` FOREIGN KEY (`idDestinatario`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `ofertas_ibfk_3` FOREIGN KEY (`idPartida`) REFERENCES `partida` (`id`);

--
-- Filtros para la tabla `partida`
--
ALTER TABLE `partida`
  ADD CONSTRAINT `partida_ibfk_1` FOREIGN KEY (`id_creador`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `userpartida`
--
ALTER TABLE `userpartida`
  ADD CONSTRAINT `userpartida_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `userpartida_ibfk_2` FOREIGN KEY (`id_partida`) REFERENCES `partida` (`id`);
--
-- Base de datos: `misbeanstest`
--
CREATE DATABASE IF NOT EXISTS `misbeanstest` DEFAULT CHARACTER SET latin1 COLLATE latin1_spanish_ci;
USE `misbeanstest`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

DROP TABLE IF EXISTS `log`;
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) NOT NULL,
  `actionId` tinyint(4) NOT NULL,
  `actionData` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=77 ;

--
-- Volcado de datos para la tabla `log`
--

INSERT INTO `log` (`id`, `fecha`, `user`, `actionId`, `actionData`) VALUES
(6, '2015-10-31 02:34:26', 2, 2, 8),
(7, '2015-10-31 02:34:55', 8, 2, 2),
(8, '2015-10-31 02:35:24', 8, 4, 2),
(9, '2015-10-31 02:35:58', 2, 4, 8),
(10, '2015-10-31 03:50:52', 7, 1, 7),
(11, '2015-10-31 03:55:14', 7, 2, 2),
(12, '2015-10-31 18:52:37', 2, 2, 8),
(13, '2015-11-03 13:15:45', 2, 2, 8),
(14, '2015-11-17 14:45:52', 2, 2, 7),
(15, '2015-11-19 14:05:24', 2, 5, NULL),
(16, '2015-11-19 14:17:07', 2, 5, NULL),
(17, '2015-11-19 16:56:23', 2, 1, 9),
(18, '2015-11-19 17:03:17', 2, 1, 9),
(19, '2015-11-19 17:05:02', 2, 1, 9),
(20, '2015-11-19 17:06:40', 2, 1, 9),
(21, '2015-11-19 17:09:36', 2, 1, 9),
(22, '2015-11-19 17:33:32', 2, 1, 9),
(23, '2015-11-19 20:21:45', 2, 2, 7),
(24, '2015-11-20 15:08:43', 2, 1, 9),
(25, '2015-11-23 21:49:15', 2, 2, 7),
(26, '2015-11-24 15:16:12', 2, 2, 8),
(27, '2015-11-24 16:28:41', 2, 2, 7),
(28, '2015-11-25 11:03:09', 8, 2, 2),
(29, '2015-11-25 11:17:18', 2, 4, 8),
(30, '2015-11-25 11:18:38', 8, 2, 2),
(31, '2015-11-25 11:20:05', 2, 4, 8),
(32, '2015-11-25 11:20:50', 2, 2, 8),
(33, '2015-11-25 11:22:02', 8, 3, 2),
(34, '2015-11-25 12:04:30', 8, 2, 2),
(35, '2015-11-25 12:11:06', 2, 3, 8),
(36, '2015-11-25 12:35:23', 8, 2, 2),
(37, '2015-11-25 12:35:38', 2, 3, 8),
(38, '2015-11-25 12:41:01', 2, 2, 8),
(39, '2015-11-25 12:41:18', 8, 3, 2),
(40, '2015-11-25 12:49:36', 2, 2, 8),
(41, '2015-11-25 12:50:11', 8, 3, 2),
(42, '2015-11-25 13:13:27', 8, 2, 2),
(43, '2015-11-25 13:13:43', 8, 2, 2),
(44, '2015-11-25 13:14:17', 2, 2, 8),
(45, '2015-11-25 13:14:31', 8, 3, 2),
(46, '2015-11-25 13:14:38', 2, 3, 8),
(47, '2015-11-25 13:14:49', 2, 3, 8),
(48, '2015-12-03 13:09:19', 2, 2, 7),
(49, '2015-12-13 15:41:17', 8, 2, 7),
(50, '2015-12-13 15:45:13', 8, 2, 7),
(51, '2015-12-13 15:45:38', 7, 4, 8),
(55, '2015-12-18 17:23:49', 7, 1, 9),
(56, '2015-12-18 18:44:54', 7, 1, 7),
(57, '2015-12-18 18:55:44', 7, 1, 9),
(58, '2015-12-19 14:24:16', 8, 2, 7),
(59, '2015-12-19 14:30:30', 7, 3, 8),
(60, '2015-12-19 21:14:14', 8, 2, 7),
(61, '2015-12-19 21:14:56', 7, 4, 8),
(62, '2015-12-19 21:15:20', 7, 2, 8),
(63, '2015-12-19 21:16:37', 8, 3, 7),
(64, '2015-12-20 20:24:36', 7, 2, 8),
(65, '2015-12-21 14:58:05', 7, 1, 1),
(66, '2015-12-21 15:03:56', 8, 1, 1),
(67, '2015-12-21 15:20:02', 7, 2, 8),
(68, '2015-12-21 15:20:56', 8, 4, 7),
(69, '2015-12-21 15:21:44', 8, 2, 7),
(70, '2015-12-21 15:23:17', 7, 2, 8),
(71, '2015-12-21 15:24:21', 8, 3, 7),
(72, '2015-12-21 15:24:47', 7, 3, 8),
(73, '2015-12-21 15:28:47', 7, 2, 8),
(74, '2015-12-21 15:29:06', 8, 3, 7),
(76, '2015-12-22 15:22:37', 8, 1, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas`
--

DROP TABLE IF EXISTS `ofertas`;
CREATE TABLE IF NOT EXISTS `ofertas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPartida` int(11) NOT NULL,
  `idCreador` int(11) NOT NULL,
  `idDestinatario` int(11) NOT NULL,
  `creado` datetime NOT NULL,
  `modificado` datetime DEFAULT NULL,
  `estado` tinyint(4) NOT NULL,
  `aluBlancaIn` int(11) NOT NULL,
  `aluRojaIn` int(11) NOT NULL,
  `aluBlancaOut` int(11) NOT NULL,
  `aluRojaOut` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idCreador` (`idCreador`),
  KEY `idDestinatario` (`idDestinatario`),
  KEY `idPartida` (`idPartida`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=5 ;

--
-- Volcado de datos para la tabla `ofertas`
--

INSERT INTO `ofertas` (`id`, `idPartida`, `idCreador`, `idDestinatario`, `creado`, `modificado`, `estado`, `aluBlancaIn`, `aluRojaIn`, `aluBlancaOut`, `aluRojaOut`) VALUES
(1, 1, 7, 8, '2015-12-21 15:20:02', '2015-12-21 15:20:56', 2, 0, 2, 4, 0),
(2, 1, 8, 7, '2015-12-21 15:21:44', '2015-12-21 15:24:47', 1, 0, 3, 4, 0),
(3, 1, 7, 8, '2015-12-21 15:23:17', '2015-12-21 15:24:21', 1, 0, 2, 4, 0),
(4, 1, 7, 8, '2015-12-21 15:28:47', '2015-12-21 15:29:06', 1, 0, 3, 5, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `partida`
--

DROP TABLE IF EXISTS `partida`;
CREATE TABLE IF NOT EXISTS `partida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE latin1_spanish_ci NOT NULL,
  `password` varchar(255) COLLATE latin1_spanish_ci DEFAULT NULL,
  `creado` datetime NOT NULL,
  `id_creador` int(11) NOT NULL,
  `fin` datetime NOT NULL,
  `max_jugadores` int(11) NOT NULL,
  `max_ofertas` int(11) NOT NULL,
  `tiempo_oferta` int(11) NOT NULL,
  `ratio` float NOT NULL,
  `alu_por_usuario` int(11) NOT NULL,
  `exp_y` int(11) NOT NULL,
  `exp_z` int(11) NOT NULL,
  `empezado` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_creador` (`id_creador`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci AUTO_INCREMENT=10 ;

--
-- Volcado de datos para la tabla `partida`
--

INSERT INTO `partida` (`id`, `nombre`, `password`, `creado`, `id_creador`, `fin`, `max_jugadores`, `max_ofertas`, `tiempo_oferta`, `ratio`, `alu_por_usuario`, `exp_y`, `exp_z`, `empezado`) VALUES
(1, 'prueba1', NULL, '2015-10-08 00:00:00', 2, '2016-02-17 18:58:42', 25, 2, 60, 0.4, 30, 1, 2, 1),
(7, 'ejemplo 1', 'asdf', '2015-10-19 13:10:56', 7, '2016-01-13 13:20:00', 100, 0, 10, 100, 0, 0, 0, 0),
(9, 'ej1a', NULL, '2015-11-19 14:17:00', 2, '2015-12-15 14:10:00', 17, 2, 1, 0.1, 10, 2, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_957A647992FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_957A6479A0D96FBF` (`email_canonical`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `username_canonical`, `full_name`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`) VALUES
(2, 'sergyzen', 'sergyzen', 'Sergio Martín Marquina', 'sergy_zen@hotmail.com', 'sergy_zen@hotmail.com', 1, 'hgog7ax1j8088ggo8ssws4k48socks8', '$2y$13$hgog7ax1j8088ggo8sswsu9xY68yVh7vsxUvDSRnvfTBKY0uHLPFe', '2015-12-22 14:45:47', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}', 0, NULL),
(7, 'userTest', 'usertest', 'prueba', 'petet2e@petete.com', 'petet2e@petete.com', 1, 'so4fiwqr1dcos084wwks444g04ws0gk', '$2y$13$so4fiwqr1dcos084wwks4u6Ua.is9K0OC3bwVolwodVu435UoOVU2', '2015-12-22 14:01:15', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL),
(8, 'petete', 'petete', 'Sergio Martín Marquina', 'smm0063@alu.ubu.es', 'smm0063@alu.ubu.es', 1, '5e6as8om72sc0044k4wgwc80cwog00c', '$2y$13$5e6as8om72sc0044k4wgwORQ0LzI4yiwGyrv5HlQwZR9LI4mkBWVS', '2015-12-22 13:54:59', 0, 0, NULL, NULL, NULL, 'a:0:{}', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `userpartida`
--

DROP TABLE IF EXISTS `userpartida`;
CREATE TABLE IF NOT EXISTS `userpartida` (
  `id_partida` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `alu_roja_inicial` int(11) NOT NULL DEFAULT '0',
  `alu_blanca_inicial` int(11) NOT NULL DEFAULT '0',
  `alu_roja_actual` int(11) NOT NULL DEFAULT '0',
  `alu_blanca_actual` int(11) NOT NULL DEFAULT '0',
  `f_utilidad` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_partida`,`id_user`),
  KEY `id_partida` (`id_partida`),
  KEY `id_jugador` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `userpartida`
--

INSERT INTO `userpartida` (`id_partida`, `id_user`, `alu_roja_inicial`, `alu_blanca_inicial`, `alu_roja_actual`, `alu_blanca_actual`, `f_utilidad`) VALUES
(1, 7, 10, 10, 10, 10, 100),
(1, 8, 13, 17, 11, 22, 2662),
(7, 2, 10, 15, 10, 15, 150),
(7, 8, 0, 0, 0, 0, 0),
(9, 2, 0, 0, 0, 0, 0);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD CONSTRAINT `ofertas_ibfk_1` FOREIGN KEY (`idCreador`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `ofertas_ibfk_2` FOREIGN KEY (`idDestinatario`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `ofertas_ibfk_3` FOREIGN KEY (`idPartida`) REFERENCES `partida` (`id`);

--
-- Filtros para la tabla `partida`
--
ALTER TABLE `partida`
  ADD CONSTRAINT `partida_ibfk_1` FOREIGN KEY (`id_creador`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `userpartida`
--
ALTER TABLE `userpartida`
  ADD CONSTRAINT `userpartida_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `userpartida_ibfk_2` FOREIGN KEY (`id_partida`) REFERENCES `partida` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
