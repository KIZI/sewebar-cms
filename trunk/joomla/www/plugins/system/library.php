<?php
/**
 * @version		$Id: library.php
 * @package		Joomla
 * @subpackage	Library
 * @copyright	Copyright (C) 2005 - 2010 Lukáš Beránek. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

//To prevent accessing the document directly, enter this code:
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class plgSystemLibrary extends JPlugin {
	
	function plgSystemLibrary(&$subject) 

	{
		parent::__construct ( $subject );
	
	}
	
	/**
	 * 
	 */
	function onAfterInitialise() {
		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin ( 'system', 'library' );
		$this->_params = new JParameter ( $this->_plugin->params );
		
		$paths = explode ( ";", $this->_params->get ( 'paths' ) );
		
		foreach ( $paths as $path ) {
			if (! empty ( $path )) {
				$newPath = $_SERVER ['DOCUMENT_ROOT'] . JURI::root ( true ) . '/' . $path;
				if (! defined ( "PATH_SEPARATOR" )) {
					if (strpos ( $_ENV ["OS"], "Win" ) !== false) {
						define ( "PATH_SEPARATOR", ";" );
					} else {
						define ( "PATH_SEPARATOR", ":" );
					}
				}
				set_include_path ( $newPath . PATH_SEPARATOR . get_include_path () );
			}
		}
		if (JDEBUG) {
			JFactory::getApplication ()->enqueueMessage ( JText::_ ( get_include_path () ), 'error' );
		}
	}
}
