<?php
//error_reporting(E_ALL);

require_once ('Zend/Cache.php');

define('PLUGIN_NAME', 'xsltmagic');
define("CACHE_DIR", './cache/');

defined( '_JEXEC' ) or die("Restricted Access");
//$mainframe->registerEvent('onPrepareContent', PLUGIN_NAME);

// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');
 
//The Content plugin Loadmodule
class plgContentxsltmagic extends JPlugin
{
        /**
        * Plugin that loads module positions within content
        */
// onPrepareContent, meaning the plugin is rendered at the first stage in preparing content for output
        public function onPrepareContent( &$row, &$params, $page=0 )
{



	// pokud chybi XML deklarace, koncime
	if (substr($row->text, 0, 5) != '<?xml')
	{
	 if (isset($_GET['debug']))
	 {
	 echo "<font color=red>Xml declaration missing. Transformation terminated</font><br />";
	 echo "<pre>$row->text</pre>";
	 die;
    
   }
	
		return;
	}

	$config = explode("\n", get_parameter('config'));
	$GLOBALS['xml_path'] = get_parameter('xml_path');
	if (JPATH_BASE == JPATH_ADMINISTRATOR)
  {   //zjistíme, jestli jsme v administraci a pokud ano, převedeme cestu na adresování frontendu
		$GLOBALS['xml_path']='../'.$GLOBALS['xml_path'];
	}

	$rules = array();
	$styles = array();
	$key = 0;
	
	

	foreach ($config as $rule)
	{
    $rule = trim($rule);
    
    if (!empty($rule))
    {	  
	if (substr($rule, 0, 1) == "#")
	{
		//comment
	}
		else 

  		if (isXsltExt($rule))
  		{
  			$styles[$key][] = $rule;
  		}
  		else
  		{
  			$key++;
  			$rules[$key] = $rule;
  			$styles[$key] = array();
  		}
		}
	}
	
	
	
	$xml_result = $row->text;
	$xml = new DOMDocument();
	$xml->loadXML($row->text);
	$xpath = new DOMXPath($xml);
	$found = false;
	$message = "no rule found";
	$GLOBALS['xmlerrmsg'] = '';
	$GLOBALS['xmlinfomsg'] = '';

	$message .= $GLOBALS['xml_path'];
	set_error_handler('handle_xml_error');

	foreach ($rules as $key => $rule)
	{
		if (!$found)
		{
		  
		
			if (isXsdExt($rule))
			{
			  $GLOBALS['xmlinfomsg'] .= "Trying XSD schema <em>$rule</em>\n";
			
				// zkusime validaci XML schmema
				if ($xml->schemaValidate($GLOBALS['xml_path'] . $rule))
				{
				  $GLOBALS['xmlinfomsg'] .= "Found XML schema <em>$rule</em>\n";
				  
					$found = true;
					$xml_result = applyTransformation($xml, $styles[$key]);
				}
			}
			else if (isRngExt($rule))
			{
			  $GLOBALS['xmlinfomsg'] .= "Trying RNG <em>$rule</em>\n";
			
				// zkusime validaci relax NG
				if ($xml_document->relaxNGValidate($GLOBALS['xml_path'] . $rule))
				{
				  $GLOBALS['xmlinfomsg'] .= "Found RNG <em>$rule</em>\n";
				
					$found = true;
					$xml_result = applyTransformation($xml, $styles[$key]);
				}
			}
			else
			{
			  $GLOBALS['xmlinfomsg'] .= "Trying XPATH <em>$rule</em>\n";
			
				// zkusime XPATH 1.0 vyraz
				$result = $xpath->evaluate($rule);

				if (!empty($result))
				{
				  $GLOBALS['xmlinfomsg'] .= "Found XPATH <em>$rule</em>\n";
				
					$found = true;
					$xml_result = applyTransformation($xml, $styles[$key]);
				}
			}
		}
	}

	restore_error_handler();

	$message = "";

	if (get_parameter('xml_msg') && !$found)
	{
		$message .= "<strong style='color: white; background-color: red; font-weight: bold;padding: 2px;border-color: black; border-style: solid;'>".get_parameter('xml_msg')."</strong>";
	}
	
	

	if (isset($_REQUEST['debug']))
	{
	  if (!$found)
	  {
      $GLOBALS['xmlinfomsg'] .= "No transformation found";
    }

  if (!empty($GLOBALS['xmlinfomsg']))
  {	
	  $message .= "<fieldset><legend style='color: blue'>Transformation details</legend><ul><li>".implode("</li><li>", explode("\n", trim($GLOBALS['xmlinfomsg'])))."</li></ul></fieldset>";
	  }

    if (!empty($GLOBALS['xmlerrmsg']))
    {	
		  $message .= "<fieldset><legend style='color: red'>Troubles occured :-(</legend><ul><li>".implode("</li><li>", explode("\n", trim($GLOBALS['xmlerrmsg'])))."</li></ul></fieldset>";
		}
	}


	$row->text = $message.$xml_result;

if (isset( $_GET['yoy']))
{
$row->text = substr($row->text, 0, $_GET['yoy']);
}

if (isset( $_GET['die']))
{
die($row->text);
}

	
}
}

