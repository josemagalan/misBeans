-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-12-2015 a las 13:16:03
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;