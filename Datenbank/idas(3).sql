-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 08. Jan 2026 um 10:21
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
  `Datum` date DEFAULT NULL,
  `Patienten_ID` int(11) DEFAULT NULL,
  `Symptome_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `diagnose_details`
--

CREATE TABLE `diagnose_details` (
  `Detail_ID` int(11) NOT NULL,
  `Diagnose_ID` int(11) NOT NULL,
  `Symptome_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fachbereiche`
--

CREATE TABLE `fachbereiche` (
  `Fachbereichs_ID` int(11) NOT NULL,
  `Fachbereichsname` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `fachbereiche`
--

INSERT INTO `fachbereiche` (`Fachbereichs_ID`, `Fachbereichsname`) VALUES
(1, 'Innere Medizin'),
(2, 'Allgemeinmedizin'),
(3, 'Kardiologie'),
(4, 'Neurologie'),
(5, 'Dermatologie'),
(6, 'Gynäkologie und Geburtshilfe'),
(7, 'Orthopädie'),
(8, 'Psychiatrie und Psychotherapie'),
(9, 'Urologie'),
(10, 'Onkologie'),
(11, 'Pneumologie'),
(12, 'Gastroenterologie'),
(13, 'Endokrinologie'),
(14, 'Rheumatologie'),
(15, 'Hals-Nasen-Ohren-Heilkunde'),
(16, 'Augenheilkunde'),
(17, 'Pädiatrie'),
(18, 'Chirurgie'),
(19, 'Radiologie'),
(20, 'Anästhesiologie');

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
  `symptome_name` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Daten für Tabelle `symptome`
--

INSERT INTO `symptome` (`Symptome_ID`, `symptome_name`) VALUES
(1, 'Akutes Abdomen'),
(2, 'Angst'),
(3, 'Anämie'),
(4, 'Apathie'),
(5, 'Appetitlosigkeit'),
(6, 'Atemnot'),
(7, 'Bauchschmerzen'),
(8, 'Benommenheit'),
(9, 'Bewusstlosigkeit'),
(10, 'Bewusstseinsstörung'),
(11, 'Blähung'),
(12, 'Blässe'),
(13, 'Blut im Stuhl'),
(14, 'Blut im Urin (Hämaturie)'),
(15, 'Brustschmerz'),
(16, 'Delir'),
(17, 'Depersonalisation'),
(18, 'Durchfall'),
(19, 'Dysmenorrhoe'),
(20, 'Dysurie'),
(21, 'Dysphagie'),
(22, 'Dysphorie'),
(23, 'Erbrechen'),
(24, 'Erektile Dysfunktion'),
(25, 'Exanthem'),
(26, 'Faszikulation'),
(27, 'Flankenschmerz'),
(28, 'Gangstörung'),
(29, 'Gedächtnisstörung'),
(30, 'Gewichtsverlust'),
(31, 'Halsschmerzen'),
(32, 'Hämaturie'),
(33, 'Husten'),
(34, 'Hypertonie'),
(35, 'Hypotonie'),
(36, 'Juckreiz'),
(37, 'Kopfschmerz'),
(38, 'Krampf'),
(39, 'Lähmung'),
(40, 'Lymphadenopathie'),
(41, 'Myalgie'),
(42, 'Nasenbluten'),
(43, 'Nykturie'),
(44, 'Ödem'),
(45, 'Palpitation'),
(46, 'Parästhesie der Haut'),
(47, 'Polyurie'),
(48, 'Pruritus (Juckreiz)'),
(49, 'Psychose'),
(50, 'Rückenschmerzen'),
(51, 'Schüttelfrost'),
(52, 'Schwindel'),
(53, 'Sehstörung'),
(54, 'Schlafstörung'),
(55, 'Schmerz'),
(56, 'Synkope'),
(57, 'Tachykardie'),
(58, 'Tremor'),
(59, 'Übelkeit');

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

--
-- Daten für Tabelle `vorerkrankungen`
--

INSERT INTO `vorerkrankungen` (`Vorerkrankungs_ID`, `Erkrankungsname`, `Beschreibung`) VALUES
(1, 'Diabetes mellitus', 'Chronische Erkrankung mit erhöhtem Blutzuckerspiegel, die zu Komplikationen wie Neuropathie oder Herzkrankheiten führen kann.'),
(2, 'Asthma bronchiale', 'Chronische Entzündung der Atemwege, die zu wiederkehrenden Atembeschwerden und Husten führt.'),
(3, 'Hypertonie', 'Erhöhter Blutdruck, der das Risiko für Herz-Kreislauf-Erkrankungen erhöht.'),
(4, 'Depression', 'Affektive Störung mit anhaltender Traurigkeit, Antriebslosigkeit und Schlafstörungen.'),
(5, 'Krebs', 'Bösartige Wucherung von Zellen, die sich unkontrolliert teilen und in andere Gewebe einwandern kann.'),
(6, 'Herzinsuffizienz', 'Schwäche des Herzens, die zu Flüssigkeitsansammlungen und Atemnot führt.'),
(7, 'Epilepsie', 'Neurologische Erkrankung mit wiederkehrenden, unprovozierten Anfällen.'),
(8, 'Adipositas', 'Pathologisches Übergewicht, das mit Komorbiditäten wie Diabetes assoziiert ist.'),
(9, 'Schwangerschaft', 'Physiologischer Zustand der Frau mit Fötusentwicklung, der als vorerkrankung in Versicherungskontexten gilt.'),
(10, 'COPD', 'Chronisch obstruktive Lungenerkrankung mit fortschreitender Atemnot und Husten.'),
(11, 'Schlafapnoe-Syndrom', 'Wiederholte Atemaussetzer im Schlaf, die zu Tagesmüdigkeit führen.'),
(12, 'Angststörung', 'Übermäßige Angst und Sorge, die das tägliche Leben beeinträchtigen.'),
(13, 'Akne', 'Hauterkrankung mit Entzündungen der Talgdrüsen, häufig im Gesicht.'),
(14, 'Lupus erythematodes', 'Autoimmunerkrankung, die Haut, Gelenke und Organe betrifft.'),
(15, 'HIV/AIDS', 'Virusinfektion, die das Immunsystem schwächt und zu opportunistischen Infektionen führt.'),
(16, 'Nierenversagen', 'Verlust der Nierenfunktion, der Dialyse oder Transplantation erfordert.'),
(17, 'Schlaganfall', 'Akuter Gefäßverschluss oder -riss im Gehirn, der neurologische Ausfälle verursacht.'),
(18, 'Herzinfarkt', 'Akuter Verschluss einer Herzkranzarterie, der Herzmuskelgewebe schädigt.'),
(19, 'Alzheimer-Krankheit', 'Progressiv fortschreitende Demenz mit Gedächtnis- und Denkstörungen.'),
(20, 'Amyotrophe Lateralsklerose (ALS)', 'Neurodegenerative Erkrankung, die Motoneuron abbaut und Lähmungen verursacht.');

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
  ADD KEY `fk_diagnose_symptome` (`Symptome_ID`);

--
-- Indizes für die Tabelle `diagnose_details`
--
ALTER TABLE `diagnose_details`
  ADD PRIMARY KEY (`Detail_ID`),
  ADD KEY `fk_diagnose_details_diagnose_id` (`Diagnose_ID`),
  ADD KEY `fk_diagnose_details_symptome_id` (`Symptome_ID`);

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
  ADD KEY `patienten_vorerkrankungs_ibfk_1` (`Patienten_ID`),
  ADD KEY `patienten_vorerkrankungs_ibfk_2` (`Vorerkrankungs_ID`);

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
  ADD KEY `fk_termine_artzt_ID` (`Artzt_ID`),
  ADD KEY `fk_termine_patienten_ID` (`Patienten_ID`);

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
-- AUTO_INCREMENT für Tabelle `diagnose_details`
--
ALTER TABLE `diagnose_details`
  MODIFY `Detail_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `fachbereiche`
