/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/

-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-03-2015 a las 10:17:22
-- Versión del servidor: 5.6.17
-- Versión de PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `am`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_a`
--

CREATE TABLE IF NOT EXISTS `model_a` (
  `c_tinyint` tinyint(4) NOT NULL,
  `c_smallint` smallint(6) unsigned zerofill NOT NULL DEFAULT '000001',
  `c_mediumint` mediumint(9) DEFAULT NULL,
  `c_int` int(11) NOT NULL,
  `c_bigint` bigint(20) NOT NULL,
  `c_decimal` decimal(10,0) NOT NULL,
  `c_float` float NOT NULL,
  `c_double` double NOT NULL,
  `c_bit` bit(8) NOT NULL,
  `c_date` date NOT NULL,
  `c_datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `c_timestamp` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `c_time` time NOT NULL,
  `c_year` year(4) NOT NULL,
  `c_char` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `c_varchar` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '<none>',
  `c_tinytext` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `c_text` text COLLATE utf8_unicode_ci NOT NULL,
  `c_mediumtext` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `c_longtext` longtext COLLATE utf8_unicode_ci NOT NULL,
  `c_unique` int(11) NOT NULL,
  `c_unique_double1` int(11) NOT NULL,
  `c_unique_double2` int(11) NOT NULL,
  `fk_model_b` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`c_tinyint`),
  UNIQUE KEY `c_unique` (`c_unique`),
  UNIQUE KEY `c_unique_double1` (`c_unique_double1`,`c_unique_double2`),
  UNIQUE KEY `c_tinyint` (`c_tinyint`),
  KEY `fk_model_b` (`fk_model_b`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_b`
--

CREATE TABLE IF NOT EXISTS `model_b` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_c`
--

CREATE TABLE IF NOT EXISTS `model_c` (
  `pk_1` int(11) NOT NULL,
  `pk_2` int(11) NOT NULL,
  `description` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pk_1`,`pk_2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `model_a`
--
ALTER TABLE `model_a`
  ADD CONSTRAINT `model_as.model_b` FOREIGN KEY (`fk_model_b`) REFERENCES `model_b` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
