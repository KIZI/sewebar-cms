<?php
	JToolBarHelper::title( JText::_( 'JUCENE' ), 'generic.png' );
	JToolBarHelper::apply( 'index', 'Bulk index' );
	JToolBarHelper::preferences( 'com_jucene' );	
	JToolBarHelper::deleteList( JText::_('DELETEINDEXCONFIRM'), 'remove', JText::_("DELETEINDEX"));
	JToolBarHelper::makeDefault('jucene_about', JText::_("ABOUTJUCENE"));
	JHTML::_('behavior.tooltip');
?>