/**
 * Vrati nazvy souboru v adresari jako pole
 *
 * @param string $directory
 * @param bool $recursive
 * @return array
 */

function getAllFiles($directory, $recursive = true) {
	$result = array();
	$handle =  opendir($directory);
	while ($datei = readdir($handle))
	{
		if (($datei != '.') && ($datei != '..'))
		{
			$file = $directory.$datei;
			if (is_dir($file)) {
				if ($recursive) {
					$result = array_merge($result, getAllFiles($file.'/'));
				}
			} else {
				$result[] = $file;
			}
		}
	}
	closedir($handle);
	return $result;
}


/**
     *
     * @param DOMDocument $xml puvodni XML
     * @param array|string $styles seznam xsl stylu
     * @return string
     */

function applyTransformation(DOMDocument $xml, $styles)
{
	if (!is_array($styles))
	{
		$styles = array($styles);
	}
	
	if (empty($styles))
	{
    $GLOBALS['xmlerrmsg'] .= "No styles found with given rule. Add some xslt files into plugin configuration after the rule\n"; 
  }
	$GLOBALS['xmlinfomsg'] .= "Applying transformation with following styles: ".implode(", ", $styles)."\n";

	$backendOptions = array(
	'cache_dir' => CACHE_DIR // Directory where to put the cache files
	);

	$cache = Zend_Cache::factory('Core', 'File', array('lifetime' => null, 'automatic_serialization' => true), $backendOptions, false, false, true);

  if (isset($_GET['nocache']))
  {
    $cache->clean(Zend_Cache::CLEANING_MODE_ALL);
  }


	// ulozime datum posledni zmeny vsech souboru v xml adresari jako asociativni pole nazev_souboru => cas_zmeny
	$timestamp_cache_id = 'xml_timestamps';
	if (!$created = $cache->load($timestamp_cache_id))
	{
		// smazeme radsi celou cache
		$cache->clean(Zend_Cache::CLEANING_MODE_ALL);

		$created = array();
		$files = getAllFiles($GLOBALS['xml_path']);
		foreach ($files as $file)
		{
			$created[basename($file)] = filectime($file);
		}

		$cache->save($created, $timestamp_cache_id);
	}

	// projdeme datum posledni zmeny u prirazenych xslt sablon a pokud nesouhlasi, smazeme cache obsahujici tuto sablonu
	foreach ($styles as $style)
	{
		$ctime = filectime($GLOBALS['xml_path'].$style);


		if ($ctime != $created[$style])
		{
			// smazeme cache obsahujici tuto sablonu


			$cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array(getMyTag($style)));

			// aktualizujeme datum
			$created[$style] = $ctime;
		}

		// ulozime cache obsahujici nove datum posledni zmeny xml
		$cache->save($created, $timestamp_cache_id);
	}

	// cacheId vytvoreno jako hash xml obsahu a jazyk
	// @todo zde se nekontroluje uzivatelsky vstup lang, zda je povolen
	$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : '';
	
	if (!empty($lang))
	{
	 $GLOBALS['xmlinfomsg'] .= "Language from URL $lang\n";
	}

	if (empty($lang))
	{
		$user = JFactory::getUser();
	
    $language = $user->getParam('language');

		$lang = substr($language, 0, 2);
		
		if (!empty($lang))
	 {
		$GLOBALS['xmlinfomsg'] .= "Setting user language $lang\n";
		}
	}
