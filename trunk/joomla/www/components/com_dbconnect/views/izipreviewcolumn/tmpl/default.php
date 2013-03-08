<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  function getGaugeWidth($count,$totalCount){
    return round($count/$totalCount*100,1);
  }
  
  echo '<a href="#" onclick="parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  
  echo '<div id="izipreviewcolumnDiv">';
    echo '<h1>'.$this->columnName.'</h1>';
    $valuesCount=0;
    $rowsCount=0;
    $valuesMax=0;
    foreach ($this->values as $valueRow){
    	$valuesCount+=$valueRow['pocet'];
      if ($valueRow['pocet']>$valuesMax){
        $valuesMax=$valueRow['pocet'];
      }
      $rowsCount++;
    }
    echo '<table id="previewInfoTable">
            <tr>';
    echo '    <td class="rowsCountTd">'.JText::_('ROWS_COUNT').': <strong>'.$valuesCount.'</strong></td>';
    echo '    <td class="differentCountTd">'.JText::_('DIFFERENT_VALUES_COUNT').': <strong>'.$rowsCount.'</strong></td>';
    echo '    <td class="graphTd">
                '.JText::_('GRAPH_STYLE').':
              </td>
              <td>
                <form method="post" id="orderForm" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewColumn&tmpl=component').'">
                  <input type="hidden" name="kbi" value="'.$this->kbiId.'" />
                  <input type="hidden" name="col" value="'.htmlspecialchars($this->columnName).'" />
                  <input type="hidden" name="order" value="'.htmlspecialchars($this->order).'" />
                  <select onchange="document.getElementById(\'orderForm\').submit();" name="graph">
                    <option value="normal" '.(($this->graphStyle=='normal')?'selected="selected"':'').'>'.JText::_('NORMAL').'</option>
                    <option value="relative" '.(($this->graphStyle=='relative')?'selected="selected"':'').'>'.JText::_('RELATIVE').'</option>
                  </select>
                </form>
              </td>
              <td style="vertical-align:middle;padding-left:5px;">
                <img src="./media/com_dbconnect/images/help.gif" alt="?" title="'.htmlspecialchars(JText::_('GRAPH_STYLE_INFO')).'" />
              </td>';
    echo '  </tr>
          </table>'; 
    echo '<div id="previewDiv">';
    echo '<table>
            <tr>
              <th>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewColumn&tmpl=component&kbi='.$this->kbiId.'&col='.$this->columnName).'&graph='.$this->graphStyle.'&order=hodnota'.((($this->order=='hodnota'))?'/desc':'').'">'.JText::_('VALUE');
                if ($this->order=='hodnota'){
                  echo '<span class="order">&uarr;</span>';
                }elseif($this->order=='hodnota/desc'){
                  echo '<span class="order">&darr;</span>';
                }
    echo '      </a>
              </th>
              <th>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewColumn&tmpl=component&kbi='.$this->kbiId.'&col='.$this->columnName).'&graph='.$this->graphStyle.'&order=pocet'.((($this->order=='pocet'))?'/desc':'').'">'.JText::_('COUNT');
                if ($this->order=='pocet'){
                  echo '<span class="order">&uarr;</span>';
                }elseif($this->order=='pocet/desc'){
                  echo '<span class="order">&darr;</span>';
                }
    echo '      </a>
              </th>
              <th>
                <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewColumn&tmpl=component&kbi='.$this->kbiId.'&col='.$this->columnName).'&graph='.$this->graphStyle.'&order=pocet'.((($this->order=='pocet'))?'/desc':'').'">'.JText::_('PERC');
                if ($this->order=='pocet'){
                  echo '<span class="order">&uarr;</span>';
                }elseif($this->order=='pocet/desc'){
                  echo '<span class="order">&darr;</span>';
                }
    echo '      </a>
              </th>
              <th></th>
            </tr>';
    if ($this->graphStyle=='relative'){
      $graphTotalCount=$valuesMax;
    }else{
      $graphTotalCount=$valuesCount;
    }        
    foreach ($this->values as $valueRow){
    	echo '<tr>
              <td class="valueTd">'.htmlspecialchars($valueRow['hodnota']).'</td>
              <td class="countTd">'.$valueRow['pocet'].'</td>
              <td class="percTd">';
                $procent=getGaugeWidth($valueRow['pocet'],$valuesCount);
      echo      $procent.'%';          
      echo '  </td>
              <td class="graphTd">
                <div style="width:'.getGaugeWidth($valueRow['pocet'],$graphTotalCount).'%;"></div>
              </td>
            </tr>';
    }
    echo '</table>';
  echo '</div>';
        
  echo '</div>';
  
?>