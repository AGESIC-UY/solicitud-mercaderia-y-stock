-- phpMyAdmin SQL Dump
-- version 2.11.4
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 29-12-2015 a las 12:53:05
-- Versión del servidor: 4.1.22
-- Versión de PHP: 5.2.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `stkwebsofpub`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamentos`
--

CREATE TABLE IF NOT EXISTS `departamentos` (
  `DepNombre` varchar(100) character set utf8 NOT NULL default '',
  `DepId` int(11) NOT NULL auto_increment,
  `DepIdDep` int(11) default NULL,
  `DepHerederos` varchar(50) NOT NULL default '0',
  `DepTipoArea` tinyint(1) NOT NULL default '0',
  `DepNoVigente` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`DepId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=85 ;

--
-- Volcar la base de datos para la tabla `departamentos`
--

INSERT INTO `departamentos` (`DepNombre`, `DepId`, `DepIdDep`, `DepHerederos`, `DepTipoArea`, `DepNoVigente`) VALUES
('Asesoria Letrada', 3, 84, '003600840003', 1, 0),
('APT - Politicas Territoriales', 25, 84, '003600840025', 1, 0),
('APT - Gobiernos Departamentales', 27, 25, '0036008400250027', 1, 0),
('Direccion', 36, 36, '0036', 1, 0),
('Sub-Direccion', 84, 36, '00360084', 1, 0),
('TI - Division Tecnologias de la informacion', 31, 36, '003600840031', 1, 0);

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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
(1, 3, 1, 1, 1, '2015-09-01', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sistemas`
--

CREATE TABLE IF NOT EXISTS `sistemas` (
  `SisId` int(4) NOT NULL auto_increment,
  `SisNom` varchar(50) NOT NULL default '',
  `SisDsc` varchar(150) NOT NULL default '',
  `SisStkEntParcial` tinyint(1) NOT NULL default '0' COMMENT 'Integra requerimiento de entrega parcial de material de stock',
  `SisLogo` varchar(40) default NULL,
  `SisLogoLogin` varchar(40) default NULL,
  `SisLogoFondo` varchar(40) default NULL,
  `SisActivo` tinyint(1) NOT NULL default '1',
  `SisUsuCre` int(11) NOT NULL default '0',
  `SisFchCre` date NOT NULL default '0000-00-00',
  `SisUsuMod` int(11) default NULL,
  `SisFchMod` date default NULL,
  PRIMARY KEY  (`SisId`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `sistemas`
--

INSERT INTO `sistemas` (`SisId`, `SisNom`, `SisDsc`, `SisStkEntParcial`, `SisLogo`, `SisLogoLogin`, `SisLogoFondo`, `SisActivo`, `SisUsuCre`, `SisFchCre`, `SisUsuMod`, `SisFchMod`) VALUES
(1, 'Solicitud de Mercaderia y Stock', 'Stock de Material de Oficina, ambiente de Insumos', 0, 'Images/LogoLogin.jpg', 'Images/LogoLogin.jpg', 'Images/LogoFondo.jpg', 1, 1, '2010-04-20', NULL, NULL);

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
(3, 'Otros Articulos', 0, 0),
(2, 'Insumos Informaticos', 0, 0),
(1, 'Insumos de Oficina', 0, 0);

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

INSERT INTO `stkartclsusu` (`UsuId`, `StkArtClsId`, `StkArtClsUsuAll`, `StkArtClsHab`) VALUES
(1, 1, 1, 1),
(1, 2, 1, 1),
(1, 3, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stkartdep`
--

CREATE TABLE IF NOT EXISTS `stkartdep` (
  `StkArtId` int(4) NOT NULL default '0',
  `DepId` int(11) NOT NULL default '0',
  `StkArtDepUsuCre` int(11) NOT NULL default '0',
  `StkArtDepFchCre` date NOT NULL default '0000-00-00',
  `StkArtDepUsuMod` int(11) default '0',
  `StkArtDepFchMod` date default NULL,
  PRIMARY KEY  (`StkArtId`,`DepId`)
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Volcar la base de datos para la tabla `stkcausal`
--

INSERT INTO `stkcausal` (`StkCauId`, `StkCauDsc`, `StkCauIn`, `StkCauOut`, `StkCauTpo`) VALUES
(8, 'No se distribuye mas', 0, 1, 'Articulo'),
(7, 'Correccion de saldo', 1, 1, 'Definitivo'),
(6, 'Rotura', 0, 1, 'Definitivo'),
(5, 'Canje', 1, 1, 'Definitivo'),
(4, 'Articulo Caduco', 0, 1, 'Articulo'),
(3, 'No se distribuye - Orden direccion', 0, 1, 'Articulo'),
(2, 'Paso a ser distribuido por otra area', 0, 1, 'Articulo'),
(1, 'Se dejo de fabricar', 0, 1, 'Articulo'),
(9, 'Donacion', 1, 1, 'Definitivo'),
(10, 'Saldo Inicial', 1, 0, 'Definitivo'),
(11, 'Articulo repetido', 0, 1, 'Articulo'),
(12, 'Compra Directa', 1, 0, 'Definitivo'),
(13, 'Nuevos Ingresos', 1, 0, 'Definitivo');

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
  `StkPrvFacFin` tinyint(1) NOT NULL default '0',
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
  `UsuUsuario` varchar(20) NOT NULL default '',
  `UsuNombre` varchar(20) NOT NULL default '',
  `UsuApellido` varchar(20) NOT NULL default '',
  `UsuPass` varchar(32) NOT NULL default '',
  `UsuPassInicia` tinyint(1) NOT NULL default '1',
  `UsuLogLast` datetime default NULL,
  `UsuPassNew` tinyint(1) default NULL,
  `SeccionesId` int(11) NOT NULL default '0',
  `UsuId` int(11) NOT NULL auto_increment,
  `DepHerederos` varchar(50) default NULL,
  `UsuFchFin` date default NULL,
  `UsuFchCre` date NOT NULL default '0000-00-00',
  `UsuUsuCre` int(11) NOT NULL default '0',
  `UsuFchMod` date default NULL,
  `UsuUsuMod` int(11) default NULL,
  `UsuMail` varchar(255) default NULL,
  `UsuStkMin` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`UsuId`),
  UNIQUE KEY `usuUsuario` (`UsuUsuario`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`UsuUsuario`, `UsuNombre`, `UsuApellido`, `UsuPass`, `UsuPassInicia`, `UsuLogLast`, `UsuPassNew`, `SeccionesId`, `UsuId`, `DepHerederos`, `UsuFchFin`, `UsuFchCre`, `UsuUsuCre`, `UsuFchMod`, `UsuUsuMod`, `UsuMail`, `UsuStkMin`) VALUES
('admin', 'Administrador', 'Administrador', 'nueva', 1, NULL, 1, 31, 1, '0036', NULL, '2015-09-01', 1, '2015-12-29', NULL, '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usudep`
--

CREATE TABLE IF NOT EXISTS `usudep` (
  `UsuDepId` int(11) NOT NULL auto_increment,
  `UsuId` int(11) NOT NULL default '0',
  `DepId` int(11) NOT NULL default '0',
  `UsuDepPri` tinyint(1) NOT NULL default '0',
  `UsuDepFchIni` date NOT NULL default '0000-00-00',
  `UsuDepFchFin` date default NULL,
  `UsuDepUsuCre` int(11) NOT NULL default '0',
  `UsuDepFchCre` date NOT NULL default '0000-00-00',
  `UsuDepUsuMod` int(11) NOT NULL default '0',
  `UsuDepFchMod` date default NULL,
  PRIMARY KEY  (`UsuDepId`),
  UNIQUE KEY `UsuId` (`UsuId`,`DepId`,`UsuDepFchIni`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Volcar la base de datos para la tabla `usudep`
--

INSERT INTO `usudep` (`UsuDepId`, `UsuId`, `DepId`, `UsuDepPri`, `UsuDepFchIni`, `UsuDepFchFin`, `UsuDepUsuCre`, `UsuDepFchCre`, `UsuDepUsuMod`, `UsuDepFchMod`) VALUES
(1, 1, 31, 1, '2015-09-01', NULL, 1, '2015-09-01', 0, NULL);
