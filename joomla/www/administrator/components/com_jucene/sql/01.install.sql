-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Pondělí 20. června 2011, 15:15
-- Verze MySQL: 5.1.47
-- Verze PHP: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáze: `sewebar`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `jos_jucene_documents`
--

DROP TABLE IF EXISTS `#__jucene_documents`;
CREATE TABLE IF NOT EXISTS `jos_jucene_documents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `database` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `uri` int(11) NOT NULL,
  `table` int(11) NOT NULL,
  `jos_document_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE IF EXISTS `#__jucene_documents` ADD CONSTRAINT fk_jucene_docs FOREIGN KEY (id) REFERENCES `#__content`(id);