-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 29 mars 2023 à 11:19
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `apirest_project`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `Id_Article` int(250) NOT NULL AUTO_INCREMENT,
  `Date_Publication` date NOT NULL,
  `Auteur` varchar(250) NOT NULL,
  `Contenu` varchar(250) NOT NULL,
  PRIMARY KEY (`Id_Article`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`Id_Article`, `Date_Publication`, `Auteur`, `Contenu`) VALUES
(6, '2023-03-18', 'Napoléon1er', 'La mort n\'est rien mais vivre vaincu et sans gloire c\'est mourrir tous les jours');

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `Id_Article` int(11) NOT NULL,
  `Id_Utilisateur` int(11) NOT NULL,
  `Like_or_Dislike` varchar(255) NOT NULL,
  PRIMARY KEY (`Id_Utilisateur`,`Id_Article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `likes`
--

INSERT INTO `likes` (`Id_Article`, `Id_Utilisateur`, `Like_or_Dislike`) VALUES
(6, 3, '0');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `Id_Utilisateur` int(250) NOT NULL AUTO_INCREMENT,
  `Username` varchar(250) NOT NULL,
  `Pwd` varchar(250) NOT NULL,
  `Role_Utilisateur` varchar(250) NOT NULL,
  PRIMARY KEY (`Id_Utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`Id_Utilisateur`, `Username`, `Pwd`, `Role_Utilisateur`) VALUES
(1, 'Napoleon1er', 'empereur_des_français', 'publisher'),
(2, 'ChuckNurris', 'Password', 'moderateur'),
(3, 'Moliere', 'JeanBaptistePoquelin', 'publisher');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
