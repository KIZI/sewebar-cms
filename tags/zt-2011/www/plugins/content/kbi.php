<?php defined( '_JEXEC' ) or die( 'Restricted access' );
/**
 * @version		$Id$
 * @package		content/kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

JPluginHelper::importPlugin('kbi', 'base');
JLoader::import('KBIntegrator', JPATH_PLUGINS . DS . 'kbi');

require_once JPATH_ROOT.DS.'components'.DS.'com_kbi'.DS.'models'.DS.'transformator.php';

/**
 * Content plugin used by dynamic KBI fragments.
 * The plugin runs before front end page rendering and it searches for KBI idendificators, executes given query and replaces original identificator with gained results.
 *
 * @package content/kbi
 */
class plgContentKbi extends JPlugin
{
	function onPrepareContent (&$row, &$params)
	{
		$pattern = '/\<!-- kbiLink({.*\}) \/kbiLink -->/U';
		$row->text = preg_replace_callback($pattern, array(__CLASS__, 'replace'), $row->text, -1, $count);

		return true;
	}

	function replace($match)
	{
		if(!isset($match[1]) || empty($match[1])) return;

		try
		{
			//WYSIWYG editor zalamuje XHTML neparove znacky na \ /> aby zustal validni JSON je potreba to vratit spet na \/>
			$json = str_replace('\ />', '\/>', $match[1]);
			$config = json_decode($json, true);

			if($config === NULL) {
				KBIDebug::log($match[1], 'Element not parsed as JSON');
				return $match[1];
			}

			$transfomator = new KbiModelTransformator($config);
			//var_dump($match);
			//var_dump($config);
			//var_dump($transfomator);

			return $transfomator->transform();
		}
		catch (Exception $ex)
		{
			KBIDebug::log(array($ex, $config), 'Query not succesfull');
		}
	}
}