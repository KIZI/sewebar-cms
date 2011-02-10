<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.filesystem.*' );

function com_install(){	
	$source = JPATH_ADMINISTRATOR.DS."components/com_jucene/lucene.zip";
	$target = JPATH_ADMINISTRATOR.DS."components/com_jucene/";
	
	if(!JArchive::extract( $source , $target )){
	
		JFactory::getApplication()->enqueueMessage(JText::_("LUCENEINSTALLFAILURE"),'error');
			
		
	}else{
	
		JFactory::getApplication()->enqueueMessage(JText::_("LUCENEINSTALLSUCCESS"));
			
	
	}
} 