// default transformation language is set in the xslt transformation

$xml_params = array();



foreach ($_GET as $key => $val)
{


	if (substr($key, 0, 5) == "xslt_")
	{
		$mykey = substr($key, 5);
		$xml_params[$mykey] = $val;
	}


}



	$cache_id = md5($xml->saveXML().$lang.implode("_", $xml_params));

	if (!$result = $cache->load($cache_id))
	{
		$tags = array();

		foreach ($styles as $style)
		{
			 // otaguj cache pouzitou sablonou
			$tags[] = getMyTag($style);

			$xsl = new DOMDocument();
			$xsl->load($GLOBALS['xml_path'].$style);


			$processor = new XSLTProcessor();
			$processor->importStyleSheet($xsl);

			if (!empty($lang))
			{
				$processor->setParameter('', 'reportLang', $lang);
			}

			foreach ($xml_params as $key => $val)
			{
				$processor->setParameter('', $key, $val);
			}

			$xml = $processor->transformToDOC($xml);
		}

		// timto zpusobem vypustime uvodni xml deklaraci
		$result = '';
		foreach($xml->childNodes as $node)
		{
			$result .= $xml->saveXML($node)."\n";
		}

		$cache->save($result, $cache_id, $tags);
	}

	return $result;
}

function getMyTag($style)
{
	return str_replace('.', '_', basename($style));
}

/**
	 * Vrati priponusouboru
     *
     * @param string $filename
     * @return string
     */

function getExt($filename)
{
	$path_info = @pathinfo($filename);
	
	if (isset($path_info['extension']))
	{
	 return strtolower($path_info['extension']);
  }
  else
  {
    return "";
  }
	
	
}

/**
 * Zjisti, jestli je pripona RelaxNG
 * 
 * @param string $filename
 * @return bool 
*/

function isRngExt($filename)
{
	return getExt($filename) == 'rng';
}

/**
 * Zjisti, jestli je pripona XSLT
 * 
 * @param string $filename
 * @return bool 
*/

function isXsltExt($filename)
{
	return getExt($filename) == 'xsl' || getExt($filename) == 'xslt';
}

/**
 * Zjisti, jestli je pripona XML Schema
 * 
 * @param string $filename
 * @return bool 
*/

function isXsdExt($filename)
{
	return getExt($filename) == 'xsd';
}

/**
     * Vraci parametr CONFIGu
	 *
     * @param string $name
     * @return string
     */

function get_parameter($name) //ve funkci jako takove to nejde, takze na vzeti parametru je fce
{
	$plugin = &JPluginHelper::getPlugin('content',PLUGIN_NAME); // incializace odkazu na tridu
	$params = new JParameter($plugin->params); // funkce pro ziskani parametru
	$value = $params->get($name); // hodnota parametru
	return $value;
}

function handle_xml_error($errno, $errstr, $errfile, $errline)
{
  if (empty($errline))
  {
  $errstr = "<strong>$errstr</strong>";
  }

	$GLOBALS["xmlerrmsg"] .= $errstr." file: $errfile line: $errline\n";
	return true;

	if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0))
	{
		$GLOBALS["xml_code_error"] = 1;
	}
	else
	return false;
}

function handle_xsl_error($errno, $errstr, $errfile, $errline)
{
	if ($errno==E_WARNING && (substr_count($errstr,"DOMDocument::loadXML()")>0))
	{
		$GLOBALS["xsl_code_error"] = 1;
	}
	else
	return false;
}

function libxml_display_error($error)// errory pro validaci, se taky nanestesti musi rucne osetrit :(
{
	$return = "<br/>\n";
	switch ($error->level) {
		case LIBXML_ERR_WARNING:
			$return .= "<b>Warning $error->code</b>: ";
			break;
		case LIBXML_ERR_ERROR:
			$return .= "<b>Error $error->code</b>: ";
			break;
		case LIBXML_ERR_FATAL:
			$return .= "<b>Fatal Error $error->code</b>: ";
			break;
	}
	$return .= trim($error->message);
	if ($error->file) {
		$return .=    " in <b>$error->file</b>";
	}
	$return .= " on line <b>$error->line</b>\n";

	return $return;
}

