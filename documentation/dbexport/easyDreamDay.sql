-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql-db
-- Erstellungszeit: 11. Jun 2025 um 22:19
-- Server-Version: 9.3.0
-- PHP-Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `easyDreamDay`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Gift`
--

CREATE TABLE `Gift` (
  `GiftId` int NOT NULL,
  `WeddingId` int NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `Gift`
--

INSERT INTO `Gift` (`GiftId`, `WeddingId`, `Name`) VALUES
(1, 1, 'Airfryer'),
(2, 1, 'Thermomix'),
(3, 2, 'Airfryer'),
(4, 2, 'Thermomix');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `GiftReservation`
--

CREATE TABLE `GiftReservation` (
  `ReservationId` int NOT NULL,
  `GiftId` int NOT NULL,
  `GuestId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Guest`
--

CREATE TABLE `Guest` (
  `GuestId` int NOT NULL,
  `WeddingId` int DEFAULT NULL,
  `UserId` int DEFAULT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `AdditionalText` text,
  `RSVP` enum('Yes','No','Pending') DEFAULT 'Pending',
  `GuestGroupId` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `Guest`
--

INSERT INTO `Guest` (`GuestId`, `WeddingId`, `UserId`, `FirstName`, `LastName`, `Phone`, `Email`, `AdditionalText`, `RSVP`, `GuestGroupId`) VALUES
(1, 1, NULL, 'Miriam', 'Gnadlinger', '0676821271629', 'miriam.gnadlinger@gmail.com', 'Braut', 'Yes', NULL),
(2, 1, NULL, 'Niklas', 'Edelbauer', '06768545875', 'niklas.edelbauer', 'Braeutigam', 'Yes', NULL),
(3, 1, NULL, 'Simone', 'Sperrer', NULL, 'simone.sperrer@gmail.com', 'Brautjungfer', 'Pending', NULL),
(4, 1, NULL, 'Annika', 'Unterbrunner', NULL, 'annika.unterbrunner@gmail.com', 'Brautjungfer', 'Pending', NULL),
(5, 1, 2, 'Marie', 'Wakolbinger', NULL, 'marie.wakolbinger@gmail.com', 'Brautjungfer', 'Pending', NULL),
(6, 1, NULL, 'Lara', 'Schlager', NULL, 'lara.schlager@gmail.com', 'Brautjungfer', 'Pending', NULL),
(7, 1, NULL, 'Johanna', 'Haider', NULL, 'johanna.haider@gmail.com', 'Schwester', 'Pending', 1),
(8, 1, NULL, 'Klemens', 'Haider', NULL, 'klemens.haider@gmail.com', 'Schwager', 'Pending', 1),
(9, 1, NULL, 'Klara', 'Haider', NULL, 'klara.haider@gmail.com', 'Nichte', 'Pending', 1),
(10, 1, NULL, 'Christa', 'Gnadlinger', NULL, 'christa.gnadlinger@gmail.com', 'Mutter', 'Pending', 2),
(11, 1, NULL, 'Hannes', 'Gnadlinger', NULL, 'hannes.gnadlinger@gmail.com', 'Vater', 'Pending', 2),
(12, 2, NULL, 'Miriam', 'Gnadlinger', '0676281271629', 'miriam.gnadlinger@gmail.com', 'Braut', 'Yes', NULL),
(13, 2, NULL, 'Tobias', 'Neubauer', '0676824515', 'tobias.neubauer@gmail.com', 'Braeutigam', 'Yes', NULL),
(14, 2, 4, 'Lena', 'Kiesenebner ', NULL, 'lena.kiesenebner@gmail.com', 'Brautjungfer', 'Pending', NULL),
(15, 2, NULL, 'Theresa', 'Gnadlinger', NULL, 'theri.gnadlinger@gmail.com', 'Brautjungfer', 'Pending', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `GuestGroup`
--

CREATE TABLE `GuestGroup` (
  `GuestGroupId` int NOT NULL,
  `WeddingId` int NOT NULL,
  `Name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `GuestGroup`
--

INSERT INTO `GuestGroup` (`GuestGroupId`, `WeddingId`, `Name`) VALUES
(1, 1, 'Haider'),
(2, 1, 'Gnadlinger');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `GuestGroup_Member`
--

