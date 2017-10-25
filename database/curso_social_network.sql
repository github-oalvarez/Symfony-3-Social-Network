-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-10-2017 a las 17:22:34
-- Versión del servidor: 5.7.19
-- Versión de PHP: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `curso_social_network`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `following`
--

DROP TABLE IF EXISTS `following`;
CREATE TABLE IF NOT EXISTS `following` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` int(255) DEFAULT NULL,
  `followed` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_following_users` (`user`),
  KEY `fk_followed` (`followed`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `following`
--

INSERT INTO `following` (`id`, `user`, `followed`) VALUES
(10, 1, 2),
(11, 1, 3),
(12, 3, 1),
(14, 21, 1),
(15, 1, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user` int(255) DEFAULT NULL,
  `publication` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_likes_users` (`user`),
  KEY `fk_likes_publication` (`publication`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `likes`
--

INSERT INTO `likes` (`id`, `user`, `publication`) VALUES
(2, 1, 10),
(6, 1, 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `type_id` int(255) DEFAULT NULL,
  `readed` varchar(3) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `extra` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notifications_users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `type_id`, `readed`, `created_at`, `extra`) VALUES
(1, 1, 'follow', 2, '1', '2017-10-13 10:01:52', NULL),
(2, 1, 'follow', 3, '1', '2017-10-13 10:01:54', NULL),
(3, 21, 'follow', 1, '0', '2017-10-15 16:21:23', NULL),
(4, 21, 'follow', 1, '0', '2017-10-15 16:27:26', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `private_messages`
--

DROP TABLE IF EXISTS `private_messages`;
CREATE TABLE IF NOT EXISTS `private_messages` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `message` longtext,
  `emitter` int(255) DEFAULT NULL,
  `receiver` int(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `readed` varchar(3) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_emmiter_privates` (`emitter`),
  KEY `fk_receiver_privates` (`receiver`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `private_messages`
--

INSERT INTO `private_messages` (`id`, `message`, `emitter`, `receiver`, `file`, `image`, `readed`, `created_at`) VALUES
(1, 'Hola Ángel', 2, 1, NULL, NULL, '1', '2017-10-15 11:44:52'),
(2, 'Hola Creativos', 21, 1, NULL, NULL, '1', '2017-10-15 11:50:34'),
(3, 'Hola Ángel', 2, 1, NULL, NULL, '1', '2017-10-15 11:44:52'),
(4, 'Te envío esto', 3, 1, NULL, '11508068746.jpeg', '1', '2017-10-15 11:59:06'),
(5, 'Hola', 21, 1, NULL, NULL, '1', '2017-10-15 12:07:57'),
(6, '1', 21, 1, NULL, NULL, '1', '2017-10-15 12:09:26'),
(7, '1', 1, 21, NULL, NULL, '0', '2017-10-15 15:59:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `publications`
--

DROP TABLE IF EXISTS `publications`;
CREATE TABLE IF NOT EXISTS `publications` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `user_id` int(255) DEFAULT NULL,
  `text` mediumtext,
  `document` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_publications_users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `publications`
--

INSERT INTO `publications` (`id`, `user_id`, `text`, `document`, `image`, `status`, `created_at`) VALUES
(1, 1, 'Prueba', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(2, 1, 'Prueba 2 Usuario 1', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(3, 2, 'Prueba 1 del usuario 2', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(4, 2, 'Prueba 1 usuario 2', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(5, 3, 'Prueba 1 del usuario 3', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(6, 3, 'Prueba2 del usuario 3', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(7, 3, 'Prueba3 del usuario 3', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(8, 3, 'Prueba 4 del usuario 3', NULL, NULL, NULL, '2017-10-08 19:40:21'),
(9, 1, 'Creando nueva publicación', NULL, NULL, NULL, '2017-10-09 20:36:27'),
(10, 1, 'Hola mundo 1', NULL, NULL, NULL, '2017-10-09 20:45:54'),
(11, 1, 'Hola mundo 2', NULL, NULL, NULL, '2017-10-09 20:46:08'),
(12, 1, 'Prueba', NULL, '11507664693.jpeg', NULL, '2017-10-10 19:44:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `nick` varchar(50) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `active` varchar(2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_uniques_fields` (`email`,`nick`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `role`, `email`, `name`, `surname`, `password`, `nick`, `bio`, `active`, `image`) VALUES
(1, 'ROLE_USER', 'hector.franco.aceituno@gmail.com', 'Héctor', 'Franco Aceituno', '$2y$04$qB0OkAsN2lRyORttdaqr3OF5GTPx/dyYUi4sUUw0u.dAhdBNBHh7a', 'Admin_', 'Developer', NULL, '11507473454.jpeg'),
(2, 'ROLE_USER', 'info@prodigia.com', 'Ángel', 'Osuna Luque', '$2y$04$mjjD1U304E4JKJWgUYnpeONnLPZt3Dfo7fXHe3r.j/TSI6BIOfPTe', 'CEO', NULL, NULL, NULL),
(3, 'ROLE_USER', 'podologiapriego@gmail.com', 'Sonia', 'Aguilera Montes', '$2y$04$k1VgGW2RTLpQrJV9qODWFO3cXle.g3tGNWWCxZ2/y/2l/qUT0GohC', 'Sonia', NULL, NULL, NULL),
(21, 'ROLE_USER', 'creativos5@prodigia.com', 'Creativos', 'Creativos', '$2y$04$WATKPSvTFsGcoSxqC28NXuxe5bFOBGMeIlp/ugulUY4ZNgOT9sbYC', 'Creativos5', NULL, NULL, NULL),
(22, 'ROLE_USER', 'administracion@prodigia.com', 'Maribel', 'Rico', '$2y$04$1EJTYmVe1QobJo4bEyU8du1DmnQcD8ejRysW4NyaisoysAHIR8Yxu', 'Administración', NULL, NULL, NULL),
(23, 'ROLE_USER', 'info@prodigia.com', 'info', 'info', '$2y$04$XiITvSIPrZ7aDLft5tmO4OttABcXMXyrmkuRKL1LmeC6SMbcT6yDa', 'info', NULL, NULL, NULL),
(24, 'ROLE_USER', 'hector.franco.aceituno@gmail.com', 'Hector', 'Franco Aceituno', '$2y$04$KUuw7LtFhz5QBJt4Iq9X5OOTmCgl5CC4IyT3UL9eos5xh8H2mbT4a', 'Admin', NULL, NULL, NULL);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `following`
--
ALTER TABLE `following`
  ADD CONSTRAINT `fk_followed` FOREIGN KEY (`followed`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_following_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `fk_likes_publication` FOREIGN KEY (`publication`) REFERENCES `publications` (`id`),
  ADD CONSTRAINT `fk_likes_users` FOREIGN KEY (`user`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `private_messages`
--
ALTER TABLE `private_messages`
  ADD CONSTRAINT `fk_emmiter_privates` FOREIGN KEY (`emitter`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_receiver_privates` FOREIGN KEY (`receiver`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `publications`
--
ALTER TABLE `publications`
  ADD CONSTRAINT `fk_publications_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