function libxml_display_errors()
{
	$errors = libxml_get_errors();
	foreach ($errors as $error)
	{
		print libxml_display_error($error);
	}
	libxml_clear_errors();
}

function zapis_error($do, $error) // chybova hlaska pokud neco nepujde
{
	$allow_error_reporting = get_parameter("allow_error_reporting");//mame nastaveno zobrazovani chybovych hlasek?
	if($allow_error_reporting == true)
	{
		$error = "<span style=\"color:red;font-weight:bold;\">".$error."</span>"; // cerveny to staci ne?
		$output = $error.$do; // spojime s puvodni promennou a vratime
		return $output;
	}
	else
	return $do;
}

function vypreparuj_data($co_pred, $co_po, $data)
{
	$n = substr(strstr($data, $co_pred),strlen($co_pred));
	$n = trim(substr($n, 0, strlen($n) - strlen(strstr($n, $co_po))));
	return $n;
}

function axml2xslt_transform(&$row, &$params, $page) // funkce pluginu jako takoveho, zadava se do $mainframe
{
	$style_directory = get_parameter("style_directory"); // parametr adresare se styly

	if (JPATH_BASE==JPATH_ADMINISTRATOR){   //zjist�me, jestli jsme v administraci a pokud ano, p�evedeme cestu na adresov�n� frontendu
		$style_directory='../'.$style_directory;
	}

	$default_style = get_parameter("default_style"); // defaultni styl ktery se nastavuje v params
	$schema_validation = get_parameter("schema_validation"); // bude slouzit pro validaci dokumentu
	$validation_matters = get_parameter("validation_matters"); // zalezi na validaci?
	// parametry definovan� ---> jsou to ty parametry u toho nastaveni pluginu
	$chyba = false; // tak tohle uz je doufam standardni zalezitost, pokud bude true, neprojde transformem
	$nekontrolovat_file = false;

	// overeni zda je to opravdu XML -> prichazi zde
	if(substr($row->text,0,5)=="<?xml")// mame se tim vubec zatezovat?
	{
		if(empty($style_directory)) // pokud je vyplneny
		{
			$chyba = true;
			$row->text = zapis_error($row->text,"Adresar se styly musi byt nastaven!<br>");
		}
		else // dodelat na konec lomitko
		{
			if(eregi("\\\\",$style_directory))//kdyby tam byly ty idiotsky druhy lomitka
			$style_directory = str_replace("\\\\","/",$style_directory); // tady se odstranuji
			if(substr($style_directory,strlen($style_directory)-1,1) != "/")//dodelani lomitka nakonec
			$style_directory = $style_directory."/";//pridani lomitka pokud tam neni
		}

		if(eregi("http://",$style_directory) || eregi("http://",$default_style))
		{
			$chyba = true;
			$row->text = zapis_error($row->text,"Styl ani adresar nesmi byt na jinem serveru!<br>");
		}

		if(!file_exists($style_directory))
		{
			$chyba = true; // pokud ten adresar neexistuje, tak to dame userovi vedet
			$row->text = zapis_error($row->text,"Adresar se styly je nastaven ale na serveru neexistuje!<br>");
		}

		if(stripos($row->text,"<?xml-stylesheet") !== false) //je v souboru deklarace toho souboru?
		{
			$text2 = vypreparuj_data("<?xml-stylesheet","?>",$row->text);
			$text = vypreparuj_data('href="','"',$text2);//nj uvozovky uvozovky
			if(empty($text))
			$text = vypreparuj_data("href='","'",$text2);// dalsi cek, pokud je prazdno
			$text = basename($text);
			$style_file = $style_directory.$text; // takze slozeni souboru z filu, kdyz je tam ta stylesheet
		}
		else if(empty($default_style)) // neni defaultni styl ani nastavena transformace, nic tedy nedelej
		{
			$chyba = true; // neni default styl, neni ani neni deklarace ve filu
			$nekontrolovat_file = true;
		}
		else //neni nastaveny styl, ale je defaultni, tak tedy pouzij ten
		{
			$style_file = $style_directory.$default_style; // pro kontrolu zda soubor existuje
		}

		if($nekontrolovat_file === false)
		{
			if(!file_exists($style_file)) // existuje ten soubor se stylem?
			{
				$chyba = true; // nepusti dal, a vyhodi chybu pokud soubor se stylem neexistuje
				$row->text = zapis_error($row->text,"Soubor ($style_file) s definovanym stylem neexistuje!<br>");
			}

			$file_extension = end(explode(".",$style_file));
			if($file_extension != "xsl")
			{
				$chyba = true;
				$row->text = zapis_error($row->text,"Soubor obsahujici xsl data musi mit koncovku .xsl! Tento soubor ma koncovku .$file_extension.<br>");
			}
		}



		if($chyba === false) // vse je ok, probiha transformace
		{
			$transform = true;
			$xp = new XsltProcessor();
			// create a DOM document and load the XSL stylesheet
			$f = fopen($style_file,"r"); //otevrit soubor
			$file_data = fread($f,filesize($style_file));//precist ho a vzit si data (nacteni do pameti)
			$xsl = new DomDocument;
			// primo vlozeny XSL kod (je to o neco rychlejsi ac se musi udelat vic operaci)
			set_error_handler('handle_xsl_error'); //kvuli errorum (jsou tu spatne vyreseny)
			$xsl->loadXML($file_data);
			restore_error_handler(); // nastaveni erroru zpet, kvuli rychlosti provadeni
			//$row->text = zapis_error($row->text,"XSL kod neni v poradku.<br>");
			// nacteni stylesheetu do dokumentu
			if($GLOBALS["xsl_code_error"] == 1)
			{
				$transform = false;
				$row->text = zapis_error($row->text,"XSL kod neni v poradku.<br>");
			}
			else
			$xp->importStylesheet($xsl);
			// docasny DOM dokument s primo vlozenejma datama k transformaci
			$xml_document = new DomDocument();
			set_error_handler('handle_xml_error'); //kvuli errorum (jsou tu spatne vyreseny)
			$xml_document->loadXML($row->text);
			restore_error_handler(); // nastaveni erroru zpet, kvuli rychlosti provadeni
			if($GLOBALS["xml_code_error"] == 1)
			{
				$transform = false;
				$row->text = zapis_error($row->text,"XML kod neni v poradku.<br>");
			}
			libxml_use_internal_errors(true);
			if(!empty($schema_validation))
			{
				if(!file_exists($style_directory.$schema_validation))
				{
					$transform = false;
					$row->text = zapis_error($row->text,"Soubor se sch�matem neexistuje.<br>");
				}
				else
				{
					if(!$xml_document->schemaValidate($style_directory.$schema_validation))
					{
						if($validation_matters == 1)
						{
							$transform = false;
							$row->text = zapis_error($row->text,"XML dokument neni validni, transformace neprobehla.<br>");
							//$row->text = zapis_error($row->text,libxml_display_errors());
						}
						else
						{
							$GLOBALS["validation_error"] = 1;
						}
					}
				}
			}
			//nahozeni parametru dulezitych k transformaci (zavedeni namespacu)
			$xp->setParameter($namespace, 'id1', 'value1');
			$xp->setParameter($namespace, 'id2', 'value2');
			//transformace jako takov� se vsemi daty, ktera jsou zapotrebi
			if($transform === true)
			{
				if ($html = $xp->transformToXML($xml_document))
				{
					$row->text = $html; //nasazeni toho pretransformovaneho textu misto puvodniho xml filu
					if($GLOBALS["validation_error"] == 1) // nebyla nahodou chyba pri validaci, na ktere ale tolik nezalezi?
					{
						$row->text = zapis_error($row->text,"XML dokument neni validni podle \"$schema_validation\", ale transformace probehla<br>");
						$GLOBALS["validation_error"] = 0;
					}
				}
				else
				$row->text = zapis_error($row->text,"Transformace XML souboru se nezdarila!<br>"); //nejaka neznama chyba pri transformaci -> to se uz bohuzel skriptem neda ovlivnit
			}
			else
			{
				$GLOBALS["xml_code_error"] = 0; //kvuli tomu aby to pri pristim projiti, pokud nebude chyba pustilo transformaci neceho jinyho
				$GLOBALS["xsl_code_error"] = 0; //kvuli tomu aby to pri pristim projiti, pokud nebude chyba pustilo transformaci neceho jinyho

			}

		}
	}
}
?>
