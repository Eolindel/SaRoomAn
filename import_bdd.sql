-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  lun. 03 août 2020 à 18:17
-- Version du serveur :  10.4.8-MariaDB
-- Version de PHP :  7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `demo_cartes`
--

-- --------------------------------------------------------

--
-- Structure de la table `maps`
--

CREATE TABLE `maps` (
  `id_map` int(11) NOT NULL,
  `building` varchar(50) NOT NULL,
  `floor` varchar(50) NOT NULL,
  `file` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `rooms`
--

CREATE TABLE `rooms` (
  `id_room` int(11) NOT NULL,
  `building` varchar(50) NOT NULL,
  `floor` varchar(50) NOT NULL,
  `idSvg` varchar(50) NOT NULL,
  `officeName` varchar(50) NOT NULL,
  `commonName` varchar(50) NOT NULL,
  `surface` float NOT NULL,
  `telephone1` varchar(20) NOT NULL,
  `telephone2` varchar(20) NOT NULL,
  `responsable` int(11) NOT NULL,
  `places` int(11) NOT NULL,
  `max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `roomusers`
--

CREATE TABLE `roomusers` (
  `id_user` bigint(20) NOT NULL,
  `prenom` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `nom` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `status` int(11) NOT NULL,
  `team` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `login` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `active` int(11) NOT NULL,
  `mail` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `notifications` tinyint(4) NOT NULL,
  `password_2` varchar(64) COLLATE latin1_general_ci NOT NULL,
  `ref_office` int(11) NOT NULL,
  `ref_workplace` int(11) NOT NULL,
  `statut` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `telephone` varchar(40) COLLATE latin1_general_ci NOT NULL,
  `roomStatus` int(11) NOT NULL,
  `ref_responsable` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `room_recovery`
--

CREATE TABLE `room_recovery` (
  `id` int(11) NOT NULL,
  `ref` int(11) NOT NULL,
  `cle_tmp` varchar(256) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `slotSchedule`
--

CREATE TABLE `slotSchedule` (
  `id_slot` int(11) NOT NULL,
  `year` year(4) NOT NULL,
  `week` tinyint(11) NOT NULL,
  `date` date NOT NULL,
  `day` tinyint(11) NOT NULL,
  `start` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `length` int(11) NOT NULL,
  `ref_room` int(11) NOT NULL,
  `ref_user` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `valid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `maps`
--
ALTER TABLE `maps`
  ADD PRIMARY KEY (`id_map`);

--
-- Index pour la table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id_room`);

--
-- Index pour la table `roomusers`
--
ALTER TABLE `roomusers`
  ADD PRIMARY KEY (`id_user`);

--
-- Index pour la table `room_recovery`
--
ALTER TABLE `room_recovery`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `slotSchedule`
--
ALTER TABLE `slotSchedule`
  ADD PRIMARY KEY (`id_slot`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `maps`
--
ALTER TABLE `maps`
  MODIFY `id_map` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id_room` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roomusers`
--
ALTER TABLE `roomusers`
  MODIFY `id_user` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `room_recovery`
--
ALTER TABLE `room_recovery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `slotSchedule`
--
ALTER TABLE `slotSchedule`
  MODIFY `id_slot` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
