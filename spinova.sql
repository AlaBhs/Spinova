-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 07 mai 2025 à 02:06
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `link_rotator`
--

-- --------------------------------------------------------

--
-- Structure de la table `links`
--

CREATE TABLE `links` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_click` tinyint(1) DEFAULT 0,
  `is_equal_distribution` tinyint(1) DEFAULT 0,
  `os_filter_enabled` tinyint(1) DEFAULT 0,
  `default_destination_url` varchar(255) DEFAULT '',
  `default_destination_visits` int(11) DEFAULT 0,
  `total_visits` int(11) NOT NULL DEFAULT 0,
  `short` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `isArchive` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `link_destinations`
--

CREATE TABLE `link_destinations` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `clicks` int(11) DEFAULT NULL,
  `visits` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `link_os_destinations`
--

CREATE TABLE `link_os_destinations` (
  `id` int(11) NOT NULL,
  `link_id` int(11) NOT NULL,
  `os` enum('windows','macos','linux','ios','android','other') NOT NULL,
  `url` varchar(255) NOT NULL,
  `visits` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `link_destinations`
--
ALTER TABLE `link_destinations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `link_id` (`link_id`);

--
-- Index pour la table `link_os_destinations`
--
ALTER TABLE `link_os_destinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link_os_unique` (`link_id`,`os`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `links`
--
ALTER TABLE `links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `link_destinations`
--
ALTER TABLE `link_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `link_os_destinations`
--
ALTER TABLE `link_os_destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `link_destinations`
--
ALTER TABLE `link_destinations`
  ADD CONSTRAINT `link_destinations_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `link_os_destinations`
--
ALTER TABLE `link_os_destinations`
  ADD CONSTRAINT `fk_link_os` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
