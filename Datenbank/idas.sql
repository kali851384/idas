-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 09. Feb 2026 um 12:53
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
  `passwort` varchar(16) NOT NULL
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
  `fax` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `arzt`
--

INSERT INTO `arzt` (`arzt_id`, `name`, `fachbereich_id`, `telefonnummer`, `email`, `fax`) VALUES
(1, 'Dr. Justin Times', 1, '+49 511 1234567', 'justin.times@clinic.de', '+49 511 1234568'),
(2, 'Dr. Holly Daze', 2, '+49 511 2345678', 'holly.daze@clinic.de', '+49 511 2345679'),
(3, 'Dr. Willie Makeit', 3, '+49 511 3456789', 'willie.makeit@clinic.de', '+49 511 3456790'),
(4, 'Dr. Al Dente', 4, '+49 511 4567890', 'al.dente@clinic.de', '+49 511 4567891'),
(5, 'Dr. Emma Grate', 5, '+49 511 5678901', 'emma.grate@clinic.de', '+49 511 5678902'),
(6, 'Dr. Mae Day', 6, '+49 511 6789012', 'mae.day@clinic.de', '+49 511 6789013'),
(7, 'Dr. Ali Gaither', 7, '+49 511 7890123', 'ali.gaither@clinic.de', '+49 511 7890124'),
(8, 'Dr. Oliver Figma', 8, '+49 511 8901234', 'oliver.figma@clinic.de', '+49 511 8901235'),
(9, 'Dr. Walter White', 9, '+49 511 9012345', 'walter.white@clinic.de', '+49 511 9012346'),
(10, 'Dr. Reed Richard', 10, '+49 511 0123456', 'reed.richard@clinic.de', '+49 511 0123457'),
(11, 'Dr. Sue Storm', 11, '+49 511 1234560', 'sue.storm@clinic.de', '+49 511 1234561'),
(12, 'Dr. Robert F. Kennedy Jr.', 12, '+49 511 2345671', 'robert.kennedy@clinic.de', '+49 511 2345672'),
(13, 'Dr. Ice Spice', 13, '+49 511 3456782', 'ice.spice@clinic.de', '+49 511 3456783'),
(14, 'Dr. North West', 14, '+49 511 4567893', 'north.west@clinic.de', '+49 511 4567894'),
(15, 'Dr. Ben Dover', 15, '+49 511 5678904', 'ben.dover@clinic.de', '+49 511 5678905'),
(16, 'Dr. Richie Poore', 16, '+49 511 6789015', 'richie.poore@clinic.de', '+49 511 6789016'),
(17, 'Dr. Ima Foxx', 17, '+49 511 7890126', 'ima.foxx@clinic.de', '+49 511 7890127'),
(18, 'Dr. Ima Pigg', 18, '+49 511 8901237', 'ima.pigg@clinic.de', '+49 511 8901238'),
(19, 'Dr. Bonnie Blue', 19, '+49 511 9012348', 'bonnie.blue@clinic.de', '+49 511 9012349'),
(20, 'Dr. Lou Natic', 20, '+49 511 0123459', 'lou.natic@clinic.de', '+49 511 0123460');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `diagnose`
--

CREATE TABLE `diagnose` (
  `diagnose_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `datum` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `email` varchar(70) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `geschlecht` varchar(10) DEFAULT NULL,
  `passwort` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `patient`
--

INSERT INTO `patient` (`patient_id`, `vorname`, `nachname`, `geburtsdatum`, `wohnort`, `plz`, `adresse`, `email`, `telefon`, `geschlecht`, `passwort`) VALUES
(1, 'Mohammad Aein', 'Mirzayan', '2005-07-30', 'hannover', NULL, 'klinger straße9', 'mohammadmirzayan20@gmail.com', NULL, 'm', '123');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `support`
--

CREATE TABLE `support` (
  `ticket_id` int(11) NOT NULL,
  `mitarbeiter_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `problembeschreibung` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `symptomdet`
--

CREATE TABLE `symptomdet` (
  `symptomdet_id` int(11) NOT NULL,
  `symptom_id` int(11) NOT NULL,
  `fachbereich_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `patient_id` (`patient_id`);

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `arzt`
--
ALTER TABLE `arzt`
  MODIFY `arzt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT für Tabelle `diagnose`
--
ALTER TABLE `diagnose`
  MODIFY `diagnose_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `fachbereich`
--
ALTER TABLE `fachbereich`
  MODIFY `fachbereich_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  MODIFY `mitarbeiter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `support`
--
ALTER TABLE `support`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `symptomdet`
--
ALTER TABLE `symptomdet`
  MODIFY `symptomdet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `symptome`
--
ALTER TABLE `symptome`
  MODIFY `symptom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT für Tabelle `termin`
--
ALTER TABLE `termin`
  MODIFY `termin_id` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `support_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE SET NULL;

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
