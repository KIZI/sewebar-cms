<?php 
/**
* @package helloworld02
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DELETE_DB_CONNECTION').'</h1>';
  
  echo '<p>'.JText::_('DELETE_DB_CONNECTION_QUESTION').'</p>';
  echo '<table>
          <tr><td>'.JText::_('DB_SERVER').'</td><td>'.$this->connection->server.'</td></tr>
          <tr><td>'.JText::_('DB_SERVER').'</td><td>'.$this->connection->username.'</td></tr>
          <tr><td>'.JText::_('DB_NAME').'</td><td>'.$this->connection->db_name.'</td></tr>
          <tr><td>'.JText::_('DB_TABLE').'</td><td>'.$this->connection->table.'</td></tr>
        </table>';
  echo '<form method="post" target="_parent" action="'.JRoute::_('index.php?option=com_dbconnect&task=deleteConnection').'">
          <input type="hidden" name="connection_id" value="'.$this->connection->id.'" />
          <input type="hidden" name="adminMode" value="'.$this->adminMode.'" />
          <input type="submit" name="xx" value="'.JText::_('DELETE').'" />&nbsp;<input type="submit" name="xx" value="'.JText::_('STORNO').'" />
        </form>';
  /*--*/

?>

