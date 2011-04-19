<?php
require 'configuration.php';

$sectionid = 1;
$catid = 1;

$config = new JConfig;

$connection = mysql_connect(
	$config->host, 
	$config->user,
	$config->password);

if($connection === FALSE)
{
	die('Chyba spojeni ' . mysql_error());
}

if( ! mysql_select_db($config->db, $connection))
{
	die('Chyba pri vyberu db ' . mysql_error());
}

$prefix = $config->dbprefix;

$nazvy = array(
	'T1_SCA_MRY',
	'T2_SCA_RFK',
	'T3_SCA_OBT',
	'T4_SCA_KTL',
	'T5_SCA_EKG',
	'T6_SCA_CHL',
	'T7_AKT_MRY',
	'T8_AKT_RFK',
	'T9_AKT_OBT',
	'T10_AKT_KTL',
	'T11_AKT_EKG',
	'T12_AKT_CHL',
	'T13_MRY_OBT',
	'T14_MRY_KTL',
	'T15_MRY_EKG',
	'T16_MRY_CHL',
	'T17_OBT_KTL',
	'T18_OBT_EKG',
	'T19_SCA_MRY',
	'T20_SCA_RFK',
	'T21_SCA_OBT',
	'T22_SCA_KTL',
	'T23_SCA_EKG',
	'T24_SCA_CHL',
	'T25_AKT_MRY',
	'T26_AKT_RFK',
	'T27_AKT_OBT',
	'T28_AKT_KTL',
	'T29_AKT_EKG',
	'T30_AKT_CHL',
	'T31_MRY_OBT',
	'T32_MRY_KTL',
	'T33_MRY_EKG',
	'T34_MRY_CHL',
	'T35_OBT_KTL',
);

foreach($nazvy as $nazev) {
	foreach(array('_FUI', '_AA') as $suffix) {
		
		$sql = sprintf("INSERT INTO `{$prefix}content` (`title`, `sectionid`, `catid`, `created`, `publish_up`) VALUES ('%s', %s, %s, NOW(), NOW())",
			mysql_real_escape_string("$nazev$suffix"),
			mysql_real_escape_string($sectionid),
			mysql_real_escape_string($catid)
		);

		if(mysql_query($sql, $connection) !== FALSE){
			echo "$nazev$suffix<br />";		
		} else {
			echo mysql_error();
		}
	}
}

?>