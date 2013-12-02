<?php  

defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('MANAGE_REPORTS').'</h1>';
  echo '<h2>'.JText::_('ANALYTICAL_REPORTS').'</h2>';
  
  $noArticles=true;
  if (count($this->reportsArticles)>0){
    echo '<table class="myAdminTable" style="width:690px;">';
    echo '<tr>
            <th>'.JText::_('TITLE').'</th>
            <th style="width:100px;">'.JText::_('MODIFIED').'</th>
            <th style="width:250px;">'.JText::_('ACTIONS').'</th>
          </tr>';
    $row=0;
    foreach ($this->reportsArticles as $article) { //TODO dodelat ikonku zamku
      if ($article->locked==3){
        continue;
      }
      $noArticles=false;
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td><a href="'.JRoute::_('index.php?option=com_content&view=article&id='.$article->id.'&catid='.$this->reportsCategory).'">'.$article->title.'</a>';
      if ($article->locked>0){
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
      echo   '<td class="actionsTd">';
      if ($article->locked<2){
        //článek můžeme upravovat
        //TODO dodělat návratovou hodnotu!!!
        echo   '<a href="'.JRoute::_('index.php?option=com_content&task=article.edit&a_id='.$article->id).'&redirect='.'">'.JText::_('EDIT_ARTICLE').'</a>
                <a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=renameArticle&tmpl=component&id='.$article->id).'" class="modal" rel="{handler:\'iframe\',size: {x: 500, y: 200}}">'.JText::_('RENAME').'</a>
                <a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=deleteArticle&tmpl=component&id='.$article->id).'" class="modal" rel="{handler:\'iframe\',size: {x: 500, y: 200}}">'.JText::_('DELETE').'</a>';
      }          
      echo   '</td>
            </tr>';
      $row=($row+1)%2;
    }
    echo '</table>';
  }
  if ($noArticles){
    echo '<div class="legendDiv">'.JText::_('NO_ARTICLES_FOUND').'</div>';
  }
  
  echo '<div style="padding:10px;"><a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&tmpl=component&task=newArticle&catid='.$this->reportsCategory).'" class="modal button" rel="{handler:\'iframe\',size: {x: 500, y: 200}}">'.JText::_('CREATE_NEW_ARTICLE').'</a></div>';
  
  echo '<h2>'.JText::_('PMML_ARTICLES').'</h2>';
  
  $noArticles=true;
  if (count($this->pmmlArticles)>0){
    echo '<table class="myAdminTable" style="width:690px;">';
    echo '<tr>
            <th>'.JText::_('TITLE').'</th>
            <th style="width:100px;">'.JText::_('MODIFIED').'</th>
            <th style="width:240px;">'.JText::_('ACTIONS').'</th>
          </tr>';
    $row=0;
    foreach ($this->pmmlArticles as $article) { //TODO dodelat ikonku zamku
      if ($article->locked==3){
        continue;
      }
      $noArticles=false;
    	echo '<tr'.($row==1?' class="row1"':'').'>
              <td><a href="'.JRoute::_('index.php?option=com_content&view=article&id='.$article->id.'&catid='.$this->pmmlCategory).'">'.$article->title.'</a>';
      if ($article->locked>0){
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
      echo   '<td class="actionsTd">';
      if ($article->locked<2){
        //článek můžeme upravovat
        echo   '<a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=renameArticle&tmpl=component&id='.$article->id).'" class="modal" rel="{handler:\'iframe\',size: {x: 500, y: 200}}">'.JText::_('RENAME').'</a>
                <a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=deleteArticle&tmpl=component&id='.$article->id).'" class="modal" rel="{handler:\'iframe\',size: {x: 500, y: 200}}">'.JText::_('DELETE').'</a>';
      }          
      echo   '</td>
            </tr>';
      $row=($row+1)%2;
    }
    echo '</table>';
  }
  if ($noArticles){
    echo '<div class="legendDiv">'.JText::_('NO_ARTICLES_FOUND').'</div>';
  }
  
  echo '<div style="padding:10px;"><a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=uploadPmmlFiles&tmpl=component&catid='.$this->pmmlCategory).'" class="modal button" rel="{handler:\'iframe\',size:{x:500,y:300}}">'.JText::_('UPLOAD_PMML_FILES').'</a></div>';
  
  
  
  
  
  
        
?>