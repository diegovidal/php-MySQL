-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 01, 2015 at 07:01 AM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bigchef`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievement`
--

CREATE TABLE IF NOT EXISTS `achievement` (
  `achievementId` int(11) NOT NULL AUTO_INCREMENT,
  `achievementMedia` varchar(30) NOT NULL,
  `achievementTitle` varchar(30) NOT NULL,
  PRIMARY KEY (`achievementId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `idAdmin` int(11) NOT NULL AUTO_INCREMENT,
  `nameAdmin` varchar(45) NOT NULL,
  `userAdmin` varchar(30) NOT NULL,
  `passwordAdmin` varchar(30) NOT NULL,
  PRIMARY KEY (`idAdmin`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `challenge`
--

CREATE TABLE IF NOT EXISTS `challenge` (
  `challengeId` int(11) NOT NULL AUTO_INCREMENT,
  `challengeDetail` text NOT NULL,
  `challengeStartDate` date NOT NULL,
  `challengeEndDate` date NOT NULL,
  `challengeName` varchar(45) NOT NULL,
  `challengeNumberOfDishes` int(5) NOT NULL,
  `challengeMaxPoints` int(5) NOT NULL,
  `challengeMedia` varchar(30) NOT NULL,
  `achievementId` int(11) DEFAULT NULL,
  PRIMARY KEY (`challengeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `commentText` text NOT NULL,
  `userId` int(11) NOT NULL,
  `submissionId` int(11) NOT NULL,
  `commentId` int(11) NOT NULL AUTO_INCREMENT,
  `commentDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`commentId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `commentreport`
--

CREATE TABLE IF NOT EXISTS `commentreport` (
  `commentreportId` int(11) NOT NULL AUTO_INCREMENT,
  `commentreportText` text NOT NULL,
  `commentId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`commentreportId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `ingredient`
--

CREATE TABLE IF NOT EXISTS `ingredient` (
  `ingredientId` int(11) NOT NULL AUTO_INCREMENT,
  `ingredientName` varchar(30) NOT NULL,
  `ingredientMedia` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`ingredientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE IF NOT EXISTS `recipe` (
  `recipeId` int(11) NOT NULL AUTO_INCREMENT,
  `recipeDishes` int(5) NOT NULL,
  `recipeCookingTime` int(5) NOT NULL,
  `recipeTitle` varchar(45) NOT NULL,
  `recipeOrigin` varchar(45) NOT NULL,
  `submissionId` int(11) DEFAULT NULL,
  PRIMARY KEY (`recipeId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=126 ;

-- --------------------------------------------------------

--
-- Table structure for table `recipexingredient`
--

CREATE TABLE IF NOT EXISTS `recipexingredient` (
  `recipexingredientId` int(11) NOT NULL AUTO_INCREMENT,
  `recipeId` int(11) NOT NULL,
  `ingredientId` int(11) NOT NULL,
  `quantity` int(8) NOT NULL,
  `measure` varchar(35) NOT NULL,
  PRIMARY KEY (`recipexingredientId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=193 ;

-- --------------------------------------------------------

--
-- Table structure for table `step`
--

CREATE TABLE IF NOT EXISTS `step` (
  `stepId` int(11) NOT NULL AUTO_INCREMENT,
  `recipeId` int(11) NOT NULL,
  `stepMedia` varchar(30) NOT NULL,
  `stepDetail` text NOT NULL,
  PRIMARY KEY (`stepId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=325 ;

-- --------------------------------------------------------

--
-- Table structure for table `submission`
--

CREATE TABLE IF NOT EXISTS `submission` (
  `submissionId` int(11) NOT NULL AUTO_INCREMENT,
  `submissionDetail` text NOT NULL,
  `submissionMedia` varchar(30) NOT NULL,
  `submissionPoints` int(5) NOT NULL,
  `userId` int(11) NOT NULL,
  `challengeId` int(11) NOT NULL,
  `recipeId` int(11) DEFAULT NULL,
  `submissionDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `submissionLikes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`submissionId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=71 ;

--
-- Triggers `submission`
--
DROP TRIGGER IF EXISTS `createUserChallenge`;
DELIMITER //
CREATE TRIGGER `createUserChallenge` AFTER INSERT ON `submission`
 FOR EACH ROW BEGIN
    DECLARE x INT;
    DECLARE achievementId INT;
	DECLARE maxDishes INT;
	DECLARE points INT;
	DECLARE userTitleId INT;
	DECLARE titleId INT;

	SET x = (SELECT COUNT(*) FROM `userxchallenge` WHERE `userId`=NEW.userId AND challengeId=NEW.challengeId);
	SET points = (SELECT SUM(`submissionLikes`)+SUM(`submissionPoints`) FROM `submission` WHERE `userId`=NEW.userId GROUP BY `userId`);

	SELECT `achievementId`, `challengeNumberOfDishes` FROM `challenge` WHERE `challengeId`=NEW.challengeId INTO achievementId, maxDishes;

	SET titleId = (SELECT titleId FROM title WHERE titlePoints <= (SELECT points) ORDER BY titlePoints DESC LIMIT 1);
	SET userTitleId = (SELECT titleId FROM user WHERE userId=NEW.userId);

	IF (SELECT x) = 0 THEN
		INSERT INTO `userxchallenge` (`userId`, `challengeId`) VALUES (NEW.userId, NEW.challengeId);
	END IF;

	IF NEW.recipeId IS NOT NULL THEN
		UPDATE `recipe` SET `submissionId` = NEW.submissionId WHERE `recipeId`=NEW.recipeId;
	END IF;

	IF (SELECT achievementId) IS NOT NULL THEN
		IF (SELECT x) = (SELECT maxDishes) THEN
			INSERT INTO `userxachievement`(`achievementId`, `userId`, `challengeId`) VALUES ((SELECT achievementId),NEW.userId,NEW.challengeId);
		END IF;
	END IF;

	IF (SELECT userTitleId) > (SELECT titleId) THEN
		UPDATE `user` SET `titleId`=(SELECT titleId) WHERE `userId`=NEW.userId;
	END IF;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `submissionreport`
--

CREATE TABLE IF NOT EXISTS `submissionreport` (
  `submissionreportId` int(11) NOT NULL AUTO_INCREMENT,
  `submissionId` int(11) NOT NULL,
  `submissionreportText` text NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`submissionreportId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `sugestionsubmissionchallenge`
--

CREATE TABLE IF NOT EXISTS `sugestionsubmissionchallenge` (
  `challengeId` int(11) NOT NULL,
  `submissionId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `s_like`
--

CREATE TABLE IF NOT EXISTS `s_like` (
  `userId` int(11) NOT NULL,
  `submissionId` int(11) NOT NULL,
  PRIMARY KEY (`submissionId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Triggers `s_like`
--
DROP TRIGGER IF EXISTS `addSubmissionLikes`;
DELIMITER //
CREATE TRIGGER `addSubmissionLikes` AFTER INSERT ON `s_like`
 FOR EACH ROW UPDATE submission 
SET submissionLikes = submissionLikes + 1 
WHERE submissionId=NEW.submissionId
//
DELIMITER ;
DROP TRIGGER IF EXISTS `removeSubmissionLikes`;
DELIMITER //
CREATE TRIGGER `removeSubmissionLikes` AFTER DELETE ON `s_like`
 FOR EACH ROW UPDATE submission 
SET submissionLikes = submissionLikes - 1 
WHERE submissionId=OLD.submissionId
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `title`
--

CREATE TABLE IF NOT EXISTS `title` (
  `titleId` int(11) NOT NULL AUTO_INCREMENT,
  `titleText` varchar(40) NOT NULL,
  `titlePoints` int(5) NOT NULL,
  PRIMARY KEY (`titleId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `userIdFacebook` varchar(30) DEFAULT NULL,
  `userEmail` varchar(45) NOT NULL,
  `userMedia` varchar(30) DEFAULT NULL,
  `userName` varchar(40) NOT NULL,
  `userPassword` varchar(40) NOT NULL,
  `titleId` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`userId`),
  UNIQUE KEY `userEmail` (`userEmail`),
  UNIQUE KEY `userIdFacebook` (`userIdFacebook`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=54 ;

-- --------------------------------------------------------

--
-- Table structure for table `userxachievement`
--

CREATE TABLE IF NOT EXISTS `userxachievement` (
  `achievementId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `challengeId` int(11) NOT NULL,
  PRIMARY KEY (`achievementId`,`userId`,`challengeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userxchallenge`
--

CREATE TABLE IF NOT EXISTS `userxchallenge` (
  `userId` int(11) NOT NULL,
  `challengeId` int(11) NOT NULL,
  PRIMARY KEY (`userId`,`challengeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
