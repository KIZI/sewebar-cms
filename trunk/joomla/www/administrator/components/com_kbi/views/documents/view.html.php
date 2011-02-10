<?php 
/**
 * @version		$Id: view.html.php 1765 2011-01-29 22:40:14Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );

/**
 * Admin list view for sources.
 *
 * @package com_kbi
 */
class KbiViewDocuments extends JView
{	
	function setDefaultToolbar()
	{
		JToolBarHelper::title(JText::_('KBI Remote Documents') . " <small>[{$this->source->name}]</small>", 'article.png');
		
		// Synchronize documents
		JToolBarHelper::custom('synchronize', 'copy', '', 'Synchronize', false, true);
		
		// View / download document
		JToolBarHelper::custom('view', 'preview', '', 'View', false);
		
		// Delete document
		JToolBarHelper::deleteList('', 'delete');
		
		// Upload document
		JToolBarHelper::addNewX('add');
				
		//JToolBarHelper::help( 'screen.banners.client' );
	}
	
	function setSynchronizeToolbar()
	{
		JToolBarHelper::title(JText::_('KBI Documents Synchronization') . " <small>[{$this->source->name}]</small>", 'article.png');
		
		JToolBarHelper::apply('apply');
		JToolBarHelper::cancel('cancel');
	}
	
	function setUploadToolbar()
	{
		JToolBarHelper::title(JText::_('KBI Remote Document upload') . " <small>[{$this->source->name}]</small>", 'article.png');
		
		// View / download document
		JToolBarHelper::custom('upload', 'apply', '', 'Upload', false);
		JToolBarHelper::cancel('cancel');
	}
	
	function setViewToolbar()
	{
		JToolBarHelper::title(JText::_('KBI Remote Document upload') . " <small>[{$this->source->name}]</small>", 'article.png');
		
		JToolBarHelper::cancel('cancel');
	}
	
	function display($tpl = NULL)
	{
		switch($this->getLayout())
		{
			case 'synchronize':
				self::setSynchronizeToolbar();
				break;
			case 'upload':
				self::setUploadToolbar();
				break;
			case 'view':
				self::setViewToolbar();
				break;
			default:
				self::setDefaultToolbar();
		}	
		
		parent::display($tpl);
	}
}
?>