--
ALTER TABLE `fachbereiche`
  MODIFY `Fachbereichs_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  MODIFY `Symptome_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT für Tabelle `termine`
--
ALTER TABLE `termine`
  MODIFY `Termin_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `vorerkrankungen`
--
ALTER TABLE `vorerkrankungen`
  MODIFY `Vorerkrankungs_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

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
  ADD CONSTRAINT `fk_diagnonse_Patienten_ID` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_diagnose_symptome` FOREIGN KEY (`Symptome_ID`) REFERENCES `symptome` (`Symptome_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `diagnose_details`
--
ALTER TABLE `diagnose_details`
  ADD CONSTRAINT `fk_diagnose_details_diagnose_id` FOREIGN KEY (`Diagnose_ID`) REFERENCES `diagnose` (`Diagnose_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_diagnose_details_symptome_id` FOREIGN KEY (`Symptome_ID`) REFERENCES `symptome` (`Symptome_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `fachbereich_symptome`
--
ALTER TABLE `fachbereich_symptome`
  ADD CONSTRAINT `fk_fachbereich_symptome_fachbereich_id` FOREIGN KEY (`Fachbereichs_ID`) REFERENCES `fachbereiche` (`Fachbereichs_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_fachbereich_symptome_symptome_id` FOREIGN KEY (`Symptome_ID`) REFERENCES `symptome` (`Symptome_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `patienten_vorerkrankungs`
--
ALTER TABLE `patienten_vorerkrankungs`
  ADD CONSTRAINT `patienten_vorerkrankungs_ibfk_1` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patienten_vorerkrankungs_ibfk_2` FOREIGN KEY (`Vorerkrankungs_ID`) REFERENCES `vorerkrankungen` (`Vorerkrankungs_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `support`
--
ALTER TABLE `support`
  ADD CONSTRAINT `fk_support_mitarbeiter_ID` FOREIGN KEY (`Mitarbeiter_ID`) REFERENCES `mitarbeiter` (`Mitarbeiter_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_support_patienten_ID` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `termine`
--
ALTER TABLE `termine`
  ADD CONSTRAINT `fk_termine_artzt_ID` FOREIGN KEY (`Artzt_ID`) REFERENCES `ärtzte` (`Artzt_ID`),
  ADD CONSTRAINT `fk_termine_patienten_ID` FOREIGN KEY (`Patienten_ID`) REFERENCES `patienten` (`Patienten_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `ärtzte`
--
ALTER TABLE `ärtzte`
  ADD CONSTRAINT `fk_ätzte_fachbereichs_ID` FOREIGN KEY (`Fachbereichs_ID`) REFERENCES `fachbereiche` (`Fachbereichs_ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
