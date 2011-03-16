<?php
/**
 * @license    GNU/GPL
 * @author Stanislav Vojíř - xvojs03
 * @copyright Stanislav Vojíř, 2009
 *   
 */
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.view');
           
class BkefViewEditFormat extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      $doc->addStyleSheet('components/com_bkef/css/main.css');
      
      echo '<h1>'.$this->h1.'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      
      $autor='';
      if ($fId>-1) {
        $autor=@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]->Author;
      }else {
        $user=& JFactory::getUser();
        $autor=$user->name;
      }
        
      ?>
      <form action="index.php?option=com_bkef&amp;task=<?php echo $this->potvrzeni?>Format" method="post" target="_parent" >
        <table>
          <tr>
            <td><?php echo JText::_('FORMAT_NAME');?>&nbsp;</td>
            <td>
              <input type="text" name="name" title="<?php echo JText::_('TITLE_EDIT_FORMAT_NAME');?>" value="<?php if ($fId>-1) echo @$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]['name']; ?>" style="width:350px;font-weight:bold;"/>
            </td>
          </tr>
          <tr>
            <td></td>
            <td class="infotext missing"><?php echo JText::_('EDIT_FORMAT_NAME_WARNING');?></td>
          </tr>
          <tr>
            <td><?php echo JText::_('FORMAT_AUTHOR');?>&nbsp;</td>
            <td>
              <input type="text" name="author" value="<?php echo $autor; ?>" title="<?php echo JText::_('TITLE_EDIT_FORMAT_AUTHOR');?>" style="width:350px;"/>
            </td>
          </tr>
          <tr><td colspan="2">&nbsp;</td></tr>
          <tr>
            <td><?php echo JText::_('FORMAT_DATA_TYPE');?></td>
            <td>
              <select name="dataType" title="<?php echo JText::_('TITLE_EDIT_FORMAT_SELECT_DATATYPE');?>">
                <option value="String"<?php
                                        if ($fId>-1)
                                         if (@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]->DataType=='String'){echo ' selected="selected" ';}
                                      ?>>String</option>
                <option value="Float" <?php
                                        if ($fId>-1)
                                         if (@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]->DataType=='Float'){echo ' selected="selected" ';}
                                      ?>>Real Number</option>
                <option value="Integer"<?php
                                        if ($fId>-1)
                                         if (@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]->DataType=='Integer'){echo ' selected="selected" ';}
                                      ?>>Integer Number</option>
              </select>
            </td>
          </tr>
          <tr>
            <td><?php echo JText::_('FORMAT_VALUE_TYPE');?></td>
            <td>
              <select name="valueType" title="<?php echo JText::_('TITLE_EDIT_FORMAT_SELECT_VALUETYPE');?>">
                <option value="Cardinal"<?php
                                        if ($fId>-1)
                                         if (@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]->ValueType=='Cardinal'){echo ' selected="selected" ';}
                                      ?>>Cardinal</option>
                <option value="Ordinal" <?php
                                        if ($fId>-1)
                                         if (@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]->ValueType=='Ordinal'){echo ' selected="selected" ';}
                                      ?>>Ordinal</option>
                <option value="Nominal"<?php
                                        if ($fId>-1)
                                         if (@$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats->Format[$fId]->ValueType=='Nominal'){echo ' selected="selected" ';}
                                      ?>>Nominal</option>
              </select>
            </td>
          </tr>
        </table>
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $this->fId; ?>" />
        <input type="hidden" name="potvrzeni" value="<?php echo $this->potvrzeni; ?>" id="potvrzeni" />
        <input type="submit" value="<?php echo JText::_('SAVE');?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>