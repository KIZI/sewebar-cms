<?php

// No direct access
defined('_JEXEC') or die;

class KbiHelpers
{
	public static function addSubmenu($vName)
	{
		$component = 'com_kbi';

		//Query Definitions
		JSubMenuHelper::addEntry(
			JText::_('Query Definitions'),
			"index.php?option={$component}&controller=querydefinitions",
			$vName == 'querydefinitions'
		);

    	//LM Servers
		JSubMenuHelper::addEntry(
			JText::_('LM Servers'),
			"index.php?option={$component}&controller=lmservers",
			$vName == 'lmservers'
		);

    	//PMML Documents
		JSubMenuHelper::addEntry(
			JText::_('PMML Documents'),
			"index.php?option={$component}&controller=documents",
			$vName == 'documents'
		);

    	//Remote Sources
		JSubMenuHelper::addEntry(
			JText::_('Remote Sources'),
			"index.php?option={$component}&controller=sources",
			$vName == 'sources'
		);

    	//Queries
		JSubMenuHelper::addEntry(
			JText::_('Queries'),
			"index.php?option={$component}&controller=queries",
			$vName == 'queries'
		);

    	//XSLTs
		JSubMenuHelper::addEntry(
			JText::_('XSLTs'),
			"index.php?option={$component}&controller=xslts",
			$vName == 'xslts'
		);
	}
}