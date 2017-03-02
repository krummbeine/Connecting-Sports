-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 16. Jan 2017 um 00:00
-- Server Version: 5.5.38-0ubuntu0.14.04.1
-- PHP-Version: 5.5.9-1ubuntu4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `ConnectingSports`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bewertung`
--

CREATE TABLE IF NOT EXISTS `bewertung` (
  `bewertung_id` int(11) NOT NULL AUTO_INCREMENT,
  `von_mitglied_id` int(11) NOT NULL,
  `sportevent_id` int(11) NOT NULL,
  `war_anwesend` int(1) DEFAULT NULL,
  `war_sympathisch` int(1) DEFAULT NULL,
  `ueber_mitglied_id` int(11) NOT NULL,
  PRIMARY KEY (`bewertung_id`),
  KEY `mitglied_id` (`von_mitglied_id`),
  KEY `event_id` (`sportevent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;

--
-- Daten für Tabelle `bewertung`
--

INSERT INTO `bewertung` (`bewertung_id`, `von_mitglied_id`, `sportevent_id`, `war_anwesend`, `war_sympathisch`, `ueber_mitglied_id`) VALUES
(28, 33, 39, 1, 1, 41),
(29, 41, 39, 1, 1, 33),
(30, 30, 36, 1, 1, 33),
(31, 30, 36, 1, 1, 42),
(32, 30, 34, 1, 1, 33),
(33, 42, 36, 0, 1, 33),
(34, 42, 36, 0, -1, 30);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kommentar`
--

CREATE TABLE IF NOT EXISTS `kommentar` (
  `kommentar_id` int(11) NOT NULL AUTO_INCREMENT,
  `mitglied_id` int(11) DEFAULT NULL,
  `sportevent_id` int(11) DEFAULT NULL,
  `datum` timestamp NULL DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`kommentar_id`),
  KEY `event_id` (`sportevent_id`),
  KEY `mitglied_id` (`mitglied_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=177 ;

--
-- Daten für Tabelle `kommentar`
--

INSERT INTO `kommentar` (`kommentar_id`, `mitglied_id`, `sportevent_id`, `datum`, `text`) VALUES
(95, 0, 21, '2017-01-04 22:13:00', 'Mi mi mi mi mi'),
(98, 0, 1, '2017-01-09 21:37:00', 'Hi !!s'),
(160, 30, 31, '2017-01-14 23:41:00', 'Hallo :)'),
(162, 33, 31, '2017-01-14 23:46:00', 'Hi !'),
(166, 41, 39, '2017-01-15 18:08:00', 'Hallo hat wer lust'),
(168, 41, 39, '2017-01-15 18:11:00', 'Bist dabei ich nehm auch ein bier für den gipfel mit ;)'),
(169, 33, 39, '2017-01-15 18:11:00', 'Super - dann bis später :)!'),
(170, 42, 33, '2017-01-15 22:22:00', 'Hi !'),
(171, 42, 36, '2017-01-15 22:22:00', 'Hey :)'),
(172, 42, 35, '2017-01-15 22:22:00', 'Ho Hey !'),
(173, 30, 34, '2017-01-15 22:27:00', 'Hey Leute :) Wer hat Lust?'),
(174, 30, 35, '2017-01-15 22:27:00', 'Wow - cool, dass du mitsteigen willst!'),
(175, 33, 37, '2017-01-15 22:48:00', 'Freue mich aufs Event :)'),
(176, 33, 35, '2017-01-15 22:48:00', 'Ich mach auch mit !');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitglied`
--

CREATE TABLE IF NOT EXISTS `mitglied` (
  `mitglied_id` int(11) NOT NULL AUTO_INCREMENT,
  `pseudonym` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `passwort` varchar(255) DEFAULT NULL,
  `geburtstag` date DEFAULT NULL,
  `meine_stadt` varchar(255) DEFAULT NULL,
  `beschreibung` varchar(255) NOT NULL,
  PRIMARY KEY (`mitglied_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=43 ;

--
-- Daten für Tabelle `mitglied`
--

