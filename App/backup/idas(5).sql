-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 09:28 PM
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
(3, 'aa@gmail.com', '$2y$10$nQl2f.r5lqfGWzm8VUonK.OxIPMzw0Cirn.VADLqL5wy9d0g3NbXW'),
(0, 'fddsf@gmail.com', '$2y$10$Fe7oF17Nh.IAA2vS/Q4JXuFqVTlhPzR9iIwWGlR6/YZ/TjyM85WO6'),
(0, 'daws@gmail.com', '$2y$10$mjpiEAJKmSbipUCyoQ5VcOmwFnuuu3rKG3l5jCbFUt0z7HmKEbCV2');

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
('fb028e3f4def9c623caeb93e8bf5896682e97aa25a8b8e930db6abe30d22115f', 1, '2026-04-26 23:08:38');

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
  `foto` varchar(255) DEFAULT NULL,
  `passwort` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `arzt`
--

INSERT INTO `arzt` (`arzt_id`, `name`, `fachbereich_id`, `telefonnummer`, `email`, `fax`, `addresse`, `foto`, `passwort`) VALUES
(4, 'Dr. Al Dente', 4, '+49 511 4567890', 'al.dente@clinic.de', '+49 511 4567891', 'Humboldtstraße 63, 30167 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(7, 'Dr. Ali Gaither', 7, '+49 511 7890123', 'ali.gaither@clinic.de', '+49 511 7890124', 'Hildesheimer Straße 256, 30173 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(15, 'Dr. Ben Dover', 15, '+49 511 5678904', 'ben.dover@clinic.de', '+49 511 5678905', 'Bödekerstraße 102, 30161 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(19, 'Dr. Bonnie Blue', 19, '+49 511 9012348', 'bonnie.blue@clinic.de', '+49 511 9012349', 'Wedekindstraße 97, 30161 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(5, 'Dr. Emma Grate', 5, '+49 511 5678901', 'emma.grate@clinic.de', '+49 511 5678902', 'Klingerstraße 22, 30175 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(2, 'Dr. Holly Daze', 2, '+49 511 2345678', 'holly.daze@clinic.de', '+49 511 2345679', 'Adenauerallee 201, 30175 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(13, 'Dr. Ice Spice', 13, '+49 511 3456782', 'ice.spice@clinic.de', '+49 511 3456783', 'Voßstraße 47, 30161 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(17, 'Dr. Ima Foxx', 17, '+49 511 7890126', 'ima.foxx@clinic.de', '+49 511 7890127', 'Wedekindstraße 97, 30161 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(18, 'Dr. Ima Pigg', 18, '+49 511 8901237', 'ima.pigg@clinic.de', '+49 511 8901238', 'Adenauerallee 201, 30175 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(1, 'Dr. Justin Times', 1, '+49 511 1234567', 'justin.times@clinic.de', '+49 511 1234568', 'Brühlstraße 85, 30169 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(20, 'Dr. Lou Natic', 20, '+49 511 0123459', 'lou.natic@clinic.de', '+49 511 0123460', 'Hildesheimer Straße 256, 30173 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(6, 'Dr. Mae Day', 6, '+49 511 6789012', 'mae.day@clinic.de', '+49 511 6789013', 'Jakobistraße 58, 30163 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(14, 'Dr. North West', 14, '+49 511 4567893', 'north.west@clinic.de', '+49 511 4567894', 'Ricklinger Stadtweg 165, 30459 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(8, 'Dr. Oliver Figma', 8, '+49 511 8901234', 'oliver.figma@clinic.de', '+49 511 8901235', 'Bödekerstraße 102, 30161 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(10, 'Dr. Reed Richard', 10, '+49 511 0123456', 'reed.richard@clinic.de', '+49 511 0123457', 'Ricklinger Stadtweg 165, 30459 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(16, 'Dr. Richie Poore', 16, '+49 511 6789015', 'richie.poore@clinic.de', '+49 511 6789016', 'Bornumer Straße 78, 30453 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(12, 'Dr. Robert F. Kennedy Jr.', 12, '+49 511 2345671', 'robert.kennedy@clinic.de', '+49 511 2345672', 'Podbielskistraße 134, 30165 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(11, 'Dr. Sue Storm', 11, '+49 511 1234560', 'sue.storm@clinic.de', '+49 511 1234561', 'Jakobistraße 58, 30163 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(9, 'Dr. Walter White', 9, '+49 511 9012345', 'walter.white@clinic.de', '+49 511 9012346', 'Podbielskistraße 134, 30165 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(3, 'Dr. Willie Makeit', 3, '+49 511 3456789', 'willie.makeit@clinic.de', '+49 511 3456790', 'Adenauerallee 201, 30175 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(21, 'Dr. Maria Schmidt', 1, '+49 30 1234568', 'maria.schmidt@clinic.de', NULL, 'Kurfürstendamm 12, 10719 Berlin', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(22, 'Dr. Klaus Weber', 1, '+49 89 1234569', 'klaus.weber@clinic.de', NULL, 'Maximilianstraße 5, 80539 München', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(23, 'Dr. Anna Müller', 1, '+49 40 1234570', 'anna.mueller@clinic.de', NULL, 'Jungfernstieg 9, 20354 Hamburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(24, 'Dr. Thomas Fischer', 2, '+49 221 2345680', 'thomas.fischer@clinic.de', NULL, 'Schildergasse 3, 50667 Köln', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(25, 'Dr. Laura Becker', 2, '+49 69 2345681', 'laura.becker@clinic.de', NULL, 'Goethestraße 14, 60313 Frankfurt am Main', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(26, 'Dr. Stefan Hoffmann', 2, '+49 711 2345682', 'stefan.hoffmann@clinic.de', NULL, 'Königstraße 56, 70173 Stuttgart', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(27, 'Dr. Sandra Wagner', 3, '+49 351 3456791', 'sandra.wagner@clinic.de', NULL, 'Prager Straße 22, 01069 Dresden', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(28, 'Dr. Michael Braun', 3, '+49 341 3456792', 'michael.braun@clinic.de', NULL, 'Grimmaische Str 5, 04109 Leipzig', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(29, 'Dr. Julia Koch', 3, '+49 211 3456793', 'julia.koch@clinic.de', NULL, 'Königsallee 10, 40212 Düsseldorf', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(30, 'Dr. Andreas Richter', 4, '+49 621 4567891', 'andreas.richter@clinic.de', NULL, 'Planken 2, 68161 Mannheim', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(31, 'Dr. Petra Klein', 4, '+49 911 4567892', 'petra.klein@clinic.de', NULL, 'Karolinenstraße 44, 90402 Nürnberg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(32, 'Dr. Markus Wolf', 4, '+49 421 4567893', 'markus.wolf@clinic.de', NULL, 'Obernstraße 2, 28195 Bremen', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(33, 'Dr. Christine Schäfer', 5, '+49 371 5678903', 'christine.schaefer@clinic.de', NULL, 'Hauptmarkt 8, 09111 Chemnitz', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(34, 'Dr. Daniel Schreiber', 5, '+49 391 5678904', 'daniel.schreiber@clinic.de', NULL, 'Breiter Weg 7, 39104 Magdeburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(35, 'Dr. Nicole Neumann', 5, '+49 431 5678905', 'nicole.neumann@clinic.de', NULL, 'Holstenstraße 10, 24103 Kiel', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(36, 'Dr. Ralf Zimmermann', 6, '+49 381 6789013', 'ralf.zimmermann@clinic.de', NULL, 'Kröpeliner Str 5, 18055 Rostock', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(37, 'Dr. Monika Krause', 6, '+49 471 6789014', 'monika.krause@clinic.de', NULL, 'Obere Str 100, 27568 Bremerhaven', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(38, 'Dr. Frank Lehmann', 6, '+49 531 6789015', 'frank.lehmann@clinic.de', NULL, 'Bohlweg 1, 38100 Braunschweig', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(39, 'Dr. Sabine Hartmann', 7, '+49 511 7890124', 'sabine.hartmann@clinic.de', NULL, 'Vahrenwalder Str 4, 30179 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(40, 'Dr. Jörg Krüger', 7, '+49 561 7890125', 'joerg.krueger@clinic.de', NULL, 'Königsstraße 20, 34117 Kassel', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(41, 'Dr. Ute Schulz', 7, '+49 521 7890126', 'ute.schulz@clinic.de', NULL, 'Bahnhofstraße 1, 33602 Bielefeld', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(42, 'Dr. Bernd Meyer', 8, '+49 251 8901235', 'bernd.meyer@clinic.de', NULL, 'Prinzipalmarkt 5, 48143 Münster', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(43, 'Dr. Gabi Werner', 8, '+49 231 8901236', 'gabi.werner@clinic.de', NULL, 'Westenhellweg 4, 44137 Dortmund', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(44, 'Dr. Peter Lange', 8, '+49 201 8901237', 'peter.lange@clinic.de', NULL, 'Kettwiger Str 200, 45127 Essen', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(45, 'Dr. Ines Bauer', 9, '+49 202 9012346', 'ines.bauer@clinic.de', NULL, 'Elberfelder Str 5, 42103 Wuppertal', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(46, 'Dr. Christian Schulze', 9, '+49 234 9012347', 'christian.schulze@clinic.de', NULL, 'Kortumstraße 38, 44787 Bochum', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(47, 'Dr. Eva Maier', 9, '+49 203 9012348', 'eva.maier@clinic.de', NULL, 'Königstraße 10, 47051 Duisburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(48, 'Dr. Hans Brandt', 10, '+49 241 0123457', 'hans.brandt@clinic.de', NULL, 'Großmarschierstraße 4, 52062 Aachen', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(49, 'Dr. Karin Vogt', 10, '+49 228 0123458', 'karin.vogt@clinic.de', NULL, 'Poststraße 5, 53111 Bonn', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(50, 'Dr. Dieter Haas', 10, '+49 214 0123459', 'dieter.haas@clinic.de', NULL, 'Hauptstraße 41, 51373 Leverkusen', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(51, 'Dr. Renate Sommer', 11, '+49 681 1234571', 'renate.sommer@clinic.de', NULL, 'Bahnhofstraße 4, 66111 Saarbrücken', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(52, 'Dr. Werner Lindner', 11, '+49 631 1234572', 'werner.lindner@clinic.de', NULL, 'Fackelrondell 12, 67655 Kaiserslautern', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(53, 'Dr. Heike Voigt', 11, '+49 621 1234573', 'heike.voigt@clinic.de', NULL, 'Bismarckstraße 7, 67059 Ludwigshafen', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(54, 'Dr. Uwe Schubert', 12, '+49 721 2345683', 'uwe.schubert@clinic.de', NULL, 'Kaiserstraße 3, 76133 Karlsruhe', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(55, 'Dr. Brigitte Seidel', 12, '+49 761 2345684', 'brigitte.seidel@clinic.de', NULL, 'Bertoldstraße 78, 79098 Freiburg im Breisgau', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(56, 'Dr. Norbert Gross', 12, '+49 7121 2345685', 'norbert.gross@clinic.de', NULL, 'Tübinger Str 44, 72764 Reutlingen', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(57, 'Dr. Claudia Böhm', 13, '+49 821 3456794', 'claudia.boehm@clinic.de', NULL, 'Maximilianstraße 14, 86150 Augsburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(58, 'Dr. Rainer Schrader', 13, '+49 851 3456795', 'rainer.schrader@clinic.de', NULL, 'Ludwigstraße 20, 94032 Passau', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(59, 'Dr. Silke Pfeiffer', 13, '+49 841 3456796', 'silke.pfeiffer@clinic.de', NULL, 'Ludwigstraße 33, 85049 Ingolstadt', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(60, 'Dr. Manfred Ernst', 14, '+49 911 4567894', 'manfred.ernst@clinic.de', NULL, 'Königstraße 20, 90402 Nürnberg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(61, 'Dr. Ingrid Kunze', 14, '+49 931 4567895', 'ingrid.kunze@clinic.de', NULL, 'Domstraße 15, 97070 Würzburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(62, 'Dr. Volker Horn', 14, '+49 921 4567896', 'volker.horn@clinic.de', NULL, 'Maximilianstraße 2, 95444 Bayreuth', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(63, 'Dr. Elke Bach', 15, '+49 371 5678906', 'elke.bach@clinic.de', NULL, 'Brückenstraße 10, 09111 Chemnitz', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(64, 'Dr. Lothar Pohl', 15, '+49 3581 5678907', 'lothar.pohl@clinic.de', NULL, 'Berliner Straße 3, 02826 Görlitz', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(65, 'Dr. Hannelore Schick', 15, '+49 3591 5678908', 'hannelore.schick@clinic.de', NULL, 'Bautzener Str 14, 02625 Bautzen', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(66, 'Dr. Armin Wirth', 16, '+49 345 6789016', 'armin.wirth@clinic.de', NULL, 'Marktplatz 14, 06108 Halle (Saale)', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(67, 'Dr. Margit Ziegler', 16, '+49 391 6789017', 'margit.ziegler@clinic.de', NULL, 'Alter Markt 3, 39104 Magdeburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(68, 'Dr. Holger Bruns', 16, '+49 3641 6789018', 'holger.bruns@clinic.de', NULL, 'Markt 14, 07743 Jena', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(69, 'Dr. Doris Baum', 17, '+49 3621 7890127', 'doris.baum@clinic.de', NULL, 'Hauptmarkt 20, 99867 Gotha', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(70, 'Dr. Günter Roth', 17, '+49 361 7890128', 'guenter.roth@clinic.de', NULL, 'Anger 3, 99084 Erfurt', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(71, 'Dr. Anja Seifert', 17, '+49 3643 7890129', 'anja.seifert@clinic.de', NULL, 'Marktstraße 44, 99423 Weimar', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(72, 'Dr. Kurt Franke', 18, '+49 395 8901238', 'kurt.franke@clinic.de', NULL, 'Marktplatz 10, 17033 Neubrandenburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(73, 'Dr. Irmgard Hahn', 18, '+49 385 8901239', 'irmgard.hahn@clinic.de', NULL, 'Schloßstraße 50, 19053 Schwerin', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(74, 'Dr. Axel Berg', 18, '+49 461 8901240', 'axel.berg@clinic.de', NULL, 'Holm 10, 24937 Flensburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(75, 'Dr. Waltraud Sauer', 19, '+49 511 9012349', 'waltraud.sauer@clinic.de', NULL, 'Georgstraße 10, 30159 Hannover', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(76, 'Dr. Joachim Graf', 19, '+49 5121 9012350', 'joachim.graf@clinic.de', NULL, 'Almsstraße 30, 31134 Hildesheim', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(77, 'Dr. Gisela Ludwig', 19, '+49 5361 9012351', 'gisela.ludwig@clinic.de', NULL, 'Porschestraße 14, 38440 Wolfsburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(78, 'Dr. Helmut Kramer', 20, '+49 4131 0123460', 'helmut.kramer@clinic.de', NULL, 'Am Sande 10, 21335 Lüneburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(79, 'Dr. Ursula Fuchs', 20, '+49 4141 0123461', 'ursula.fuchs@clinic.de', NULL, 'Deichstraße 10, 21682 Stade', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(80, 'Dr. Eberhard Stein', 20, '+49 4321 0123462', 'eberhard.stein@clinic.de', NULL, 'Holstenstraße 8, 24534 Neumünster', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(0, 'Dr. Klaus Neumann', 4, '+49 30 8765432', 'klaus.neumann@neurologie.de', NULL, 'Unter den Linden 10, 10117 Berlin', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(0, 'Dr. Sabine Hoffmann', 4, '+49 89 8765433', 'sabine.hoffmann@neurologie.de', NULL, 'Leopoldstraße 3, 80802 München', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK'),
(0, 'Dr. Rainer Schreiber', 4, '+49 40 8765434', 'rainer.schreiber@neurologie.de', NULL, 'Eppendorfer Baum 5, 20249 Hamburg', NULL, '$2y$10$ILyID4VTmFstaP63/9WmquQ1plzFoIjpwIYoKADAhNHBGrqZNbbaK');

-- --------------------------------------------------------

--
-- Table structure for table `arzt_arbeitszeiten`
--

CREATE TABLE `arzt_arbeitszeiten` (
  `id` int(11) NOT NULL,
  `arzt_id` int(11) NOT NULL,
  `wochentag` tinyint(4) NOT NULL,
  `von` time NOT NULL,
  `bis` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `arzt_blocked_slots`
--

CREATE TABLE `arzt_blocked_slots` (
  `id` int(11) NOT NULL,
  `arzt_id` int(11) NOT NULL,
  `datum` date NOT NULL,
  `von` time DEFAULT NULL,
  `bis` time DEFAULT NULL,
  `grund` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Mohammad Aein', 'Mirzayan', '2005-07-30', 'hannover', '30159', 'klinger straße9', '20@gmail.com', 'null', 'm', '$2y$12$WBQd4uy9KL.sU942a9IHO.3A9VheGxuUlXkE1IKA3d9R86eqfeZzi'),
(2, 'dsad', 'dfskmflk', '0000-00-00', '', '', '', 'haithem@gmail.com', '', '', '$2y$10$wEPPjvkvz0ktwFkKsca3IuJoepQ16xMMofhis6jw0FB2e8ibyQF0K');

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
(1, NULL, NULL, 1, 'dsa', 'das', 'offen', '2026-04-19 21:58:06', NULL),
(2, NULL, NULL, 1, 'asd', 'safd', 'offen', '2026-04-23 19:54:50', NULL),
(3, NULL, NULL, 1, '324rdfsd', 'fdsfds', 'offen', '2026-04-26 23:09:16', NULL);

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
(2, 11, 1, '2026-04-22 11:00:00', 'dassa', 'Abgesagt'),
(3, 1, 1, '2100-10-10 13:00:00', 'dsa', 'Bevorstehend'),
(5, 2, 1, '0000-00-00 00:00:00', '', 'Bevorstehend');

-- --------------------------------------------------------

--
-- Table structure for table `vorerkrankungen`
--

CREATE TABLE `vorerkrankungen` (
  `vorerkrankung_id` int(11) NOT NULL,
  `erkrankungsname` varchar(50) NOT NULL,
  `beschreibung` text DEFAULT NULL,
  `seit` varchar(20) DEFAULT NULL,
  `patient_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vorerkrankungen`
--

INSERT INTO `vorerkrankungen` (`vorerkrankung_id`, `erkrankungsname`, `beschreibung`, `seit`, `patient_id`) VALUES
(1, 'Lebererkrankung', NULL, '11', 1),
(2, 'Rheuma', '42', '2424', 1),
(3, 'COPD', NULL, '321312', 1),
(4, 'Bluthochdruck', NULL, '2015', 1);

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
-- Indexes for table `arzt_arbeitszeiten`
--
ALTER TABLE `arzt_arbeitszeiten`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_day` (`arzt_id`,`wochentag`);

--
-- Indexes for table `arzt_blocked_slots`
--
ALTER TABLE `arzt_blocked_slots`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`);

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
-- AUTO_INCREMENT for table `arzt_arbeitszeiten`
--
ALTER TABLE `arzt_arbeitszeiten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `arzt_blocked_slots`
--
ALTER TABLE `arzt_blocked_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  MODIFY `mitarbeiter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `support`
--
ALTER TABLE `support`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `termin`
--
ALTER TABLE `termin`
  MODIFY `termin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  MODIFY `vorerkrankung_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
