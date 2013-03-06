<?php 
  defined('_JEXEC') or die('Restricted access');
  echo '<div id="iziDiv">';
  
  function getGaugeWidth($count,$totalCount){
    return round($count/$totalCount*100,1);
  }
    
    
  echo '<a href="#" onclick="parent.close();" class="backButton">'.JText::_('CLOSE').'</a>';
  
  echo '<div id="izipreviewcolumnDiv">';
    echo '<h1>'.((string)$this->field->Name).'</h1>';
    $valuesCount=0;
    $rowsCount=0;
    $valuesMax=0;    
    if (count($this->categoriesArr)>0){  
      /*vypsani jednotlivych hodnot*/
      foreach ($this->categoriesArr as $category){
      	$valuesCount+=$category['frequency'];
        if ($category['frequency']>$valuesMax){
          $valuesMax=$category['frequency'];
        }
        $rowsCount++;
      }
      echo '<table id="previewInfoTable">
              <tr>';
      echo '    <td class="rowsCountTd">'.JText::_('ROWS_COUNT').': <strong>'.$valuesCount.'</strong></td>';
      echo '    <td class="differentCountTd">'.JText::_('DIFFERENT_CATEGORIES_COUNT').': <strong>'.$rowsCount.'</strong></td>';
      echo '    <td class="graphTd">
                  '.JText::_('GRAPH_STYLE').':
                </td>
                <td>
                  <form method="post" id="orderForm" action="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewAttribute&tmpl=component').'">
                    <input type="hidden" name="kbi" value="'.$this->kbiId.'" />
                    <input type="hidden" name="attribute" value="'.htmlspecialchars((string)$this->field->Name).'" />
                    <input type="hidden" name="order" value="'.htmlspecialchars($this->order).'" />
                    <select onchange="document.getElementById(\'orderForm\').submit();" name="graph">
                      <option value="normal" '.(($this->graphStyle=='normal')?'selected="selected"':'').'>'.JText::_('NORMAL').'</option>
                      <option value="relative" '.(($this->graphStyle=='relative')?'selected="selected"':'').'>'.JText::_('RELATIVE').'</option>
                    </select>
                  </form>
                </td>
                <td style="vertical-align:middle;padding-left:5px;">
                  <img src="./media/com_dbconnect/images/help.gif" alt="?" title="'.htmlspecialchars(JText::_('GRAPH_STYLE_INFO')).'" />
                </td>
                ';
      echo '  </tr>
            </table>'; 
      echo '<div id="previewDiv">';
      echo '<table>
              <tr>
                <th>
                  <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewAttribute&tmpl=component&kbi='.$this->kbiId.'&attribute='.((string)$this->field->Name)).'&graph='.$this->graphStyle.'&order=order'.((($this->order=='order'))?'/desc':'').'">'.JText::_('ORDER');
                  if ($this->order=='order'){
                    echo '<span class="order">&uarr;</span>';
                  }elseif($this->order=='order/desc'){
                    echo '<span class="order">&darr;</span>';
                  }
      echo '      </a>
                </th>
                <th>
                  <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewAttribute&tmpl=component&kbi='.$this->kbiId.'&attribute='.((string)$this->field->Name)).'&graph='.$this->graphStyle.'&order=name'.((($this->order=='name'))?'/desc':'').'">'.JText::_('VALUE_CATEGORY_NAME');
                  if ($this->order=='name'){
                    echo '<span class="order">&uarr;</span>';
                  }elseif($this->order=='name/desc'){
                    echo '<span class="order">&darr;</span>';
                  }
      echo '      </a>
                </th>
                <th>
                  <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewAttribute&tmpl=component&kbi='.$this->kbiId.'&attribute='.((string)$this->field->Name)).'&graph='.$this->graphStyle.'&order=frequency'.((($this->order=='frequency'))?'/desc':'').'">'.JText::_('FREQUENCY');
                  if ($this->order=='frequency'){
                    echo '<span class="order">&uarr;</span>';
                  }elseif($this->order=='frequency/desc'){
                    echo '<span class="order">&darr;</span>';
                  }
      echo '      </a>
                </th>
                <th>
                  <a href="'.JRoute::_('index.php?option=com_dbconnect&controller=izi&task=previewAttribute&tmpl=component&kbi='.$this->kbiId.'&attribute='.((string)$this->field->Name)).'&graph='.$this->graphStyle.'&order=frequency'.((($this->order=='frequency'))?'/desc':'').'">'.JText::_('PERC');
                  if ($this->order=='frequency'){
                    echo '<span class="order">&uarr;</span>';
                  }elseif($this->order=='frequency/desc'){
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
      foreach ($this->categoriesArr as $category){
      	echo '<tr>
                <td class="orderTd">'.htmlspecialchars($category['order'].'.').'</td>
                <td class="valueTd">'.htmlspecialchars($category['name']).'</td>
                <td class="countTd">'.$category['frequency'].'</td>
                <td class="percTd">';
                  $procent=getGaugeWidth($category['frequency'],$valuesCount);
        echo      $procent.'%';          
        echo '  </td>
                <td class="graphTd">
                  <div style="width:'.getGaugeWidth($category['frequency'],$graphTotalCount).'%;"></div>
                </td>
              </tr>';
      }
      echo '</table>';
    echo '</div>';
      /*--vypsani jednotlivych hodnot*/
    }else{
      echo '<div class="noValuesDiv">'.JText::_('NO_CATEGORIES_FOUND_IN_THIS_ATTRIBUTE').'</div>';
    }
    
        
  echo '</div>';
  
?>