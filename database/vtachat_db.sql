-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 26 oct. 2025 à 19:21
-- Version du serveur : 5.7.44
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vtachat_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

DROP TABLE IF EXISTS `clients`;
CREATE TABLE IF NOT EXISTS `clients` (
  `id_client` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `telephone` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_client`),
  UNIQUE KEY `unq_clients` (`nom`,`telephone`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id_client`, `nom`, `telephone`, `adresse`) VALUES
(1, 'Mouhamed SY', NULL, NULL),
(12, 'Abdou Mahamane', NULL, NULL),
(23, 'Moctar', NULL, NULL),
(24, 'Ali Garba', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `ligne_ventes`
--

DROP TABLE IF EXISTS `ligne_ventes`;
CREATE TABLE IF NOT EXISTS `ligne_ventes` (
  `id_ligne` int(11) NOT NULL AUTO_INCREMENT,
  `id_vente` int(11) NOT NULL,
  `produit` text NOT NULL,
  `quantite` decimal(10,0) NOT NULL,
  `prix` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  PRIMARY KEY (`id_ligne`),
  KEY `fk_ligne_ventes_ventes` (`id_vente`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `ligne_ventes`
--

INSERT INTO `ligne_ventes` (`id_ligne`, `id_vente`, `produit`, `quantite`, `prix`, `total`) VALUES
(1, 1, 'Fer 100m', 2, 4500, 9000),
(2, 2, 'Ordinateur', 1, 400000, 400000),
(3, 2, 'Tablette', 5, 130000, 650000),
(4, 2, 'StarLink V4', 2, 245000, 490000),
(5, 2, 'StarLink Mini', 1, 125000, 125000),
(6, 3, 'Fil de fer Roulaux', 30, 6500, 195000),
(7, 4, 'Tuyau ', 12, 3500, 42000),
(8, 5, 'Fer ', 2, 2500, 5000),
(9, 5, 'Tube carre', 4, 4000, 16000),
(10, 5, 'Tuyau', 5, 1250, 6250);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

DROP TABLE IF EXISTS `paiements`;
CREATE TABLE IF NOT EXISTS `paiements` (
  `id_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `id_vente` int(11) NOT NULL,
  `date_paiement` date NOT NULL,
  `montant` int(11) DEFAULT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT '0',
  `responsable` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_paiement`),
  KEY `fk_paiements_ventes` (`id_vente`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `id_vente`, `date_paiement`, `montant`, `statut`, `responsable`) VALUES
(1, 1, '2025-10-25', 5000, 1, 'Système vente'),
(2, 1, '2025-10-25', 2, 1, 'Abdou abdou'),
(3, 1, '0000-00-00', 111, 1, ''),
(4, 1, '0000-00-00', 11, 1, ''),
(5, 1, '0000-00-00', 12, 1, ''),
(6, 1, '0000-00-00', 864, 1, 'Moctar'),
(7, 1, '0000-00-00', 500, 1, ''),
(8, 1, '0000-00-00', 250, 1, ''),
(9, 2, '2025-10-25', 1200000, 1, 'Système vente'),
(10, 2, '0000-00-00', 400000, 1, 'Mouhamed Sy'),
(11, 2, '0000-00-00', 65000, 1, 'Moctar'),
(12, 3, '2025-10-26', 115000, 1, 'Système vente'),
(13, 4, '2025-10-26', 30000, 1, 'Système vente'),
(14, 3, '0000-00-00', 80000, 1, 'Abdoul Kader'),
(15, 5, '2025-10-26', 25000, 1, 'Système vente');

-- --------------------------------------------------------

--
-- Structure de la table `statut_paiement`
--

DROP TABLE IF EXISTS `statut_paiement`;
CREATE TABLE IF NOT EXISTS `statut_paiement` (
  `id_statut_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL COMMENT 'Ex : “Payé”, “Partiel”, “Crédit”',
  PRIMARY KEY (`id_statut_paiement`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `statut_paiement`
--

INSERT INTO `statut_paiement` (`id_statut_paiement`, `libelle`) VALUES
(1, 'Payé'),
(2, 'Partiel'),
(3, 'Crédit');

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

DROP TABLE IF EXISTS `ventes`;
CREATE TABLE IF NOT EXISTS `ventes` (
  `id_vente` int(11) NOT NULL AUTO_INCREMENT,
  `id_client` int(11) NOT NULL,
  `id_statut_paiement` int(11) NOT NULL,
  `date_vente` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `commentaire` text,
  `montant_total` int(11) NOT NULL DEFAULT '0',
  `montant_regle` int(11) NOT NULL DEFAULT '0',
  `statut` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_vente`),
  KEY `fk_ventes_clients` (`id_client`),
  KEY `fk_ventes_statut_paiement` (`id_statut_paiement`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id_vente`, `id_client`, `id_statut_paiement`, `date_vente`, `commentaire`, `montant_total`, `montant_regle`, `statut`) VALUES
(1, 1, 2, '2025-10-25 13:21:00', NULL, 9000, 6750, 0),
(2, 23, 1, '2025-10-25 18:32:00', NULL, 1665000, 1665000, 0),
(3, 12, 1, '2025-10-26 17:30:00', NULL, 195000, 195000, 0),
(4, 1, 2, '2025-10-26 17:30:00', NULL, 42000, 30000, 0),
(5, 24, 2, '2025-10-26 18:48:00', NULL, 27250, 25000, 0);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ligne_ventes`
--
ALTER TABLE `ligne_ventes`
  ADD CONSTRAINT `fk_ligne_ventes_ventes` FOREIGN KEY (`id_vente`) REFERENCES `ventes` (`id_vente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `fk_paiements_ventes` FOREIGN KEY (`id_vente`) REFERENCES `ventes` (`id_vente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD CONSTRAINT `fk_ventes_clients` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id_client`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ventes_statut_paiement` FOREIGN KEY (`id_statut_paiement`) REFERENCES `statut_paiement` (`id_statut_paiement`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
