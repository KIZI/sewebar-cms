<?php
/**
 * @version		$Id: features.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );

require_once(dirname(__FILE__).'/../models/AncestorGetData.php');
require_once(dirname(__FILE__).'/../models/GetDataARBuilderQuery.php');

/**
 * Generates JSON from FeaturesList and DataDescription that initializes ARDesigner.
 *
 * @package		com_kbi
 */
class KbiControllerFeatures extends JController
{
	function display()
	{		
		$document =& JFactory::getDocument();
		$document->setMimeEncoding( 'application/json' );
		
		$viewName = JRequest::getVar('view', 'features');
		$viewType = 'raw';
		$view =& $this->getView($viewName, $viewType);
		
		$query_id = JRequest::getInt('id_query', NULL);
			
		$view->assign('value', '');
		
		if($query_id != NULL) {
			require_once(dirname(__FILE__).'/../../../administrator/components/com_kbi/models/queries.php');
			$model_queries = new KbiModelQueries;
			$query = $model_queries->getQuery($query_id);
			
			$datadescriptionfile = dirname(__FILE__).'/../models/datadescription.xml';
			//$featurelistfile = dirname(__FILE__).'/../models/featurelist.xml';

			$sr = new GetDataARBuilderQuery($datadescriptionfile, $query->featurelist, null, 'en');
			$result = $sr->getData();
			//var_dump($result);
			$view->assignRef('value', $result);
		}
		
		$view->display();		
	}
}
