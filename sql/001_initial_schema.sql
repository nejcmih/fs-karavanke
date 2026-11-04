-- Baseline database schema for KD FS Karavanke
-- Compatible with MariaDB 10.4+ (local XAMPP) and MariaDB 10.11+ (cPanel)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `clanki` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `naslov` varchar(200) NOT NULL,
  `kategorija` varchar(100) NOT NULL,
  `kraj` varchar(100) DEFAULT NULL,
  `datum` date NOT NULL,
  `preview` text NOT NULL,
  `besedilo` text NOT NULL,
  `naslovnaslika` varchar(200) NOT NULL,
  `slike` text DEFAULT NULL,
  `mapa` varchar(200) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `usersId` int(11) NOT NULL AUTO_INCREMENT,
  `usersName` varchar(100) NOT NULL,
  `usersUid` varchar(100) NOT NULL,
  `usersPwd` varchar(100) NOT NULL,
  PRIMARY KEY (`usersId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

