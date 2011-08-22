-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Erstellungszeit: 22. August 2011 um 19:23
-- Server Version: 5.0.51
-- PHP-Version: 5.2.6-1+lenny9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `rcs`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Benutzer`
--

CREATE TABLE IF NOT EXISTS `Benutzer` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` char(20) NOT NULL default '',
  `KurzName` char(10) NOT NULL default '',
  `Gast` tinyint(4) NOT NULL default '0',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ID` (`ID`),
  KEY `ID_2` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Folgen`
--

CREATE TABLE IF NOT EXISTS `Folgen` (
  `ID` int(11) NOT NULL auto_increment,
  `Serie` tinyint(4) NOT NULL default '0',
  `FolgenNummer` tinyint(4) NOT NULL default '0',
  `Datum` int(12) NOT NULL default '0',
  `Aufgabe` tinyint(4) NOT NULL default '0',
  `Benutzer` tinyint(4) NOT NULL default '0',
  `Status` tinyint(4) NOT NULL default '0',
  `comment` tinytext NOT NULL,
  `active` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ID` (`ID`),
  KEY `ID_2` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Serien`
--

CREATE TABLE IF NOT EXISTS `Serien` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(80) NOT NULL default '',
  `KurzName` varchar(10) NOT NULL default '',
  `Folgen` tinyint(4) NOT NULL default '0',
  `active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `ID` (`ID`),
  KEY `ID_2` (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