INSERT INTO `mitglied` (`mitglied_id`, `pseudonym`, `facebook_id`, `passwort`, `geburtstag`, `meine_stadt`, `beschreibung`) VALUES
(0, 'Admin', '0', '99c8ef576f385bc322564d5694df6fc2', '1993-10-09', '83024 Rosenheim', 'Danke, dass du Connecting Sports nutzt!'),
(30, 'Lina', '0', '99c8ef576f385bc322564d5694df6fc2', '1991-09-10', '83024 Rosenheim', 'Ich bin eine Blume! ^^'),
(33, 'Helbig', '0', '99c8ef576f385bc322564d5694df6fc2', '1993-10-09', '83024 Rosenheim', 'Bin ein Student aus Rosenheim und suche neue Leute zum gemeinsamen Sport und Spaß haben :) !'),
(34, 'Blume', '0', '99c8ef576f385bc322564d5694df6fc2', '1993-10-09', '83024 Rosenheim', 'Hey Ho! Ich bin da Weihnachtsmann!'),
(35, 'Emma', '0', '99c8ef576f385bc322564d5694df6fc2', '1994-05-01', '83334 Inzell', 'Ich bin neu hier :)'),
(36, 'Mia', '0', '99c8ef576f385bc322564d5694df6fc2', '1996-12-02', '83022 Rosenheim', 'Hallo :) Wie geht es euch?'),
(37, 'Joseph', '0', '99c8ef576f385bc322564d5694df6fc2', '1991-09-21', 'Traunstein', 'Hey! I''m a student from England ;)'),
(39, 'kilian1', '0', '1127ced909eb95dce2a713cbbd988b57', '1993-04-30', '83735 Bayrischzell', 'Ich bin neu hier und gehe gern auf den berg'),
(40, 'kiliw', '0', '99c8ef576f385bc322564d5694df6fc2', '1993-04-30', '83735 Bayrischzell', 'Hallo ich bin hier neu'),
(41, 'kilian', '0', '99c8ef576f385bc322564d5694df6fc2', '1993-04-30', '83735 Bayrischzell', 'Hallo, bin neu hier !'),
(42, 'Sommerlicht', '0', '99c8ef576f385bc322564d5694df6fc2', '1993-10-09', '83334 Inzell', 'Das Sommerlicht :) !');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sportart`
--

CREATE TABLE IF NOT EXISTS `sportart` (
  `sportart_id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(255) DEFAULT NULL,
  `bild_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`sportart_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Daten für Tabelle `sportart`
--

INSERT INTO `sportart` (`sportart_id`, `titel`, `bild_url`) VALUES
(1, 'Tennis', '/Fussball.jpg'),
(3, 'Eishockey', '/Eishockey.JPG'),
(4, 'Fahrradfahren', '/Fahrradfahren.JPG'),
(10, 'Bergsteigen', '/Berggehen.JPG'),
(11, 'Bergwandern', '/wandern.JPG'),
(12, 'Langlaufen', '/Langlauf.JPG'),
(13, 'Schwimmen', '/IMG_0383.JPG'),
(14, 'Alpinskifahren', '/Alpinskifahren.JPG'),
(15, 'Nordic Walking', '/Nordic Walk.jpg');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sportevent`
--

CREATE TABLE IF NOT EXISTS `sportevent` (
  `sportevent_id` int(11) NOT NULL AUTO_INCREMENT,
  `sportart_id` int(11) NOT NULL,
  `titel` varchar(255) DEFAULT NULL,
  `beschreibung` varchar(255) DEFAULT NULL,
  `ort` varchar(255) DEFAULT NULL,
  `startdatum` date DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `mitglied_id` int(11) NOT NULL,
  `startuhrzeit` time NOT NULL,
  `enduhrzeit` time NOT NULL,
  `enddatum` date NOT NULL,
  `adresse` varchar(100) NOT NULL,
  PRIMARY KEY (`sportevent_id`),
  KEY `sportart_id` (`sportart_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=40 ;

--
-- Daten für Tabelle `sportevent`
--

