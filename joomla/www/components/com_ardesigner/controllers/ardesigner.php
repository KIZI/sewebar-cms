<?php
/**
 * @version		$Id: ardesigner.php 1586 2010-10-24 22:32:27Z andrej $
 * @package		com_kbi
 * @author		Andrej Hazucha
 * @copyright	Copyright (C) 2010 All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

require_once(dirname(__FILE__).'/../models/AncestorSerializeRules.php');
require_once(dirname(__FILE__).'/../models/SerializeRulesTaskSetting.php');

/**
 * Controller for ARDesigner.
 *
 * @package com_kbi
 */
class KbiControllerARDesigner extends JController
{
	function display()
	{
		$document =& JFactory::getDocument();
		$viewName = JRequest::getVar('view', 'ardesigner');
		$viewType = $document->getType();
		$data = JRequest::getVar('data', NULL);
		
		if($viewType == 'raw' && $data != NULL){
			$this->processData($data);
		} else {
			$view =& $this->getView($viewName, $viewType);
			$view->setLayout('default');
			$view->display();
		}		
	}
	
	function processData(&$data)
	{
		/*$toSolve;
	    for($i = 1 ; $i <= $data['rules']; $i++){
	        for($j = 0; $j < count($data['rule'.$i]); $j++){
	            $toSolve[$i-1][$j] = $data['rule'.$i][$j];
	        }
	    }
	    /*for($i = 1 ; $i < ($_GET['data']['rules']+1); $i++){
	        for($j = 0; $j < count($_GET['data']['rule'.$i]); $j++){
	            $toSolve[$i-1][$j] = $_GET['data']['rule'.$i][$j];
	        }
	    }*/
	    
		/*
	    $sr = new SerializeRules();
	    $sr->solve($toSolve);
	    echo $result;*/
		$toSolve = str_replace('\\"', '"', $_POST['data']);
    	//var_dump($toSolve);
	    $sr = new SerializeRulesTaskSetting();
	    //$sr = new SerializeRulesARQuery();
	    echo $sr->serializeRules($toSolve);
	}
}