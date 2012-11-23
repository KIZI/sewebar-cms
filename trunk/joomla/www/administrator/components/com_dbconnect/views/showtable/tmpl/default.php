<?php 
  defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('TABLE').': '.$this->dbtable.'</h1>';
  
  /*tabulka s radky*/
  if ((count($this->rows)>0)&&(count($this->columns)>0)){
    $columns=array();
    echo '<table class="myAdminTable">';
    //zahlavi
    echo '<tr>';
    foreach ($this->columns as $column){
    	$columns[]=$column['Field'];
      echo '<th>'.$column['Field'].'</th>';
    }
    echo '</tr>';
    //radky
    foreach ($this->rows as $row) {
    	echo '<tr>';
    	foreach ($columns as $col) {
     	  echo '<td>'.$row[$col].'</td>'; 
      }
    	echo '</tr>';
    }
    echo '</table>';
  }else{
    echo '<div class="error">'.JText::_('NO_ROWS_FOUND').'</div>';
  }
  /*--tabulka s radky*/
  /*strankovani*/
  echo '<p>';
  jimport('joomla.html.pagination');
  $pageNav = new JPagination($this->total,$this->limitstart,$this->limit);
  echo  '<form action="'.JRoute::_('').'" name="adminForm" id="adminForm" method="post">';
  echo $pageNav->getListFooter();
  echo '</form>';
  echo '</p>';
  /*--*/

?>

