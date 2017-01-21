-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  db
-- Généré le :  Sam 21 Janvier 2017 à 13:39
-- Version du serveur :  5.7.14
-- Version de PHP :  5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
  `resiste_plaque` int(11) NOT NULL DEFAULT '0'
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
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `persos`
--
ALTER TABLE `persos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT pour la table `persos_competences`
--
ALTER TABLE `persos_competences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `persos_competences`
--
ALTER TABLE `persos_competences`
  ADD CONSTRAINT `persos_competences_ibfk_1` FOREIGN KEY (`id_perso`) REFERENCES `persos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

