-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 24-12-2014 a las 11:09:29
-- Versión del servidor: 4.1.22
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `stkwebagesic`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE IF NOT EXISTS `departamentos` (
  `DepNombre` varchar(100) character set utf8 NOT NULL default '',
  `departamentosId` int(11) NOT NULL auto_increment,
  `departamentosIddep` int(11) default NULL,
  `ccentro` int(11) default '0',
  `DepHerederos` varchar(50) NOT NULL default '0',
  `DepTipoArea` tinyint(1) NOT NULL default '0',
  `DepNoVigente` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`departamentosId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=112 ;

--
-- Volcar la base de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`DepNombre`, `departamentosId`, `departamentosIddep`, `ccentro`, `DepHerederos`, `DepTipoArea`, `DepNoVigente`) VALUES
('Coordinacion General', 5, 84, 0, '003600840005', 1, 0),
('Gerencia de Planificacion y Gestion Financiero Contable', 7, 5, 0, '0036008400050007', 1, 0),
('Gerencia de Gestion y Desarrollo Humano', 8, 5, 0, '0036008400050008', 0, 0),
('Administracion y Finanzas', 9, 7, 0, '00360084000500070009', 1, 0),
('Comunicacion e Imagen Institucional', 10, 5, 0, '0036008400050010', 0, 0),
('Administracion Documental', 72, 7, 0, '00360084000500070072', 0, 0),
('Adm.Finanzas - Adquisiciones', 73, 9, 0, '003600840005000700090073', 0, 0),
('Adm.Finanzas - Biblioteca', 74, 9, 0, '003600840005000700090074', 0, 0),
('Adm.Finanzas - Contaduria', 75, 9, 0, '003600840005000700090075', 0, 0),
('Adm.Finanzas - Intendencia', 76, 9, 0, '003600840005000700090076', 0, 0),
('Adm.Finanzas - Proveeduria', 77, 9, 0, '003600840005000700090077', 0, 0),
('Adm.Finanzas - Tesoreria', 78, 9, 0, '003600840005000700090078', 0, 0),
('Subdierccion', 84, 36, 0, '00360084', 1, 0),
('Direccion', 36, 36, 0, '0036', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ivas`
--

CREATE TABLE IF NOT EXISTS `ivas` (
  `IVA` varchar(15) NOT NULL default '',
  `IVAVal` decimal(6,2) NOT NULL default '0.00',
  PRIMARY KEY  (`IVA`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `ivas`
--

INSERT INTO `ivas` (`IVA`, `IVAVal`) VALUES
('Exento', 1.00),
('Minimo', 1.10),
('Basico', 1.22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sisperfiles`
--

CREATE TABLE IF NOT EXISTS `sisperfiles` (
  `SisId` int(4) NOT NULL default '0',
  `SisPflId` int(4) NOT NULL auto_increment,
  `SisPflDsc` varchar(50) NOT NULL default '',
  `SisPflUniAll` char(1) NOT NULL default '' COMMENT 'habilita a los usuarios del perfil a consultar solo su unidad o todas',
  `SisPflEstIni` varchar(20) NOT NULL default '' COMMENT 'cuando el usuario accede a stock, inicia en una bandeja de solicitudes según este estado (estado por defecto).',
  `SisPflVig` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`SisId`,`SisPflId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `sisperfiles`
--

INSERT INTO `sisperfiles` (`SisId`, `SisPflId`, `SisPflDsc`, `SisPflUniAll`, `SisPflEstIni`, `SisPflVig`) VALUES
(1, 1, 'Solicitante', 'N', 'Construyendo', 1),
(1, 2, 'Operador', 'S', 'Pendiente de Entrega', 1),
(1, 3, 'Administrador Stock', 'S', 'Pendiente de Entrega', 1),
(1, 6, 'Consultor - Financiero', 'S', 'Pendiente de Entrega', 1),
(1, 5, 'Autorizador', 'N', 'Autorizar', 1),
(1, 7, 'Consultor - Unidad', 'N', 'Pendiente de Entrega', 1),
(1, 8, 'Articulador', 'N', '', 0),
(1, 9, 'Proveedores', 'N', '', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sispflaction`
--

CREATE TABLE IF NOT EXISTS `sispflaction` (
  `SisPflId` int(4) NOT NULL default '0',
  `SisAction` varchar(20) NOT NULL default '',
  `SisObjeto` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`SisPflId`,`SisAction`,`SisObjeto`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `sispflaction`
--

INSERT INTO `sispflaction` (`SisPflId`, `SisAction`, `SisObjeto`) VALUES
(1, 'Alta', 'Solicitud'),
(2, 'Vista', 'Articulos'),
(3, 'Mantenimiento', 'Articulos'),
(3, 'Mantenimiento', 'Proveedores'),
(5, 'Alta', 'Solicitud'),
(6, 'Vista', 'Articulos'),
(8, 'Mantenimiento', 'Articulos'),
(9, 'Mantenimiento', 'Proveedores'),
(9, 'Vista', 'Articulos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sispflusuarios`
--

CREATE TABLE IF NOT EXISTS `sispflusuarios` (
  `SisId` int(4) NOT NULL default '1',
  `SisPflId` int(4) NOT NULL default '0',
  `UsuId` int(11) NOT NULL default '0',
  `SisClsPri` int(11) NOT NULL default '0',
  `SisPflUsuUsuCre` int(11) NOT NULL default '0',
  `SisPflUsuFchCre` date NOT NULL default '0000-00-00',
  `SisPflUsuUsuMod` int(11) default NULL,
  `SisPflUsuFchMod` date default NULL,
  PRIMARY KEY  (`SisId`,`SisPflId`,`UsuId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `sispflusuarios`
--

INSERT INTO `sispflusuarios` (`SisId`, `SisPflId`, `UsuId`, `SisClsPri`, `SisPflUsuUsuCre`, `SisPflUsuFchCre`, `SisPflUsuUsuMod`, `SisPflUsuFchMod`) VALUES
(1, 11, 1, 0, 1, '2010-04-20', 152, '2012-08-01'),
(1, 1, 109, 0, 85, '2011-07-14', NULL, NULL),
(1, 1, 116, 0, 17, '2011-07-29', 80, '2013-09-05'),
(1, 2, 4, 0, 1, '2010-09-27', NULL, NULL),
(1, 3, 5, 0, 1, '2010-10-21', 80, '2012-12-10'),
(1, 5, 6, 0, 1, '2010-12-13', NULL, NULL),
(1, 1, 7, 0, 1, '2010-12-13', 80, '2011-06-30'),
(1, 1, 10, 0, 1, '2010-12-13', 80, '2013-04-02'),
(1, 1, 11, 0, 1, '2010-12-13', 17, '2011-07-04'),
(1, 1, 111, 0, 71, '2011-07-21', 80, '2011-08-02'),
(1, 5, 8, 0, 1, '2010-12-13', 80, '2013-04-02'),
(1, 5, 9, 0, 1, '2010-12-13', 17, '2011-07-04'),
(1, 5, 13, 0, 1, '2010-12-13', 80, '2013-04-23'),
(1, 1, 14, 0, 1, '2010-12-13', 80, '2013-04-23'),
(1, 1, 106, 0, 17, '2011-07-12', 80, '2012-07-30'),
(1, 3, 17, 0, 1, '2010-12-17', 80, '2011-08-02'),
(1, 1, 18, 0, 1, '2010-12-22', 71, '2011-07-11'),
(1, 1, 19, 0, 3, '2010-12-24', NULL, NULL),
(1, 1, 20, 0, 3, '2010-12-24', 77, '2011-12-28'),
(1, 5, 21, 0, 3, '2010-12-24', NULL, NULL),
(1, 1, 22, 0, 3, '2010-12-24', NULL, NULL),
(1, 1, 23, 0, 3, '2010-12-24', NULL, NULL),
(1, 5, 24, 0, 3, '2010-12-24', 3, '2010-12-28'),
(1, 1, 25, 0, 3, '2010-12-24', 77, '2013-07-23'),
(1, 5, 26, 0, 3, '2010-12-28', 17, '2011-07-12'),
(1, 1, 27, 0, 3, '2010-12-28', 77, '2013-07-23'),
(1, 1, 28, 0, 3, '2010-12-28', 80, '2012-12-13'),
(1, 1, 29, 0, 3, '2010-12-28', NULL, NULL),
(1, 1, 30, 0, 3, '2010-12-28', NULL, NULL),
(1, 1, 31, 0, 3, '2010-12-28', NULL, NULL),
(1, 1, 32, 0, 3, '2010-12-28', 80, '2012-07-26'),
(1, 1, 33, 0, 3, '2010-12-28', 3, '2010-12-28'),
(1, 1, 34, 0, 3, '2010-12-28', 80, '2012-10-09'),
(1, 5, 35, 0, 3, '2010-12-28', NULL, NULL),
(1, 5, 36, 0, 3, '2010-12-28', 80, '2012-08-23'),
(1, 1, 37, 0, 3, '2010-12-28', NULL, NULL),
(1, 1, 38, 0, 3, '2010-12-28', 71, '2011-07-11'),
(1, 1, 39, 0, 3, '2010-12-28', NULL, NULL),
(1, 1, 40, 0, 3, '2010-12-28', NULL, NULL),
(1, 5, 41, 0, 3, '2010-12-28', 3, '2010-12-28'),
(1, 1, 105, 0, 17, '2011-07-12', 80, '2011-08-22'),
(1, 1, 104, 0, 102, '2011-07-11', NULL, NULL),
(1, 1, 44, 0, 3, '2010-12-28', 17, '2011-07-04'),
(1, 1, 45, 0, 3, '2010-12-28', 80, '2012-02-08'),
(1, 1, 46, 0, 3, '2010-12-28', 80, '2011-07-19'),
(1, 5, 47, 0, 3, '2010-12-28', 17, '2011-08-03'),
(1, 1, 48, 0, 3, '2010-12-28', 71, '2011-07-11'),
(1, 1, 49, 0, 3, '2010-12-28', 80, '2013-04-23'),
(1, 5, 50, 0, 3, '2010-12-28', NULL, NULL),
(1, 1, 51, 0, 3, '2010-12-28', NULL, NULL),
(1, 5, 52, 0, 3, '2010-12-28', 3, '2010-12-28'),
(1, 5, 53, 0, 3, '2010-12-28', NULL, NULL),
(1, 5, 54, 0, 3, '2010-12-28', 80, '2011-10-03'),
(1, 1, 103, 0, 102, '2011-07-11', NULL, NULL),
(1, 5, 102, 0, 71, '2011-07-11', 80, '2012-12-10'),
(1, 1, 119, 0, 80, '2011-08-03', NULL, NULL),
(1, 1, 58, 0, 3, '2011-02-02', 80, '2012-12-27'),
(1, 6, 59, 0, 3, '2011-02-02', 77, '2013-07-23'),
(1, 5, 60, 0, 3, '2011-02-02', 80, '2011-07-14'),
(1, 1, 61, 0, 3, '2011-02-02', NULL, NULL),
(1, 5, 62, 0, 3, '2011-02-02', NULL, NULL),
(1, 1, 63, 0, 3, '2011-02-02', 77, '2011-11-03'),
(1, 1, 64, 0, 3, '2011-02-02', NULL, NULL),
(1, 5, 65, 0, 3, '2011-02-02', NULL, NULL),
(1, 1, 66, 0, 3, '2011-02-02', NULL, NULL),
(1, 5, 120, 0, 71, '2011-08-04', NULL, NULL),
(1, 5, 68, 0, 3, '2011-02-02', NULL, NULL),
(1, 1, 69, 0, 3, '2011-02-02', NULL, NULL),
(1, 1, 70, 0, 3, '2011-02-02', NULL, NULL),
(1, 3, 71, 0, 3, '2011-02-02', 80, '2011-08-02'),
(1, 1, 72, 0, 3, '2011-02-03', 80, '2013-09-11'),
(1, 1, 73, 0, 3, '2011-02-03', NULL, NULL),
(1, 5, 74, 0, 3, '2011-02-03', 80, '2013-04-23'),
(1, 1, 117, 0, 17, '2011-08-01', 80, '2012-06-22'),
(1, 1, 76, 0, 3, '2011-02-08', 80, '2013-04-23'),
(1, 3, 77, 0, 3, '2011-03-18', 80, '2011-10-13'),
(1, 2, 78, 0, 3, '2011-04-29', 80, '2012-08-24'),
(1, 5, 79, 0, 3, '2011-04-29', 80, '2012-11-01'),
(1, 3, 80, 0, 3, '2011-04-29', 80, '2013-09-05'),
(1, 5, 81, 0, 80, '2011-06-29', NULL, NULL),
(1, 1, 82, 0, 80, '2011-06-30', 80, '2013-04-23'),
(1, 1, 101, 0, 71, '2011-07-08', NULL, NULL),
(1, 1, 84, 0, 17, '2011-07-04', 80, '2012-12-10'),
(1, 7, 85, 0, 80, '2011-07-05', 80, '2011-12-14'),
(1, 6, 86, 0, 71, '2011-07-06', 80, '2011-08-02'),
(1, 5, 87, 0, 71, '2011-07-06', 80, '2011-08-02'),
(1, 5, 110, 0, 71, '2011-07-15', NULL, NULL),
(1, 1, 89, 0, 71, '2011-07-06', NULL, NULL),
(1, 5, 90, 0, 71, '2011-07-06', 80, '2012-07-23'),
(1, 1, 91, 0, 17, '2011-07-07', 80, '2012-02-02'),
(1, 1, 92, 0, 17, '2011-07-07', 80, '2011-08-02'),
(1, 1, 93, 0, 17, '2011-07-07', 80, '2011-08-02'),
(1, 5, 94, 0, 17, '2011-07-07', 80, '2011-08-02'),
(1, 1, 115, 0, 17, '2011-07-28', 77, '2013-07-23'),
(1, 1, 113, 0, 71, '2011-07-26', NULL, NULL),
(1, 1, 114, 0, 17, '2011-07-28', 80, '2011-08-02'),
(1, 5, 98, 0, 17, '2011-07-07', 80, '2012-08-24'),
(1, 1, 112, 0, 71, '2011-07-21', 80, '2011-08-04'),
(1, 5, 100, 0, 17, '2011-07-07', 17, '2011-07-12'),
(1, 10, 137, 0, 136, '2012-02-08', 152, '2012-12-04'),
(1, 5, 122, 0, 71, '2011-08-11', NULL, NULL),
(1, 1, 123, 0, 71, '2011-08-16', 77, '2012-01-09'),
(1, 3, 124, 0, 80, '2011-10-03', NULL, NULL),
(1, 3, 125, 0, 80, '2011-10-12', 80, '2012-12-10'),
(1, 1, 126, 0, 77, '2011-11-03', NULL, NULL),
(1, 12, 127, 0, 1, '2011-11-25', 152, '2012-12-27'),
(1, 5, 128, 0, 77, '2011-11-28', NULL, NULL),
(1, 1, 129, 0, 77, '2011-11-28', NULL, NULL),
(1, 1, 130, 0, 77, '2011-11-28', 80, '2013-04-02'),
(1, 5, 131, 0, 85, '2011-11-30', 85, '2011-11-30'),
(1, 1, 132, 0, 71, '2011-12-08', 80, '2012-12-28'),
(1, 5, 133, 0, 77, '2012-01-09', NULL, NULL),
(1, 1, 134, 0, 77, '2012-01-10', NULL, NULL),
(1, 5, 135, 0, 77, '2012-01-20', NULL, NULL),
(1, 10, 136, 3, 1, '2012-02-06', NULL, NULL),
(1, 11, 138, 0, 136, '2012-02-08', 152, '2012-12-12'),
(1, 10, 139, 0, 136, '2012-02-08', 152, '2012-06-25'),
(1, 10, 140, 0, 136, '2012-02-08', 152, '2012-08-23'),
(1, 1, 141, 0, 17, '2012-02-09', NULL, NULL),
(1, 10, 142, 0, 136, '2012-02-17', NULL, NULL),
(1, 10, 143, 0, 142, '2012-02-23', 152, '2012-07-11'),
(1, 1, 144, 0, 80, '2012-04-13', NULL, NULL),
(1, 5, 145, 0, 80, '2012-04-25', 80, '2012-04-25'),
(1, 10, 156, 0, 152, '2012-06-25', 152, '2012-08-23'),
(1, 10, 155, 0, 152, '2012-06-25', NULL, NULL),
(1, 10, 148, 0, 127, '2012-05-04', 152, '2012-08-23'),
(1, 1, 149, 0, 80, '2012-05-28', NULL, NULL),
(1, 1, 150, 0, 80, '2012-05-28', NULL, NULL),
(1, 5, 151, 0, 80, '2012-06-11', NULL, NULL),
(1, 11, 152, 0, 127, '2012-06-12', NULL, NULL),
(1, 5, 153, 0, 80, '2012-06-22', NULL, NULL),
(1, 1, 157, 0, 80, '2012-06-25', NULL, NULL),
(1, 11, 158, 0, 152, '2012-07-11', NULL, NULL),
(1, 1, 159, 0, 80, '2012-07-11', NULL, NULL),
(1, 11, 160, 0, 152, '2012-08-09', 152, '2012-12-12'),
(1, 1, 161, 0, 17, '2012-08-28', 80, '2012-09-05'),
(1, 5, 162, 0, 17, '2012-08-29', 80, '2012-09-06'),
(1, 5, 163, 0, 17, '2012-08-29', 80, '2012-09-06'),
(1, 1, 164, 0, 17, '2012-08-29', NULL, NULL),
(1, 1, 165, 0, 77, '2012-09-05', 77, '2013-07-23'),
(1, 1, 166, 0, 77, '2012-09-05', NULL, NULL),
(1, 5, 167, 0, 17, '2012-09-11', NULL, NULL),
(1, 5, 168, 0, 17, '2012-09-11', NULL, NULL),
(1, 1, 169, 0, 17, '2012-09-11', NULL, NULL),
(1, 1, 170, 0, 80, '2012-09-19', NULL, NULL),
(1, 6, 171, 0, 80, '2012-10-12', 80, '2012-10-24'),
(1, 3, 172, 0, 17, '2012-10-12', 80, '2013-07-03'),
(1, 6, 173, 0, 80, '2012-10-18', 80, '2012-10-18'),
(1, 6, 174, 0, 80, '2012-10-25', 80, '2012-10-25'),
(1, 5, 175, 0, 80, '2012-11-01', 80, '2012-11-06'),
(1, 1, 176, 0, 80, '2012-11-15', 80, '2013-09-05'),
(1, 1, 177, 0, 80, '2012-11-15', 80, '2013-09-05'),
(1, 3, 178, 0, 17, '2012-11-29', NULL, NULL),
(1, 6, 179, 0, 17, '2012-11-29', NULL, NULL),
(1, 6, 180, 0, 17, '2012-11-29', NULL, NULL),
(1, 3, 181, 0, 17, '2012-11-29', NULL, NULL),
(1, 5, 182, 0, 17, '2012-11-29', NULL, NULL),
(1, 5, 183, 0, 17, '2012-11-29', 80, '2012-12-10'),
(1, 3, 184, 0, 17, '2012-11-29', NULL, NULL),
(1, 6, 185, 0, 17, '2012-11-29', NULL, NULL),
(1, 11, 186, 0, 152, '2012-11-29', NULL, NULL),
(1, 11, 187, 0, 152, '2012-12-04', NULL, NULL),
(1, 11, 188, 0, 152, '2012-12-04', NULL, NULL),
(1, 1, 189, 0, 77, '2012-12-13', NULL, NULL),
(1, 1, 190, 0, 80, '2012-12-27', NULL, NULL),
(1, 1, 191, 0, 80, '2012-12-27', NULL, NULL),
(1, 1, 192, 0, 80, '2012-12-27', 80, '2013-04-23'),
(1, 1, 193, 0, 80, '2012-12-27', NULL, NULL),
(1, 1, 194, 0, 80, '2012-12-28', 80, '2013-09-05'),
(1, 5, 195, 0, 80, '2012-12-28', 80, '2013-09-05'),
(1, 5, 196, 0, 80, '2013-02-21', NULL, NULL),
(1, 1, 197, 0, 77, '2013-03-20', 80, '2013-09-05'),
(1, 5, 198, 0, 80, '2013-04-02', NULL, NULL),
(1, 1, 199, 0, 77, '2013-04-11', NULL, NULL),
(1, 5, 200, 0, 77, '2013-04-18', NULL, NULL),
(1, 6, 201, 0, 80, '2013-04-23', NULL, NULL),
(1, 5, 202, 0, 17, '2013-07-03', NULL, NULL),
(1, 2, 203, 0, 172, '2013-07-22', NULL, NULL),
(1, 1, 204, 0, 77, '2013-07-23', NULL, NULL),
(1, 3, 205, 0, 80, '2013-08-06', 80, '2013-08-06'),
(1, 1, 206, 0, 80, '2013-09-06', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sistemas`
--

CREATE TABLE IF NOT EXISTS `sistemas` (
  `SisId` int(4) NOT NULL auto_increment,
  `SisNom` varchar(50) NOT NULL default '',
  `SisDsc` varchar(150) NOT NULL default '',
  `SisStkEntParcial` tinyint(1) NOT NULL default '0' COMMENT 'Integra requerimiento de entrega parcial de material de stock',
  `SisUsuCre` int(11) NOT NULL default '0',
  `SisFchCre` date NOT NULL default '0000-00-00',
  `SisUsuMod` int(11) default NULL,
  `SisFchMod` date default NULL,
  PRIMARY KEY  (`SisId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `sistemas`
--

INSERT INTO `sistemas` (`SisId`, `SisNom`, `SisDsc`, `SisStkEntParcial`, `SisUsuCre`, `SisFchCre`, `SisUsuMod`, `SisFchMod`) VALUES
(1, 'Solicitud de Mercaderia y Stock', 'Stock de Material de Oficina, ambiente de Insumos', 0, 1, '2010-04-20', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkartcls`
--

CREATE TABLE IF NOT EXISTS `stkartcls` (
  `StkArtClsId` int(11) NOT NULL default '0',
  `StkArtClsDsc` varchar(100) NOT NULL default '',
  `StkArtClsComp` tinyint(1) NOT NULL default '0',
  `StkArtClsEspec` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`StkArtClsId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `stkartcls`
--

INSERT INTO `stkartcls` (`StkArtClsId`, `StkArtClsDsc`, `StkArtClsComp`, `StkArtClsEspec`) VALUES
(1, 'Insumos Informaticos', 0, 0),
(2, 'Otros Articulos', 0, 0),
(5, 'Insumos de Oficina', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkartclsusu`
--

CREATE TABLE IF NOT EXISTS `stkartclsusu` (
  `UsuId` int(11) NOT NULL default '0',
  `StkArtClsId` int(11) NOT NULL default '0',
  `StkArtClsUsuAll` tinyint(1) NOT NULL default '1',
  `StkArtClsHab` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`UsuId`,`StkArtClsId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `stkartclsusu`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkartdep`
--

CREATE TABLE IF NOT EXISTS `stkartdep` (
  `StkArtId` int(4) NOT NULL default '0',
  `DepartamentosId` int(11) NOT NULL default '0',
  `StkArtDepUsuCre` int(11) NOT NULL default '0',
  `StkArtDepFchCre` date NOT NULL default '0000-00-00',
  `StkArtDepUsuMod` int(11) default '0',
  `StkArtDepFchMod` date default NULL,
  PRIMARY KEY  (`StkArtId`,`DepartamentosId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `stkartdep`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkarticulos`
--

CREATE TABLE IF NOT EXISTS `stkarticulos` (
  `StkArtId` int(4) NOT NULL auto_increment,
  `StkArtCantReal` int(8) NOT NULL default '0',
  `StkArtCantFicto` int(8) NOT NULL default '0',
  `StkArtCantMinimo` int(8) NOT NULL default '0',
  `StkArtCostoBasico` decimal(12,2) NOT NULL default '0.00',
  `StkArtCostoPromedio` decimal(12,2) NOT NULL default '0.00',
  `StkArtFchFin` date default NULL,
  `StkCauBjaId` int(4) NOT NULL default '0',
  `StkArtClsId` int(11) NOT NULL default '0',
  `StkArtIVA` decimal(6,2) NOT NULL default '0.00',
  `StkArtUsuCre` int(11) default NULL,
  `StkArtFchCre` datetime default NULL,
  `StkArtUsuMod` int(11) default NULL,
  `StkArtFchMod` date default NULL,
  `StkArtDsc` varchar(150) NOT NULL default '',
  `StkArtCritico` int(4) default NULL,
  PRIMARY KEY  (`StkArtId`),
  UNIQUE KEY `StkArtDsc` (`StkArtDsc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `stkarticulos`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkcausal`
--

CREATE TABLE IF NOT EXISTS `stkcausal` (
  `StkCauId` int(4) NOT NULL auto_increment,
  `StkCauDsc` varchar(80) NOT NULL default '',
  `StkCauIn` tinyint(1) NOT NULL default '0',
  `StkCauOut` tinyint(1) NOT NULL default '0',
  `StkCauTpo` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`StkCauId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

--
-- Volcar la base de datos para la tabla `stkcausal`
--

INSERT INTO `stkcausal` (`StkCauId`, `StkCauDsc`, `StkCauIn`, `StkCauOut`, `StkCauTpo`) VALUES
(1, 'Se dejo de fabricar', 0, 1, 'Articulo'),
(2, 'Paso a ser distribuido por otra area', 0, 1, 'Articulo'),
(3, 'No se distribuye - Orden direccion', 0, 1, 'Articulo'),
(4, 'Articulo Caduco', 0, 1, 'Articulo'),
(5, 'Canje', 1, 1, 'Definitivo'),
(7, 'Rotura', 0, 1, 'Definitivo'),
(8, 'Correccion de saldo', 1, 1, 'Definitivo'),
(10, 'No se distribuye mas', 0, 1, 'Articulo'),
(13, 'Donacion', 1, 1, 'Definitivo'),
(17, 'Saldo Inicial', 1, 0, 'Definitivo'),
(32, 'Articulo repetido', 0, 1, 'Articulo'),
(33, 'Compra Directa', 1, 0, 'Definitivo'),
(35, 'Nuevos Ingresos', 1, 0, 'Definitivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkestados`
--

CREATE TABLE IF NOT EXISTS `stkestados` (
  `StkEstId` varchar(20) NOT NULL default '',
  `StkEstDsc` varchar(80) default NULL,
  `StkEstUsuCre` int(11) NOT NULL default '0',
  `StkEstFchCre` date NOT NULL default '0000-00-00',
  `StkEstUsuMod` int(11) default NULL,
  `StkEstFchMod` date default NULL,
  `StkEstObs` varchar(250) default NULL,
  PRIMARY KEY  (`StkEstId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `stkestados`
--

INSERT INTO `stkestados` (`StkEstId`, `StkEstDsc`, `StkEstUsuCre`, `StkEstFchCre`, `StkEstUsuMod`, `StkEstFchMod`, `StkEstObs`) VALUES
('Pendiente de Entrega', 'Solicitudes en condiciones de asignar el Material', 1, '2010-04-22', NULL, NULL, 'Esperando q'' Operador del Stock asigne el material'),
('Cancelada', 'Solicitudes Anuladas por Solicitante, Autorizador o Administrador', 1, '2010-04-22', NULL, NULL, 'No hay Stock, Desierta Solicitud, o Desierto Artículo, etc.'),
('Finalizada', 'Solicitud cerrada (es posible la existencia de articulos sin entrega)', 1, '2010-04-22', NULL, NULL, 'Material adjudicado, Finalizada Solicitud o Finalizado Artículo'),
('Construyendo', 'En proceso por Solicitante', 1, '2010-04-29', NULL, NULL, 'En construcción en poder del usuario solicitante o autorizador(actuando de solicitante)'),
('Imprimir Remito', 'Remitos Entrega de pedido parcial o total', 1, '2010-09-23', NULL, NULL, 'Se confirmo parcial o total el pedido, impresión de Remito para la firma y entrega'),
('Autorizar', 'Autorizar solicitud de pedido', 1, '2010-10-20', NULL, NULL, 'Estado intermedio entre la construccion del pedido y en condiciones de entregar pedido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkestperfiles`
--

CREATE TABLE IF NOT EXISTS `stkestperfiles` (
  `StkEstId` varchar(20) NOT NULL default '0',
  `SisPflId` int(4) NOT NULL default '0',
  PRIMARY KEY  (`StkEstId`,`SisPflId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `stkestperfiles`
--

INSERT INTO `stkestperfiles` (`StkEstId`, `SisPflId`) VALUES
('Autorizar', 1),
('Autorizar', 2),
('Autorizar', 3),
('Autorizar', 5),
('Autorizar', 6),
('Autorizar', 7),
('Autorizar', 8),
('Autorizar', 9),
('Cancelada', 1),
('Cancelada', 2),
('Cancelada', 3),
('Cancelada', 5),
('Cancelada', 6),
('Cancelada', 7),
('Cancelada', 8),
('Cancelada', 9),
('Construyendo', 1),
('Construyendo', 2),
('Construyendo', 3),
('Construyendo', 5),
('Construyendo', 6),
('Construyendo', 7),
('Construyendo', 8),
('Construyendo', 9),
('Finalizada', 1),
('Finalizada', 2),
('Finalizada', 3),
('Finalizada', 5),
('Finalizada', 6),
('Finalizada', 7),
('Finalizada', 8),
('Finalizada', 9),
('Imprimir Remito', 2),
('Imprimir Remito', 3),
('Pendiente de Entrega', 1),
('Pendiente de Entrega', 2),
('Pendiente de Entrega', 3),
('Pendiente de Entrega', 5),
('Pendiente de Entrega', 6),
('Pendiente de Entrega', 7),
('Pendiente de Entrega', 8),
('Pendiente de Entrega', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkmovarticulos`
--

CREATE TABLE IF NOT EXISTS `stkmovarticulos` (
  `StkMovArtId` int(11) NOT NULL auto_increment,
  `StkArtId` int(4) NOT NULL default '0',
  `StkMovArtFch` date NOT NULL default '0000-00-00',
  `StkMovArtTpo` char(1) NOT NULL default '',
  `StkMovArtCant` int(8) NOT NULL default '0',
  `StkSolId` int(11) default NULL,
  `StkPrvFacId` int(11) default NULL,
  `StkMovArtPorId` int(2) default NULL,
  `StkMovArtPrecio` decimal(17,2) default NULL,
  `StkMovArtPrecioCIva` decimal(17,2) default NULL,
  `StkMovArtUsuCre` int(11) NOT NULL default '0',
  `StkMovArtFchCre` datetime NOT NULL default '0000-00-00 00:00:00',
  `StkMovArtUsuMod` int(11) default NULL,
  `StkMovArtFchMod` datetime default NULL,
  `StkMovArtObs` varchar(100) default NULL,
  PRIMARY KEY  (`StkMovArtId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `stkmovarticulos`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkproveedores`
--

CREATE TABLE IF NOT EXISTS `stkproveedores` (
  `StkPrvId` int(4) NOT NULL auto_increment,
  `StkPrvRzoSoc` varchar(50) NOT NULL default '',
  `StkPrvRut` varchar(20) default NULL,
  `StkPrvDir` varchar(100) default NULL,
  `StkPrvTel` varchar(30) default NULL,
  `StkPrvFax` varchar(30) default NULL,
  `StkPrvMail` varchar(50) default NULL,
  `StkPrvService` tinyint(1) NOT NULL default '0',
  `StkPrvObs` varchar(100) default NULL,
  `StkPrvUsuCre` int(11) NOT NULL default '0',
  `StkPrvFchCre` date NOT NULL default '0000-00-00',
  `StkPrvUsuMod` int(11) default NULL,
  `StkPrvFchMod` date default NULL,
  PRIMARY KEY  (`StkPrvId`),
  UNIQUE KEY `StkPrvRzoSoc` (`StkPrvRzoSoc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `stkproveedores`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkprvfacturas`
--

CREATE TABLE IF NOT EXISTS `stkprvfacturas` (
  `StkPrvFacId` int(8) NOT NULL auto_increment,
  `StkPrvId` int(4) NOT NULL default '0',
  `StkPrvFacNum` varchar(20) NOT NULL default '',
  `StkPrvFacFch` date NOT NULL default '0000-00-00',
  `StkLicId` int(4) default NULL,
  `StkPrvFacFinalizada` tinyint(1) NOT NULL default '0',
  `StkPrvFacObs` varchar(255) default NULL,
  `StkPrvFacUsuCre` int(11) NOT NULL default '0',
  `StkPrvFacFchCre` date NOT NULL default '0000-00-00',
  `StkPrvFacUsuMod` int(11) default NULL,
  `StkPrvFacFchMod` date default NULL,
  PRIMARY KEY  (`StkPrvFacId`),
  UNIQUE KEY `PrvFacNum` (`StkPrvId`,`StkPrvFacNum`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `stkprvfacturas`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stksolarticulos`
--

CREATE TABLE IF NOT EXISTS `stksolarticulos` (
  `StkSolId` int(11) NOT NULL default '0',
  `StkArtId` int(4) NOT NULL default '0',
  `StkSolArtCantSol` int(8) NOT NULL default '0',
  `StkSolArtCantAcred` int(8) default NULL,
  `StkSolArtCantCanje` int(8) NOT NULL default '0',
  `StkSolArtCantPen` int(8) default NULL,
  `StkSolArtEspRep` char(1) default NULL COMMENT 'Espera reposición stock',
  `StkSolArtEstado` varchar(15) NOT NULL default '',
  `StkSolArtObs` varchar(100) default NULL,
  `StkSolArtUsuCre` int(11) NOT NULL default '0',
  `StkSolArtFchCre` date NOT NULL default '0000-00-00',
  `StkSolArtUsuMod` int(11) default NULL,
  `StkSolArtFchMod` date default NULL,
  PRIMARY KEY  (`StkSolId`,`StkArtId`,`StkSolArtEstado`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `stksolarticulos`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stksolicitudes`
--

CREATE TABLE IF NOT EXISTS `stksolicitudes` (
  `StkSolFchMod` date default NULL,
  `StkSolId` int(11) NOT NULL auto_increment,
  `StkSolUsuSol` int(11) NOT NULL default '0',
  `StkSolSecId` int(11) NOT NULL default '0',
  `StkSolFchSol` date NOT NULL default '0000-00-00',
  `StkSolFchFin` date default NULL,
  `StkSolEstado` varchar(20) NOT NULL default '',
  `StkSolBien` varchar(10) NOT NULL default 'Consumo',
  `StkSolParcial` int(1) default NULL,
  `StkSolImprimiendo` int(1) default NULL,
  `StkSolObs` varchar(100) default NULL,
  `StkSolCambio` char(1) default NULL,
  `StkSolUsuAut` int(11) default NULL,
  `StkSolFchAut` date default NULL,
  `StkSolUsuCre` int(11) NOT NULL default '0',
  `StkSolFchCre` date NOT NULL default '0000-00-00',
  `StkSolUsuMod` int(11) default NULL,
  PRIMARY KEY  (`StkSolId`),
  KEY `StkSolUsuSol` (`StkSolUsuSol`,`StkSolFchSol`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Volcar la base de datos para la tabla `stksolicitudes`
--


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `usuUsuario` varchar(20) NOT NULL default '',
  `usuNombre` varchar(20) NOT NULL default '',
  `usuApellido` varchar(20) NOT NULL default '',
  `UsuPass` varchar(32) NOT NULL default '',
  `UsuPassInicia` tinyint(1) NOT NULL default '1',
  `UsuLogLast` datetime default NULL,
  `UsuPassNew` tinyint(1) default NULL,
  `seccionesId` int(11) NOT NULL default '0',
  `UsuId` int(11) NOT NULL auto_increment,
  `DepHerederos` varchar(50) default NULL,
  `UsuFchFin` date default NULL,
  `UsuFchCre` date NOT NULL default '0000-00-00',
  `usuUsuCre` int(11) NOT NULL default '0',
  `usuFchMod` date default NULL,
  `usuUsuMod` int(11) default NULL,
  `UsuMail` varchar(255) default NULL,
  `UsuStkMin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`UsuId`),
  UNIQUE KEY `usuUsuario` (`usuUsuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuUsuario`, `usuNombre`, `usuApellido`, `UsuPass`, `UsuPassInicia`, `UsuLogLast`, `UsuPassNew`, `seccionesId`, `UsuId`, `DepHerederos`, `UsuFchFin`, `UsuFchCre`, `usuUsuCre`, `usuFchMod`, `usuUsuMod`, `UsuMail`, `UsuStkMin`) VALUES
('admin', 'Administrador', 'del Sistema', 'nueva', 1, NULL, NULL, 36, 1, '0036', NULL, '2014-12-24', 1, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usudep`
--

CREATE TABLE IF NOT EXISTS `usudep` (
  `UsuDepId` int(11) NOT NULL auto_increment,
  `UsuId` int(11) NOT NULL default '0',
  `departamentosId` int(11) NOT NULL default '0',
  `UsuDepPri` tinyint(1) NOT NULL default '0',
  `UsuDepFchIni` date NOT NULL default '0000-00-00',
  `UsuDepFchFin` date default NULL,
  `UsuDepUsuCre` int(11) NOT NULL default '0',
  `UsuDepFchCre` date NOT NULL default '0000-00-00',
  `UsuDepUsuMod` int(11) NOT NULL default '0',
  `UsuDepFchMod` date default NULL,
  PRIMARY KEY  (`UsuDepId`),
  UNIQUE KEY `UsuId` (`UsuId`,`departamentosId`,`UsuDepFchIni`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `usudep`
--

INSERT INTO `usudep` (`UsuDepId`, `UsuId`, `departamentosId`, `UsuDepPri`, `UsuDepFchIni`, `UsuDepFchFin`, `UsuDepUsuCre`, `UsuDepFchCre`, `UsuDepUsuMod`, `UsuDepFchMod`) VALUES
(1, 1, 36, 1, '2014-12-24', NULL, 1, '2014-12-24', 0, NULL);
