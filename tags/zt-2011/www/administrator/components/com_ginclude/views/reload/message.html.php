<?php

// no direct access 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
 
/**
 * HTML View class for the gInclude Component
 *  
 * @package    gInclude
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
 
class GincludeViewMessage extends JView
{
  function display($tpl = null){        
    /*Ověření, jestli jde o přístup z administrace nebo front-endu*/
    if (JPATH_BASE==JPATH_ADMINISTRATOR){
      require_once(JApplicationHelper::getPath('toolbar_html'));
      TOOLBAR_ginclude::_DEFAULT();
    }else{
      echo '<div class="componentheading">'.JText::_('GINCLUDE-UPDATE').'</div>';
      $doc = &JFactory::getDocument();
      $doc->addStyleSheet('components/com_ginclude/css/general.css');
      $doc->addStyleSheet('components/com_ginclude/css/component.css');
    }
    /**/

    echo '<h2>'.$this->title.'</h2>';    
    echo '<div>'.$this->text.'</div>';       
  }
}

?>