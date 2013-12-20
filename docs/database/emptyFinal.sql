-- phpMyAdmin SQL Dump
-- version 4.1.0
-- http://www.phpmyadmin.net
--
-- Client :  localhost:3306
-- Généré le :  Ven 20 Décembre 2013 à 07:33
-- Version du serveur :  5.5.34
-- Version de PHP :  5.4.23

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `arcanswer`
--

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
  `id_post` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user_post` int(10) unsigned DEFAULT NULL COMMENT 'Author of the post',
  `id_thread_post` int(10) unsigned DEFAULT NULL COMMENT 'Thread of the post',
  `content_post` text NOT NULL,
  `date_post` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solution_post` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_post`),
  KEY `IDX_FK_POST_USER` (`id_user_post`),
  KEY `IDX_FK_POST_THREAD` (`id_thread_post`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `post_vote_view`
--
CREATE TABLE IF NOT EXISTS `post_vote_view` (
`id_post` int(10) unsigned
,`id_user_post` int(10) unsigned
,`id_thread_post` int(10) unsigned
,`content_post` text
,`date_post` timestamp
,`solution_post` tinyint(1)
,`total_votes` decimal(27,0)
);
-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id_tag` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_tag` varchar(250) NOT NULL,
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `IDX_UQ_TAG_NAME` (`name_tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Structure de la table `tag_thread`
--

CREATE TABLE IF NOT EXISTS `tag_thread` (
  `id_tag` int(10) unsigned NOT NULL,
  `id_thread` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_tag`,`id_thread`),
  KEY `IDX_FK_TAGTHREAD_THREAD` (`id_thread`),
  KEY `IDX_FK_TAGTHREAD_TAG` (`id_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `thread`
--

CREATE TABLE IF NOT EXISTS `thread` (
  `id_thread` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_post_thread` int(10) unsigned NOT NULL COMMENT 'Main post of the thread',
  `title_thread` varchar(250) NOT NULL,
  PRIMARY KEY (`id_thread`),
  KEY `IDX_FK_THREAD_POST` (`id_post_thread`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_user` varchar(250) NOT NULL,
  `password_user` char(40) NOT NULL COMMENT 'SHA1',
  `nickname_user` varchar(250) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `IDX_UQ_USER_LOGIN` (`login_user`),
  UNIQUE KEY `IDX_UQ_USER_NICKNAME` (`nickname_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Structure de la table `vote`
--

CREATE TABLE IF NOT EXISTS `vote` (
  `id_post` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `value_vote` smallint(6) NOT NULL,
  PRIMARY KEY (`id_post`,`id_user`),
  KEY `IDX_FK_VOTE_POST` (`id_post`),
  KEY `IDX_FK_VOTE_USER` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la vue `post_vote_view`
--
DROP TABLE IF EXISTS `post_vote_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `post_vote_view` AS select `post`.`id_post` AS `id_post`,`post`.`id_user_post` AS `id_user_post`,`post`.`id_thread_post` AS `id_thread_post`,`post`.`content_post` AS `content_post`,`post`.`date_post` AS `date_post`,`post`.`solution_post` AS `solution_post`,sum(`vote`.`value_vote`) AS `total_votes` from (`post` left join `vote` on((`post`.`id_post` = `vote`.`id_post`))) group by `post`.`id_post` order by `post`.`date_post`;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_POST_OWNER` FOREIGN KEY (`id_user_post`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_POST_THREAD` FOREIGN KEY (`id_thread_post`) REFERENCES `thread` (`id_thread`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tag_thread`
--
ALTER TABLE `tag_thread`
  ADD CONSTRAINT `FK_TAGTHREAD_TAG` FOREIGN KEY (`id_tag`) REFERENCES `tag` (`id_tag`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_TAGTHREAD_THREAD` FOREIGN KEY (`id_thread`) REFERENCES `thread` (`id_thread`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `thread`
--
ALTER TABLE `thread`
  ADD CONSTRAINT `FK_THREAD_POST` FOREIGN KEY (`id_post_thread`) REFERENCES `post` (`id_post`) ON UPDATE CASCADE;

--
-- Contraintes pour la table `vote`
--
ALTER TABLE `vote`
  ADD CONSTRAINT `FK_VOTE_POST` FOREIGN KEY (`id_post`) REFERENCES `post` (`id_post`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_VOTE_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
