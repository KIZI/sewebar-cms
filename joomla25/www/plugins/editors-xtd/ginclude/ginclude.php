<?php

// ochrana proti přímému přístupu
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
jimport( 'joomla.environment.uri' );
/**
 * gInclude buton - editors-xtd plugin pro vkládání obsahů pomocí komponenty gInclude
 *
 * @author Stanislav Vojíř <stanislav.vojir@gmail.com>
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonGinclude extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param 	array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function plgButtonGinclude(& $subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 * Display the button
	 *
	 * @return array A two element array of ( imageName, textToInsert )
	 */
	function onDisplay($name)
	{
		$mainframe=JFactory::getApplication();

		$doc = & JFactory::getDocument();
		/* @var $doc JDocumentHTML */
		
		//Zpracování parametrů pluginu
		$title 	   = $this->params->get( 'title', 'ginclude' );
		$category = $this->params->get( 'category', -1 );

    $_SESSION['ginclude']['hide_filterDelete']=$this->params->get('hide_filterDelete',0);
    $_SESSION['ginclude']['filterDelete']=$this->params->get('filterDelete',0);
		if ($category>0){
      $_SESSION['ginclude']['category']=$category;
      $_SESSION['ginclude']['default_category']=$category;
      $_SESSION['ginclude']['hide_category']=$this->params->get('hide_category',0);
    }
    
    $jRoot=JURI::root();
    $jAjaxRoot=$jRoot;
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      $jAjaxRoot.='administrator/';
    }

		$declaration	= 
		"
    
    function gInclude(id, part) {                                                                 
      var a = new Request({
                method: 'get',
                url: '".$jAjaxRoot."index.php?option=com_ginclude&no_html=1&task=getArticle&article='+id+'&part='+part,
                onComplete: function(response) {  
                  if(response!=''){  
                    jInsertEditorText( '".str_replace("'","\'",$this->params->get('beforeCode','<div>&nbsp;</div>'))."'+response+'".str_replace("'","\'",$this->params->get('afterCode','<div>&nbsp;</div>'))."', '".$name."');
                  }
                }
              }).send();
 
      SqueezeBox.close()
		}
	";
		
		
		$doc->addScriptDeclaration($declaration);
		$imageUrl=$jRoot.'plugins/editors-xtd/ginclude/assets/j_button_ginclude.png';
		//if (JPATH_BASE==JPATH_ADMINISTRATOR){$imageUrl='../'.$imageUrl;};
		$declaration	=
		" .button2-left .ginclude 	{ background: url(".$imageUrl.") 100% 0 no-repeat; } ";
		
		$doc->addStyleDeclaration($declaration);
		
		//$template = $mainframe->getTemplate();

    $link = 'index.php?option=com_ginclude&task=smartPage&tmpl=component';
		JHTML::_('behavior.modal');

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', $title);
		$button->set('name', 'ginclude');
		$button->set('options', "{handler: 'iframe', size: {x: 1000, y: 500}}");

		return $button;
	}
}