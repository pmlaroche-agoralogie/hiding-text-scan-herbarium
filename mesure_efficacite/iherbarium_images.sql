-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 24 Octobre 2017 à 15:55
-- Version du serveur: 5.5.24-log
-- Version de PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `iherbarium_images`
--

-- --------------------------------------------------------

--
-- Structure de la table `images_verif`
--

CREATE TABLE IF NOT EXISTS `images_verif` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `zones` text NOT NULL,
  `resultat` int(11) NOT NULL,
  `datetest` int(20) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `nom_image` (`nom`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `images_verif`
--

INSERT INTO `images_verif` (`uid`, `nom`, `zones`, `resultat`, `datetest`) VALUES
(1, '1441405745852EZ9e0Fn1KuWzMW9Q_jpg.jpg', '1096,1829,76,17', 1, 1500626400),
(2, '1441406017803clsqtQorydBXPhoF_jpg.jpg', '890,799,36,23;1087,1841,63,11;1206,1846,89,12', 0, 1500626401),
(3, '1441408898671vWocQ93FUb48FfJQ_jpg.jpg', '', -1, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
