<?php
/**
 * HTML View class for the gInclude Component
 *  
 * @package    gInclude
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
 
/**
 * HTML View class for the HelloWorld Component
 *
 * @package    gInclude
 */
 
class BkefViewInfluences extends JView
{

  /**
   *  Funkce pro zobrazení přehledu článků pro jejich výběr
   */ 
  function influencesHtml(){    
    
    libxml_use_internal_errors(true);
    $xdoc = new DomDocument;
    $cestaKTransformaci = JPATH_COMPONENT.DS.'influences'.DS."bkef-styl-jakub.xsl";
                  
    //V případě, že by měla proměnná xmldocument obsahovat celý dokument jako string, je potřebná funkce na dalším řádku.
    $xdoc->loadXML($this->xmldocument); 
    
    //Kontrola zda existuje soubor, na základě kterého se má provést transformace.       
    /*if(file_exists($cestaKTransformaci)){*/
    	//pokud ano načte transformaci a následně ji provede a výsledek vrátí jako string.
    	$xdoc2 = new DomDocument(); 
    	$xdoc2->load($cestaKTransformaci);
    	$xsl = new XSLTProcessor();
    	$xsl->importStylesheet($xdoc2);
    	if ($html = $xsl->transformToXML($xdoc)) {
    		//Výpis výsledku transformace 
    		return $html."<div id=\"coZaskrtnout\" style=\"display: none;\">".$_GET["additionalInfo"]."</div>";
    	} else {
    	  return (JText::_('TRANSFORMATION_ERROR'));
    	}
    /*}*/
    
    libxml_clear_errors(); 
  } 
  
  function influencesHtml1(){    
    
    //$cestaKTransformaci = JPATH_COMPONENT.DS.'influences'.DS."bkef-styl-jakub.xsl";
                  
    return $this->xmldocument;
  } 

  function display($tpl = null)
  {   
    /*Ověření, jestli jde o přístup z administrace nebo front-endu*/
    $doc = &JFactory::getDocument();
    $doc->addScript('components/com_bkef/influences/influences.js');
    echo "<h1>Mutual Influences: ".$this->articleTitle."</h1>";
    $user=& JFactory::getUser();
	echo "<script type=\"text/javascript\">prihlasenyUzivatel=\"".$user->name."\";</script>";
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      //$doc->addStyleSheet('components/com_bkef/influences/style.css');
      //$doc->addScript('components/com_bkef/influences/influences.js');
      require_once(JApplicationHelper::getPath('toolbar_html'));
      TOOLBAR_bkef::_DEFAULT();
    }else{
      $doc->addStyleSheet('components/com_bkef/css/general.css');
      $doc->addStyleSheet('components/com_bkef/css/component.css');
      //$doc->addScript('components/com_bkef/influences/influences.js');
      echo '<div class="componentheading">'.JText::_('BKEF').'</div>';
      
    }
    $doc->addScriptDeclaration("cestaKObrazku='components/com_bkef/influences/img/';");
    $doc->addScriptDeclaration("prihlasenyUzivatel='".$this->username."';");
    //$doc->addScript('components/com_bkef/influences/influences.js');
    
    /**/
    
    echo $this->influencesHTML();    
  }
}

?>
