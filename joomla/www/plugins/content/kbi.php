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

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_kbi'.DS.'models'.DS.'transformator.php';

/**
 * Content plugin used by dynamic KBI fragments. The plugin runs before front end page rendering and it searches for KBI idendificators, executes given query and replaces original identificator with gained results.
 *
 * @package content/kbi
 */
class plgContentKbi extends JPlugin 
{
	function onPrepareContent ( &$row, &$params )
	{
		preg_match_all('/\{kbi (.*)\}/U', $row->text, $matches);
		
		foreach( $matches[1] as $values )
		{
			$p = $this->parseNameValues($values);
			
			$transfomator = new KbiModelTransformator(array(
				'id_source' => $p['source'],
				'id_query' => $p['query'],
				'id_xslt' => $p['xslt'],
				'parameters' => isset($p['parameters']) ? $this->recleanup($p['parameters']) : NULL,
			));
			
			$html = $transfomator->transform();
			$row->text = str_replace("{kbi $values}", $html, $row->text);
		}
		
		return true;
	}
	
	function recleanup($p) {
		return str_replace('&gt;', '>', 
			str_replace('&lt;', '<', $p));
	}
	
	function parseNameValues ($text) {
		$values = array();
		$matches = array();
		
		if (preg_match_all('/([^:\s]+)[\s]*:[\s]*("(?P<value1>[^"]+)"|' . '\'(?P<value2>[^\']+)\'|(?P<value3>.+?)\b)/', $text, $matches, PREG_SET_ORDER))
			foreach ($matches as $match) {
				$values[trim($match[1])] = @$match['value1'] . @$match['value2'] . trim(@$match['value3']);
			}
		
		return $values;
	}
}