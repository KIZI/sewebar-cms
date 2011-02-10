<?php
/**
 * @version		$Id: jucene.php 
 * @package		Joomla
 * @subpackage	Jucene
 * @copyright	Copyright (C) 2005 - 2010 Lukáš Beránek. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
defined('_JEXEC') or die ('Restricted acces');



jimport( 'joomla.application.component.view' );

/**
 * 
 * 
 * 
 * @author admin
 *
 */
class JuceneViewJucene extends JView{

	/**
	 * Hellos view display method
	 * @return void
	 **/
	function display($tpl = null)
	{
		// Get data from the model
		$data				= &$this->get( 'data');
		$wrong = array('30', '60', 'on');
		$i = 0;
		foreach ($data as $key => $val){
			$data[$key] = ($val < $wrong[$i] || $val !== $wrong[$i]) ? JText::_('OK') : JText::sprintf('CHANGESETTINGVALUE',$wrong[$i]);
			$i++;
		}
		
		$this->assign('data',$data);
		
		parent::display($tpl);
	}





}