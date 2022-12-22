-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2022 at 06:09 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `transactus`
--
CREATE DATABASE IF NOT EXISTS `transactus` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `transactus`;

-- --------------------------------------------------------

--
-- Table structure for table `cusers`
--

CREATE TABLE `cusers` (
  `idd` int(11) NOT NULL,
  `eemail` varchar(364) DEFAULT NULL,
  `ppw` varchar(128) NOT NULL,
  `entropy` varchar(128) DEFAULT NULL,
  `ipp` varchar(256) NOT NULL,
  `acctlvl` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `outputs`
--

CREATE TABLE `outputs` (
  `oid` int(12) NOT NULL,
  `txlink` varchar(32) NOT NULL,
  `oaddr` varchar(62) NOT NULL,
  `oamt` int(24) NOT NULL,
  `caddr` varchar(62) DEFAULT NULL,
  `camt` int(24) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pubkeypool`
--

CREATE TABLE `pubkeypool` (
  `pkid` int(11) NOT NULL,
  `pubkey` varchar(99) NOT NULL,
  `derivacct` int(11) NOT NULL,
  `derivindex` int(11) NOT NULL,
  `walletid` varchar(16) NOT NULL,
  `walletindex` int(11) NOT NULL,
  `pubkeyposition` int(11) NOT NULL,
  `cuser` int(11) NOT NULL,
  `assocaddr` varchar(148) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `txs`
--

CREATE TABLE `txs` (
  `idd` int(12) NOT NULL,
  `walletid` int(16) NOT NULL,
  `txlink` varchar(32) NOT NULL,
  `usrcreated` int(12) NOT NULL,
  `lasthash` mediumtext NOT NULL,
  `nextsigned` varchar(3) NOT NULL DEFAULT 'no',
  `mofn` int(2) NOT NULL,
  `finished` varchar(3) NOT NULL DEFAULT 'no',
  `usrsigned` int(12) DEFAULT NULL,
  `fromaddr` varchar(62) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE `wallet` (
  `wid` int(11) NOT NULL,
  `mofn` int(11) NOT NULL,
  `nsigners` int(11) NOT NULL,
  `wlink` varchar(32) NOT NULL,
  `p1` varchar(99) DEFAULT NULL,
  `p2` varchar(99) DEFAULT NULL,
  `p3` varchar(99) DEFAULT NULL,
  `p4` varchar(99) DEFAULT NULL,
  `p5` varchar(99) DEFAULT NULL,
  `p6` varchar(99) DEFAULT NULL,
  `p7` varchar(99) DEFAULT NULL,
  `p8` varchar(99) DEFAULT NULL,
  `p9` varchar(99) DEFAULT NULL,
  `p10` varchar(99) DEFAULT NULL,
  `a1` varchar(99) DEFAULT NULL,
  `a2` varchar(99) DEFAULT NULL,
  `a3` varchar(99) DEFAULT NULL,
  `a4` varchar(99) DEFAULT NULL,
  `a5` varchar(99) DEFAULT NULL,
  `a6` varchar(99) DEFAULT NULL,
  `a7` varchar(99) DEFAULT NULL,
  `a8` varchar(99) DEFAULT NULL,
  `a9` varchar(99) DEFAULT NULL,
  `a10` varchar(99) DEFAULT NULL,
  `aactive` varchar(3) NOT NULL DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `walletmembers`
--

CREATE TABLE `walletmembers` (
  `wmid` int(11) NOT NULL,
  `walletid` varchar(32) NOT NULL,
  `cuser` int(11) NOT NULL,
  `ualias` varchar(55) DEFAULT NULL,
  `derivacct` int(11) NOT NULL,
  `signingpos` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cusers`
--
ALTER TABLE `cusers`
  ADD PRIMARY KEY (`idd`);

--
-- Indexes for table `outputs`
--
ALTER TABLE `outputs`
  ADD PRIMARY KEY (`oid`);

--
-- Indexes for table `pubkeypool`
--
ALTER TABLE `pubkeypool`
  ADD PRIMARY KEY (`pkid`);

--
-- Indexes for table `txs`
--
ALTER TABLE `txs`
  ADD PRIMARY KEY (`idd`);

--
-- Indexes for table `wallet`
--
ALTER TABLE `wallet`
  ADD PRIMARY KEY (`wid`);

--
-- Indexes for table `walletmembers`
--
ALTER TABLE `walletmembers`
  ADD PRIMARY KEY (`wmid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cusers`
--
ALTER TABLE `cusers`
  MODIFY `idd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `outputs`
--
ALTER TABLE `outputs`
  MODIFY `oid` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pubkeypool`
--
ALTER TABLE `pubkeypool`
  MODIFY `pkid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=621;

--
-- AUTO_INCREMENT for table `txs`
--
ALTER TABLE `txs`
  MODIFY `idd` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `wallet`
--
ALTER TABLE `wallet`
  MODIFY `wid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `walletmembers`
--
ALTER TABLE `walletmembers`
  MODIFY `wmid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
