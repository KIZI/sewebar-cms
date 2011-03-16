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
           
class BkefViewEditCollation extends JView
{
  function display($tpl = null)
  {               
      $doc = & JFactory::getDocument();
      if (JPATH_BASE!=JPATH_ADMINISTRATOR){
        $doc->addStyleSheet('components/com_bkef/css/general.css');
        $doc->addStyleSheet('components/com_bkef/css/component.css');
      }         
      
      echo '<h1>'.$this->h1.'</h1>';
      
      $xml=$this->xml;
      $maId=intval($this->maId);
      $fId=intval($this->fId);
      
      if ($fId>-1){
        $format=$xml->MetaAttributes[0]->MetaAttribute[$maId]->Formats[0]->Format[$fId];
      }
      ?>
      <form action="index.php?option=com_bkef&amp;task=editCollation" method="post" target="_parent" >
      <table><tr><td><?php echo JText::_('COLLATION_TYPE'); ?>:</td><td><select name="type" title="<?php echo JText::_('TITLE_EDIT_COLLATION_TYPE_SELECT'); ?>">
        <option value="Alphabetical"<?php
                                      if (@$format->Collation[0]['type']=='Alphabetical'){echo ' selected="selected" ';}
                                    ?>>Alphabetical</option>
        <option value="Numerical"<?php
                                      if (@$format->Collation[0]['type']=='Numerical'){echo ' selected="selected" ';}
                                    ?>>Numerical</option>
        <?php
          if (@$format->AllowedRange[0]->Enumeration){
        ?>
          <option value="Enumeration"<?php
                                      if (@$format->Collation[0]['type']=='Enumeration'){echo ' selected="selected" ';}
                                    ?>>Enumeration</option>
        <?php
          }
        ?>  
      </select>
      </td>
      </tr>
      <tr>
      <td><?php echo JText::_('SENSE'); ?>
      </td>
      <td>
        <select name="sense" title="<?php echo JText::_('TITLE_EDIT_COLLATION_SENSE_SELECT'); ?>">
          <option value="Descending" <?php
                                       if (@$format->Collation[0]['sense']=='Descending'){echo ' selected="selected" ';}
                                     ?> >Descending</option>
          <option value="Ascending"<?php
                                       if (@$format->Collation[0]['sense']=='Ascending'){echo ' selected="selected" ';}
                                     ?>>Ascending</option>
        </select>
      </td>
      </tr>
      </table>
        <input type="hidden" name="article" value="<?php echo $this->article; ?>" />
        <input type="hidden" name="maId" value="<?php echo $this->maId; ?>" />
        <input type="hidden" name="fId" value="<?php echo $this->fId; ?>" />
        <input type="hidden" name="potvrzeni" value="1" />
        <input type="submit" value="<?php echo JText::_('SAVE'); ?>..." />
      </form>
      <?php
      //parent::display($tpl);
  }
}

?>