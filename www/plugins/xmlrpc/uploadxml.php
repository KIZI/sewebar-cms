<?php

/**
 * @version		$Id: joomla.php 10381 2008-06-01 03:35:53Z pasamio $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

global $mainframe;
$mainframe->registerEvent('onGetWebServices', 'blaonGetWebServices');


jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.path');
jimport('joomla.filesystem.folder');

/**
 * Get available web services for this plugin
 *
 * @access	public
 * @return	array	Array of web service descriptors
 * @since	1.5
 */
function blaonGetWebServices() {
    global $xmlrpcString, $xmlrpcInt, $xmlrpcArray;

    // Initialize variables
    $services = array();

    // Site search service
    $services['uploadXML.uploadFile'] = array(
        'function' => 'plgUploadXMLJoomlaServices::nahrajXML',
        'docstring' => 'Nahraje daný soubor',
        'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcInt))
    );
    $services['uploadXML.listFiles'] = array(
        'function' => 'plgUploadXMLJoomlaServices::listFiles',
        'docstring' => 'Vypise seznam souboru daneho uzivatele',
        'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcString))
    );
    $services['uploadXML.getArticle'] = array(
        'function' => 'plgUploadXMLJoomlaServices::getArticle',
        'docstring' => 'Vypise seznam souboru daneho uzivatele',
        'signature' => array(array($xmlrpcString, $xmlrpcString, $xmlrpcString, $xmlrpcInt))
    );
    return $services;
}

/**
 * Joomla! plugin pro nahrávání xml souborů s jejich validací proti schématům uloženým ve složce templateSchemas
 *
 * @package XML-RPC
 * @since 1.5
 */
class plgUploadXMLJoomla extends JPlugin {

    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param object $subject The object to observe
     * @param object $params  The object that holds the plugin parameters
     * @since 1.5
     */
    function plguploadXMLJoomla(& $subject, $config) {
        parent::__construct($subject, $config);
    }

}

class plgUploadXMLJoomlaServices {

    function getArticle($user, $pass, $aticleId) {

        if (!plgUploadXMLJoomlaServices::authenticateUser($user, $pass)) {
            $myVal = new xmlrpcval("Nepovedlo se kvuli autentikaci uzivatele.", "string");
            return $myVal;
        }

        $db = & JFactory::getDBO();
        $query = 'SELECT introtext FROM #__content'
                . ' WHERE '
                . $db->nameQuote('id')
                . ' = '
                . $aticleId;
        //$query = "SELECT introtext FROM jos_content WHERE id='1'";
        $db->setQuery($query);
        $myVal = new xmlrpcval($db->loadResult(), "string");
        return $myVal;
    }

    function listFiles($user, $pass, $section, $categorie) {

        $plugin = & JPluginHelper::getPlugin('xmlrpc', 'uploadxml');
        $params = new JParameter($plugin->params);

        $groupsArticles = $params->get('groupsArticles');
        $groupUs = $params->get('groupUsers');

        $groupUsers = new UserArticle($groupUs, $groupsArticles);

        if ($section == '') {
            $section = $params->get('sectionid');
        }
        if ($categorie == '') {
            $categorie = $params->get('catid');
        }
        $order = "ID";
        $order_dir = "ASC";
        $limitstart = 0;
        $limit = 30;

        if (!plgUploadXMLJoomlaServices::authenticateUser($user, $pass)) {
            $myVal = new xmlrpcval("Nepovedlo se kvuli autentikaci uzivatele.", "string");
            return $myVal;
        }

        $db = & JFactory::getDBO();

        //nastavení where částí SQL dotazu
        $whereClause = "";
        if ($section != -1) {
            $whereClause.=" AND ct.sectionid='" . $section . "'";
        }
        if ($categorie != -1) {
            $whereClause.=" AND ct.catid='" . $categorie . "'";
        }
        if ($filter != '') {
            $whereClause.=" AND ct.title LIKE '%" . $filter . "%'";
        }

        $userObject = & JFactory::getUser($user);

        if (!$userObject->authorize('com_content', 'edit', 'content', 'all')) {
            if ($userObject->authorize('com_content', 'edit', 'content', 'own')) {
                $whereClause.=" AND ct.created_by='" . $userObject->get('id') . "'";
            } else {

            }
        }

        $articles = $groupUsers->getArticles($userObject->id);
        for ($actualArticle = 0; $actualArticle < count($articles); $actualArticle++) {
            if($articles[$actualArticle] == ""){
                continue;
            }
            if ($actualArticle == 0) {
                $whereClause.=" AND ( ";
            }
            if ($actualArticle + 1 == count($articles)) {
                $whereClause.= " ct.id = '" . $articles[$actualArticle] . "')";
                break;
            }
            $whereClause.=" ct.id = '" . $articles[$actualArticle] . "' OR ";
        }

        $whereClause.=" AND ct.checked_out='0'"; //kontrola, jestli daný článek neupravuje někdo jiný...
        $whereClause.=" AND ct.state > '-2'";
        $db->setQuery("SELECT ct.title,ct.id FROM #__content ct LEFT JOIN #__sections sec ON ct.sectionid=sec.id LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE true $whereClause order by $order $order_dir", $limitstart, $limit);

        $rows = $db->loadObjectList();
        return $rows;
    }

