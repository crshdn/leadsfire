-- LeadsFire Click Tracker Database Schema
-- Generated from CPVLab Pro backup
-- Compatible with MariaDB 10.5+ / MySQL 5.7+

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `affiliatesources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `affiliatesources` (
  `AffiliateSourceID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Affiliate` varchar(100) NOT NULL,
  `CurrencyID` smallint(5) unsigned DEFAULT 1,
  `RevenueParam` varchar(100) NOT NULL DEFAULT 'revenue',
  `SubIdSeparator` varchar(20) NOT NULL DEFAULT '_',
  `OfferParameter` varchar(50) DEFAULT NULL,
  `OfferTemplate` varchar(500) DEFAULT NULL,
  `PostbackURL` varchar(200) DEFAULT NULL,
  `PageName` varchar(100) DEFAULT NULL,
  `SubIdPlace` varchar(100) DEFAULT NULL,
  `RevenuePlace` varchar(100) DEFAULT NULL,
  `StatusPlace` varchar(100) DEFAULT NULL,
  `TransactionPlace` varchar(100) DEFAULT NULL,
  `Custom1Place` varchar(100) DEFAULT NULL,
  `Custom2Place` varchar(100) DEFAULT NULL,
  `Custom3Place` varchar(100) DEFAULT NULL,
  `Custom4Place` varchar(100) DEFAULT NULL,
  `Custom5Place` varchar(100) DEFAULT NULL,
  `StatusValues` varchar(200) DEFAULT NULL,
  `PassTsStatusValues` varchar(200) DEFAULT NULL,
  `DateAdded` datetime NOT NULL,
  PRIMARY KEY (`AffiliateSourceID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `alertprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alertprofiles` (
  `AlertProfileID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `AlertProfileName` varchar(100) NOT NULL,
  `DefaultProfile` tinyint(1) NOT NULL DEFAULT 0,
  `alert1Views` varchar(10) DEFAULT NULL,
  `alert1Conversion` varchar(10) DEFAULT NULL,
  `alert2Views` varchar(10) DEFAULT NULL,
  `alert2Clicks` varchar(10) DEFAULT NULL,
  `alert3Views` varchar(10) DEFAULT NULL,
  `alert3Subscribers` varchar(10) DEFAULT NULL,
  `alert4Views` varchar(10) DEFAULT NULL,
  `alert4SR` varchar(10) DEFAULT NULL,
  `alert5Views` varchar(10) DEFAULT NULL,
  `alert5CTR` varchar(10) DEFAULT NULL,
  `alert6Views` varchar(10) DEFAULT NULL,
  `alert6CR` varchar(10) DEFAULT NULL,
  `calert1Views` varchar(10) DEFAULT NULL,
  `calert2Views` varchar(10) DEFAULT NULL,
  `CreateDate` datetime NOT NULL,
  `ModifyDate` datetime DEFAULT NULL,
  `EmailAddresses` text DEFAULT NULL,
  PRIMARY KEY (`AlertProfileID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alerts` (
  `AlertID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `AlertName` varchar(45) NOT NULL,
  `AlertValue` varchar(45) NOT NULL,
  PRIMARY KEY (`AlertID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `apiintegrationaccounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apiintegrationaccounts` (
  `ApiAccountID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ApiIntegrationID` smallint(5) unsigned NOT NULL,
  `AccountID` varchar(150) NOT NULL,
  `AccountName` varchar(250) NOT NULL,
  `Currency` varchar(3) DEFAULT NULL,
  `BusinessCountryCode` varchar(2) DEFAULT NULL,
  `BusinessName` varchar(250) DEFAULT NULL,
  `Timezone` varchar(100) DEFAULT NULL,
  `TimezoneOffset` mediumint(9) DEFAULT 0,
  PRIMARY KEY (`ApiAccountID`),
  UNIQUE KEY `uq_apiintegrationid_accountid` (`ApiIntegrationID`,`AccountID`),
  CONSTRAINT `fk_apiintegrations_apiintegrationaccounts` FOREIGN KEY (`ApiIntegrationID`) REFERENCES `apiintegrations` (`ApiIntegrationID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `apiintegrationmatchcampaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apiintegrationmatchcampaigns` (
  `ApiMatchID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ApiAccountID` smallint(5) unsigned NOT NULL,
  `IntegrationCampaignID` varchar(150) NOT NULL,
  `IntegrationCampaignName` varchar(250) NOT NULL,
  `ManagerID` varchar(150) DEFAULT NULL,
  `IntegrationCampaignType` varchar(50) DEFAULT NULL,
  `CampaignID` smallint(5) unsigned NOT NULL,
  `LastImportDate` datetime DEFAULT NULL,
  PRIMARY KEY (`ApiMatchID`),
  KEY `fk_apiintegrationaccounts_apiintegrationmatchcampaigns` (`ApiAccountID`),
  CONSTRAINT `fk_apiintegrationaccounts_apiintegrationmatchcampaigns` FOREIGN KEY (`ApiAccountID`) REFERENCES `apiintegrationaccounts` (`ApiAccountID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `apiintegrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apiintegrations` (
  `ApiIntegrationID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ApiIntegrationName` varchar(190) DEFAULT NULL,
  `ApiTypeID` tinyint(3) unsigned DEFAULT NULL,
  `CostSyncType` tinyint(3) unsigned DEFAULT 2,
  `Active` tinyint(1) unsigned DEFAULT NULL,
  `DateAdded` datetime DEFAULT NULL,
  `Username` varchar(2000) DEFAULT NULL,
  `Password` varchar(190) DEFAULT NULL,
  `DeveloperKey` varchar(2000) DEFAULT NULL,
  `AccessToken` text DEFAULT NULL,
  `CustomerID` varchar(190) DEFAULT NULL,
  `Settings` text DEFAULT NULL,
  `AccessTokenExpire` datetime DEFAULT NULL,
  `RefreshToken` text DEFAULT NULL,
  `RefreshTokenExpire` datetime DEFAULT NULL,
  `LastApiLogID` int(10) unsigned DEFAULT NULL,
  `OptionSyncCosts` tinyint(1) unsigned DEFAULT 1,
  `OptionPassConversions` tinyint(1) unsigned DEFAULT 1,
  PRIMARY KEY (`ApiIntegrationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `apilog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apilog` (
  `ApiLogID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ApiIntegrationID` smallint(5) unsigned DEFAULT NULL,
  `DateAdded` datetime DEFAULT NULL,
  `CampaignsMatchedCount` smallint(5) unsigned DEFAULT NULL,
  `Status` text DEFAULT NULL,
  PRIMARY KEY (`ApiLogID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `apilogcampaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apilogcampaigns` (
  `ApiLogID` int(10) unsigned NOT NULL,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `KeywordsImportedCount` int(10) unsigned DEFAULT NULL,
  `Status` text DEFAULT NULL,
  `FileName` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`ApiLogID`,`CampaignID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `apilogtargets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apilogtargets` (
  `ApiLogID` int(10) unsigned NOT NULL,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `Cost` double DEFAULT NULL,
  PRIMARY KEY (`ApiLogID`,`CampaignID`,`SubIdID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `apitypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `apitypes` (
  `ApiTypeID` tinyint(3) unsigned NOT NULL,
  `ApiTypeName` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`ApiTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `bidads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bidads` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `AdValue` varchar(191) NOT NULL,
  `Cost` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`CampaignID`,`AdValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `bidsubids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `bidsubids` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `Cost` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`CampaignID`,`SubIdID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `blockedclicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blockedclicks` (
  `ClickID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned DEFAULT NULL,
  `SubIdID` int(10) unsigned DEFAULT NULL,
  `ViewDate` datetime DEFAULT NULL,
  `BlockReason` tinyint(4) DEFAULT 1,
  `IPBinary` varbinary(16) DEFAULT NULL,
  `UserAgent` varchar(191) DEFAULT NULL,
  `Referrer` varchar(191) NOT NULL DEFAULT '',
  PRIMARY KEY (`ClickID`),
  KEY `idxCampaignIDViewDate` (`CampaignID`,`ViewDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `blockrules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `blockrules` (
  `BlockRuleID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `BlockName` varchar(100) DEFAULT NULL,
  `StartIP` int(10) unsigned DEFAULT NULL,
  `EndIP` int(10) unsigned DEFAULT NULL,
  `BlockUA` varchar(191) DEFAULT NULL,
  `BlockReferrer` varchar(191) DEFAULT NULL,
  `Active` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`BlockRuleID`),
  KEY `idxStartIPEndIP` (`StartIP`,`EndIP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachead` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `Extra1ID` int(10) unsigned DEFAULT NULL,
  `Extra2ID` int(10) unsigned DEFAULT NULL,
  `Extra3ID` int(10) unsigned DEFAULT NULL,
  `Extra4ID` int(10) unsigned DEFAULT NULL,
  `Extra5ID` int(10) unsigned DEFAULT NULL,
  `Extra6ID` int(10) unsigned DEFAULT NULL,
  `Extra7ID` int(10) unsigned DEFAULT NULL,
  `Extra8ID` int(10) unsigned DEFAULT NULL,
  `Extra9ID` int(10) unsigned DEFAULT NULL,
  `Extra10ID` int(10) unsigned DEFAULT NULL,
  `AdValueID` int(10) unsigned DEFAULT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Engages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  UNIQUE KEY `idxExstingDetails` (`CampaignID`,`DateInterval`,`Extra1ID`,`Extra2ID`,`Extra3ID`,`Extra4ID`,`Extra5ID`,`Extra6ID`,`Extra7ID`,`Extra8ID`,`Extra9ID`,`Extra10ID`,`AdValueID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachecampaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachecampaign` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  UNIQUE KEY `idxExistingDetails` (`CampaignID`,`DateInterval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cacheconversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cacheconversion` (
  `ClickID` bigint(20) unsigned NOT NULL,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `ReferrerID` int(10) unsigned NOT NULL DEFAULT 1,
  `Extra1ID` int(10) unsigned DEFAULT NULL,
  `Extra2ID` int(10) unsigned DEFAULT NULL,
  `Extra3ID` int(10) unsigned DEFAULT NULL,
  `Extra4ID` int(10) unsigned DEFAULT NULL,
  `Extra5ID` int(10) unsigned DEFAULT NULL,
  `Extra6ID` int(10) unsigned DEFAULT NULL,
  `Extra7ID` int(10) unsigned DEFAULT NULL,
  `Extra8ID` int(10) unsigned DEFAULT NULL,
  `Extra9ID` int(10) unsigned DEFAULT NULL,
  `Extra10ID` int(10) unsigned DEFAULT NULL,
  `AdValueID` int(10) unsigned DEFAULT NULL,
  `DeviceID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `IspID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `ViewDate` datetime NOT NULL,
  `ConversionDate` datetime NOT NULL,
  `Revenue` double DEFAULT NULL,
  `IPBinary` varbinary(16) DEFAULT NULL,
  `UserAgentID` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`ClickID`),
  KEY `idxViewDate` (`ViewDate`),
  KEY `idxConversionDate` (`ConversionDate`),
  KEY `idxReferrerID` (`ReferrerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachedevice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachedevice` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `DeviceID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`),
  KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`DeviceID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachedrilldown`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachedrilldown` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CacheHash` char(32) NOT NULL DEFAULT '',
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `CityID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `Extra1ID` int(10) unsigned DEFAULT NULL,
  `Extra2ID` int(10) unsigned DEFAULT NULL,
  `Extra3ID` int(10) unsigned DEFAULT NULL,
  `Extra4ID` int(10) unsigned DEFAULT NULL,
  `Extra5ID` int(10) unsigned DEFAULT NULL,
  `Extra6ID` int(10) unsigned DEFAULT NULL,
  `Extra7ID` int(10) unsigned DEFAULT NULL,
  `Extra8ID` int(10) unsigned DEFAULT NULL,
  `Extra9ID` int(10) unsigned DEFAULT NULL,
  `Extra10ID` int(10) unsigned DEFAULT NULL,
  `AdValueID` int(10) unsigned DEFAULT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Engages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`),
  KEY `idxDrillDownHash` (`CacheHash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachegeo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachegeo` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `AdValueID` int(10) unsigned DEFAULT NULL,
  `DeviceID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `IspID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `CityID` mediumint(8) unsigned DEFAULT 1,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  UNIQUE KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`SubIdID`,`DestinationID`,`OfferID`,`AdValueID`,`DeviceID`,`IspID`,`CityID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cacheisp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cacheisp` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `IspID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`),
  KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`IspID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachelanding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachelanding` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `Level` tinyint(3) NOT NULL DEFAULT 1,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Engages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  UNIQUE KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`DestinationID`,`OfferID`,`Level`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`,`Level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cacheoffer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cacheoffer` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  `ConversionTime` float unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  UNIQUE KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`OfferID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cacherelation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cacherelation` (
  `ClickID` bigint(20) unsigned NOT NULL,
  `CacheTarget` int(10) unsigned DEFAULT NULL,
  `CacheAd` int(10) unsigned DEFAULT NULL,
  `CacheLanding` int(10) unsigned DEFAULT NULL,
  `CacheLevels` varchar(200) DEFAULT NULL,
  `CacheAfter` int(10) unsigned DEFAULT NULL,
  `CacheThankYou` int(10) unsigned DEFAULT NULL,
  `CacheOffer` int(10) unsigned DEFAULT NULL,
  `CacheCampaign` int(10) unsigned DEFAULT NULL,
  `CacheTraffic` int(10) unsigned DEFAULT NULL,
  `CacheDevice` int(10) unsigned DEFAULT NULL,
  `CacheIsp` int(10) unsigned DEFAULT NULL,
  `CacheTrendDay` int(10) unsigned DEFAULT NULL,
  `CacheTrendHour` int(10) unsigned DEFAULT NULL,
  `CacheGeo` int(10) unsigned DEFAULT NULL,
  `DateAdded` date DEFAULT NULL,
  PRIMARY KEY (`ClickID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachesubscriber`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachesubscriber` (
  `ClickID` bigint(20) unsigned NOT NULL,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `ReferrerID` int(10) unsigned NOT NULL DEFAULT 1,
  `Extra1ID` int(10) unsigned DEFAULT NULL,
  `Extra2ID` int(10) unsigned DEFAULT NULL,
  `Extra3ID` int(10) unsigned DEFAULT NULL,
  `Extra4ID` int(10) unsigned DEFAULT NULL,
  `Extra5ID` int(10) unsigned DEFAULT NULL,
  `Extra6ID` int(10) unsigned DEFAULT NULL,
  `Extra7ID` int(10) unsigned DEFAULT NULL,
  `Extra8ID` int(10) unsigned DEFAULT NULL,
  `Extra9ID` int(10) unsigned DEFAULT NULL,
  `Extra10ID` int(10) unsigned DEFAULT NULL,
  `AdValueID` int(10) unsigned DEFAULT NULL,
  `DeviceID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `IspID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `ViewDate` datetime NOT NULL,
  `SubscribeDate` datetime NOT NULL,
  `Revenue` double DEFAULT NULL,
  `IPBinary` varbinary(16) DEFAULT NULL,
  `UserAgentID` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`ClickID`),
  KEY `idxViewDate` (`ViewDate`),
  KEY `idxSubscribeDate` (`SubscribeDate`),
  KEY `idxReferrerID` (`ReferrerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachetarget`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachetarget` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CacheHash` char(32) NOT NULL DEFAULT '',
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `ReferrerID` int(10) unsigned NOT NULL DEFAULT 1,
  `SiteCategoryID` smallint(5) unsigned NOT NULL DEFAULT 0,
  `Extra1ID` int(10) unsigned DEFAULT NULL,
  `Extra2ID` int(10) unsigned DEFAULT NULL,
  `Extra3ID` int(10) unsigned DEFAULT NULL,
  `Extra4ID` int(10) unsigned DEFAULT NULL,
  `Extra5ID` int(10) unsigned DEFAULT NULL,
  `Extra6ID` int(10) unsigned DEFAULT NULL,
  `Extra7ID` int(10) unsigned DEFAULT NULL,
  `Extra8ID` int(10) unsigned DEFAULT NULL,
  `Extra9ID` int(10) unsigned DEFAULT NULL,
  `Extra10ID` int(10) unsigned DEFAULT NULL,
  `AdValueID` int(10) unsigned DEFAULT NULL,
  `DeviceID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `IspID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `IsDirectTraffic` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `IsMobileTraffic` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Engages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`),
  KEY `idxTargetHash` (`CacheHash`),
  KEY `idxReferrerID` (`ReferrerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachethankyou`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachethankyou` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Engages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  UNIQUE KEY `idxExistingDetails` (`CampaignID`,`DateInterval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachetotals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachetotals` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  `Profit` float NOT NULL DEFAULT 0,
  `ROI` float NOT NULL DEFAULT 0,
  `NewViews` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `NewSubscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `NewProfit` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CampaignID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachetraffic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachetraffic` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `IsDirectTraffic` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `IsMobileTraffic` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  UNIQUE KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`IsDirectTraffic`,`IsMobileTraffic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachetrendday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachetrendday` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Engages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`),
  KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`SubIdID`,`DestinationID`,`OfferID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cachetrendhour`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cachetrendhour` (
  `CacheID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateInterval` date NOT NULL,
  `HourInterval` tinyint(4) unsigned NOT NULL,
  `HourIntervalName` varchar(4) NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `Views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Engages` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Clicks` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Conversion` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Subscribers` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Cost` float NOT NULL DEFAULT 0,
  `Revenue` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`CacheID`),
  KEY `idxCampaignDate` (`CampaignID`,`DateInterval`),
  KEY `idxExistingDetails` (`CampaignID`,`DateInterval`,`HourInterval`,`SubIdID`,`DestinationID`,`OfferID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `campaigngroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaigngroups` (
  `CampaignGroupID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignGroup` varchar(100) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  PRIMARY KEY (`CampaignGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `campaignlevels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaignlevels` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `LevelID` tinyint(3) NOT NULL DEFAULT 1,
  `LevelName` varchar(100) DEFAULT NULL,
  `LevelLinks` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CampaignID`,`LevelID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `campaignoptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaignoptions` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `OptionID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `OptionName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CampaignID`,`OptionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaigns` (
  `CampaignID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignName` text DEFAULT NULL,
  `CampaignTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `CpvSourceID` smallint(5) unsigned DEFAULT 0,
  `SourceID` varchar(10) NOT NULL DEFAULT '',
  `Source` varchar(100) NOT NULL DEFAULT '',
  `EngageSeconds` smallint(5) unsigned NOT NULL DEFAULT 0,
  `CreateDate` datetime NOT NULL,
  `CreateUserID` smallint(5) unsigned NOT NULL,
  `ModifyDate` datetime DEFAULT NULL,
  `ModifyUserID` smallint(5) unsigned DEFAULT NULL,
  `DeleteDate` datetime DEFAULT NULL,
  `DeleteUserID` smallint(5) unsigned DEFAULT NULL,
  `LastViews` int(11) NOT NULL DEFAULT 0,
  `LastViewsNew` int(11) NOT NULL DEFAULT 0,
  `LastConversion` int(10) unsigned NOT NULL DEFAULT 0,
  `LastProfit` double NOT NULL DEFAULT 0,
  `LastProfitNew` double NOT NULL DEFAULT 0,
  `LastROI` double NOT NULL DEFAULT 0,
  `LastReportUpdate` datetime DEFAULT NULL,
  `RealTimeCPV` double NOT NULL DEFAULT 0,
  `CostTypeID` tinyint(4) NOT NULL DEFAULT 1,
  `DestinationType` tinyint(4) NOT NULL DEFAULT 1,
  `TrackingType` tinyint(4) NOT NULL DEFAULT 2,
  `FunnelSetup` tinyint(4) NOT NULL DEFAULT 0,
  `AssignedTo` int(11) NOT NULL DEFAULT 0,
  `PassTarget` tinyint(1) NOT NULL DEFAULT 0,
  `PassTargetParam` varchar(45) NOT NULL DEFAULT 'target',
  `PassTargetOffer` tinyint(1) NOT NULL DEFAULT 0,
  `PassTargetOfferParam` varchar(45) NOT NULL DEFAULT 'target',
  `PassSubIdLP` tinyint(1) NOT NULL DEFAULT 0,
  `PassSubId` tinyint(1) NOT NULL DEFAULT 0,
  `PassCookie` tinyint(1) NOT NULL DEFAULT 0,
  `PassCookieParam` varchar(45) NOT NULL DEFAULT 'cookie',
  `RedirectType` tinyint(3) unsigned NOT NULL DEFAULT 2,
  `KeyCode` varchar(45) NOT NULL DEFAULT '',
  `FailurePage` text DEFAULT NULL,
  `GroupID` smallint(5) unsigned NOT NULL DEFAULT 0,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `HideBlankPages` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `TrackingDomain` varchar(191) NOT NULL DEFAULT '',
  `UseCertifiedDomain` tinyint(3) unsigned DEFAULT 0,
  `IntermediateHopUrl` text DEFAULT NULL,
  `SplitShare` tinyint(4) NOT NULL DEFAULT 50,
  `ShareLanding` tinyint(4) NOT NULL DEFAULT 0,
  `ShareOffer` tinyint(4) NOT NULL DEFAULT 0,
  `Priority` smallint(5) unsigned NOT NULL DEFAULT 1,
  `OptimizationProfileID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `AlertProfileID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `CaptureReferrer` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureMobileDetails` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureNonMobileDetails` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureResolutionDetails` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureLanguageHeader` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureISP` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureGeo` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureUserAgent` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `CaptureIP` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `ExtraTokens` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `Notes` text DEFAULT NULL,
  `LpProtectType` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `LpProtectKey` varchar(100) DEFAULT NULL,
  `LpProtectParameter` varchar(30) DEFAULT 'sig',
  `LpProtectMessage` text DEFAULT NULL,
  `LpProtectRedirect` text DEFAULT NULL,
  `LastEditOption` tinyint(3) unsigned DEFAULT 1,
  PRIMARY KEY (`CampaignID`),
  UNIQUE KEY `idxCampaignKey` (`KeyCode`),
  KEY `idxCampaignTypeID` (`CampaignTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `campaignscustomviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaignscustomviews` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `UserID` smallint(5) unsigned NOT NULL,
  `CustomViewID` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`CampaignID`,`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `campaignstokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaignstokens` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `KeywordTokenParam` varchar(30) DEFAULT NULL,
  `KeywordTokenPlace` varchar(90) DEFAULT NULL,
  `CostTokenParam` varchar(30) DEFAULT NULL,
  `CostTokenPlace` varchar(90) DEFAULT NULL,
  `UniqueTokenParam` varchar(30) DEFAULT NULL,
  `UniqueTokenPlace` varchar(90) DEFAULT NULL,
  `UniqueTokenPass` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName1` varchar(40) DEFAULT NULL,
  `ExtraTokenParam1` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace1` varchar(90) DEFAULT NULL,
  `ExtraTokenPass1` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName2` varchar(40) DEFAULT NULL,
  `ExtraTokenParam2` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace2` varchar(90) DEFAULT NULL,
  `ExtraTokenPass2` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName3` varchar(40) DEFAULT NULL,
  `ExtraTokenParam3` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace3` varchar(90) DEFAULT NULL,
  `ExtraTokenPass3` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName4` varchar(40) DEFAULT NULL,
  `ExtraTokenParam4` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace4` varchar(90) DEFAULT NULL,
  `ExtraTokenPass4` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName5` varchar(40) DEFAULT NULL,
  `ExtraTokenParam5` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace5` varchar(90) DEFAULT NULL,
  `ExtraTokenPass5` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName6` varchar(40) DEFAULT NULL,
  `ExtraTokenParam6` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace6` varchar(90) DEFAULT NULL,
  `ExtraTokenPass6` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName7` varchar(40) DEFAULT NULL,
  `ExtraTokenParam7` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace7` varchar(90) DEFAULT NULL,
  `ExtraTokenPass7` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName8` varchar(40) DEFAULT NULL,
  `ExtraTokenParam8` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace8` varchar(90) DEFAULT NULL,
  `ExtraTokenPass8` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName9` varchar(40) DEFAULT NULL,
  `ExtraTokenParam9` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace9` varchar(90) DEFAULT NULL,
  `ExtraTokenPass9` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName10` varchar(40) DEFAULT NULL,
  `ExtraTokenParam10` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace10` varchar(90) DEFAULT NULL,
  `ExtraTokenPass10` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName11` varchar(40) DEFAULT NULL,
  `ExtraTokenParam11` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace11` varchar(90) DEFAULT NULL,
  `ExtraTokenPass11` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName12` varchar(40) DEFAULT NULL,
  `ExtraTokenParam12` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace12` varchar(90) DEFAULT NULL,
  `ExtraTokenPass12` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName13` varchar(40) DEFAULT NULL,
  `ExtraTokenParam13` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace13` varchar(90) DEFAULT NULL,
  `ExtraTokenPass13` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName14` varchar(40) DEFAULT NULL,
  `ExtraTokenParam14` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace14` varchar(90) DEFAULT NULL,
  `ExtraTokenPass14` tinyint(4) NOT NULL DEFAULT 0,
  `ExtraTokenName15` varchar(40) DEFAULT NULL,
  `ExtraTokenParam15` varchar(30) DEFAULT NULL,
  `ExtraTokenPlace15` varchar(90) DEFAULT NULL,
  `ExtraTokenPass15` tinyint(4) NOT NULL DEFAULT 0,
  `AdTokenName` varchar(40) DEFAULT NULL,
  `AdTokenParam` varchar(30) DEFAULT NULL,
  `AdTokenPlace` varchar(90) DEFAULT NULL,
  `AdTokenPass` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`CampaignID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `campaigntypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `campaigntypes` (
  `CampaignTypeID` tinyint(3) unsigned NOT NULL,
  `CampaignType` varchar(100) NOT NULL,
  `CampaignTypeShort` varchar(100) DEFAULT NULL,
  `CampaignTypeAbbr` varchar(100) DEFAULT NULL,
  `OrderIndex` tinyint(3) unsigned NOT NULL,
  `PageName` varchar(100) DEFAULT NULL,
  `SubTitleImage` varchar(100) DEFAULT NULL,
  `AnchorText` varchar(100) DEFAULT NULL,
  `MenuText` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`CampaignTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `clicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clicks` (
  `ClickID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `DestinationID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `ViewDate` datetime NOT NULL,
  `EngageDate` datetime DEFAULT NULL,
  `ClickDate` datetime DEFAULT NULL,
  `ConversionDate` datetime DEFAULT NULL,
  `SubscribeDate` datetime DEFAULT NULL,
  `ConversionDateReport` datetime DEFAULT NULL,
  `IPBinary` varbinary(16) DEFAULT NULL,
  `Cost` double DEFAULT NULL,
  `Revenue` double DEFAULT NULL,
  `IsDup` tinyint(4) NOT NULL DEFAULT 0,
  `ReferrerID` int(10) unsigned NOT NULL DEFAULT 1,
  `SiteCategoryID` smallint(5) unsigned NOT NULL DEFAULT 0,
  `DeviceID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `IspID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `DirectTraffic` tinyint(4) NOT NULL DEFAULT 0,
  `MobileDevice` tinyint(4) NOT NULL DEFAULT 0,
  `BrowserLanguageCode` char(2) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `CityID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `UserAgentID` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`ClickID`),
  KEY `idxCampaignID` (`CampaignID`),
  KEY `idxCampaignIDViewDate` (`CampaignID`,`ViewDate`),
  KEY `idxReferrerID` (`ReferrerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `clicksextra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clicksextra` (
  `ClickID` bigint(20) unsigned NOT NULL,
  `Extra1` varchar(191) DEFAULT NULL,
  `Extra2` varchar(191) DEFAULT NULL,
  `Extra3` varchar(191) DEFAULT NULL,
  `Extra4` varchar(191) DEFAULT NULL,
  `Extra5` varchar(191) DEFAULT NULL,
  `Extra6` varchar(191) DEFAULT NULL,
  `Extra7` varchar(191) DEFAULT NULL,
  `Extra8` varchar(191) DEFAULT NULL,
  `Extra9` varchar(191) DEFAULT NULL,
  `Extra10` varchar(191) DEFAULT NULL,
  `Extra11` varchar(191) DEFAULT NULL,
  `Extra12` varchar(191) DEFAULT NULL,
  `Extra13` varchar(191) DEFAULT NULL,
  `Extra14` varchar(191) DEFAULT NULL,
  `Extra15` varchar(191) DEFAULT NULL,
  `AdValue` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`ClickID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `clicksips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clicksips` (
  `IPBinary` varbinary(16) NOT NULL,
  `ClickDetails` varchar(50) NOT NULL DEFAULT '',
  `ClickLevel` tinyint(4) NOT NULL DEFAULT 1,
  `AssociatedClicks` varchar(191) NOT NULL DEFAULT '',
  `DateAdded` date NOT NULL,
  PRIMARY KEY (`IPBinary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `clickslp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clickslp` (
  `ClickID` bigint(20) unsigned NOT NULL,
  `Level` tinyint(4) NOT NULL,
  `DestinationID` mediumint(8) unsigned DEFAULT NULL,
  `ClickDate` datetime DEFAULT NULL,
  PRIMARY KEY (`ClickID`,`Level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `clickstscode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clickstscode` (
  `ClickID` bigint(20) unsigned NOT NULL,
  `UniqueCode` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`ClickID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `columncategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `columncategories` (
  `ColumnCategoryID` tinyint(3) unsigned NOT NULL,
  `ColumnCategory` varchar(100) DEFAULT NULL,
  `ColumnCategoryIndex` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`ColumnCategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `columns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `columns` (
  `ColumnID` tinyint(3) unsigned NOT NULL,
  `ColumnName` varchar(100) NOT NULL,
  `ColumnSort` varchar(100) NOT NULL,
  `ClassName` varchar(100) NOT NULL DEFAULT '',
  `ColumnColor` varchar(10) NOT NULL DEFAULT '034CB5',
  `DecimalPlaces` tinyint(4) NOT NULL DEFAULT -1,
  `IsPercent` tinyint(1) NOT NULL DEFAULT 0,
  `IsDollar` tinyint(1) NOT NULL DEFAULT 0,
  `IsDateTime` tinyint(1) NOT NULL DEFAULT 0,
  `IsSortable` tinyint(1) NOT NULL DEFAULT 1,
  `IsDatabaseSortable` tinyint(1) NOT NULL DEFAULT 0,
  `IsFixed` tinyint(1) NOT NULL DEFAULT 0,
  `IsTotal` tinyint(1) NOT NULL DEFAULT 0,
  `IsLongColumn` tinyint(1) NOT NULL DEFAULT 0,
  `IsMobile` tinyint(1) NOT NULL DEFAULT 0,
  `IsColorColumn` tinyint(1) NOT NULL DEFAULT 0,
  `IsGroupingColumn` tinyint(3) unsigned DEFAULT 0,
  `YesValue` varchar(50) NOT NULL DEFAULT '',
  `ColumnCategoryID` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`ColumnID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `columnscharts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `columnscharts` (
  `ColumnID` tinyint(3) unsigned NOT NULL,
  `CampaignTypeID` tinyint(3) unsigned NOT NULL,
  `ReportTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `ColumnCaption` varchar(100) NOT NULL,
  `ColumnCaption2` varchar(100) NOT NULL,
  `ColumnIndex` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`ColumnID`,`CampaignTypeID`,`ReportTypeID`),
  KEY `idxCampaignTypeReportVisible` (`CampaignTypeID`,`ReportTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `columnschartsuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `columnschartsuser` (
  `ColumnID` tinyint(3) unsigned NOT NULL,
  `CampaignTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `ReportTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `UserID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `IsVisible` tinyint(1) NOT NULL,
  PRIMARY KEY (`ColumnID`,`CampaignTypeID`,`ReportTypeID`,`UserID`),
  KEY `idxCampaignTypeReportVisible3` (`CampaignTypeID`,`ReportTypeID`,`UserID`,`IsVisible`),
  KEY `idxReportTypeVisible3` (`ReportTypeID`,`UserID`,`IsVisible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `columnstypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `columnstypes` (
  `ColumnID` tinyint(3) unsigned NOT NULL,
  `CampaignTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `ReportTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `ColumnCaption` varchar(100) NOT NULL,
  `ColumnCaption2` varchar(100) NOT NULL,
  `ColumnHeader` varchar(100) NOT NULL DEFAULT '',
  `ColumnHeader2` varchar(100) NOT NULL DEFAULT '',
  `ColumnIndexList` smallint(5) unsigned NOT NULL DEFAULT 0,
  `ColumnIndex` smallint(5) unsigned NOT NULL,
  `DefaultState` tinyint(1) NOT NULL,
  PRIMARY KEY (`ColumnID`,`CampaignTypeID`,`ReportTypeID`),
  KEY `idxCampaignTypeReportVisible` (`CampaignTypeID`,`ReportTypeID`),
  KEY `idxReportTypeVisible` (`ReportTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `columnstypesuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `columnstypesuser` (
  `ColumnID` tinyint(3) unsigned NOT NULL,
  `CampaignTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `ReportTypeID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `UserID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `IsVisible` tinyint(1) NOT NULL,
  PRIMARY KEY (`ColumnID`,`CampaignTypeID`,`ReportTypeID`,`UserID`),
  KEY `idxCampaignTypeReportVisible2` (`CampaignTypeID`,`ReportTypeID`,`UserID`,`IsVisible`),
  KEY `idxReportTypeVisible2` (`ReportTypeID`,`UserID`,`IsVisible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `columnsvisible`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `columnsvisible` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `ReportTypeID` tinyint(3) unsigned NOT NULL,
  `ColumnID` tinyint(3) unsigned NOT NULL,
  `UserID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `ColumnIndex` smallint(5) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`CampaignID`,`ReportTypeID`,`ColumnID`,`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `config` (
  `Name` varchar(100) NOT NULL,
  `Value` varchar(100) NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `configtext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `configtext` (
  `Name` varchar(100) NOT NULL,
  `Value` text NOT NULL,
  PRIMARY KEY (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `conversionextra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversionextra` (
  `ClickID` bigint(20) unsigned NOT NULL,
  `Status` varchar(190) DEFAULT NULL,
  `TransactionID` varchar(190) DEFAULT NULL,
  `Custom1` varchar(190) DEFAULT NULL,
  `Custom2` varchar(190) DEFAULT NULL,
  `Custom3` varchar(190) DEFAULT NULL,
  `Custom4` varchar(190) DEFAULT NULL,
  `Custom5` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`ClickID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cpvsources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpvsources` (
  `CpvSourceID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Source` varchar(100) NOT NULL,
  `SourceID` varchar(100) NOT NULL,
  `CurrencyID` smallint(5) unsigned DEFAULT 1,
  `KeywordToken` varchar(100) NOT NULL,
  `KeywordTokenPlace` varchar(100) NOT NULL DEFAULT '',
  `CostToken` varchar(100) NOT NULL DEFAULT '',
  `CostTokenPlace` varchar(100) NOT NULL DEFAULT '',
  `UniqueToken` varchar(100) NOT NULL DEFAULT '',
  `UniqueTokenPlace` varchar(100) NOT NULL DEFAULT '',
  `AppendToken` varchar(191) NOT NULL DEFAULT '',
  `Timezone` varchar(100) NOT NULL DEFAULT 'America/New_York',
  `CostTypeID` tinyint(4) NOT NULL DEFAULT 1,
  `AdTokenName` varchar(100) NOT NULL DEFAULT '',
  `AdTokenUrl` varchar(100) NOT NULL DEFAULT '',
  `AdTokenParam` varchar(100) NOT NULL DEFAULT '',
  `AdTokenPlace` varchar(100) NOT NULL DEFAULT '',
  `PostbackUrl` varchar(500) DEFAULT NULL,
  `DateAdded` datetime NOT NULL,
  PRIMARY KEY (`CpvSourceID`),
  KEY `idxSource` (`Source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cpvsourcestokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpvsourcestokens` (
  `CpvSourceTokenID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `CpvSourceID` smallint(5) unsigned NOT NULL,
  `ExtraTokenName` varchar(100) NOT NULL,
  `ExtraTokenUrl` varchar(100) DEFAULT NULL,
  `ExtraTokenParam` varchar(100) NOT NULL,
  `ExtraTokenPlace` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`CpvSourceTokenID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cpvtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cpvtemplates` (
  `CpvTemplateID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `CpvTemplate` varchar(100) NOT NULL,
  `RowsToSkip` smallint(5) unsigned NOT NULL,
  `KeywordColumn` varchar(100) NOT NULL,
  `AdColumn` varchar(100) NOT NULL DEFAULT '',
  `ViewsColumn` varchar(100) NOT NULL,
  `CostColumn` varchar(100) NOT NULL,
  `CpcColumn` varchar(100) NOT NULL DEFAULT '',
  `CampaignIDColumn` varchar(100) NOT NULL DEFAULT 'CampaignID',
  `CampaignNameColumn` varchar(100) NOT NULL DEFAULT 'Campaign',
  `Active` tinyint(1) NOT NULL,
  `CpvSourceID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`CpvTemplateID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cronjobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronjobs` (
  `CronID` smallint(5) unsigned NOT NULL,
  `CronName` varchar(190) DEFAULT NULL,
  `CronFile` varchar(190) DEFAULT NULL,
  `Active` tinyint(3) unsigned DEFAULT NULL,
  `ScheduleInterval` mediumint(8) unsigned DEFAULT NULL,
  `ScheduleType` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `OrderIndex` smallint(5) unsigned DEFAULT NULL,
  `LastRun` datetime DEFAULT NULL,
  `NextRun` datetime DEFAULT NULL,
  `DocLink` varchar(500) DEFAULT NULL,
  `DateUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`CronID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cronlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronlog` (
  `CronLogID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CronID` smallint(5) unsigned DEFAULT NULL,
  `DateAdded` datetime DEFAULT NULL,
  `NextRunDate` datetime DEFAULT NULL,
  `Operations` text DEFAULT NULL,
  `Result` text DEFAULT NULL,
  PRIMARY KEY (`CronLogID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cronparams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronparams` (
  `CronID` int(11) NOT NULL,
  `ParameterName` varchar(100) NOT NULL,
  `ParameterValue` text DEFAULT NULL,
  `ParameterDescription` text DEFAULT NULL,
  `OrderIndex` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`CronID`,`ParameterName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cronresults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronresults` (
  `CronResultID` int(11) NOT NULL,
  `CronKey` varchar(100) NOT NULL,
  `CronValue` varchar(100) NOT NULL,
  PRIMARY KEY (`CronResultID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cronruns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cronruns` (
  `CronRunID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `Seconds` float unsigned DEFAULT NULL,
  `JobsCount` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`CronRunID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `currency` (
  `CurrencyID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `CurrencyName` varchar(64) DEFAULT NULL,
  `CurrencyCode` varchar(50) DEFAULT NULL,
  `CurrencySign` varchar(10) DEFAULT NULL,
  `ExchangeRate` double DEFAULT NULL,
  `SortOrder` tinyint(3) unsigned DEFAULT 255,
  PRIMARY KEY (`CurrencyID`),
  UNIQUE KEY `CurrencyCode` (`CurrencyCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `currencyhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencyhistory` (
  `Date` date NOT NULL,
  `CurrencyID` smallint(6) NOT NULL,
  `Rate` double DEFAULT NULL,
  PRIMARY KEY (`Date`,`CurrencyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `customdomains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customdomains` (
  `CustomDomainID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `CustomDomain` varchar(100) DEFAULT NULL,
  `DateAdded` datetime DEFAULT NULL,
  PRIMARY KEY (`CustomDomainID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `customviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customviews` (
  `CustomViewID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `CustomViewName` varchar(100) NOT NULL,
  `CustomViewDescription` varchar(191) NOT NULL,
  `ColumnsNumber` tinyint(3) unsigned NOT NULL,
  `DefaultProfile` tinyint(1) NOT NULL DEFAULT 0,
  `CreateDate` datetime NOT NULL,
  `ModifyDate` datetime DEFAULT NULL,
  PRIMARY KEY (`CustomViewID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `customviewscolumns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `customviewscolumns` (
  `CustomViewID` smallint(5) unsigned NOT NULL,
  `ColumnID` tinyint(3) unsigned NOT NULL,
  `ColumnIndex` smallint(5) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`CustomViewID`,`ColumnID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `destinations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `destinations` (
  `DestinationID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `PathID` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `Offer` varchar(500) NOT NULL,
  `Url` varchar(500) NOT NULL,
  `Payout` double NOT NULL DEFAULT 0,
  `Share` tinyint(4) NOT NULL DEFAULT 0,
  `CurrentShare` tinyint(4) NOT NULL DEFAULT 0,
  `LandingPageID` int(11) NOT NULL DEFAULT 0,
  `AffiliateSourceID` smallint(5) unsigned NOT NULL DEFAULT 0,
  `PredefLpID` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `PredefOfferID` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `Level` tinyint(4) NOT NULL DEFAULT 1,
  `Sent` int(11) NOT NULL DEFAULT 0,
  `SharePath` tinyint(4) NOT NULL DEFAULT 50,
  `CurrentSharePath` tinyint(4) NOT NULL DEFAULT 0,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`DestinationID`),
  KEY `idxCampaignID` (`CampaignID`),
  KEY `idxCampaignInactiveLevel` (`CampaignID`,`Inactive`,`Level`),
  KEY `idxCampaignPath` (`CampaignID`,`PathID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `errors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `errors` (
  `ErrorID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ErrorDate` datetime NOT NULL,
  `UserID` smallint(5) unsigned DEFAULT NULL,
  `ErrorTypeID` tinyint(3) unsigned NOT NULL DEFAULT 3,
  `Page` varchar(200) NOT NULL DEFAULT '',
  `Context` varchar(200) NOT NULL DEFAULT '',
  `Error` text NOT NULL,
  `Query` text NOT NULL,
  `ErrorCode` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`ErrorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `errortypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `errortypes` (
  `ErrorTypeID` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `ErrorType` varchar(100) NOT NULL,
  PRIMARY KEY (`ErrorTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `hiddencampaignalerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hiddencampaignalerts` (
  `CampaignID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `Views` int(10) unsigned NOT NULL,
  PRIMARY KEY (`CampaignID`,`Views`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `hiddentargetalerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `hiddentargetalerts` (
  `CampaignID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `SubIdID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`CampaignID`,`SubIdID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `inactiveads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inactiveads` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `AdValue` varchar(191) NOT NULL DEFAULT '',
  PRIMARY KEY (`CampaignID`,`AdValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `inactivesubids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inactivesubids` (
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`CampaignID`,`SubIdID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `landingpagegroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `landingpagegroups` (
  `LandingPageGroupID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `LandingPageGroup` varchar(100) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  PRIMARY KEY (`LandingPageGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `logins` (
  `LoginID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `LoginDate` datetime DEFAULT NULL,
  `IPBinary` varbinary(16) DEFAULT NULL,
  `Username` varchar(100) DEFAULT '',
  `Password` varchar(100) DEFAULT '',
  `LoginType` tinyint(1) DEFAULT 0,
  `UserAgent` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`LoginID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmbots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmbots` (
  `BotID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `BotName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BotID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmbottypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmbottypes` (
  `BotTypeID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `BotType` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`BotTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmcities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmcities` (
  `CityID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `CityName` varchar(100) NOT NULL,
  `RegionID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `CountryCode` varchar(2) NOT NULL DEFAULT '',
  `RegionCode` varchar(10) NOT NULL,
  PRIMARY KEY (`CityID`),
  UNIQUE KEY `idxCityNameRegionCountry` (`CityName`,`RegionCode`,`CountryCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmcontinents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmcontinents` (
  `ContinentID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `ContinentCode` varchar(2) NOT NULL,
  `ContinentName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ContinentID`),
  UNIQUE KEY `idxContinentCode` (`ContinentCode`),
  UNIQUE KEY `idxContinentName` (`ContinentName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmcountries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmcountries` (
  `CountryID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `CountryCode` varchar(2) NOT NULL,
  `CountryCode3` varchar(3) NOT NULL,
  `CountryName` varchar(100) NOT NULL,
  `IsEU` tinyint(4) NOT NULL DEFAULT 0,
  `ContinentID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`CountryID`),
  UNIQUE KEY `idxCountryCode` (`CountryCode`),
  UNIQUE KEY `idxCountryName` (`CountryName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmisps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmisps` (
  `IspID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `IspName` varchar(100) NOT NULL,
  PRIMARY KEY (`IspID`),
  UNIQUE KEY `idxIspName` (`IspName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmispscondition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmispscondition` (
  `IspID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `IspName` varchar(100) NOT NULL,
  PRIMARY KEY (`IspID`),
  UNIQUE KEY `idxIspName` (`IspName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `mmregions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `mmregions` (
  `RegionID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `RegionCode` varchar(10) NOT NULL,
  `RegionName` varchar(100) NOT NULL,
  `CountryID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `CountryCode` varchar(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`RegionID`),
  UNIQUE KEY `idxRegionCodeCountryCode` (`RegionCode`,`CountryCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `offergroups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `offergroups` (
  `OfferGroupID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `OfferGroup` varchar(100) DEFAULT NULL,
  `CreateDate` datetime DEFAULT NULL,
  PRIMARY KEY (`OfferGroupID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `optimizationprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optimizationprofiles` (
  `OptimizationProfileID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `OptimizationProfileName` varchar(100) NOT NULL,
  `DefaultProfile` tinyint(1) NOT NULL DEFAULT 0,
  `topt1ROI` varchar(10) DEFAULT NULL,
  `topt1PPV` varchar(10) DEFAULT NULL,
  `lopt2ROI` varchar(10) DEFAULT NULL,
  `lopt2PPV` varchar(10) DEFAULT NULL,
  `oopt3ROI` varchar(10) DEFAULT NULL,
  `topt4Views` varchar(10) DEFAULT NULL,
  `topt4Clicks` varchar(10) DEFAULT NULL,
  `topt5Views` varchar(10) DEFAULT NULL,
  `topt5Conversion` varchar(10) DEFAULT NULL,
  `topt6Views` varchar(10) DEFAULT NULL,
  `topt6ROI` varchar(10) DEFAULT NULL,
  `topt6PPV` varchar(10) DEFAULT NULL,
  `lopt7Views` varchar(10) DEFAULT NULL,
  `lopt7Clicks` varchar(10) DEFAULT NULL,
  `lopt8Views` varchar(10) DEFAULT NULL,
  `lopt8Conversion` varchar(10) DEFAULT NULL,
  `oopt9Views` varchar(10) DEFAULT NULL,
  `oopt9Conversion` varchar(10) DEFAULT NULL,
  `oopt10Visitors` varchar(10) DEFAULT NULL,
  `oopt10Conversion` varchar(10) DEFAULT NULL,
  `CreateDate` datetime NOT NULL,
  `ModifyDate` datetime DEFAULT NULL,
  PRIMARY KEY (`OptimizationProfileID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `optimizations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `optimizations` (
  `OptimizeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `OptimizeName` varchar(45) NOT NULL,
  `OptimizeValue` varchar(45) NOT NULL,
  PRIMARY KEY (`OptimizeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `PageID` smallint(5) unsigned NOT NULL,
  `PageName` varchar(100) NOT NULL,
  PRIMARY KEY (`PageID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `pagesettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagesettings` (
  `PageName` varchar(100) NOT NULL,
  `UserID` smallint(5) unsigned NOT NULL DEFAULT 1,
  `WidgetSettings` text DEFAULT NULL,
  `WidgetPosition` text DEFAULT NULL,
  PRIMARY KEY (`PageName`,`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `parsingtemplates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `parsingtemplates` (
  `ParsingTemplateID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ReferrerName` varchar(191) NOT NULL,
  `Parameter` varchar(191) NOT NULL,
  `SiteCategoryID` smallint(6) NOT NULL DEFAULT 1,
  PRIMARY KEY (`ParsingTemplateID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `predeflps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `predeflps` (
  `PredefLpID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `LpName` varchar(500) NOT NULL,
  `LpUrl` varchar(500) NOT NULL,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `Notes` text NOT NULL,
  `GroupID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `DateAdded` datetime NOT NULL,
  PRIMARY KEY (`PredefLpID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `predefoffers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `predefoffers` (
  `PredefOfferID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `OfferName` varchar(500) NOT NULL,
  `OfferUrl` varchar(500) NOT NULL,
  `Payout` double NOT NULL DEFAULT 0,
  `AffiliateSourceID` smallint(5) unsigned NOT NULL DEFAULT 0,
  `Inactive` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `Notes` text NOT NULL,
  `GroupID` mediumint(8) unsigned NOT NULL DEFAULT 1,
  `DateAdded` datetime NOT NULL,
  PRIMARY KEY (`PredefOfferID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `redirectconditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirectconditions` (
  `RedirectConditionID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `RedirectProfileID` mediumint(8) unsigned NOT NULL,
  `OrderIndex` smallint(5) unsigned NOT NULL,
  `RedirectTypeID` tinyint(3) unsigned NOT NULL,
  `RedirectOperatorID` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `ValueCondition` text DEFAULT NULL,
  `ExtraCondition` text DEFAULT NULL,
  PRIMARY KEY (`RedirectConditionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `redirectconditiontypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirectconditiontypes` (
  `RedirectConditionTypeID` tinyint(3) unsigned NOT NULL,
  `Caption` varchar(100) NOT NULL,
  `LookupTable` varchar(100) DEFAULT NULL,
  `LookupIDField` varchar(100) DEFAULT NULL,
  `LookupValueField` varchar(100) DEFAULT NULL,
  `OrderIndex` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`RedirectConditionTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `redirectprofiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirectprofiles` (
  `RedirectProfileID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `RedirectProfileName` varchar(100) NOT NULL,
  `ProfileOperator` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `LandingPageID` mediumint(8) DEFAULT NULL,
  `OfferID` mediumint(8) unsigned DEFAULT NULL,
  `PathID` mediumint(8) unsigned DEFAULT NULL,
  `RedirectUrl` varchar(500) NOT NULL DEFAULT '',
  `Active` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `CreateDate` datetime NOT NULL,
  `ModifyDate` datetime DEFAULT NULL,
  `RedirectTypeID` tinyint(3) unsigned DEFAULT 1,
  PRIMARY KEY (`RedirectProfileID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `redirectprofilestypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirectprofilestypes` (
  `RedirectTypeID` tinyint(3) unsigned NOT NULL,
  `RedirectTypeName` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`RedirectTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `referrerdomains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `referrerdomains` (
  `ReferrerDomainID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ReferrerDomain` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`ReferrerDomainID`),
  UNIQUE KEY `idxReferrerDomain` (`ReferrerDomain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `referrers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `referrers` (
  `ReferrerID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Referrer` varchar(191) NOT NULL DEFAULT '',
  `ReferrerDomainID` int(10) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`ReferrerID`),
  UNIQUE KEY `idxReferrer` (`Referrer`),
  KEY `idxReferrerDomain` (`ReferrerDomainID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `reportdetailscampaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reportdetailscampaign` (
  `ReportDetailID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ReportID` mediumint(8) unsigned NOT NULL,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `Views` int(11) NOT NULL DEFAULT 0,
  `Clicks` int(10) unsigned NOT NULL DEFAULT 0,
  `CTR` double NOT NULL DEFAULT 0,
  `Cost` double NOT NULL DEFAULT 0,
  `Revenue` double NOT NULL DEFAULT 0,
  `EPV` double NOT NULL DEFAULT 0,
  `PPV` double NOT NULL DEFAULT 0,
  `Profit` double NOT NULL DEFAULT 0,
  `ROI` double NOT NULL DEFAULT 0,
  `Conversion` int(11) NOT NULL DEFAULT 0,
  `CR` double NOT NULL DEFAULT 0,
  `CPA` double NOT NULL DEFAULT 0,
  `Sent` int(11) NOT NULL DEFAULT 0,
  `EPS` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`ReportDetailID`),
  KEY `idxReportID` (`ReportID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `reportdetailsgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reportdetailsgroup` (
  `ReportDetailID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ReportID` mediumint(8) unsigned NOT NULL,
  `SubIdID` int(10) unsigned NOT NULL,
  `Views` int(10) unsigned NOT NULL,
  `Cost` double NOT NULL,
  `CPV` double DEFAULT NULL,
  `Clicks` int(11) NOT NULL DEFAULT 0,
  `CTR` double NOT NULL DEFAULT 0,
  `CPC` double NOT NULL DEFAULT 0,
  `Conversion` int(11) NOT NULL DEFAULT 0,
  `CR` double NOT NULL DEFAULT 0,
  `CPA` double NOT NULL DEFAULT 0,
  `Revenue` double NOT NULL DEFAULT 0,
  `EPV` double DEFAULT 0,
  `PPV` double DEFAULT 0,
  `eCPM` double NOT NULL,
  `Profit` double NOT NULL,
  `ROI` double NOT NULL DEFAULT 0,
  `Engages` int(11) NOT NULL DEFAULT 0,
  `EngageRate` double NOT NULL DEFAULT 0,
  `Sent` int(11) NOT NULL DEFAULT 0,
  `EPS` double NOT NULL DEFAULT 0,
  PRIMARY KEY (`ReportDetailID`),
  KEY `idxReportID` (`ReportID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `ReportID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ReportName` varchar(100) NOT NULL,
  `CampaignID` mediumint(8) unsigned NOT NULL,
  `DateAdded` datetime NOT NULL,
  PRIMARY KEY (`ReportID`),
  KEY `idxCampaignID` (`CampaignID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `reporttypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `reporttypes` (
  `ReportTypeID` tinyint(3) unsigned NOT NULL,
  `ReportTypeName` varchar(100) NOT NULL,
  PRIMARY KEY (`ReportTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `sessiondata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessiondata` (
  `id` varchar(32) NOT NULL,
  `data` text DEFAULT NULL,
  `access` bigint(14) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idxAccess` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `sitecategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sitecategories` (
  `SiteCategoryID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `SiteCategory` varchar(100) NOT NULL,
  `CreateDate` datetime DEFAULT NULL,
  PRIMARY KEY (`SiteCategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smbrands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smbrands` (
  `BrandID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `BrandName` varchar(191) NOT NULL,
  PRIMARY KEY (`BrandID`),
  UNIQUE KEY `idxBrandname` (`BrandName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smbrowserlanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smbrowserlanguages` (
  `BrowserLanguageCode` char(2) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `BrowserLanguage` varchar(100) NOT NULL,
  PRIMARY KEY (`BrowserLanguageCode`),
  UNIQUE KEY `idxBrowserLanguage` (`BrowserLanguage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smbrowsers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smbrowsers` (
  `BrowserID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `BrowserName` varchar(191) NOT NULL,
  PRIMARY KEY (`BrowserID`),
  UNIQUE KEY `idxBrowserName` (`BrowserName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smbrowserversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smbrowserversions` (
  `BrowserVersionID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `BrowserVersion` varchar(190) NOT NULL,
  `BrowserID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`BrowserVersionID`),
  UNIQUE KEY `idxBrowserVersionBrowserID` (`BrowserVersion`,`BrowserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smdatarate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smdatarate` (
  `DataRateID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `DataRate` int(10) unsigned DEFAULT NULL,
  `DataRateName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`DataRateID`),
  UNIQUE KEY `idxDataRate` (`DataRate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smdevicenamebrand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smdevicenamebrand` (
  `DeviceNameID` smallint(5) unsigned NOT NULL DEFAULT 0,
  `BrandID` smallint(5) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`DeviceNameID`,`BrandID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smdevicenames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smdevicenames` (
  `DeviceNameID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceName` varchar(191) DEFAULT NULL,
  `DeviceModel` varchar(191) DEFAULT NULL,
  `MarketingName` varchar(191) DEFAULT NULL,
  PRIMARY KEY (`DeviceNameID`),
  UNIQUE KEY `idxMarketingName` (`MarketingName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smdevices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smdevices` (
  `DeviceID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceCode` varchar(190) DEFAULT NULL,
  `DeviceNameID` smallint(5) unsigned DEFAULT 1,
  `BrandID` smallint(5) unsigned DEFAULT 1,
  `DeviceTypeID` tinyint(3) unsigned DEFAULT 4,
  `BrowserVersionID` smallint(5) unsigned DEFAULT 1,
  `OperatingSystemVersionID` smallint(5) unsigned DEFAULT 1,
  `ResolutionID` smallint(5) unsigned DEFAULT 1,
  `DisplaySizeID` smallint(5) unsigned DEFAULT 1,
  `DataRateID` tinyint(3) unsigned DEFAULT 1,
  `PointingMethodID` tinyint(3) unsigned DEFAULT 1,
  `SmsSupport` tinyint(1) DEFAULT 0,
  `MmsSupport` tinyint(1) DEFAULT 0,
  `PdfSupport` tinyint(1) DEFAULT 0,
  `RssSupport` tinyint(1) DEFAULT 0,
  `PushSupport` tinyint(1) DEFAULT 0,
  `FlashSupport` tinyint(1) DEFAULT 0,
  `ClickToCall` tinyint(1) DEFAULT 0,
  `DualOrientation` tinyint(1) DEFAULT 0,
  `QwertyKeyword` tinyint(1) DEFAULT 0,
  `NumberSupport` tinyint(1) DEFAULT 0,
  `WifiSupport` tinyint(1) DEFAULT 0,
  `IframeSupport` tinyint(1) DEFAULT 0,
  `CookieSupport` tinyint(1) DEFAULT 0,
  `PartyCookieSupport` tinyint(1) DEFAULT 0,
  `EmbeddedVideo` tinyint(1) DEFAULT 0,
  `JavaScriptSupport` tinyint(1) DEFAULT 0,
  `StreamMp4` tinyint(1) DEFAULT 0,
  `StreamMov` tinyint(1) DEFAULT 0,
  `StreamFlv` tinyint(1) DEFAULT 0,
  `PlaybackMp4` tinyint(1) DEFAULT 0,
  `PlaybackMov` tinyint(1) DEFAULT 0,
  `PlaybackFlv` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`DeviceID`),
  UNIQUE KEY `idxDeviceCode` (`DeviceCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smdevicetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smdevicetypes` (
  `DeviceTypeID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `DeviceType` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`DeviceTypeID`),
  UNIQUE KEY `idxDeviceType` (`DeviceType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smdisplays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smdisplays` (
  `DisplaySizeID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `DisplayWidth` smallint(5) unsigned DEFAULT NULL,
  `DisplayHeight` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`DisplaySizeID`),
  UNIQUE KEY `idxDisplayWidthHeight` (`DisplayWidth`,`DisplayHeight`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smos` (
  `OperatingSystemID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `OperatingSystem` varchar(191) NOT NULL,
  PRIMARY KEY (`OperatingSystemID`),
  UNIQUE KEY `idxOperatingSystem` (`OperatingSystem`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smosversions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smosversions` (
  `OperatingSystemVersionID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `OperatingSystemVersion` varchar(190) NOT NULL,
  `OperatingSystemID` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`OperatingSystemVersionID`),
  UNIQUE KEY `idxOsVersionOsID` (`OperatingSystemVersion`,`OperatingSystemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smpointingmethod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smpointingmethod` (
  `PointingMethodID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `PointingMethod` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`PointingMethodID`),
  UNIQUE KEY `idxPointingMethod` (`PointingMethod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `smresolutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `smresolutions` (
  `ResolutionID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ResolutionWidth` smallint(5) unsigned DEFAULT NULL,
  `ResolutionHeight` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`ResolutionID`),
  UNIQUE KEY `idxResolutionWidthHeight` (`ResolutionWidth`,`ResolutionHeight`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `subids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `subids` (
  `SubIdID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SubId` varchar(45) NOT NULL,
  `Keyword` varchar(191) NOT NULL,
  `DateAdded` datetime NOT NULL,
  PRIMARY KEY (`SubIdID`),
  UNIQUE KEY `idxKeyword` (`Keyword`),
  UNIQUE KEY `idxSubId` (`SubId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `tempconv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tempconv` (
  `RequestID` int(11) NOT NULL AUTO_INCREMENT,
  `RequestType` tinyint(4) DEFAULT 1,
  `Network` varchar(190) DEFAULT NULL,
  `RequestAmount` varchar(200) DEFAULT NULL,
  `RequestTid` text DEFAULT NULL,
  `RequestClick` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `RequestCampaign` mediumint(8) unsigned DEFAULT NULL,
  `RequestUrl` varchar(2000) DEFAULT NULL,
  `PostVars` text DEFAULT NULL,
  `SecretKey` varchar(100) DEFAULT NULL,
  `RequestDate` datetime DEFAULT NULL,
  PRIMARY KEY (`RequestID`),
  KEY `idxNetwork` (`Network`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `tempdrct`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tempdrct` (
  `DirectID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` datetime DEFAULT NULL,
  `CampaignID` mediumint(8) unsigned DEFAULT NULL,
  `IPAddressStr` varchar(200) DEFAULT NULL,
  `IPBinary` varbinary(16) DEFAULT NULL,
  `Url` varchar(2000) DEFAULT NULL,
  `PostVars` text DEFAULT NULL,
  PRIMARY KEY (`DirectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

DROP TABLE IF EXISTS `temprqst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `temprqst` (
  `RequestID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` datetime DEFAULT NULL,
  `Source` varchar(20) DEFAULT NULL,
  `CampaignID` mediumint(8) unsigned DEFAULT NULL,
  `IPAddressStr` varchar(200) DEFAULT NULL,
  `IPBinary` varbinary(16) DEFAULT NULL,
  `cParam` varchar(500) DEFAULT NULL,
  `lParam` varchar(500) DEFAULT NULL,
  `uParam` text DEFAULT NULL,
  `Url` text DEFAULT NULL,
  `PostVars` text DEFAULT NULL,
  PRIMARY KEY (`RequestID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

DROP TABLE IF EXISTS `tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tokens` (
  `TokenID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `TokenValue` varchar(191) NOT NULL,
  PRIMARY KEY (`TokenID`),
  UNIQUE KEY `idxTokenValue` (`TokenValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trackings` (
  `TrackingID` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `CampaignID` mediumint(8) unsigned DEFAULT NULL,
  `TrackingCode` text DEFAULT NULL,
  `IsPostbackUrl` tinyint(3) unsigned DEFAULT 0,
  `SendPercentage` tinyint(3) unsigned DEFAULT 100,
  `Enabled` tinyint(3) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`TrackingID`),
  KEY `idxCampaignID` (`CampaignID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `trackingsoffers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `trackingsoffers` (
  `TrackingID` mediumint(8) unsigned NOT NULL,
  `OfferID` mediumint(9) NOT NULL,
  PRIMARY KEY (`TrackingID`,`OfferID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `useragents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `useragents` (
  `UserAgentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserAgent` varchar(191) NOT NULL DEFAULT '',
  PRIMARY KEY (`UserAgentID`),
  UNIQUE KEY `idxUserAgent` (`UserAgent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `userroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userroles` (
  `UserRoleID` tinyint(3) unsigned NOT NULL,
  `UserRoleName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`UserRoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `UserID` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `Username` varchar(45) NOT NULL DEFAULT '',
  `Password` varchar(45) NOT NULL DEFAULT '',
  `UserRoleID` tinyint(3) unsigned DEFAULT 1,
  `DateAdded` datetime NOT NULL,
  `LastLogin` datetime DEFAULT NULL,
  `Timezone` varchar(100) NOT NULL DEFAULT 'America/New_York',
  `StatsInterval` int(10) unsigned DEFAULT 8,
  `StatsCustomFrom` varchar(45) DEFAULT '',
  `StatsCustomTo` varchar(45) DEFAULT '',
  `DefaultPage` varchar(100) NOT NULL DEFAULT 'campaigns.php',
  `LiveRefresh` int(11) NOT NULL DEFAULT 600,
  `LiveRecords` int(11) NOT NULL DEFAULT 100,
  `LiveView` tinyint(4) NOT NULL DEFAULT 0,
  `CampaignsSort` varchar(100) NOT NULL DEFAULT 'CampaignName',
  `CampaignsDir` varchar(20) NOT NULL DEFAULT 'asc',
  `AssignedCampaigns` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `UserAccessType` tinyint(3) unsigned DEFAULT 1,
  `AssignedDomains` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `UserAccessTypeDomains` tinyint(3) unsigned DEFAULT 1,
  `WebhookUrl` text DEFAULT NULL,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS=1;
