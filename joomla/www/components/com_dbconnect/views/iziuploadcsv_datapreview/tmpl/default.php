<?php 
                             
  defined('_JEXEC') or die('Restricted access');

  $html= '<h2>'.JText::_('DATA_PREVIEW').'</h2>';
                       
  $html.= '<table>
             <tr>';
               $columnsCount=0;
               foreach ($this->csvData as $column){
               	 $html.= '<th>'.htmlspecialchars($column['name']).' <em>'.$column['datatype'].'</em></th>';
                 $columnsCount++;
               }
  $html.= '  </tr>';
  foreach ($this->csvRows as $row){
  	$html.= '<tr>';
              foreach ($row as $value) {
                $html.= '<td>'.htmlspecialchars($value).'</td>';	
              }
    $html.= '</tr>';
  }
  $html.= '</table>';
  
  echo json_encode(array('cols_count'=>$columnsCount,'rows_count'=>$this->rowsCount,'html'=>$html)); 
     
?>