	function authorizeArticleNew($username, $articleID, &$debug = '') {
        $userObject = & JFactory::getUser($username);
        $plugin = & JPluginHelper::getPlugin('xmlrpc', 'uploadxml');
        $params = new JParameter($plugin->params);
        $groupUsers = new UserArticle($params->get('groupUsers'), $params->get('groupsArticles'));
				
		// je mezi uzivateli z parametru pluginu
		if(array_key_exists($userObject->get('id'), $groupUsers->users)) {
			// v tom pripade nemuze vytvaret
			return false;
		}
		
        if ($userObject->authorize('com_content', 'add', 'content', 'all')) {
            /* uživatel může vytvaret */
            return true;
        } else {
            /* uživatel nemůže vytvaret */
            return false;
        }
    }

    function authorizeArticleEdit($username, $articleID, &$debug = '') {
        $userObject = & JFactory::getUser($username);
		
		$plugin = & JPluginHelper::getPlugin('xmlrpc', 'uploadxml');
        $params = new JParameter($plugin->params);
        $groupUsers = new UserArticle($params->get('groupUsers'), $params->get('groupsArticles'));
		
		//var_dump($groupUsers->users);
		// je mezi uzivateli z parametru pluginu
		if(array_key_exists($userObject->get('id'), $groupUsers->users)) {
			if(!in_array($articleID, $groupUsers->articles[$groupUsers->users[$userObject->get('id')]])) {
				return false;
			}
		}
		
        if ($userObject->authorize('com_content', 'edit', 'content', 'all')) {
            /* uživatel může upravovat vše */
            return true;
        } else if ($userObject->authorize('com_content', 'edit', 'content', 'own')) {
            $db = & JFactory::getDBO();
            $whereClause.=" AND ct.id = '" . $articleID . "' AND ct.created_by='" . $userObject->get('id') . "'";
            $db->setQuery("SELECT ct.title,ct.id FROM #__content ct LEFT JOIN #__sections sec ON ct.sectionid=sec.id LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE true $whereClause", 0, 30);
            $rows = $db->loadObjectList();
            if (count($rows) > 0) {
                /* uživatel může upravovat své články a jedná se o jeho článek */
                return true;
            } else {
                /* uživatel může upravovat své články a nejedná se o jeho článek */
                return false;
            }
        } else {
            /* uživatel nemůže upravovat nic */
            return false;
        }
    }