CREATE TABLE `GuestGroup_Member` (
  `GuestGroupId` int NOT NULL,
  `GuestId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Photo`
--

CREATE TABLE `Photo` (
  `PhotoId` int NOT NULL,
  `WeddingId` int NOT NULL,
  `Link` varchar(255) NOT NULL,
  `Description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Schedule`
--

CREATE TABLE `Schedule` (
  `ScheduleId` int NOT NULL,
  `WeddingId` int NOT NULL,
  `Time` time NOT NULL,
  `EventName` varchar(255) NOT NULL,
  `MeetingPoint` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `Schedule`
--

INSERT INTO `Schedule` (`ScheduleId`, `WeddingId`, `Time`, `EventName`, `MeetingPoint`) VALUES
(1, 1, '09:00:00', 'Brautpaarfotos', 'Stiftsgelände'),
(2, 1, '10:00:00', 'Kirchenbeginn', 'Kirchberg'),
(3, 1, '12:00:00', 'Agape', 'Kirchenplatz'),
(4, 1, '13:00:00', 'Essen', 'Gasthof König'),
(5, 1, '15:30:00', 'Kaffe und Kuchenbuffet', 'Gasthof König'),
(6, 2, '10:00:00', 'Beginn', 'Rathaus Kremsmünster'),
(7, 2, '12:00:00', 'Essen', 'Gasthaus Hohe Linde '),
(8, 2, '15:00:00', 'Kuchen', 'Gasthaus Hohe Linde'),
(9, 2, '18:00:00', 'Abendprogramm', 'Gasthaus Hohe Linde');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ToDo`
--

CREATE TABLE `ToDo` (
  `ToDoId` int NOT NULL,
  `WeddingId` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `Done` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `ToDo`
--

INSERT INTO `ToDo` (`ToDoId`, `WeddingId`, `Name`, `Date`, `Time`, `Done`) VALUES
(1, 1, 'Brautkleid aussuchen', '2025-10-15', '10:00:00', 0),
(2, 1, 'Brautkleid abholen', '2026-01-13', '10:00:00', 0),
(3, 2, 'Blumen abholen', '2025-06-11', '10:00:00', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User`
--

CREATE TABLE `User` (
  `UserId` int NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Phone` varchar(15) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `Role` enum('Planner','Guest') NOT NULL,
  `PasswordHash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `User`
--

INSERT INTO `User` (`UserId`, `Username`, `Phone`, `Email`, `Role`, `PasswordHash`) VALUES
(1, 'Theresa', '0676821271629', 'theresa.gnadlinger@gmail.com', 'Planner', '$2y$10$BgCs0cFaowF59A46vP.6..w/JMgoIWjgRei2cBTc4IH5Rnxtl98qS'),
(2, 'marie', NULL, 'marie.wakolbinger@gmail.com', 'Guest', '$2y$10$Wcl25muWEwPE0E5dQxBYCejmK4BH8rew4jZAOudoAs.0QymPgew/6'),
(3, 'Johanna', '0676821271629', 'johanna.haider@gmail.com', 'Planner', '$2y$10$pETsETUn/BGdSWdaM4PJ6./u7CkrXgYYbK6yxbgBEdLr2ciEjBjhi'),
(4, 'Lena', NULL, 'lena.kiesenebner@gmail.com', 'Guest', '$2y$10$QxmYgW2B4flSIW3QjbCEJeDEERI0xw7t03Adt2iF34aX92bjzEj0.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Wedding`
--

CREATE TABLE `Wedding` (
  `WeddingId` int NOT NULL,
  `Date` date NOT NULL,
  `Time` time NOT NULL,
  `Location1` varchar(255) NOT NULL,
  `Location2` varchar(255) NOT NULL,
  `CeremonyType` enum('standesamtlich','kirchlich','frei') NOT NULL,
  `Dresscode` varchar(255) NOT NULL,
  `Partner1Id` int DEFAULT NULL,
  `Partner2Id` int DEFAULT NULL,
  `PlannerId` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `Wedding`
--

INSERT INTO `Wedding` (`WeddingId`, `Date`, `Time`, `Location1`, `Location2`, `CeremonyType`, `Dresscode`, `Partner1Id`, `Partner2Id`, `PlannerId`) VALUES
(1, '2026-02-14', '10:00:00', 'Kirchberg', 'Gasthof König', 'kirchlich', 'Tracht', 1, 2, 1),
(2, '2025-06-13', '10:00:00', 'Rathaus Kremsmünster', 'Gasthaus Hohe Linde', 'standesamtlich', 'Dresscode: Blau', 12, 13, 3);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `Gift`
--
ALTER TABLE `Gift`
  ADD PRIMARY KEY (`GiftId`),
  ADD KEY `WeddingId` (`WeddingId`);

--
-- Indizes für die Tabelle `GiftReservation`
--
ALTER TABLE `GiftReservation`
  ADD PRIMARY KEY (`ReservationId`),
  ADD KEY `GiftId` (`GiftId`),
  ADD KEY `GuestId` (`GuestId`);

--
-- Indizes für die Tabelle `Guest`
--
ALTER TABLE `Guest`
  ADD PRIMARY KEY (`GuestId`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `WeddingId` (`WeddingId`),
  ADD KEY `GuestGroupId` (`GuestGroupId`);

--
-- Indizes für die Tabelle `GuestGroup`
--
ALTER TABLE `GuestGroup`
  ADD PRIMARY KEY (`GuestGroupId`),
  ADD KEY `WeddingId` (`WeddingId`);

--
-- Indizes für die Tabelle `GuestGroup_Member`
--
ALTER TABLE `GuestGroup_Member`
  ADD PRIMARY KEY (`GuestGroupId`,`GuestId`),
  ADD KEY `GuestId` (`GuestId`);

--
-- Indizes für die Tabelle `Photo`
--
ALTER TABLE `Photo`
  ADD PRIMARY KEY (`PhotoId`),
  ADD KEY `WeddingId` (`WeddingId`);

--
-- Indizes für die Tabelle `Schedule`
--
ALTER TABLE `Schedule`
  ADD PRIMARY KEY (`ScheduleId`),
  ADD KEY `WeddingId` (`WeddingId`);

--
-- Indizes für die Tabelle `ToDo`
--
ALTER TABLE `ToDo`
  ADD PRIMARY KEY (`ToDoId`),
  ADD KEY `WeddingId` (`WeddingId`);

--
-- Indizes für die Tabelle `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`UserId`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indizes für die Tabelle `Wedding`
--
ALTER TABLE `Wedding`
  ADD PRIMARY KEY (`WeddingId`),
  ADD KEY `Partner1Id` (`Partner1Id`),
  ADD KEY `Partner2Id` (`Partner2Id`),
  ADD KEY `PlannerId` (`PlannerId`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `Gift`
--
ALTER TABLE `Gift`
  MODIFY `GiftId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `GiftReservation`
--
ALTER TABLE `GiftReservation`
  MODIFY `ReservationId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Guest`
--
ALTER TABLE `Guest`
  MODIFY `GuestId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT für Tabelle `GuestGroup`
--
ALTER TABLE `GuestGroup`
  MODIFY `GuestGroupId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT für Tabelle `Photo`
--
ALTER TABLE `Photo`
  MODIFY `PhotoId` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `Schedule`
--
ALTER TABLE `Schedule`
  MODIFY `ScheduleId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT für Tabelle `ToDo`
--
ALTER TABLE `ToDo`
  MODIFY `ToDoId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `User`
--
ALTER TABLE `User`
  MODIFY `UserId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `Wedding`
--
ALTER TABLE `Wedding`
  MODIFY `WeddingId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `Gift`
--
ALTER TABLE `Gift`
  ADD CONSTRAINT `Gift_ibfk_1` FOREIGN KEY (`WeddingId`) REFERENCES `Wedding` (`WeddingId`);

--
-- Constraints der Tabelle `GiftReservation`
--
ALTER TABLE `GiftReservation`
  ADD CONSTRAINT `GiftReservation_ibfk_1` FOREIGN KEY (`GiftId`) REFERENCES `Gift` (`GiftId`),
  ADD CONSTRAINT `GiftReservation_ibfk_2` FOREIGN KEY (`GuestId`) REFERENCES `Guest` (`GuestId`);

--
-- Constraints der Tabelle `Guest`
--
ALTER TABLE `Guest`
  ADD CONSTRAINT `Guest_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `User` (`UserId`),
  ADD CONSTRAINT `Guest_ibfk_2` FOREIGN KEY (`WeddingId`) REFERENCES `Wedding` (`WeddingId`),
  ADD CONSTRAINT `Guest_ibfk_3` FOREIGN KEY (`GuestGroupId`) REFERENCES `GuestGroup` (`GuestGroupId`);

--
-- Constraints der Tabelle `GuestGroup`
--
ALTER TABLE `GuestGroup`
  ADD CONSTRAINT `GuestGroup_ibfk_1` FOREIGN KEY (`WeddingId`) REFERENCES `Wedding` (`WeddingId`);

--
-- Constraints der Tabelle `GuestGroup_Member`
--
ALTER TABLE `GuestGroup_Member`
  ADD CONSTRAINT `GuestGroup_Member_ibfk_1` FOREIGN KEY (`GuestGroupId`) REFERENCES `GuestGroup` (`GuestGroupId`),
  ADD CONSTRAINT `GuestGroup_Member_ibfk_2` FOREIGN KEY (`GuestId`) REFERENCES `Guest` (`GuestId`);

--
-- Constraints der Tabelle `Photo`
--
ALTER TABLE `Photo`
  ADD CONSTRAINT `Photo_ibfk_1` FOREIGN KEY (`WeddingId`) REFERENCES `Wedding` (`WeddingId`);

--
-- Constraints der Tabelle `Schedule`
--
ALTER TABLE `Schedule`
  ADD CONSTRAINT `Schedule_ibfk_1` FOREIGN KEY (`WeddingId`) REFERENCES `Wedding` (`WeddingId`);

--
-- Constraints der Tabelle `ToDo`
--
ALTER TABLE `ToDo`
  ADD CONSTRAINT `ToDo_ibfk_1` FOREIGN KEY (`WeddingId`) REFERENCES `Wedding` (`WeddingId`);

--
-- Constraints der Tabelle `Wedding`
--
ALTER TABLE `Wedding`
  ADD CONSTRAINT `Wedding_ibfk_1` FOREIGN KEY (`Partner1Id`) REFERENCES `Guest` (`GuestId`),
  ADD CONSTRAINT `Wedding_ibfk_2` FOREIGN KEY (`Partner2Id`) REFERENCES `Guest` (`GuestId`),
  ADD CONSTRAINT `Wedding_ibfk_3` FOREIGN KEY (`PlannerId`) REFERENCES `User` (`UserId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