INSERT INTO `sportevent` (`sportevent_id`, `sportart_id`, `titel`, `beschreibung`, `ort`, `startdatum`, `level`, `mitglied_id`, `startuhrzeit`, `enduhrzeit`, `enddatum`, `adresse`) VALUES
(21, 1, 'Hochschul-Tennis', 'Spiele mit deinen Kommilitonen!', '83024 Rosenheim', '2017-01-16', '2', 0, '09:00:00', '10:30:00', '2017-01-16', 'Hochschulstr. 1'),
(24, 3, 'Eishockey-Treff 2017', 'Der Rentner Treff im altem Baggasää!', '82205 Gilching', '2017-01-15', '1', 30, '08:40:00', '08:50:00', '2017-01-16', 'Talbauernweg 1'),
(26, 4, 'Inn-Radeln', 'Chillig am Inn entlangradeln!', '83022 Rosenheim', '2017-01-19', '3', 30, '11:00:00', '13:00:00', '2017-01-19', 'Ellmaierstraße 27'),
(31, 14, 'Ski-Schule für Anfänger : Schnupperstunde', 'Lernen Sie spielend einfach Ski-Fahren mit uns - Eine Schnupperstunde gratis!', '6384 Waidring', '2017-01-16', '0', 30, '08:00:00', '09:00:00', '2017-01-17', 'Steinplatte'),
(33, 10, 'Traitenblicktour', 'Eine Tages Tour auf den Großen Traiten', '83098 Brannenburg', '2017-01-20', '4', 40, '08:00:00', '17:30:00', '2017-01-20', 'bierweg 4'),
(34, 10, 'Seebergtour', 'Eine halbtagestour auf den Seeberg', '83735 Bayrischzell', '2016-01-30', '2', 0, '08:15:00', '12:15:00', '2016-01-30', 'Seebergstraße 10'),
(35, 10, 'Schliersbergalm', 'Hüttenausflug auf die Schliersbergalm', '83727 Schliersee', '2017-02-02', '1', 40, '10:00:00', '15:00:00', '2017-02-02', 'Bahnhof 10'),
(36, 10, 'Extremtour mit Klettersteig', 'Bin ein guter Sportler und will Klettern', 'Rosenheim', '2016-02-09', '4', 0, '15:00:00', '19:00:00', '2016-02-09', 'Lessingstr., 10'),
(37, 10, 'Bergtour ans Sudelfeld', 'Wandern im Schnee', '83735 Bayrischzell', '2017-12-18', '3', 0, '19:00:00', '21:00:00', '2017-12-18', 'Lehrer-vogl str 7'),
(38, 10, 'Herbsttour', 'Den goldenen Herbst genießen', '83735 Bayrischzell', '2016-10-10', '0', 0, '10:00:00', '15:00:00', '2016-10-10', 'Lehrer-vogl str 7'),
(39, 10, 'Mount Everest Bergtour', 'Bitte packt Luft mit ein, es wird etwas "atemlos" da oben ;)', '83735 Bayrischzell', '2017-01-18', '6', 0, '08:00:00', '16:00:00', '2017-01-18', 'Lehrer-vogl str 7');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v_mitglied_sportevent`
--

CREATE TABLE IF NOT EXISTS `v_mitglied_sportevent` (
  `sportevent_id` int(11) NOT NULL,
  `mitglied_id` int(11) NOT NULL,
  PRIMARY KEY (`sportevent_id`,`mitglied_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `v_mitglied_sportevent`
--

INSERT INTO `v_mitglied_sportevent` (`sportevent_id`, `mitglied_id`) VALUES
(21, 30),
(21, 33),
(31, 30),
(33, 30),
(33, 33),
(33, 42),
(34, 30),
(34, 33),
(35, 30),
(35, 33),
(35, 42),
(36, 30),
(36, 33),
(36, 42),
(37, 30),
(37, 33),
(38, 30),
(39, 30),
(39, 33),
(39, 41);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `v_sportart_mitglied`
--

CREATE TABLE IF NOT EXISTS `v_sportart_mitglied` (
  `sportart_id` int(11) NOT NULL,
  `mitglied_id` int(11) NOT NULL,
  `beschreibung` varchar(255) DEFAULT NULL,
  `level` int(1) NOT NULL,
  PRIMARY KEY (`sportart_id`,`mitglied_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `v_sportart_mitglied`
--

INSERT INTO `v_sportart_mitglied` (`sportart_id`, `mitglied_id`, `beschreibung`, `level`) VALUES
(1, 30, 'Ich bin ganz gut im Tennis eigentlich :D !', 1),
(4, 33, 'Bin ganz gut im Radeln - aber kein Profi!', 2),
(10, 0, NULL, 1),
(10, 30, 'Voll der PRO im BERGELN!', 0),
(10, 33, 'Schnaufe ganz schön beim Bergsteigen ^^', 0),
(10, 41, NULL, 1),
(11, 30, NULL, 1),
(11, 39, NULL, 1),
(13, 33, 'Im Schwimmen bin ich voll der Pro :P', 6),
(14, 30, 'Fahre gern Ski!', 5);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `sportevent`
--
ALTER TABLE `sportevent`
  ADD CONSTRAINT `sportevent_ibfk_1` FOREIGN KEY (`sportart_id`) REFERENCES `sportart` (`sportart_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
