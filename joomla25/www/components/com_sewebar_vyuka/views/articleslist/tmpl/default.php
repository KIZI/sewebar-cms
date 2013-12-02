<?php 

defined('_JEXEC') or die('Restricted access');
                     
  echo '<h1>'.JText::_('MANAGE_REPORTS').'</h1>';
  /*filtrovaci formular*/
  echo '<div>
          <form action="'./*JRoute::_('index.php?option=com_sewebar_vyuka&task=articleslist').*/'" id="filterForm" method="post">
            <table>
              <tr>
                <td>'.JText::_('FILTER_BY_USERGROUP').'</td>
                <td>
                  <select name="usergroup" onchange="document.getElementById(\'filterForm\').submit();">
                    <option value="">--'.JText::_('NOT_SET').'--</option>';
                  foreach ($this->userGroups as $userGroup) {
                    echo '<option value="'.$userGroup->id.'" '.(($userGroup->id==$this->usergroupId)?'selected="selected"':'').'>'.$userGroup->title.'</option>';	
                  }              
  echo '          </select>
                </td>
              </tr>
            </table>
          </form>
        </div>';
  /*--filtrovaci formular*/      
  echo '<h2>'.JText::_('ANALYTICAL_REPORTS').'</h2>';
  $noArticles=true;
  if (count($this->reportsArticles)>0){
    echo '<table class="myAdminTable" style="width:670px;">';
    echo '<tr>
            <th>'.JText::_('TITLE').'</th>
            <th style="width:100px;">'.JText::_('MODIFIED').'</th>
            <th style="width:100px;">'.JText::_('ACTIONS').'</th>
          </tr>';
    $row=0;
    foreach ($this->reportsArticles as $article) { 
      if (@$article->locked==3){
        continue;
      }
      $noArticles=false;
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td><a href="'.JRoute::_('index.php?option=com_content&view=article&id='.$article->id.'&catid='.$this->reportsCategory).'">'.$article->title.'</a>';
      if (@$article->locked>0){
        //článek je zamčený - zobrazíme zámek
        echo ' <img src="media/com_sewebar_vyuka/images/lock.png" alt="Lock" />';
      }        
      echo '  </td>
              <td>';
      if (substr($article->mdate,0,2)=='00'){
        echo $article->cdate;
      }else{
        echo $article->mdate;
      }        
             
      echo '  </td>';
      echo   '<td class="actionsTd">
                <a href="'.JRoute::_('index.php?option=com_content&view=article&id='.$article->id.'&catid='.$this->reportsCategory).'">'.JText::_('SHOW_ARTICLE').'</a>
              </td>
            </tr>';
      $row=($row+1)%2;
    }
    echo '</table>';
  }
  if ($noArticles){
    echo '<div class="legendDiv">'.JText::_('NO_ARTICLES_FOUND').'</div>';
  }
  
  
  echo '<h2>'.JText::_('PMML_ARTICLES').'</h2>';
  
  $noArticles=true;
  if (count($this->pmmlArticles)>0){
    echo '<table class="myAdminTable" style="width:670px;">';
    echo '<tr>
            <th>'.JText::_('TITLE').'</th>
            <th style="width:100px;">'.JText::_('MODIFIED').'</th>
            <th style="width:100px;">'.JText::_('ACTIONS').'</th>
          </tr>';
    $row=0;
    foreach ($this->pmmlArticles as $article) { //TODO dodelat ikonku zamku
      if (@$article->locked==3){
        continue;
      }
      $noArticles=false;
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td><a href="'.JRoute::_('index.php?option=com_content&view=article&id='.$article->id.'&catid='.$this->pmmlCategory).'">'.$article->title.'</a>';       
      echo '  </td>
              <td>';
      if (substr($article->mdate,0,2)=='00'){
        echo $article->cdate;
      }else{
        echo $article->mdate;
      }        
             
      echo '  </td>
              <td class="actionsTd">
                <a href="'.JRoute::_('index.php?option=com_content&view=article&id='.$article->id.'&catid='.$this->pmmlCategory).'">'.JText::_('SHOW_ARTICLE').'</a>
              </td>
            </tr>';
      $row=($row+1)%2;
    }
    echo '</table>';
  }
  if ($noArticles){
    echo '<div class="legendDiv">'.JText::_('NO_ARTICLES_FOUND').'</div>';
  }
  
  
  
  
  
  
        
?>