-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 30 mars 2023 à 10:30
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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`Id_Article`, `Date_Publication`, `Auteur`, `Contenu`) VALUES
(21, '2023-03-30', 'Homer', 'On ne voit bien qu\'avec le coeur, l\'essentiel est invisible pour les yeux'),
(19, '2023-03-30', 'Napoléon1er', 'fais de ta vie, et d\'un rêve une réalité'),
(20, '2023-03-30', 'Moliere', 'Espère rine de l\'homme, s\'il travail pour sa propre vie et non pour son éternité'),
(17, '2023-03-30', 'Napoléon1er', 'La mort n\'est rien, mais vivre vaincu et sans gloire c\'est mourrir tous les jours'),
(16, '2023-03-29', 'Homer', 'J\'aime les réçiss grec'),
(18, '2023-03-30', 'Napoléon1er', 'La mort n\'est rien, mais vivre vaincu et sans gloire c\'est mourrir tous les jours'),
(22, '2023-03-30', 'Homer', 'Ceux qui donne un sens à la vie, Donne un sens à la mort'),
(23, '2023-03-30', 'Moliere', 'la vérité de demain se nourrit de l\'erreur d\'hier'),
(24, '2023-03-30', 'Napoléon1er', 'les yeux sont aveugle il faut chercher avec le coeur'),
(25, '2023-03-30', 'Moliere', 'la vérité de demain se nourrit de l\'erreur d\'hier');

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
(16, 1, '1'),
(16, 3, '0'),
(17, 4, '1');

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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`Id_Utilisateur`, `Username`, `Pwd`, `Role_Utilisateur`) VALUES
(1, 'Napoleon1er', 'empereur_des_français', 'publisher'),
(2, 'ChuckNurris', 'Password', 'moderateur'),
(3, 'Moliere', 'JeanBaptistePoquelin', 'publisher'),
(4, 'Homer', 'liliade', 'publisher');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
