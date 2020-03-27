-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 15 giu, 2011 at 12:18 
-- Versione MySQL: 5.5.8
-- Versione PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `prova`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `Prenotazioni`
--

CREATE TABLE IF NOT EXISTS `Prenotazioni` (
  `IDprenotazione` int NOT NULL ,
  `Email` varchar(30) NOT NULL ,
  `Richiesti` int NOT NULL,
  `Assegnati` int NOT NULL,
  `OraInizio` varchar(30) NOT NULL,
  `OraFine` varchar(30) NOT NULL,
  PRIMARY KEY (`IDprenotazione`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=166 ;

--
-- Dump dei dati per la tabella `Prenotazioni`
--



--
--
--

CREATE TABLE IF NOT EXISTS `utenti` (
  `Email` varchar(30) NOT NULL ,
  `Password` varchar(100) NOT NULL,
  PRIMARY KEY (`Email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=166 ;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`Email`, `Password`) VALUES
('a@p.it', md5('pO1')),
('b@p.it', md5('pO2')),
('c@p.it', md5('pO3'));
