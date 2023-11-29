-- MySQL dump 10.13  Distrib 5.7.33, for Win64 (x86_64)
--
-- Host: localhost    Database: s_amwright
-- ------------------------------------------------------
-- Server version	5.7.33

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accountstatus`
--

DROP TABLE IF EXISTS `accountstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accountstatus` (
  `AccountStatusID` int(11) NOT NULL AUTO_INCREMENT,
  `AccountStatusName` varchar(50) NOT NULL,
  PRIMARY KEY (`AccountStatusID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accountstatus`
--

LOCK TABLES `accountstatus` WRITE;
/*!40000 ALTER TABLE `accountstatus` DISABLE KEYS */;
INSERT INTO `accountstatus` VALUES (1,'Regular Acct'),(2,'Plus Acct'),(3,'Premium Acct'),(4,'Inactive Acct');
/*!40000 ALTER TABLE `accountstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `UserID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `ItemQuantity` int(11) NOT NULL,
  PRIMARY KEY (`UserID`,`ItemID`),
  KEY `Cart_Item_idx` (`ItemID`),
  CONSTRAINT `Cart_Item` FOREIGN KEY (`ItemID`) REFERENCES `item` (`ItemID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `Cart_User` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `DepartmentID` int(11) NOT NULL AUTO_INCREMENT,
  `DeptName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`DepartmentID`),
  UNIQUE KEY `DeptName` (`DeptName`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item` (
  `ItemID` int(11) NOT NULL AUTO_INCREMENT,
  `SKU` int(11) NOT NULL,
  `ItemName` varchar(50) NOT NULL,
  `ItemType` varchar(50) NOT NULL,
  `SellerID` int(11) NOT NULL,
  `Price` decimal(6,2) NOT NULL,
  `QuantityAvailable` int(11) NOT NULL,
  PRIMARY KEY (`ItemID`),
  UNIQUE KEY `SellerID` (`SellerID`,`ItemName`),
  UNIQUE KEY `SellerID_2` (`SellerID`,`SKU`),
  KEY `Item_Seller_idx` (`SellerID`),
  CONSTRAINT `Item_Seller` FOREIGN KEY (`SellerID`) REFERENCES `seller` (`SellerID`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item`
--

LOCK TABLES `item` WRITE;
/*!40000 ALTER TABLE `item` DISABLE KEYS */;
/*!40000 ALTER TABLE `item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `list`
--

DROP TABLE IF EXISTS `list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list` (
  `ListID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `listName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ListID`,`UserID`),
  UNIQUE KEY `listName` (`listName`,`UserID`),
  KEY `FK_List_User_idx` (`UserID`),
  CONSTRAINT `FK_List_User` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `list`
--

LOCK TABLES `list` WRITE;
/*!40000 ALTER TABLE `list` DISABLE KEYS */;
/*!40000 ALTER TABLE `list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `listitem`
--

DROP TABLE IF EXISTS `listitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `listitem` (
  `ListID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `ItemQuantity` int(11) NOT NULL,
  PRIMARY KEY (`ListID`,`ItemID`),
  KEY `FK_ListItem_Item_idx` (`ItemID`),
  CONSTRAINT `FK_ListItem_Item` FOREIGN KEY (`ItemID`) REFERENCES `item` (`ItemID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_ListItem_List` FOREIGN KEY (`ListID`) REFERENCES `list` (`ListID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `listitem`
--

LOCK TABLES `listitem` WRITE;
/*!40000 ALTER TABLE `listitem` DISABLE KEYS */;
/*!40000 ALTER TABLE `listitem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `OrderID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `TotalPrice` decimal(6,2) NOT NULL,
  `OrderStatus` enum('Placed','Shipped','Delivered','Canceled') NOT NULL,
  `CreditCardNumber` varchar(16) NOT NULL,
  `OrderDate` date NOT NULL,
  PRIMARY KEY (`OrderID`),
  KEY `Order_User_idx` (`UserID`),
  CONSTRAINT `Order_User` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderitem`
--

DROP TABLE IF EXISTS `orderitem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderitem` (
  `OrderID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `ItemQuantity` int(11) NOT NULL,
  PRIMARY KEY (`OrderID`,`ItemID`),
  KEY `FK_OrderItem_Item_idx` (`ItemID`),
  CONSTRAINT `FK_OrderItem_Item` FOREIGN KEY (`ItemID`) REFERENCES `item` (`ItemID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_OrderItem_Order` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderitem`
--

LOCK TABLES `orderitem` WRITE;
/*!40000 ALTER TABLE `orderitem` DISABLE KEYS */;
/*!40000 ALTER TABLE `orderitem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `return`
--

DROP TABLE IF EXISTS `return`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `return` (
  `OrderID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `ItemQuantity` int(11) DEFAULT NULL,
  `ReturnDate` date DEFAULT NULL,
  PRIMARY KEY (`OrderID`,`ItemID`),
  KEY `FK_Return_Item_idx` (`ItemID`),
  CONSTRAINT `FK_Return_Item` FOREIGN KEY (`ItemID`) REFERENCES `item` (`ItemID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_Return_User` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `return`
--

LOCK TABLES `return` WRITE;
/*!40000 ALTER TABLE `return` DISABLE KEYS */;
/*!40000 ALTER TABLE `return` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seller`
--

DROP TABLE IF EXISTS `seller`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller` (
  `SellerID` int(11) NOT NULL AUTO_INCREMENT,
  `SellerName` varchar(50) NOT NULL,
  `DepartmentID` int(11) NOT NULL,
  `PhoneNumber` bigint(20) DEFAULT NULL,
  `EmailAddress` varchar(75) NOT NULL,
  PRIMARY KEY (`SellerID`),
  UNIQUE KEY `SellerName` (`SellerName`),
  KEY `Seller_Department_idx` (`DepartmentID`),
  CONSTRAINT `Seller_Department` FOREIGN KEY (`DepartmentID`) REFERENCES `department` (`DepartmentID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seller`
--

LOCK TABLES `seller` WRITE;
/*!40000 ALTER TABLE `seller` DISABLE KEYS */;
/*!40000 ALTER TABLE `seller` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `AccountStatusID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `DeliveryAddress` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Language` varchar(50) NOT NULL,
  `PhoneNumber` bigint(20) DEFAULT NULL,
  `EmailAddress` varchar(150) NOT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `EmailAddress` (`EmailAddress`),
  KEY `User_AccountStatus_idx` (`AccountStatusID`),
  CONSTRAINT `User_AccountStatus` FOREIGN KEY (`AccountStatusID`) REFERENCES `accountstatus` (`AccountStatusID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-11-29 13:09:06
