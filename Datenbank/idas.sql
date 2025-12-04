-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 04. Dez 2025 um 10:22
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
-- Tabellenstruktur für Tabelle `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `Benutzername` varchar(40) DEFAULT NULL,
  `Passwort` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `diagnose`
--

CREATE TABLE `diagnose` (
  `Diagnose_ID` int(11) NOT NULL,
  `Beschreibung` text DEFAULT NULL,
  `Datum` date DEFAULT NULL,
  `Patienten_ID` int(11) DEFAULT NULL,
  `Artzt_ID` int(11) DEFAULT NULL,
  `Symptome_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fachbereiche`
--

CREATE TABLE `fachbereiche` (
  `Fachbereichs_ID` int(11) NOT NULL,
  `Fachbereichsname` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fachbereich_symptome`
--

CREATE TABLE `fachbereich_symptome` (
  `Fachbereichs_ID` int(11) NOT NULL,
  `Symptome_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `mitarbeiter`
--

CREATE TABLE `mitarbeiter` (
  `Mitarbeiter_ID` int(11) NOT NULL,
  `Vorname` varchar(20) DEFAULT NULL,
  `Nachname` varchar(20) DEFAULT NULL,
  `Geburtsdatum` date DEFAULT NULL,
  `E_Mail` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `patienten`
--

CREATE TABLE `patienten` (
  `Patienten_ID` int(11) NOT NULL,
  `Vorname` varchar(20) DEFAULT NULL,
  `Nachname` varchar(20) DEFAULT NULL,
  `Wohnort` varchar(40) DEFAULT NULL,
  `Straße` varchar(50) DEFAULT NULL,
  `Hausnummer` varchar(5) DEFAULT NULL,
  `Geburtsdatum` date DEFAULT NULL,
  `E_Mail` varchar(40) DEFAULT NULL,
  `Telefonnummer` varchar(20) DEFAULT NULL,
  `Geschlecht` varchar(15) DEFAULT NULL,
  `Passwort` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `patienten_vorerkrankungs`
--

CREATE TABLE `patienten_vorerkrankungs` (
  `Patienten_ID` int(11) DEFAULT NULL,
  `Vorerkrankungs_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `support`
--

CREATE TABLE `support` (
  `SupportTicket_ID` int(11) NOT NULL,
  `Mitarbeiter_ID` int(11) DEFAULT NULL,
  `Patienten_ID` int(11) DEFAULT NULL,
  `Problembeschreibung` text DEFAULT NULL,
  `Bearbeitet` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `symptome`
--

CREATE TABLE `symptome` (
  `Symptome_ID` int(11) NOT NULL,
  `Beschreibung` text DEFAULT NULL,
  `Schweregrad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `termine`
--

CREATE TABLE `termine` (
  `Termin_ID` int(11) NOT NULL,
  `Datum` date DEFAULT NULL,
  `Uhrzeit` varchar(5) DEFAULT NULL,
  `Grund` text DEFAULT NULL,
  `Patienten_ID` int(11) DEFAULT NULL,
  `Artzt_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vorerkrankungen`
--

CREATE TABLE `vorerkrankungen` (
  `Vorerkrankungs_ID` int(11) NOT NULL,
  `Erkrankungsname` text DEFAULT NULL,
  `Beschreibung` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ärtzte`
--

CREATE TABLE `ärtzte` (
  `Artzt_ID` int(11) NOT NULL,
  `Name` varchar(40) DEFAULT NULL,
  `Fachbereichs_ID` int(11) DEFAULT NULL,
  `E_Mail` varchar(40) DEFAULT NULL,
  `Telefon` varchar(20) DEFAULT NULL,
  `Fax` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`);

--
-- Indizes für die Tabelle `diagnose`
--
ALTER TABLE `diagnose`
  ADD PRIMARY KEY (`Diagnose_ID`),
  ADD KEY `fk_diagnonse_Patienten_ID` (`Patienten_ID`),
  ADD KEY `fk_diagnose_artzt_ID` (`Artzt_ID`),
  ADD KEY `fk_diagnose_symptome` (`Symptome_ID`);

--
-- Indizes für die Tabelle `fachbereiche`
--
ALTER TABLE `fachbereiche`
  ADD PRIMARY KEY (`Fachbereichs_ID`);

--
-- Indizes für die Tabelle `fachbereich_symptome`
--
ALTER TABLE `fachbereich_symptome`
  ADD KEY `fk_fachbereich_symptome_fachbereich_id` (`Fachbereichs_ID`),
  ADD KEY `fk_fachbereich_symptome_symptome_id` (`Symptome_ID`);

--
-- Indizes für die Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  ADD PRIMARY KEY (`Mitarbeiter_ID`);

--
-- Indizes für die Tabelle `patienten`
--
ALTER TABLE `patienten`
  ADD PRIMARY KEY (`Patienten_ID`);

--
-- Indizes für die Tabelle `patienten_vorerkrankungs`
--
ALTER TABLE `patienten_vorerkrankungs`
  ADD KEY `Patienten_ID` (`Patienten_ID`),
  ADD KEY `Vorerkrankungs_ID` (`Vorerkrankungs_ID`);

--
-- Indizes für die Tabelle `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`SupportTicket_ID`),
  ADD KEY `fk_support_mitarbeiter_ID` (`Mitarbeiter_ID`),
  ADD KEY `fk_support_patienten_ID` (`Patienten_ID`);

--
-- Indizes für die Tabelle `symptome`
--
ALTER TABLE `symptome`
  ADD PRIMARY KEY (`Symptome_ID`);

--
-- Indizes für die Tabelle `termine`
--
ALTER TABLE `termine`
  ADD PRIMARY KEY (`Termin_ID`),
  ADD KEY `fk_termine_patienten_ID` (`Patienten_ID`),
  ADD KEY `fk_termine_artzt_ID` (`Artzt_ID`);

--
-- Indizes für die Tabelle `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  ADD PRIMARY KEY (`Vorerkrankungs_ID`);

--
-- Indizes für die Tabelle `ärtzte`
--
ALTER TABLE `ärtzte`
  ADD PRIMARY KEY (`Artzt_ID`),
  ADD KEY `fk_ätzte_fachbereichs_ID` (`Fachbereichs_ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `diagnose`
--
ALTER TABLE `diagnose`
  MODIFY `Diagnose_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `fachbereiche`
--
ALTER TABLE `fachbereiche`
  MODIFY `Fachbereichs_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `mitarbeiter`
--
ALTER TABLE `mitarbeiter`
  MODIFY `Mitarbeiter_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `patienten`
--
ALTER TABLE `patienten`
  MODIFY `Patienten_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `support`
--
ALTER TABLE `support`
  MODIFY `SupportTicket_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `symptome`
--
ALTER TABLE `symptome`
  MODIFY `Symptome_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `termine`
--
ALTER TABLE `termine`
  MODIFY `Termin_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  MODIFY `Vorerkrankungs_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `ärtzte`
--
ALTER TABLE `ärtzte`
  MODIFY `Artzt_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `diagnose`
--
ALTER TABLE `diagnose`
  ADD CONSTRAINT `fk_diagnonse_Patienten_ID` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`),
  ADD CONSTRAINT `fk_diagnose_artzt_ID` FOREIGN KEY (`Artzt_ID`) REFERENCES `ärtzte` (`Artzt_ID`),
  ADD CONSTRAINT `fk_diagnose_symptome` FOREIGN KEY (`Symptome_ID`) REFERENCES `symptome` (`Symptome_ID`);

--
-- Constraints der Tabelle `fachbereich_symptome`
--
ALTER TABLE `fachbereich_symptome`
  ADD CONSTRAINT `fk_fachbereich_symptome_fachbereich_id` FOREIGN KEY (`Fachbereichs_ID`) REFERENCES `fachbereiche` (`Fachbereichs_ID`),
  ADD CONSTRAINT `fk_fachbereich_symptome_symptome_id` FOREIGN KEY (`Symptome_ID`) REFERENCES `symptome` (`Symptome_ID`);

--
-- Constraints der Tabelle `patienten_vorerkrankungs`
--
ALTER TABLE `patienten_vorerkrankungs`
  ADD CONSTRAINT `patienten_vorerkrankungs_ibfk_1` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`),
  ADD CONSTRAINT `patienten_vorerkrankungs_ibfk_2` FOREIGN KEY (`Vorerkrankungs_ID`) REFERENCES `vorerkrankungen` (`Vorerkrankungs_ID`);

--
-- Constraints der Tabelle `support`
--
ALTER TABLE `support`
  ADD CONSTRAINT `fk_support_mitarbeiter_ID` FOREIGN KEY (`Mitarbeiter_ID`) REFERENCES `mitarbeiter` (`Mitarbeiter_ID`),
  ADD CONSTRAINT `fk_support_patienten_ID` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`);

--
-- Constraints der Tabelle `termine`
--
ALTER TABLE `termine`
  ADD CONSTRAINT `fk_termine_artzt_ID` FOREIGN KEY (`Artzt_ID`) REFERENCES `ärtzte` (`Artzt_ID`),
  ADD CONSTRAINT `fk_termine_patienten_ID` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`);

--
-- Constraints der Tabelle `ärtzte`
--
ALTER TABLE `ärtzte`
  ADD CONSTRAINT `fk_ätzte_fachbereichs_ID` FOREIGN KEY (`Fachbereichs_ID`) REFERENCES `fachbereiche` (`Fachbereichs_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
