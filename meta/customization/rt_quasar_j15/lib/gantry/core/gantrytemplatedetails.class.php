<?php
/**
 * @package   gantry
 * @subpackage core
 * @version   2.0.12 February 12, 2010
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

jimport('joomla.error.profiler');		

/**
 * Populates the parameters and template configuration form the templateDetails.xml and params.ini
 *
 * @package gantry
 * @subpackage core
 */
class GantryTemplateDetails {

	var $xml;
	var $positions = array ();
	var $params = array ();
    var $_pramas_ini;

	function GantryTemplateDetails() {
	}

	function init(&$gantry) {

		if (array_key_exists('gantry_profile', $_GET)){
			$profile = JProfiler::getInstance('GantryTemplateDetails->init()');
			$profile->mark('Start');
		}
		$this->xml = new JSimpleXML;
		if (array_key_exists('gantry_profile', $_GET)){
			$profile->mark('LoadFile Start');
		}
		$this->xml->loadFile($gantry->templatePath . '/templateDetails.xml');
		if (array_key_exists('gantry_profile', $_GET)){
			$profile->mark('LoadFile Stop');
		}
		$this->positions = & $this->_getPositions();
		$this->params = $this->_getParams($gantry);
		if (array_key_exists('gantry_profile', $_GET)){
			$profile->mark('Stop');
			var_dump($profile->getBuffer());
		}
	}

	function & _getPositions() {

		if (array_key_exists('gantry_profile', $_GET)){
			$profile = JProfiler::getInstance('GantryTemplateDetails->_getPositions()');
			$profile->mark('Start');
		}
		// positions
		$data = array ();
		$positions = $this->xml->document->positions[0]->children();
		foreach ($positions as $position) {
			array_push($data, $position->data());
		}
		if (array_key_exists('gantry_profile', $_GET)){
			$profile->mark('Stop');
			var_dump($profile->getBuffer());
		}
		return $data;
	}
	
	function &_getUniquePositions() {
		if (array_key_exists('gantry_profile', $_GET)){
			$profile = JProfiler::getInstance('GantryTemplateDetails->_getUniquePositions()');
			$profile->mark('Start');
		}
		// positions
		$data = array ();
		$positions = $this->xml->document->positions[0]->children();
		foreach ($positions as $position) {
			$name = $position->data();
			$name = preg_replace("/(\-a|\-b|\-c|\-d|\-e|\-f)$/i", "", $name);
			if (!in_array($name, $data)) array_push($data, $name);
		}
		if (array_key_exists('gantry_profile', $_GET)){
			$profile->mark('Stop');
			var_dump($profile->getBuffer());
		}
		return $data;
	}

	function parsePosition($position, $pattern) {
		if (null == $pattern) {
			$pattern = "(-)?";
		}
		$filtered_positions = array ();

		if (count($this->positions) > 0) {
			$regpat = "/^" . $position . $pattern . "/";
			foreach ($this->positions as $key => $value) {
				if (preg_match($regpat, $value) == 1) {
					$filtered_positions[] = $value;
				}
			}
		}
		return $filtered_positions;
	}

	function _getParams(&$gantry) {
		if (array_key_exists('gantry_profile', $_GET)){
			$profile = JProfiler::getInstance('GantryTemplateDetails->_getParams()');
			$profile->mark('Start');
		}
		$params_file = $gantry->templatePath.DS.'params.ini';
		$content="";

        $ingorables = array('spacer','gspacer','gantry');

		if (is_readable( $params_file ) )
		{
			$content = file_get_contents($params_file);
            $gantry->_base_params_checksum = md5($content);
		}
		$this->_pramas_ini = new JParameter($content);

		$data = array ();
		$params = $this->xml->document->params[0]->children();

		foreach ($params as $param) {
            //skip for unsupported types
			if (in_array($param->attributes('type'), $ingorables))
				continue;
            $this->_getParamInfo($gantry, $param, $data);
		}
		$this->params = $data;
		if (array_key_exists('gantry_profile', $_GET)){
			$profile->mark('Stop');
			var_dump($profile->getBuffer());
		}
		return $data;
	}

    function _getParamInfo(&$gantry, &$param, &$data, $prefix = ""){
        switch($param->attributes('type')){
            case 'groupedselection':
                $this->_decodeParamInfo($gantry, $param, $data, $prefix);
                // this should fall through and process children like chain and group
            case 'chain':
            case 'group':
                $prename = $prefix.$param->attributes('name')."-";
                foreach($param->children() as $subparam){
                    $this->_getParamInfo($gantry, $subparam, $data, $prename);
                }
                break;
            default:
                $this->_decodeParamInfo($gantry, $param, $data, $prefix);
                break;
        }
    }

    function _decodeParamInfo(&$gantry, &$param, &$data, $prefix = ""){
        $attributes = $param->attributes();
        $data[$prefix.$attributes['name']] = array (
            'name' => $prefix.$attributes['name'],
            'type' => $attributes['type'],
            'default' => (array_key_exists('default',$attributes))?$attributes['default']:false,
            'value' => $this->_pramas_ini->get($prefix.$attributes['name'],(array_key_exists('default',$attributes))?$attributes['default']:false),
            'sitebase' => $this->_pramas_ini->get($prefix.$attributes['name'],(array_key_exists('default',$attributes))?$attributes['default']:false), 
            'setbyurl' => (array_key_exists('setbyurl',$attributes))?($attributes['setbyurl'] == 'true')?true:false :false,
            'setbycookie' => (array_key_exists('setbycookie',$attributes))?($attributes['setbycookie'] == 'true')?true:false :false,
            'setbysession' => (array_key_exists('setbysession',$attributes))?($attributes['setbysession'] == 'true')?true:false :false,
            'setincookie' => (array_key_exists('setbycookie',$attributes))?($attributes['setbycookie'] == 'true')?true:false :false,
            'setinsession' => (array_key_exists('setinsession',$attributes))?($attributes['setinsession'] == 'true')?true:false :false,
            'setinmenuitem' => (array_key_exists('setinmenuitem',$attributes))?($attributes['setinmenuitem'] == 'true')?true:false :true,
            'setbymenuitem' => (array_key_exists('setbymenuitem',$attributes))?($attributes['setbymenuitem'] == 'true')?true:false :true,
            'isbodyclass' => (array_key_exists('isbodyclass',$attributes))?($attributes['isbodyclass'] == 'true')?true:false :false,
            'setby' => 'default'
        );

        if ($data[$prefix.$attributes['name']]['setbyurl']) $gantry->_setbyurl[] = $prefix.$attributes['name'];
        if ($data[$prefix.$attributes['name']]['setbysession']) $gantry->_setbysession[] = $prefix.$attributes['name'];
        if ($data[$prefix.$attributes['name']]['setbycookie']) $gantry->_setbycookie[] = $prefix.$attributes['name'];
        if ($data[$prefix.$attributes['name']]['setinsession']) $gantry->_setinsession[] = $prefix.$attributes['name'];
        if ($data[$prefix.$attributes['name']]['setincookie']) $gantry->_setincookie[] = $prefix.$attributes['name'];
        if ($data[$prefix.$attributes['name']]['setinmenuitem']) {
            $gantry->_setinmenuitem[] = $prefix.$attributes['name'];
        }
        else {
            $gantry->dontsetinmenuitem[] = $prefix.$attributes['name'];
        }
        if ($data[$prefix.$attributes['name']]['setbymenuitem']) $gantry->_setbymenuitem[] = $prefix.$attributes['name'];
        if ($data[$prefix.$attributes['name']]['isbodyclass']) $gantry->_bodyclasses[] = $prefix.$attributes['name'];

    }
}