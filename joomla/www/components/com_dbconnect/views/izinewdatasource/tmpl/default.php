<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  function getGaugeWidth($count,$totalCount){
    return round($count/$totalCount*100,1);
  }
  
  ?>
  <script type="text/javascript">
  //<![CDATA[

    function reloadParent(){
      parent.close();
      ///parent.reload();
    }
  //]]>
  </script>
  
  <?php
  echo '<a href="#" onclick="reloadParent()" class="backButton">'.JText::_('CLOSE').'</a>';
  
  echo '<div id="izipreviewcolumnDiv">';
    echo '<h1>'.$this->columnName.'</h1>';
    $valuesCount=0;
    foreach ($this->values as $valueRow){
    	$valuesCount+=$valueRow['pocet'];
    }
    
    echo '<div id="previewDiv">';
    echo '<table>
            <tr>
              <th>'.JText::_('VALUE').'</th>
              <th>'.JText::_('COUNT').'</th>
              <th>'.JText::_('PERC').'</th>
              <th></th>
            </tr>';
    foreach ($this->values as $valueRow){
    	echo '<tr>
              <td class="valueTd">'.htmlspecialchars($valueRow['hodnota']).'</td>
              <td class="countTd">'.$valueRow['pocet'].'</td>
              <td class="percTd">';
                $procent=getGaugeWidth($valueRow['pocet'],$valuesCount);
      echo      $procent.'%';          
      echo '  </td>
              <td class="graphTd">
                <div style="width:'.$procent.'%;"></div>
              </td>
            </tr>';
    }
    echo '</table>';
  echo '</div>';
        
  echo '</div>';
  
?>