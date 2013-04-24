<?php 
  defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('DB_CONNECTION').'</h1>';
  
  /*info o danem pripojeni*/
  echo '<table>
          <tr>
            <td>'.JText::_('DB_TYPE').'</td>
            <td><strong>'.$this->connection->db_type.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('DB_SERVER').'</td>
            <td><strong>'.$this->connection->server.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('USERNAME').'</td>
            <td><strong>'.$this->connection->username.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('DATABASE_NAME').'</td>
            <td><strong>'.$this->connection->db_name.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('TABLE_NAME').'</td>
            <td><strong>'.$this->connection->table.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('PRIMARY_KEY').'</td>
            <td><strong>'.$this->connection->primary_key.'</strong></td>
          </tr>
          <tr>
            <td>'.JText::_('SHARED_CONNECTION').'</td>
            <td>'.($this->connection->shared?'ok':'-').'</td>
          </tr>
        </table>';
  /**/
?>

