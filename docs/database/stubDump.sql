-- phpMyAdmin SQL Dump
-- version 4.0.2
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Dim 01 Décembre 2013 à 19:21
-- Version du serveur: 5.6.11-log
-- Version de PHP: 5.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

set foreign_key_checks = 0;

--
-- Base de données: `arcanswer`
--
CREATE DATABASE IF NOT EXISTS `arcanswer` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `arcanswer`;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id_post` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user_post` int(10) unsigned DEFAULT NULL COMMENT 'Author of the post',
  `id_thread_post` int(10) unsigned NOT NULL COMMENT 'Thread of the post',
  `content_post` text NOT NULL,
  `date_post` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solution_post` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_post`),
  KEY `IDX_FK_POST_USER` (`id_user_post`),
  KEY `IDX_FK_POST_THREAD` (`id_thread_post`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Vider la table avant d'insérer `post`
--

TRUNCATE TABLE `post`;
--
-- Contenu de la table `post`
--

INSERT INTO `post` VALUES
(1, 1, 1, 'XYValueSeries sunSeries = new XYValueSeries("Sunshine hours");\r\nfor(int i=0; i< v.length; i++)\r\n  sunSeries.add((float)(i+1), maxValue*1.1, v[i]);\r\n\r\nXYSeriesRenderer sunRenderer = new XYSeriesRenderer();\r\nsunRenderer.setColor(Color.BLUE);\r\nsunRenderer.setDisplayChartValues(true);\r\nsetDisplayChartValues show "maxValue*1.1", but I need to see the values (v[i] NOT maxValue*1.1) above circles\r\n\r\nthank you in advance', '2013-11-15 08:18:16', 19),
(2, 2, 2, 'I''m working on CMS system using AngularJs, The end2end code is humongous and i think it''s not a good way to put all these test cases in one file and run one time.\r\n\r\nNow i''m using karma for runner tool. My question is Can i divide my end2end scenarios file by different component in order to i can run each component individually?\r\n\r\nI tried to do this, i separated all scenarios into different file by different component and add all in ''files'' in karma.e2e.conf.js. Just like below:\r\n\r\n    files: [', '2013-11-15 08:18:16', 0),
(3, 3, 3, 'I have a problem filtering an array with experimental data - the data is a long array(double). The index of this array is simply time (second) and the values could be negative or positive. The problem is that is sometimes experimental data is quite wrong(( - this is due to to errors in our device. This error values are very different from other array values. For example first 100 seconds values equals to +75 +(small delta value) or - 40 -(small delta value), but then, during 3 second value is +2', '2013-11-15 08:20:32', 0),
(4, 4, 4, 'I''m trying to use HTML5 SSE in my web application. Whenever data is available, I need to push updates to client browsers.\r\n\r\nI''m using Servlet 3.0 and deploy in JBOSS 7.1.1 final.\r\n\r\npublic void doGetTest(HttpServletRequest request,\r\n                       HttpServletResponse response) throws  IOException {\r\n\r\n    try {\r\n\r\n        response.setContentType("text/event-stream");\r\n        response.setCharacterEncoding("UTF-8");\r\n        response.setStatus(200);\r\n        response.addHeader("Cache-Con', '2013-11-15 08:20:32', 0),
(5, 5, 5, 'How do I pass ''key'' to the activeClass(key) method using AngularJs?\r\n\r\n<li ng-class="activeClass({{key}})" ng-repeat="(key, data) in bigData">\r\n    <label>{{data.label}}\r\n        <input type="radio" name="market" value="{{key}}" ng-model="myModel">\r\n    </label>\r\n</li>', '2013-11-15 08:20:32', 0),
(6, 6, 6, 'Is there a way of having ranges (price) with a progressive gap, rather than a fixed one?\r\n\r\nLike this:\r\n\r\n0-100 100-150 150-250 250-400 400-600', '2013-11-15 08:20:32', 0),
(7, 7, 7, 'We want to add a document to our document library using REST api. We have 1 tenant and 2 site collections.\r\n\r\nMain site collection: https://site.sharepoint.com Second: https://site.sharepoint.com/sites/subsite\r\n\r\nWe can upload document to main site collection using our code but when we do the same thing to second one we get FORBIDDEN error.\r\n\r\nCan you help us with that.\r\n\r\nHere is the code that we use api.\r\n\r\n url = targetSiteUrl + "/_api/web/lists/getByTitle(@TargetLibrary)/RootFolder/Files/add', '2013-11-15 08:20:32', 0),
(8, 8, 8, 'This is my Twitter bootstrap code, I want to give SQL Server connection to this, so I want to convert this into asp.net Wizard control, how can I???? After Finish button only I want to save my data into sql server..... in 1st step, I take Starting date & no.of Days in the 2nd step, based on the 1st step I want to create days on by one as labels dynamically, and to check these Days, I want to create CheckBoxes dynamically....\r\n\r\n                             <div id="MyWizard" class="wizard">\r\n   ', '2013-11-15 08:31:43', 0),
(9, 9, 9, 'How can I get the created/modified date of a file in Shell Scripting ?\r\n\r\ne.g: if we use ls -lrt <file path/name>\r\n-rw-rw-r-- 1 tos.tls tos 7140612 Nov 15 15:27 result_file_Nov1513_15:23:49\r\nis there any way to get the created date (YYYY-MM-DD) of a file without doing text processing for above output (using a direct command) ?', '2013-11-15 08:31:43', 0),
(10, 10, 10, 'I''m new to phpstorm and I''m loving it, but can''t find how to disable the autocompletion of doble quotation marks when I write html tags'' attributes. I''m used to not put them and it''s very annoying for me.', '2013-11-15 08:31:43', 0),
(11, 11, 11, 'The Camel documentation says "The FTP consumer (with the same endpoint) does not support concurrency (the backing FTP client is not thread safe). You can use multiple FTP consumers to poll from different endpoints. It is only a single endpoint that does not support concurrent consumers."\r\n\r\nhttp://camel.apache.org/ftp2.html.\r\n\r\nIssues is with a route configuration that has multiple consumer endpoints on the same ftp server, with the same accounts, but different path:\r\n\r\nPseudo code:\r\n\r\n<from uri', '2013-11-15 08:31:43', 0),
(12, 12, 12, 'I make a test to compare the speed of hive and shark. cluster: 1 master node, 3 slave nodes\r\n\r\nhere is a report of shark\r\n\r\nshark/spark deployed by standalone model: workers:3 | cores:12 | memory:15GB Total, 12GB Used data: 100000 rows ,10m\r\n\r\nhadoop still deploy on these nodes.\r\n\r\nwhen I select one table, the shark is faster than hive. but, when I select tow table(join select), the shark is more slower than hive.\r\n\r\nI debug the shark, and find these info:\r\n\r\n13/11/16 00:07:09 DEBUG scheduler.DA', '2013-11-15 08:31:43', 0),
(13, 13, 13, 'I know that DynamoDB is bound to a writes and read per second limit, which I set. This means that when I delete items they are bound to the same limits. I want to be able to delete many records at some point in time, without that having a negative effect on the other operations that my app is doing.\r\n\r\nSo for example, if I run a script to delete 10,000 items and it takes 1 minutes, I don''t want my database to stop serving other users that are using my app. Is there a way to separate the two, one', '2013-11-15 08:32:25', 0),
(14, 14, 14, 'I want to know if there is any significant gain over performance in Apache if we remove the htaccess files from our code and simply put all the redirect rules(which are currently in htaccess files) directly into the server config?\r\n\r\nI have been through various posts and some say that the performance would be better. I want to know what are the pros and cons of both the approaches.\r\n\r\nI have noted down following, please add your inputs to the list.\r\n\r\nPros:\r\n\r\n1) Performance improvement.\r\n\r\nCons', '2013-11-15 08:32:25', 0),
(15, 2, 1, 'Hummm good question dear', '2013-12-01 14:40:50', 0),
(16, 7, 1, 'I know I know I know !!!!', '2013-12-01 14:40:50', 0),
(19, 11, 1, 'Here is the solution : reload the page ^_^', '2013-12-01 14:42:53', 0);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `post_vote_view`
--
DROP VIEW IF EXISTS `post_vote_view`;
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

DROP TABLE IF EXISTS `tag`;
CREATE TABLE IF NOT EXISTS `tag` (
  `id_tag` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_tag` varchar(250) NOT NULL,
  PRIMARY KEY (`id_tag`),
  UNIQUE KEY `IDX_UQ_TAG_NAME` (`name_tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Vider la table avant d'insérer `tag`
--

TRUNCATE TABLE `tag`;
--
-- Contenu de la table `tag`
--

INSERT INTO `tag` (`id_tag`, `name_tag`) VALUES
(27, '.Net'),
(10, 'ajax'),
(21, 'c'),
(23, 'c#'),
(22, 'c++'),
(3, 'CSS'),
(8, 'Dragon'),
(2, 'HTML'),
(24, 'Java'),
(1, 'JavaScript'),
(4, 'jQuery'),
(6, 'Json'),
(26, 'LolCode'),
(30, 'MySQL Workbench'),
(28, 'objective-c'),
(5, 'PHP'),
(25, 'Python'),
(7, 'Ruby on Rails'),
(29, 'sql-server'),
(9, 'XML');

-- --------------------------------------------------------

--
-- Structure de la table `tag_thread`
--

DROP TABLE IF EXISTS `tag_thread`;
CREATE TABLE IF NOT EXISTS `tag_thread` (
  `id_tag` int(10) unsigned NOT NULL,
  `id_thread` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_tag`,`id_thread`),
  KEY `IDX_FK_TAGTHREAD_THREAD` (`id_thread`),
  KEY `IDX_FK_TAGTHREAD_TAG` (`id_tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Vider la table avant d'insérer `tag_thread`
--

TRUNCATE TABLE `tag_thread`;
--
-- Contenu de la table `tag_thread`
--

INSERT INTO `tag_thread` (`id_tag`, `id_thread`) VALUES
(3, 1),
(9, 1),
(21, 2),
(8, 3),
(23, 4),
(8, 5),
(23, 5),
(22, 6),
(24, 6),
(28, 6),
(7, 7),
(27, 7),
(27, 8),
(29, 8),
(1, 9),
(2, 9),
(8, 9),
(21, 9),
(26, 9),
(28, 9),
(5, 10),
(24, 10),
(22, 11),
(6, 12),
(26, 12),
(21, 13),
(2, 14),
(9, 14),
(10, 14),
(26, 14);

-- --------------------------------------------------------

--
-- Structure de la table `thread`
--

DROP TABLE IF EXISTS `thread`;
CREATE TABLE IF NOT EXISTS `thread` (
  `id_thread` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_post_thread` int(10) unsigned NOT NULL COMMENT 'Main post of the thread',
  `title_thread` varchar(250) NOT NULL,
  PRIMARY KEY (`id_thread`),
  KEY `IDX_FK_THREAD_POST` (`id_post_thread`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Vider la table avant d'insérer `thread`
--

TRUNCATE TABLE `thread`;
--
-- Contenu de la table `thread`
--

INSERT INTO `thread` (`id_thread`, `id_post_thread`, `title_thread`) VALUES
(1, 1, 'AChartEngine - Bubble Chart - How Show Values?'),
(2, 2, 'How to organize Angular End2End test cases to when your test code is humongous?'),
(3, 3, 'Array of expiremental data filtering'),
(4, 4, 'Server Sent Events flush not working properly (using javax.servlet 3.0 and deploy in JBOSS 7.1.1 fin'),
(5, 5, 'how do I pass the key to ng-class in ng-repeat?'),
(6, 6, 'Solr range gap progressive'),
(7, 7, 'Sharepoint online upload document to subsite'),
(8, 8, 'convert Bootstrap code to asp.net c#'),
(9, 9, 'How can I get the created/modified date of a file in Shell Scripting?'),
(10, 10, 'how to disable autocompletion for quotation marks when writing html attributes in phpstorm?'),
(11, 11, 'Issues with a route configuration that has multiple consumer endpoints on the same ftp'),
(12, 12, 'my shark/spark is so slow, can you help me?'),
(13, 13, 'Delete many items in DynamoDB without effecting active users'),
(14, 14, 'Pros and cons of removing htaccess file for apache performance?');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login_user` varchar(250) NOT NULL,
  `password_user` char(40) NOT NULL COMMENT 'SHA1',
  `nickname_user` varchar(250) NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `IDX_UQ_USER_LOGIN` (`login_user`),
  UNIQUE KEY `IDX_UQ_USER_NICKNAME` (`nickname_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- Vider la table avant d'insérer `user`
--

TRUNCATE TABLE `user`;
--
-- Contenu de la table `user`
--

INSERT INTO `user` (`id_user`, `login_user`, `password_user`, `nickname_user`) VALUES
(1, 'thorin', 'bilbo', 'Thorin Oakenshield'),
(2, 'balin', 'bilbo', 'Balin'),
(3, 'bifur', 'bilbo', 'Bifur'),
(4, 'bofur', 'bilbo', 'Bofur'),
(5, 'bombur', 'bilbo', 'Bombur'),
(6, 'dori', 'bilbo', 'Dori'),
(7, 'dwalin', 'bilbo', 'Dwalin'),
(8, 'fili', 'bilbo', 'Fili'),
(9, 'gloin', 'bilbo', 'Gloin'),
(10, 'kili', 'bilbo', 'Kili'),
(11, 'nori', 'bilbo', 'Nori'),
(12, 'oin', 'bilbo', 'Oin'),
(13, 'ori', 'bilbo', 'Ori'),
(14, 'elrond', 'dwarfsucks', 'Elrond');

-- --------------------------------------------------------

--
-- Structure de la table `vote`
--

DROP TABLE IF EXISTS `vote`;
CREATE TABLE IF NOT EXISTS `vote` (
  `id_post` int(10) unsigned NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `value_vote` smallint(6) NOT NULL,
  PRIMARY KEY (`id_post`,`id_user`),
  KEY `IDX_FK_VOTE_POST` (`id_post`),
  KEY `IDX_FK_VOTE_USER` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Vider la table avant d'insérer `vote`
--

TRUNCATE TABLE `vote`;
--
-- Contenu de la table `vote`
--

INSERT INTO `vote` (`id_post`, `id_user`, `value_vote`) VALUES
(1, 2, 1),
(1, 3, 1),
(2, 2, 1),
(2, 3, 1),
(2, 4, -1),
(2, 5, 1),
(2, 6, 1),
(2, 7, -1),
(2, 8, 1),
(2, 9, 1),
(2, 10, 1),
(2, 11, -1),
(3, 2, 1),
(3, 3, 1),
(3, 4, -1),
(3, 5, 1),
(3, 6, 1),
(3, 7, -1),
(3, 8, -1),
(3, 9, 1),
(3, 10, 1),
(3, 11, -1),
(4, 1, 1),
(4, 2, 1),
(4, 3, 1),
(4, 4, 1),
(4, 5, 1),
(4, 6, 1),
(4, 7, 1),
(4, 8, 1),
(4, 9, 1),
(4, 10, 1),
(4, 11, 1),
(5, 1, -1),
(5, 2, 1),
(5, 3, 1),
(5, 4, 1),
(5, 5, -1),
(5, 6, 1),
(5, 7, 1),
(5, 8, 1),
(5, 9, 1),
(5, 10, -1),
(5, 11, 1),
(5, 12, 1),
(5, 13, -1),
(5, 14, 1),
(6, 1, -1),
(6, 2, 1),
(6, 3, 1),
(6, 4, 1),
(6, 5, -1),
(6, 6, 1),
(6, 7, 1),
(6, 8, 1),
(6, 9, 1),
(6, 10, -1),
(6, 11, 1),
(6, 12, 1),
(6, 13, -1),
(6, 14, 1),
(7, 1, -1),
(7, 2, 1),
(7, 3, 1),
(7, 4, 1),
(7, 5, -1),
(7, 6, 1),
(7, 7, 1),
(7, 8, 1),
(7, 9, 1),
(7, 10, -1),
(7, 11, 1),
(7, 12, 1),
(7, 13, -1),
(7, 14, 1),
(8, 1, -1),
(8, 2, -1),
(8, 3, 1),
(8, 4, 1),
(8, 5, -1),
(8, 6, -1),
(8, 7, 1),
(8, 8, -1),
(8, 9, 1),
(8, 10, -1),
(8, 11, -1),
(8, 12, -1),
(8, 13, -1),
(8, 14, 1),
(9, 1, -1),
(9, 2, 1),
(9, 3, 1),
(9, 4, 1),
(9, 5, -1),
(9, 6, 1),
(9, 7, 1),
(9, 8, 1),
(9, 9, 1),
(9, 10, -1),
(9, 11, 1),
(9, 12, 1),
(9, 13, -1),
(9, 14, 1),
(14, 1, -1),
(14, 2, 1),
(14, 3, 1),
(14, 4, 1),
(14, 5, -1),
(14, 6, 1),
(14, 7, 1),
(14, 8, 1),
(14, 9, 1),
(14, 10, -1),
(14, 11, 1),
(14, 12, 1),
(14, 13, -1),
(14, 14, 1),
(16, 4, 1),
(16, 8, 1),
(16, 12, 1),
(16, 13, 1),
(19, 2, 1),
(19, 5, 1),
(19, 6, 1);

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
  

set foreign_key_checks = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
