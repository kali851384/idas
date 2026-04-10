-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 10. Apr 2026 um 15:10
-- Server-Version: 10.4.32-MariaDB
-- PHP-Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `idas`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_account`
--

CREATE TABLE `admin_account` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(40) NOT NULL,
  `passwort` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `admin_account`
--

INSERT INTO `admin_account` (`admin_id`, `email`, `passwort`) VALUES
(2, 'admin@gmail.com', '$2y$10$r7cZNdVmxi0YoU4K7MvNkesAO3Eg2yXV3RIjHXQix/4.8uLC/zDSG'),
(3, 'aa@gmail.com', '$2y$10$nQl2f.r5lqfGWzm8VUonK.OxIPMzw0Cirn.VADLqL5wy9d0g3NbXW');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `app_sessions`
--

CREATE TABLE `app_sessions` (
  `token` varchar(64) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `erstellt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `arzt`
--

CREATE TABLE `arzt` (
  `arzt_id` int(11) NOT NULL,
  `name` varchar(40) NOT NULL,
  `fachbereich_id` int(11) NOT NULL,
  `telefonnummer` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `addresse` varchar(255) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `arzt`
--

INSERT INTO `arzt` (`arzt_id`, `name`, `fachbereich_id`, `telefonnummer`, `email`, `fax`, `addresse`, `foto`) VALUES
(1, 'Dr. Justin Times', 1, '+49 511 1234567', 'justin.times@clinic.de', '+49 511 1234568', 'Brühlstraße 85, 30169 Hannover', NULL),
(2, 'Dr. Holly Daze', 2, '+49 511 2345678', 'holly.daze@clinic.de', '+49 511 2345679', 'Adenauerallee 201, 30175 Hannover', NULL),
(3, 'Dr. Willie Makeit', 3, '+49 511 3456789', 'willie.makeit@clinic.de', '+49 511 3456790', 'Adenauerallee 201, 30175 Hannover', NULL),
(4, 'Dr. Al Dente', 4, '+49 511 4567890', 'al.dente@clinic.de', '+49 511 4567891', 'Humboldtstraße 63, 30167 Hannover', NULL),
(5, 'Dr. Emma Grate', 5, '+49 511 5678901', 'emma.grate@clinic.de', '+49 511 5678902', 'Klingerstraße 22, 30175 Hannover', NULL),
(6, 'Dr. Mae Day', 6, '+49 511 6789012', 'mae.day@clinic.de', '+49 511 6789013', 'Jakobistraße 58, 30163 Hannover', NULL),
(7, 'Dr. Ali Gaither', 7, '+49 511 7890123', 'ali.gaither@clinic.de', '+49 511 7890124', 'Hildesheimer Straße 256, 30173 Hannover', NULL),
(8, 'Dr. Oliver Figma', 8, '+49 511 8901234', 'oliver.figma@clinic.de', '+49 511 8901235', 'Bödekerstraße 102, 30161 Hannover', NULL),
(9, 'Dr. Walter White', 9, '+49 511 9012345', 'walter.white@clinic.de', '+49 511 9012346', 'Podbielskistraße 134, 30165 Hannover', NULL),
(10, 'Dr. Reed Richard', 10, '+49 511 0123456', 'reed.richard@clinic.de', '+49 511 0123457', 'Ricklinger Stadtweg 165, 30459 Hannover', NULL),
(11, 'Dr. Sue Storm', 11, '+49 511 1234560', 'sue.storm@clinic.de', '+49 511 1234561', 'Jakobistraße 58, 30163 Hannover', NULL),
(12, 'Dr. Robert F. Kennedy Jr.', 12, '+49 511 2345671', 'robert.kennedy@clinic.de', '+49 511 2345672', 'Podbielskistraße 134, 30165 Hannover', NULL),
(13, 'Dr. Ice Spice', 13, '+49 511 3456782', 'ice.spice@clinic.de', '+49 511 3456783', 'Voßstraße 47, 30161 Hannover', NULL),
(14, 'Dr. North West', 14, '+49 511 4567893', 'north.west@clinic.de', '+49 511 4567894', 'Ricklinger Stadtweg 165, 30459 Hannover', NULL),
(15, 'Dr. Ben Dover', 15, '+49 511 5678904', 'ben.dover@clinic.de', '+49 511 5678905', 'Bödekerstraße 102, 30161 Hannover', NULL),
(16, 'Dr. Richie Poore', 16, '+49 511 6789015', 'richie.poore@clinic.de', '+49 511 6789016', 'Bornumer Straße 78, 30453 Hannover', NULL),
(17, 'Dr. Ima Foxx', 17, '+49 511 7890126', 'ima.foxx@clinic.de', '+49 511 7890127', 'Wedekindstraße 97, 30161 Hannover', NULL),
(18, 'Dr. Ima Pigg', 18, '+49 511 8901237', 'ima.pigg@clinic.de', '+49 511 8901238', 'Adenauerallee 201, 30175 Hannover', NULL),
(19, 'Dr. Bonnie Blue', 19, '+49 511 9012348', 'bonnie.blue@clinic.de', '+49 511 9012349', 'Wedekindstraße 97, 30161 Hannover', NULL),
(20, 'Dr. Lou Natic', 20, '+49 511 0123459', 'lou.natic@clinic.de', '+49 511 0123460', 'Hildesheimer Straße 256, 30173 Hannover', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `diagnose`
--

CREATE TABLE `diagnose` (
  `diagnose_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `beschreibung` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `diagnose`
--

INSERT INTO `diagnose` (`diagnose_id`, `patient_id`, `datum`, `name`, `beschreibung`) VALUES
(1, 1, '2026-04-10', 'dsa', 'dsadsa'),
(2, 2, '2026-04-10', 'das', 'gf');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `diagnosedet`
--

CREATE TABLE `diagnosedet` (
  `diagnose_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fachbereich`
--

CREATE TABLE `fachbereich` (
  `fachbereich_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `fachbereich`
--

INSERT INTO `fachbereich` (`fachbereich_id`, `name`) VALUES
(1, 'Allgemeinmedizin'),
(2, 'Kardiologie'),
(3, 'Dermatologie'),
(4, 'Neurologie'),
(5, 'Orthopädie'),
(6, 'Gynäkologie'),
(7, 'Pädiatrie'),
(8, 'Psychiatrie'),
(9, 'Onkologie'),
(10, 'Urologie'),
(11, 'HNO-Heilkunde'),
(12, 'Radiologie'),
(13, 'Anästhesiologie'),
(14, 'Chirurgie'),
(15, 'Gastroenterologie'),
(16, 'Endokrinologie'),
(17, 'Pulmonologie'),
(18, 'Rheumatologie'),
(19, 'Hämatologie'),
(20, 'Plastische Chirurgie');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `kontakt_nachrichten`
--

CREATE TABLE `kontakt_nachrichten` (
  `kontakt_id` int(11) NOT NULL,
  `vorname` varchar(50) NOT NULL,
  `nachname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefon` varchar(30) DEFAULT NULL,
  `betreff` varchar(100) DEFAULT NULL,
  `nachricht` text NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('offen','erledigt') NOT NULL DEFAULT 'offen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `kontakt_nachrichten`
--

INSERT INTO `kontakt_nachrichten` (`kontakt_id`, `vorname`, `nachname`, `email`, `telefon`, `betreff`, `nachricht`, `datum`, `status`) VALUES
(1, 'fsad', 'mknlk', 'mklm', NULL, '1', 'cyx', '0000-00-00 00:00:00', 'offen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitarbeiter`
--

CREATE TABLE `mitarbeiter` (
  `mitarbeiter_id` int(11) NOT NULL,
  `vorname` varchar(40) NOT NULL,
  `nachname` varchar(40) NOT NULL,
  `geburtsdatum` date NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `patient`
--

CREATE TABLE `patient` (
  `patient_id` int(11) NOT NULL,
  `vorname` varchar(30) NOT NULL,
  `nachname` varchar(30) NOT NULL,
  `geburtsdatum` date NOT NULL,
  `wohnort` varchar(50) DEFAULT NULL,
  `plz` varchar(10) DEFAULT NULL,
  `adresse` varchar(70) DEFAULT NULL,
  `email` varchar(70) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `geschlecht` varchar(10) DEFAULT NULL,
  `passwort` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `patient`
--

INSERT INTO `patient` (`patient_id`, `vorname`, `nachname`, `geburtsdatum`, `wohnort`, `plz`, `adresse`, `email`, `telefon`, `geschlecht`, `passwort`) VALUES
(1, 'Mohammad Aein', 'Mirzayan', '2005-07-30', 'hannover', NULL, 'klinger straße9', 'mohammadmirzayan20@gmail.com', NULL, 'm', '123'),
(2, 'dsad', 'dfskmflk', '0000-00-00', '', '', '', 'sasdffs@gmail.com', '', '', '$2y$10$wEPPjvkvz0ktwFkKsca3IuJoepQ16xMMofhis6jw0FB2e8ibyQF0K');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `support`
--

CREATE TABLE `support` (
  `ticket_id` int(11) NOT NULL,
  `kontakt_id` int(11) DEFAULT NULL,
  `mitarbeiter_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `problembeschreibung` text NOT NULL,
  `betreff` varchar(120) DEFAULT NULL,
  `status` enum('offen','in_bearbeitung','geschlossen') NOT NULL DEFAULT 'offen',
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `antwort` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `symptomdet`
--

CREATE TABLE `symptomdet` (
  `symptomdet_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL,
  `fachbereich_id` int(11) NOT NULL,
  `punkte` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `symptomdet`
--

INSERT INTO `symptomdet` (`symptomdet_id`, `symptom_id`, `fachbereich_id`, `punkte`) VALUES
(1, 1, 1, 3),
(2, 1, 7, 2),
(3, 1, 17, 2),
(4, 1, 19, 1),
(5, 1, 9, 1),
(6, 2, 17, 3),
(7, 2, 11, 2),
(8, 2, 1, 2),
(9, 2, 2, 1),
(10, 2, 7, 1),
(11, 3, 4, 3),
(12, 3, 1, 2),
(13, 3, 11, 1),
(14, 3, 8, 1),
(15, 4, 1, 2),
(16, 4, 16, 2),
(17, 4, 8, 2),
(18, 4, 19, 2),
(19, 4, 9, 1),
(24, 6, 15, 3),
(25, 6, 1, 2),
(26, 6, 4, 1),
(27, 6, 7, 1),
(28, 7, 15, 3),
(29, 7, 1, 2),
(30, 7, 7, 1),
(31, 8, 15, 3),
(32, 8, 14, 2),
(33, 8, 1, 2),
(34, 8, 6, 1),
(35, 9, 2, 3),
(36, 9, 17, 2),
(37, 9, 14, 2),
(38, 9, 1, 1),
(39, 10, 17, 3),
(40, 10, 2, 3),
(41, 10, 1, 1),
(42, 10, 13, 1),
(43, 11, 4, 3),
(44, 11, 11, 2),
(45, 11, 2, 2),
(46, 11, 1, 1),
(47, 12, 3, 3),
(48, 12, 1, 1),
(49, 12, 7, 1),
(50, 12, 18, 1),
(51, 13, 11, 3),
(52, 13, 1, 2),
(53, 13, 7, 1),
(54, 14, 11, 3),
(55, 14, 1, 2),
(56, 14, 17, 1),
(57, 15, 11, 3),
(58, 15, 1, 2),
(59, 15, 17, 1),
(60, 16, 5, 2),
(61, 16, 18, 3),
(62, 16, 1, 1),
(63, 16, 4, 1),
(64, 17, 18, 3),
(65, 17, 5, 3),
(66, 17, 1, 1),
(67, 18, 5, 3),
(68, 18, 4, 2),
(69, 18, 18, 2),
(70, 18, 1, 1),
(71, 19, 18, 2),
(72, 19, 5, 2),
(73, 19, 14, 2),
(74, 19, 2, 1),
(75, 19, 1, 1),
(76, 20, 9, 3),
(77, 20, 16, 2),
(78, 20, 15, 2),
(79, 20, 1, 1),
(80, 21, 16, 3),
(81, 21, 1, 2),
(82, 21, 8, 1),
(83, 22, 8, 3),
(84, 22, 4, 2),
(85, 22, 1, 1),
(86, 23, 8, 3),
(87, 23, 4, 1),
(88, 23, 1, 1),
(89, 24, 8, 3),
(90, 24, 2, 1),
(91, 24, 1, 1),
(92, 25, 2, 3),
(93, 25, 8, 2),
(94, 25, 16, 1),
(95, 25, 1, 1),
(96, 26, 4, 3),
(97, 26, 16, 2),
(98, 26, 8, 1),
(99, 27, 4, 2),
(100, 27, 16, 2),
(101, 27, 1, 1),
(102, 28, 11, 3),
(103, 28, 4, 1),
(104, 29, 11, 3),
(105, 29, 1, 1),
(106, 29, 7, 1),
(107, 30, 1, 2),
(108, 30, 11, 1),
(109, 31, 15, 3),
(110, 31, 1, 2),
(111, 31, 16, 1),
(112, 32, 15, 3),
(113, 32, 1, 2),
(114, 33, 15, 3),
(115, 33, 1, 2),
(116, 34, 3, 3),
(117, 34, 1, 1),
(118, 34, 18, 1),
(119, 35, 3, 2),
(120, 35, 16, 3),
(121, 35, 1, 1),
(122, 36, 3, 3),
(123, 36, 16, 2),
(124, 36, 1, 1),
(125, 37, 4, 3),
(126, 37, 8, 2),
(127, 37, 1, 1),
(128, 38, 4, 3),
(129, 38, 8, 2),
(130, 38, 1, 1),
(131, 39, 4, 3),
(132, 39, 1, 1),
(133, 40, 4, 3),
(134, 40, 5, 2),
(135, 40, 18, 1),
(136, 41, 4, 3),
(137, 41, 5, 2),
(138, 41, 18, 1),
(139, 42, 1, 3),
(140, 42, 19, 2),
(141, 42, 9, 1),
(142, 43, 9, 2),
(143, 43, 19, 2),
(144, 43, 16, 2),
(145, 43, 1, 1),
(146, 44, 16, 3),
(147, 44, 1, 2),
(148, 44, 10, 1),
(149, 45, 10, 3),
(150, 45, 16, 3),
(151, 45, 1, 1),
(152, 46, 16, 2),
(153, 46, 1, 2),
(154, 46, 11, 1),
(155, 47, 19, 3),
(156, 47, 9, 2),
(157, 47, 1, 1),
(158, 48, 19, 3),
(159, 48, 11, 2),
(160, 48, 1, 1),
(161, 49, 15, 3),
(162, 49, 9, 2),
(163, 49, 19, 2),
(164, 49, 1, 1),
(165, 50, 15, 3),
(166, 50, 10, 2),
(167, 50, 19, 1),
(168, 50, 1, 1),
(169, 5, 1, 1),
(170, 5, 15, 1),
(171, 5, 6, 1),
(172, 5, 4, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `symptome`
--

CREATE TABLE `symptome` (
  `symptom_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `symptome`
--

INSERT INTO `symptome` (`symptom_id`, `name`) VALUES
(1, 'Fieber'),
(2, 'Husten'),
(3, 'Kopfschmerzen'),
(4, 'Müdigkeit'),
(5, 'Übelkeit'),
(6, 'Erbrechen'),
(7, 'Durchfall'),
(8, 'Bauchschmerzen'),
(9, 'Brustschmerzen'),
(10, 'Atemnot'),
(11, 'Schwindel'),
(12, 'Hautausschlag'),
(13, 'Halsschmerzen'),
(14, 'Laufende Nase'),
(15, 'Nasenverstopfung'),
(16, 'Muskelschmerzen'),
(17, 'Gelenkschmerzen'),
(18, 'Rückenschmerzen'),
(19, 'Schwellung'),
(20, 'Unerklärter Gewichtsverlust'),
(21, 'Unerklärte Gewichtszunahme'),
(22, 'Schlaflosigkeit'),
(23, 'Depression'),
(24, 'Angst'),
(25, 'Herzrasen'),
(26, 'Zittern'),
(27, 'Verschwommenes Sehen'),
(28, 'Hörverlust'),
(29, 'Ohrenschmerzen'),
(30, 'Zahnweh'),
(31, 'Verstopfung'),
(32, 'Bauchblähungen'),
(33, 'Sodbrennen'),
(34, 'Juckreiz'),
(35, 'Haarausfall'),
(36, 'Spröde Nägel'),
(37, 'Gedächtnisverlust'),
(38, 'Verwirrung'),
(39, 'Krampfanfälle'),
(40, 'Taubheitsgefühl'),
(41, 'Muskelkraftverlust'),
(42, 'Schüttelfrost'),
(43, 'Nachtschweiß'),
(44, 'Starker Durst'),
(45, 'Häufiges Wasserlassen'),
(46, 'Trockener Mund'),
(47, 'Leichte Blutergüsse'),
(48, 'Blutende Zahnfleisch'),
(49, 'Gelbsucht'),
(50, 'Dunkler Urin');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `termin`
--

CREATE TABLE `termin` (
  `termin_id` int(11) NOT NULL,
  `arzt_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  `beschreibung` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `termin`
--

INSERT INTO `termin` (`termin_id`, `arzt_id`, `patient_id`, `datum`, `beschreibung`) VALUES
(1, 5, 1, '2026-04-10 11:52:00', 'dsadsda');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vorerkrankungen`
--

CREATE TABLE `vorerkrankungen` (
  `vorerkrankung_id` int(11) NOT NULL,
  `erkrankungsname` varchar(50) NOT NULL,
  `beschreibung` text DEFAULT NULL,
  `patient_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `admin_account`
--
ALTER TABLE `admin_account`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `app_sessions`
--
ALTER TABLE `app_sessions`
  ADD PRIMARY KEY (`token`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indizes für die Tabelle `arzt`
--
ALTER TABLE `arzt`
  ADD PRIMARY KEY (`arzt_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fachbereich_id` (`fachbereich_id`);

--
-- Indizes für die Tabelle `diagnose`
--
ALTER TABLE `diagnose`
  ADD PRIMARY KEY (`diagnose_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indizes für die Tabelle `diagnosedet`
--
ALTER TABLE `diagnosedet`
  ADD PRIMARY KEY (`diagnose_id`,`symptom_id`),
  ADD KEY `symptom_id` (`symptom_id`);

--
-- Indizes für die Tabelle `fachbereich`
--
ALTER TABLE `fachbereich`
  ADD PRIMARY KEY (`fachbereich_id`);

--
-- Indizes für die Tabelle `kontakt_nachrichten`
--
ALTER TABLE `kontakt_nachrichten`
  ADD PRIMARY KEY (`kontakt_id`);

--
-- Indizes für die Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD PRIMARY KEY (`mitarbeiter_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indizes für die Tabelle `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `mitarbeiter_id` (`mitarbeiter_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `kontakt_id` (`kontakt_id`);

--
-- Indizes für die Tabelle `symptomdet`
--
ALTER TABLE `symptomdet`
  ADD PRIMARY KEY (`symptomdet_id`),
  ADD KEY `symptom_id` (`symptom_id`),
  ADD KEY `fachbereich_id` (`fachbereich_id`);

--
-- Indizes für die Tabelle `symptome`
--
ALTER TABLE `symptome`
  ADD PRIMARY KEY (`symptom_id`);

--
-- Indizes für die Tabelle `termin`
--
ALTER TABLE `termin`
  ADD PRIMARY KEY (`termin_id`),
  ADD KEY `arzt_id` (`arzt_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indizes für die Tabelle `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  ADD PRIMARY KEY (`vorerkrankung_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `admin_account`
--
ALTER TABLE `admin_account`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `arzt`
--
ALTER TABLE `arzt`
  MODIFY `arzt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT für Tabelle `diagnose`
--
ALTER TABLE `diagnose`
  MODIFY `diagnose_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `fachbereich`
--
ALTER TABLE `fachbereich`
  MODIFY `fachbereich_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `kontakt_nachrichten`
--
ALTER TABLE `kontakt_nachrichten`
  MODIFY `kontakt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  MODIFY `mitarbeiter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `support`
--
ALTER TABLE `support`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `symptomdet`
--
ALTER TABLE `symptomdet`
  MODIFY `symptomdet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT für Tabelle `symptome`
--
ALTER TABLE `symptome`
  MODIFY `symptom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT für Tabelle `termin`
--
ALTER TABLE `termin`
  MODIFY `termin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  MODIFY `vorerkrankung_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `arzt`
--
ALTER TABLE `arzt`
  ADD CONSTRAINT `arzt_ibfk_1` FOREIGN KEY (`fachbereich_id`) REFERENCES `fachbereich` (`fachbereich_id`);

--
-- Constraints der Tabelle `diagnose`
--
ALTER TABLE `diagnose`
  ADD CONSTRAINT `diagnose_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `diagnosedet`
--
ALTER TABLE `diagnosedet`
  ADD CONSTRAINT `diagnosedet_ibfk_1` FOREIGN KEY (`diagnose_id`) REFERENCES `diagnose` (`diagnose_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `diagnosedet_ibfk_2` FOREIGN KEY (`symptom_id`) REFERENCES `symptome` (`symptom_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `support`
--
ALTER TABLE `support`
  ADD CONSTRAINT `support_ibfk_1` FOREIGN KEY (`mitarbeiter_id`) REFERENCES `mitarbeiter` (`mitarbeiter_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `support_ibfk_3` FOREIGN KEY (`kontakt_id`) REFERENCES `kontakt_nachrichten` (`kontakt_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints der Tabelle `symptomdet`
--
ALTER TABLE `symptomdet`
  ADD CONSTRAINT `symptomdet_ibfk_1` FOREIGN KEY (`symptom_id`) REFERENCES `symptome` (`symptom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `symptomdet_ibfk_2` FOREIGN KEY (`fachbereich_id`) REFERENCES `fachbereich` (`fachbereich_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `termin`
--
ALTER TABLE `termin`
  ADD CONSTRAINT `termin_ibfk_1` FOREIGN KEY (`arzt_id`) REFERENCES `arzt` (`arzt_id`),
  ADD CONSTRAINT `termin_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  ADD CONSTRAINT `vorerkrankungen_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
