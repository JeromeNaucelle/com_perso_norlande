-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  db
-- Généré le :  Dim 11 Juin 2017 à 19:49
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `Norlande`
--

-- --------------------------------------------------------

--
-- Structure de la table `competences`
--

CREATE TABLE `competences` (
  `competence_id` int(10) UNSIGNED NOT NULL,
  `famille` text NOT NULL,
  `maitrise` text NOT NULL,
  `competence_nom` text NOT NULL,
  `niveau` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `entraineur` tinyint(1) NOT NULL DEFAULT '0',
  `niveau_langue` varchar(35) NOT NULL DEFAULT '',
  `lecture_ecriture` int(11) NOT NULL DEFAULT '0',
  `rumeurs` int(11) NOT NULL DEFAULT '0',
  `actions_guerre` int(11) NOT NULL DEFAULT '0',
  `coup_force` int(11) NOT NULL DEFAULT '0',
  `voix_noires` int(11) NOT NULL DEFAULT '0',
  `voix_blanches` int(11) NOT NULL DEFAULT '0',
  `voix_peuple` int(11) NOT NULL DEFAULT '0',
  `voix_roi` int(11) NOT NULL DEFAULT '0',
  `veto` int(11) NOT NULL DEFAULT '0',
  `manigance` int(11) NOT NULL DEFAULT '0',
  `lieux_pouvoir` varchar(40) NOT NULL DEFAULT '',
  `force_physique` int(11) NOT NULL DEFAULT '0',
  `bonus_mana` int(11) NOT NULL DEFAULT '0',
  `globes_sortilege` int(11) NOT NULL DEFAULT '0',
  `bonus_coups` int(11) NOT NULL DEFAULT '0',
  `bonus_coup_etoffe` int(11) NOT NULL DEFAULT '0',
  `esquive_etoffe` int(11) NOT NULL DEFAULT '0',
  `resiste_etoffe` int(11) NOT NULL DEFAULT '0',
  `esquive_cuir` int(11) NOT NULL DEFAULT '0',
  `resiste_cuir` int(11) NOT NULL DEFAULT '0',
  `esquive_maille` int(11) NOT NULL DEFAULT '0',
  `resiste_maille` int(11) NOT NULL DEFAULT '0',
  `esquive_plaque` int(11) NOT NULL DEFAULT '0',
  `resiste_plaque` int(11) NOT NULL DEFAULT '0',
  `parcelles` varchar(255) DEFAULT '',
  `possessions_depart` varchar(255) DEFAULT '',
  `a_prevoir` varchar(255) DEFAULT '',
  `connaissances` varchar(255) DEFAULT '',
  `aide_jeu` varchar(255) DEFAULT '',
  `maniement` varchar(255) DEFAULT '',
  `attaque_spe` varchar(255) DEFAULT '',
  `attaque_spe_tranchant` varchar(255) DEFAULT '',
  `attaque_spe_contondant` varchar(255) DEFAULT '',
  `attaque_spe_hast` varchar(255) DEFAULT '',
  `attaque_spe_tir` varchar(255) DEFAULT '',
  `attaque_spe_lancer` varchar(255) DEFAULT '',
  `sortilege` varchar(255) DEFAULT '',
  `sort_masse1` varchar(255) DEFAULT '',
  `sort_masse2` varchar(255) DEFAULT '',
  `immunite_etoffe` varchar(255) DEFAULT '',
  `immunite_cuir` varchar(255) DEFAULT '',
  `immunite_maille` varchar(255) DEFAULT '',
  `immunite_plaque` varchar(255) DEFAULT '',
  `amelioration` varchar(255) DEFAULT '',
  `capacite` varchar(255) DEFAULT '',
  `technique1` varchar(255) DEFAULT '',
  `technique2` varchar(255) DEFAULT '',
  `piege1` varchar(255) DEFAULT '',
  `piege2` varchar(255) DEFAULT '',
  `breuvage1` varchar(255) DEFAULT '',
  `breuvage2` varchar(255) DEFAULT '',
  `breuvage3` varchar(255) DEFAULT '',
  `breuvage4` varchar(255) DEFAULT '',
  `breuvage5` varchar(255) DEFAULT '',
  `invocation1` varchar(350) DEFAULT '',
  `invocation2` varchar(350) DEFAULT '',
  `metamorphose` varchar(350) DEFAULT '',
  `pouvoir1` varchar(255) DEFAULT '',
  `pouvoir2` varchar(255) DEFAULT '',
  `pouvoir3` varchar(255) DEFAULT '',
  `pouvoir4` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `persos`
--

CREATE TABLE `persos` (
  `id` int(11) NOT NULL,
  `nom` text NOT NULL,
  `lignee` text NOT NULL,
  `pieces_or` int(11) NOT NULL DEFAULT '0',
  `pieces_argent` int(11) NOT NULL DEFAULT '0',
  `pieces_cuivre` int(11) NOT NULL DEFAULT '0',
  `points_creation` int(11) NOT NULL DEFAULT '0',
  `cristaux_incolores` int(11) NOT NULL,
  `cristaux_occultisme` int(11) NOT NULL,
  `cristaux_societe` int(11) NOT NULL,
  `cristaux_belligerance` int(11) NOT NULL,
  `cristaux_intrigue` int(11) NOT NULL,
  `entrainements` text,
  `histoire` text NOT NULL,
  `armure` enum('Etoffe','Cuir','Maille','Plaque') NOT NULL DEFAULT 'Etoffe',
  `derniere_session` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `persos_competences`
--

CREATE TABLE `persos_competences` (
  `id` int(11) NOT NULL,
  `id_perso` int(11) NOT NULL,
  `competence_id` int(11) NOT NULL,
  `date_acquisition` date NOT NULL,
  `valide` int(11) NOT NULL DEFAULT '1',
  `xp_used` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `persos_users`
--

CREATE TABLE `persos_users` (
  `user_id` int(11) NOT NULL,
  `perso_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`competence_id`),
  ADD KEY `competence_id` (`competence_id`),
  ADD KEY `competence_id_2` (`competence_id`);

--
-- Index pour la table `persos`
--
ALTER TABLE `persos`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `persos_competences`
--
ALTER TABLE `persos_competences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_perso` (`id_perso`),
  ADD KEY `competence_id` (`competence_id`);

--
-- Index pour la table `persos_users`
--
ALTER TABLE `persos_users`
  ADD PRIMARY KEY (`user_id`,`perso_id`),
  ADD UNIQUE KEY `perso_id_2` (`perso_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `perso_id` (`perso_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `persos`
--
ALTER TABLE `persos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT pour la table `persos_competences`
--
ALTER TABLE `persos_competences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `persos_competences`
--
ALTER TABLE `persos_competences`
  ADD CONSTRAINT `persos_competences_ibfk_1` FOREIGN KEY (`id_perso`) REFERENCES `persos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `persos_users`
--
ALTER TABLE `persos_users`
  ADD CONSTRAINT `persos_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `zd15e_users` (`id`),
  ADD CONSTRAINT `persos_users_ibfk_2` FOREIGN KEY (`perso_id`) REFERENCES `persos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
