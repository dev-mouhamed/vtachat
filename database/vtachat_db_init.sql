-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 23 nov. 2025 à 12:46
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