    /**
     * Metoda pro vzdáelné přidávání článků.
     *
     * @param	string	XML dokument
     * @param	string	přihlašovací jméno
     * @param	string	heslo
     * @param	string	Název článku
     * @param	int	id článku, pokud má být určitě nový pak -1
     * @param	string	Anotace článku.
     * @return	string	Výsledek přidání.
     * @since	1.5
     */
    function nahrajXML($xmldocument, $user, $pass, $nazevClanku, $id) {
        global $mainframe, $xmlrpcerruser, $proc;
    	$plugin =& JPluginHelper::getPlugin('xmlrpc', 'uploadxml');
        $params = new JParameter($plugin->params);

        $category = $params->get('catid');
        $section = $params->get('sectionid');
        $state = $params->get('publikovat');
        $xmlpath = $params->get("xmlpath");


        $db =& JFactory::getDBO();
        if(!plgUploadXMLJoomlaServices::authenticateUser($user, $pass)) {
            $myVal = new xmlrpcval("Nepovedlo se kvuli autentikaci uzivatele. $user, $pass", "string");
            return $myVal;
        }
		
        // Autorizace
        if(empty($id) || $id <= 0) {
        	if(!plgUploadXMLJoomlaServices::authorizeArticleNew($user, $id, $debug)) {
    	        $myVal = new xmlrpcval("Nepovedlo se kvuli nedostatecnym pravum pro vytvareni novych dokumentu.", "string");
        	    return $myVal;
        	}
        } else {
	        if(!plgUploadXMLJoomlaServices::authorizeArticleEdit($user, $id, $debug)) {
    	        $myVal = new xmlrpcval("Nepovedlo se kvuli nedostatecnym pravum pro zapis.", "string");
        	    return $myVal;
        	}
        }

        libxml_use_internal_errors(true);


        $odpovida = false;
        $title = $alias = $title_alias = $nazevClanku;

        $pattern = "../" . $xmlpath . "/*.xsd";
        $files = glob($pattern); // vrati pole s nazvy xsd souboru

	//return new xmlrpcval(dirname(__FILE__) . " ". $pattern .implode(", ", $files), "string");

        $xml = new DomDocument();
        $xml->loadXML($xmldocument);




        if (!empty($files)) {
            error_reporting(0);
            $valid = false;

            foreach ($files as $xsdfile) {
                if ($xml->schemaValidate($xsdfile)) // odpovida schematu
                {
                    $valid = true;

                    $xslfile = str_replace(".xsd", ".xsl", $xsdfile);


                    if (file_exists($xslfile)) // existuje xls sablona?
                    {
                    	$xdoc2 = new DomDocument();
                  		$xdoc2->load($xslfile);
                  		$xsl = new XSLTProcessor();
                  		$xsl->importStylesheet($xdoc2);
                        // transformace
                        $xmldocument = $xsl->transformToXML($xml);
                    }
                }

            }

            if (!$valid) {
                return new xmlrpcval("ERROR Nahravany soubor neodpovida zadnemu schematu", "string");
            }
        }


        /*
        //POZOR to tu je pouze docasne, kvuli vypnute xsl transformaci
        if (!$XMLtext or $XMLtext == '') {
            $XMLtext = $xmldocument;
        }*/

        //$query = "insert into #__content (title,alias,title_alias, introtext, fulltext, state, sectionid, mask, catid, created, created_by, created_by_alias, modified, modified_by, checked_out, checked_out_time, publish_up, publish_down, images, urls, attribs, version, parentid, ordering, metakey, metadesc, access, hits, metadata) " +
        //  + "values ($title, $alias, $title_alias, $introtext, $XMLtext, $state, $sectionid, $mask, $catid, $created,)";
        //$query = "insert into #__content (title,alias,title_alias, introtext, fulltext, created_by) " +
        //  + "values ($title, $alias, $title_alias, $introtext, $XMLtext, $createdBy)";
        $uid = plgUploadXMLJoomlaServices::getUserID($user);
        $now = date('YmdHis',$now);


        // columns fulltext and introtext are both of mediumtext datatype
        //as the primary column for saving article text joomla uses introtext, not mediumtext
        //if xml is saved into fulltext column, Joomla will insert <hr> line to the output when rendering the page
        //damaging the XML

        if($id == -1) {
            $query = "insert into #__content (title, images, urls, attribs, metakey, metadesc, introtext,  sectionid, catid, created_by, created_by_alias, state, created, modified) values ('$title','','','','','', '$xmldocument','$section','$category','$uid','$user','$state', NOW(), NOW());";
        }
        else {
        	$created = "select created from #__content where id=".$id.";";
        	$result1 = $db->Execute($created);
        	$vytvoreno = $db->loadResult();
            $query = "replace into #__content set title='$title', images='', urls='', attribs='', metakey='xml', metadesc='Compliant with $complyingWith', introtext='$xmldocument',id='$id', sectionid='$section', catid='$category',created_by = '$uid',created_by_alias='$user',state='$state', modified = NOW(), created = '$vytvoreno';";
        }

        $result = $db->Execute($query);

        //tady je potřeba nahrát ten soubor do DB.
        $myVal=new xmlrpcval("Soubor se povedlo uspesne nahrat", "string");

        return $myVal;
    }

    function authenticateUser($username, $password) {
        // Get the global JAuthentication object
        jimport('joomla.user.authentication');
        $auth = & JAuthentication::getInstance();
        $credentials = array('username' => $username, 'password' => $password);
        $options = array();
        $response = $auth->authenticate($credentials, $options);
        return $response->status === JAUTHENTICATE_STATUS_SUCCESS;
    }

    function getUserID($username) {
        $user = & JFactory::getUser($username);
        $id = $user->id;
        return $id;
    }

}

class UserArticle {

    function __construct($groupUsers, $groupArticles) {
        $this->users = array();
        $this->articles = array();

        $groups = explode("\n", $groupUsers);
        for ($actualGroup = 0; $actualGroup < count($groups); $actualGroup++) {
            $grUs = explode(" ", $groups[$actualGroup]);
            $groupId = $grUs[0];
            $users = explode(";", $grUs[1]);
            for ($actualUser = 0; $actualUser < count($users); $actualUser++) {
                $this->users[$users[$actualUser]] = $groupId;
            }
        }

        $articles = explode("\n", $groupArticles);
        for ($actualRow = 0; $actualRow < count($articles); $actualRow++) {
            $grAr = explode(" ", $articles[$actualRow]);
            $groupId = $grAr[0];
            $articlesSingle = explode(";", $grAr[1]);
            $articlesArray = array();
            for ($actualArticle = 0; $actualArticle < count($articlesSingle); $actualArticle++) {
                $articlesArray[] = $articlesSingle[$actualArticle];
            }
            $this->articles[$groupId] = $articlesArray;
        }
    }

    function getArticles($userId) {
        $articles = array();
        try{
            $groupId = $this->users[$userId];
            $articles = $this->articles[$groupId];
        }
        catch (Exception $e){
            
        }
        return $articles;
    }

}
