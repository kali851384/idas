-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2026 at 02:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `idas`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_account`
--

CREATE TABLE `admin_account` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(40) NOT NULL,
  `passwort` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_account`
--

INSERT INTO `admin_account` (`admin_id`, `email`, `passwort`) VALUES
(2, 'admin@gmail.com', '$2y$10$r7cZNdVmxi0YoU4K7MvNkesAO3Eg2yXV3RIjHXQix/4.8uLC/zDSG'),
(3, 'aa@gmail.com', '$2y$10$nQl2f.r5lqfGWzm8VUonK.OxIPMzw0Cirn.VADLqL5wy9d0g3NbXW'),
(2, 'admin@gmail.com', '$2y$10$r7cZNdVmxi0YoU4K7MvNkesAO3Eg2yXV3RIjHXQix/4.8uLC/zDSG'),
(3, 'aa@gmail.com', '$2y$10$nQl2f.r5lqfGWzm8VUonK.OxIPMzw0Cirn.VADLqL5wy9d0g3NbXW');

-- --------------------------------------------------------

--
-- Table structure for table `app_sessions`
--

CREATE TABLE `app_sessions` (
  `token` varchar(64) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `erstellt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_sessions`
--

INSERT INTO `app_sessions` (`token`, `patient_id`, `erstellt`) VALUES
('aa1ab1dec7c643d8ec53e6bcd5487aba6ac25ec6496539ffc3ebe2f800472645', 1, '2026-04-19 22:32:23');

-- --------------------------------------------------------

--
-- Table structure for table `arzt`
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
-- Dumping data for table `arzt`
--

INSERT INTO `arzt` (`arzt_id`, `name`, `fachbereich_id`, `telefonnummer`, `email`, `fax`, `addresse`, `foto`) VALUES
(4, 'Dr. Al Dente', 4, '+49 511 4567890', 'al.dente@clinic.de', '+49 511 4567891', 'Humboldtstraße 63, 30167 Hannover', NULL),
(7, 'Dr. Ali Gaither', 7, '+49 511 7890123', 'ali.gaither@clinic.de', '+49 511 7890124', 'Hildesheimer Straße 256, 30173 Hannover', NULL),
(15, 'Dr. Ben Dover', 15, '+49 511 5678904', 'ben.dover@clinic.de', '+49 511 5678905', 'Bödekerstraße 102, 30161 Hannover', NULL),
(19, 'Dr. Bonnie Blue', 19, '+49 511 9012348', 'bonnie.blue@clinic.de', '+49 511 9012349', 'Wedekindstraße 97, 30161 Hannover', NULL),
(5, 'Dr. Emma Grate', 5, '+49 511 5678901', 'emma.grate@clinic.de', '+49 511 5678902', 'Klingerstraße 22, 30175 Hannover', NULL),
(2, 'Dr. Holly Daze', 2, '+49 511 2345678', 'holly.daze@clinic.de', '+49 511 2345679', 'Adenauerallee 201, 30175 Hannover', NULL),
(13, 'Dr. Ice Spice', 13, '+49 511 3456782', 'ice.spice@clinic.de', '+49 511 3456783', 'Voßstraße 47, 30161 Hannover', NULL),
(17, 'Dr. Ima Foxx', 17, '+49 511 7890126', 'ima.foxx@clinic.de', '+49 511 7890127', 'Wedekindstraße 97, 30161 Hannover', NULL),
(18, 'Dr. Ima Pigg', 18, '+49 511 8901237', 'ima.pigg@clinic.de', '+49 511 8901238', 'Adenauerallee 201, 30175 Hannover', NULL),
(1, 'Dr. Justin Times', 1, '+49 511 1234567', 'justin.times@clinic.de', '+49 511 1234568', 'Brühlstraße 85, 30169 Hannover', NULL),
(20, 'Dr. Lou Natic', 20, '+49 511 0123459', 'lou.natic@clinic.de', '+49 511 0123460', 'Hildesheimer Straße 256, 30173 Hannover', NULL),
(6, 'Dr. Mae Day', 6, '+49 511 6789012', 'mae.day@clinic.de', '+49 511 6789013', 'Jakobistraße 58, 30163 Hannover', NULL),
(14, 'Dr. North West', 14, '+49 511 4567893', 'north.west@clinic.de', '+49 511 4567894', 'Ricklinger Stadtweg 165, 30459 Hannover', NULL),
(8, 'Dr. Oliver Figma', 8, '+49 511 8901234', 'oliver.figma@clinic.de', '+49 511 8901235', 'Bödekerstraße 102, 30161 Hannover', NULL),
(10, 'Dr. Reed Richard', 10, '+49 511 0123456', 'reed.richard@clinic.de', '+49 511 0123457', 'Ricklinger Stadtweg 165, 30459 Hannover', NULL),
(16, 'Dr. Richie Poore', 16, '+49 511 6789015', 'richie.poore@clinic.de', '+49 511 6789016', 'Bornumer Straße 78, 30453 Hannover', NULL),
(12, 'Dr. Robert F. Kennedy Jr.', 12, '+49 511 2345671', 'robert.kennedy@clinic.de', '+49 511 2345672', 'Podbielskistraße 134, 30165 Hannover', NULL),
(11, 'Dr. Sue Storm', 11, '+49 511 1234560', 'sue.storm@clinic.de', '+49 511 1234561', 'Jakobistraße 58, 30163 Hannover', NULL),
(9, 'Dr. Walter White', 9, '+49 511 9012345', 'walter.white@clinic.de', '+49 511 9012346', 'Podbielskistraße 134, 30165 Hannover', NULL),
(3, 'Dr. Willie Makeit', 3, '+49 511 3456789', 'willie.makeit@clinic.de', '+49 511 3456790', 'Adenauerallee 201, 30175 Hannover', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `diagnose`
--

CREATE TABLE `diagnose` (
  `diagnose_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `beschreibung` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diagnose`
--

INSERT INTO `diagnose` (`diagnose_id`, `patient_id`, `datum`, `name`, `beschreibung`) VALUES
(1, 1, '2026-04-10', 'dsa', 'dsadsa'),
(2, 2, '2026-04-10', 'das', 'gf'),
(1, 1, '2026-04-10', 'dsa', 'dsadsa'),
(2, 2, '2026-04-10', 'das', 'gf');

-- --------------------------------------------------------

--
-- Table structure for table `diagnosedet`
--

CREATE TABLE `diagnosedet` (
  `diagnose_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fachbereich`
--

CREATE TABLE `fachbereich` (
  `fachbereich_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fachbereich`
--

INSERT INTO `fachbereich` (`fachbereich_id`, `name`) VALUES
(1, 'Allgemeinmedizin'),
(13, 'Anästhesiologie'),
(14, 'Chirurgie'),
(3, 'Dermatologie'),
(16, 'Endokrinologie'),
(15, 'Gastroenterologie'),
(6, 'Gynäkologie'),
(19, 'Hämatologie'),
(11, 'HNO-Heilkunde'),
(2, 'Kardiologie'),
(4, 'Neurologie'),
(9, 'Onkologie'),
(5, 'Orthopädie'),
(7, 'Pädiatrie'),
(20, 'Plastische Chirurgie'),
(8, 'Psychiatrie'),
(17, 'Pulmonologie'),
(12, 'Radiologie'),
(18, 'Rheumatologie'),
(10, 'Urologie');

-- --------------------------------------------------------

--
-- Table structure for table `kontakt_nachrichten`
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
-- Dumping data for table `kontakt_nachrichten`
--

INSERT INTO `kontakt_nachrichten` (`kontakt_id`, `vorname`, `nachname`, `email`, `telefon`, `betreff`, `nachricht`, `datum`, `status`) VALUES
(1, 'fsad', 'mknlk', 'mklm', NULL, '1', 'cyx', '0000-00-00 00:00:00', 'offen'),
(1, 'fsad', 'mknlk', 'mklm', NULL, '1', 'cyx', '0000-00-00 00:00:00', 'offen');

-- --------------------------------------------------------

--
-- Table structure for table `mitarbeiter`
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
-- Table structure for table `patient`
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
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `vorname`, `nachname`, `geburtsdatum`, `wohnort`, `plz`, `adresse`, `email`, `telefon`, `geschlecht`, `passwort`) VALUES
(1, 'Mohammad Aein', 'Mirzayan', '2005-07-30', 'hannover', NULL, 'klinger straße9', 'mohammadmirzayan20@gmail.com', NULL, 'm', '$2y$12$WBQd4uy9KL.sU942a9IHO.3A9VheGxuUlXkE1IKA3d9R86eqfeZzi'),
(2, 'dsad', 'dfskmflk', '0000-00-00', '', '', '', 'sasdffs@gmail.com', '', '', '$2y$10$wEPPjvkvz0ktwFkKsca3IuJoepQ16xMMofhis6jw0FB2e8ibyQF0K'),
(1, 'Mohammad Aein', 'Mirzayan', '2005-07-30', 'hannover', NULL, 'klinger straße9', 'mohammadmirzayan20@gmail.com', NULL, 'm', '$2y$12$WBQd4uy9KL.sU942a9IHO.3A9VheGxuUlXkE1IKA3d9R86eqfeZzi'),
(2, 'dsad', 'dfskmflk', '0000-00-00', '', '', '', 'sasdffs@gmail.com', '', '', '$2y$10$wEPPjvkvz0ktwFkKsca3IuJoepQ16xMMofhis6jw0FB2e8ibyQF0K');

-- --------------------------------------------------------

--
-- Table structure for table `support`
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

--
-- Dumping data for table `support`
--

INSERT INTO `support` (`ticket_id`, `kontakt_id`, `mitarbeiter_id`, `patient_id`, `problembeschreibung`, `betreff`, `status`, `datum`, `antwort`) VALUES
(0, NULL, NULL, 1, 'dsa', 'das', 'offen', '2026-04-19 21:58:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `symptomdet`
--

CREATE TABLE `symptomdet` (
  `symptomdet_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL,
  `fachbereich_id` int(11) NOT NULL,
  `punkte` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `symptomdet`
--

INSERT INTO `symptomdet` (`symptomdet_id`, `symptom_id`, `fachbereich_id`, `punkte`) VALUES
(1, 1, 1, 3),
(2, 1, 7, 2),
(5, 1, 9, 1),
(3, 1, 17, 2),
(4, 1, 19, 1),
(8, 2, 1, 2),
(9, 2, 2, 1),
(10, 2, 7, 1),
(7, 2, 11, 2),
(6, 2, 17, 3),
(12, 3, 1, 2),
(11, 3, 4, 3),
(14, 3, 8, 1),
(13, 3, 11, 1),
(15, 4, 1, 2),
(17, 4, 8, 2),
(19, 4, 9, 1),
(16, 4, 16, 2),
(18, 4, 19, 2),
(169, 5, 1, 1),
(172, 5, 4, 1),
(171, 5, 6, 1),
(170, 5, 15, 1),
(25, 6, 1, 2),
(26, 6, 4, 1),
(27, 6, 7, 1),
(24, 6, 15, 3),
(29, 7, 1, 2),
(30, 7, 7, 1),
(28, 7, 15, 3),
(33, 8, 1, 2),
(34, 8, 6, 1),
(32, 8, 14, 2),
(31, 8, 15, 3),
(38, 9, 1, 1),
(35, 9, 2, 3),
(37, 9, 14, 2),
(36, 9, 17, 2),
(41, 10, 1, 1),
(40, 10, 2, 3),
(42, 10, 13, 1),
(39, 10, 17, 3),
(46, 11, 1, 1),
(45, 11, 2, 2),
(43, 11, 4, 3),
(44, 11, 11, 2),
(48, 12, 1, 1),
(47, 12, 3, 3),
(49, 12, 7, 1),
(50, 12, 18, 1),
(52, 13, 1, 2),
(53, 13, 7, 1),
(51, 13, 11, 3),
(55, 14, 1, 2),
(54, 14, 11, 3),
(56, 14, 17, 1),
(58, 15, 1, 2),
(57, 15, 11, 3),
(59, 15, 17, 1),
(62, 16, 1, 1),
(63, 16, 4, 1),
(60, 16, 5, 2),
(61, 16, 18, 3),
(66, 17, 1, 1),
(65, 17, 5, 3),
(64, 17, 18, 3),
(70, 18, 1, 1),
(68, 18, 4, 2),
(67, 18, 5, 3),
(69, 18, 18, 2),
(75, 19, 1, 1),
(74, 19, 2, 1),
(72, 19, 5, 2),
(73, 19, 14, 2),
(71, 19, 18, 2),
(79, 20, 1, 1),
(76, 20, 9, 3),
(78, 20, 15, 2),
(77, 20, 16, 2),
(81, 21, 1, 2),
(82, 21, 8, 1),
(80, 21, 16, 3),
(85, 22, 1, 1),
(84, 22, 4, 2),
(83, 22, 8, 3),
(88, 23, 1, 1),
(87, 23, 4, 1),
(86, 23, 8, 3),
(91, 24, 1, 1),
(90, 24, 2, 1),
(89, 24, 8, 3),
(95, 25, 1, 1),
(92, 25, 2, 3),
(93, 25, 8, 2),
(94, 25, 16, 1),
(96, 26, 4, 3),
(98, 26, 8, 1),
(97, 26, 16, 2),
(101, 27, 1, 1),
(99, 27, 4, 2),
(100, 27, 16, 2),
(103, 28, 4, 1),
(102, 28, 11, 3),
(105, 29, 1, 1),
(106, 29, 7, 1),
(104, 29, 11, 3),
(107, 30, 1, 2),
(108, 30, 11, 1),
(110, 31, 1, 2),
(109, 31, 15, 3),
(111, 31, 16, 1),
(113, 32, 1, 2),
(112, 32, 15, 3),
(115, 33, 1, 2),
(114, 33, 15, 3),
(117, 34, 1, 1),
(116, 34, 3, 3),
(118, 34, 18, 1),
(121, 35, 1, 1),
(119, 35, 3, 2),
(120, 35, 16, 3),
(124, 36, 1, 1),
(122, 36, 3, 3),
(123, 36, 16, 2),
(127, 37, 1, 1),
(125, 37, 4, 3),
(126, 37, 8, 2),
(130, 38, 1, 1),
(128, 38, 4, 3),
(129, 38, 8, 2),
(132, 39, 1, 1),
(131, 39, 4, 3),
(133, 40, 4, 3),
(134, 40, 5, 2),
(135, 40, 18, 1),
(136, 41, 4, 3),
(137, 41, 5, 2),
(138, 41, 18, 1),
(139, 42, 1, 3),
(141, 42, 9, 1),
(140, 42, 19, 2),
(145, 43, 1, 1),
(142, 43, 9, 2),
(144, 43, 16, 2),
(143, 43, 19, 2),
(147, 44, 1, 2),
(148, 44, 10, 1),
(146, 44, 16, 3),
(151, 45, 1, 1),
(149, 45, 10, 3),
(150, 45, 16, 3),
(153, 46, 1, 2),
(154, 46, 11, 1),
(152, 46, 16, 2),
(157, 47, 1, 1),
(156, 47, 9, 2),
(155, 47, 19, 3),
(160, 48, 1, 1),
(159, 48, 11, 2),
(158, 48, 19, 3),
(164, 49, 1, 1),
(162, 49, 9, 2),
(161, 49, 15, 3),
(163, 49, 19, 2),
(168, 50, 1, 1),
(166, 50, 10, 2),
(165, 50, 15, 3),
(167, 50, 19, 1);

-- --------------------------------------------------------

--
-- Table structure for table `symptome`
--

CREATE TABLE `symptome` (
  `symptom_id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `symptome`
--

INSERT INTO `symptome` (`symptom_id`, `name`) VALUES
(24, 'Angst'),
(10, 'Atemnot'),
(32, 'Bauchblähungen'),
(8, 'Bauchschmerzen'),
(48, 'Blutende Zahnfleisch'),
(9, 'Brustschmerzen'),
(23, 'Depression'),
(50, 'Dunkler Urin'),
(7, 'Durchfall'),
(6, 'Erbrechen'),
(1, 'Fieber'),
(37, 'Gedächtnisverlust'),
(49, 'Gelbsucht'),
(17, 'Gelenkschmerzen'),
(35, 'Haarausfall'),
(13, 'Halsschmerzen'),
(45, 'Häufiges Wasserlassen'),
(12, 'Hautausschlag'),
(25, 'Herzrasen'),
(28, 'Hörverlust'),
(2, 'Husten'),
(34, 'Juckreiz'),
(3, 'Kopfschmerzen'),
(39, 'Krampfanfälle'),
(14, 'Laufende Nase'),
(47, 'Leichte Blutergüsse'),
(4, 'Müdigkeit'),
(41, 'Muskelkraftverlust'),
(16, 'Muskelschmerzen'),
(43, 'Nachtschweiß'),
(15, 'Nasenverstopfung'),
(29, 'Ohrenschmerzen'),
(18, 'Rückenschmerzen'),
(22, 'Schlaflosigkeit'),
(42, 'Schüttelfrost'),
(19, 'Schwellung'),
(11, 'Schwindel'),
(33, 'Sodbrennen'),
(36, 'Spröde Nägel'),
(44, 'Starker Durst'),
(40, 'Taubheitsgefühl'),
(46, 'Trockener Mund'),
(5, 'Übelkeit'),
(21, 'Unerklärte Gewichtszunahme'),
(20, 'Unerklärter Gewichtsverlust'),
(27, 'Verschwommenes Sehen'),
(31, 'Verstopfung'),
(38, 'Verwirrung'),
(30, 'Zahnweh'),
(26, 'Zittern');

-- --------------------------------------------------------

--
-- Table structure for table `termin`
--

CREATE TABLE `termin` (
  `termin_id` int(11) NOT NULL,
  `arzt_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `datum` datetime NOT NULL,
  `beschreibung` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Bevorstehend'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `termin`
--

INSERT INTO `termin` (`termin_id`, `arzt_id`, `patient_id`, `datum`, `beschreibung`, `status`) VALUES
(1, 15, 1, '2026-04-22 11:30:00', 'dass', 'Bevorstehend'),
(2, 11, 1, '2026-04-22 11:00:00', 'dassa', 'Abgesagt');

-- --------------------------------------------------------

--
-- Table structure for table `vorerkrankungen`
--

CREATE TABLE `vorerkrankungen` (
  `vorerkrankung_id` int(11) NOT NULL,
  `erkrankungsname` varchar(50) NOT NULL,
  `beschreibung` text DEFAULT NULL,
  `patient_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_sessions`
--
ALTER TABLE `app_sessions`
  ADD PRIMARY KEY (`token`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `diagnosedet`
--
ALTER TABLE `diagnosedet`
  ADD PRIMARY KEY (`diagnose_id`,`symptom_id`),
  ADD KEY `symptom_id` (`symptom_id`);

--
-- Indexes for table `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD PRIMARY KEY (`mitarbeiter_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `mitarbeiter_id` (`mitarbeiter_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `kontakt_id` (`kontakt_id`);

--
-- Indexes for table `termin`
--
ALTER TABLE `termin`
  ADD PRIMARY KEY (`termin_id`);

--
-- Indexes for table `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  ADD PRIMARY KEY (`vorerkrankung_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  MODIFY `mitarbeiter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `termin`
--
ALTER TABLE `termin`
  MODIFY `termin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
