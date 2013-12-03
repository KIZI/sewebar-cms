<?php
  jimport( 'joomla.application.component.model' );
              
                     
  class sewebarModelArticles extends JModel{
  	
    /**
     *  Funkce vracející podmínku pro WHERE v SQL, která kontroluje, jestli patří kategorie či článek do některého z access levelů uživatele
     *  @return string   
     */     
    private function getAccessWhereSql($tableName=''){
      $user =& JFactory::getUser();
      $viewLevels=$user->getAuthorisedViewLevels();
      if ($tableName!=''){$tableName.='.';}
    
      $viewLevelsArr=array();            
      $viewLevelsSql='';
      if (count($viewLevels)>0){
        foreach ($viewLevels as $viewLevel) {
          if (!in_array($viewLevel,$viewLevelsArr)){
            $viewLevelsSql.=' OR '.$tableName.'access="'.$viewLevel.'"';
            $viewLevelsArr[]=$viewLevel;
          }
        }
      }
      unset($viewLevelsArr);
      unset($viewLevels);
      return substr($viewLevelsSql,3);
    }
    
    /**
     *  Funkce vracející hodnotu konkrétní položky
     */         
    public function getArticlesInCategoryByUsergroup($userGroupId,$categoryId=-1){          
      $db = & JFactory::getDBO();
      $user =& JFactory::getUser();
                                                              
      //nastavení where částí SQL dotazu
      $whereClause="(state>=0) AND (".$this->getAccessWhereSql('ct').")";
      if ($categoryId>-1){
        $whereClause.=" AND (ct.catid='".$categoryId."')";
      }
      //                       
      $db->setQuery("SELECT ct.title,ct.id,date_format(ct.created, '%d.%m.%y %h:%i') as cdate,date_format(ct.modified, '%d.%m.%y %h:%i') as mdate,cat.title as categoryTitle,ct.checked_out FROM #__content ct LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE $whereClause");
      
      $rows = $db->loadObjectList();
      $resultRows=array();
      if (count($rows)>0){
        //musíme zkontrolovat, jestli může uživatelská skupina upravovat dané články
        foreach ($rows as $row){
          if (JAccess::checkGroup($userGroupId,'core.delete','com_content.article.'.$row->id)){
            $resultRows[]=$row;
          }
        }
      }
                        
      return $resultRows; 
    }
    
    /**
     *  Funkce vracející hodnotu konkrétní položky
     */         
    public function getArticlesInCategory($categoryId=-1,$editor=true,$canDelete=true,$isAuthor=false){
      $db = & JFactory::getDBO();
      $user =& JFactory::getUser();

      //nastavení where částí SQL dotazu
      $whereClause="(state>=0) AND (".$this->getAccessWhereSql('ct').")";
      if ($categoryId>-1){
        $whereClause.=" AND (ct.catid='".$categoryId."')";
      }
      if ($isAuthor){
        $whereClause.=' AND (ct.created_by="'.$user->id.'" OR ct.modified_by="'.$user->id.'")';
      }
      //                       
      $db->setQuery("SELECT ct.title,ct.id,date_format(ct.created, '%d.%m.%y %h:%i') as cdate,date_format(ct.modified, '%d.%m.%y %h:%i') as mdate,cat.title as categoryTitle,ct.checked_out FROM #__content ct LEFT JOIN #__categories cat ON ct.catid=cat.id WHERE $whereClause");
      
      $rows = $db->loadObjectList();
      if (($editor)&&(count($rows)>0)){   
        //musíme zkontrolovat, jestli může uživatel upravovat dané články
        $userId=$user->get('id');
        foreach ($rows as $row) {   
        	if(!($user->authorise('core.edit','com_content.article.'.$row->id))){
            $row->locked=3;  
          }else{
            if ($row->checked_out>0){
              if ($row->checked_out!=$userId){
                $row->locked=2;
              }else{
                $row->locked=1;          
              }
            }else{
              $row->locked=0;
            }
          }
        }
      }elseif (($canDelete)&&(count($rows)>0)){
        //musíme zkontrolovat, jestli může uživatel upravovat dané články
        $userId=$user->get('id');
        foreach ($rows as $row) {
        	if(!($user->authorise('core.delete','com_content.article.'.$row->id))){
            $row->locked=3;
          }else{
            $row->locked=0;
          }
        }
      }
                        
      return $rows; 
    }
    
    /**
     *  Funkce vracející z DB záznam článku dle zadaného ID
     */         
    public function getArticle($articleId){
      $db=&JFactory::getDBO();
      $db->setQuery("SELECT * FROM #__content WHERE id=".$db->quote($articleId)." LIMIT 1;");
      return $db->loadObject();
    }
    
    /**
     *  Funkce pro smazání článku se zadaným ID
     *        
     *  @return boolean     
     */         
    public function deleteArticle($articleId){
      $user =& JFactory::getUser();
      if($user->authorise('core.delete','com_content.article.'.$articleId)){
        $db=&JFactory::getDBO();
        $db->setQuery("UPDATE #__content SET state=-2 WHERE id=".$db->quote($articleId)." LIMIT 1;");
        $db->query();
        return true;
      }else{
        return false;
      }
    } 
    
    /**
     *  Funkce pro přejmenování článku
     */         
    public function renameArticle($articleId,$title){
      $user=&JFactory::getUser();
      if (($user->authorise('core.delete','com_content.article.'.$articleId))||($user->authorise('core.edit','com_content.article.'.$articleId))){
        $db = & JFactory::getDBO();
        //vyresime pripadne opakovani nazvu v dane kategorii
        $titleAppendix=0;
        $db->setQuery("SELECT id FROM #__content WHERE catid=".$db->quote($categoryId).' AND title='.$db->quote($title).' AND id!='.$db->quote($articleId).' LIMIT 1;');
        while ($db->loadObject()){
          $titleAppendix++;
          $db->setQuery("SELECT id FROM #__content WHERE catid=".$db->quote($categoryId).' AND title='.$db->quote($title.' ('.$titleAppendix.')').' AND id!='.$db->quote($articleId).' LIMIT 1;');  
        }
        if ($titleAppendix>0){
          $title.=' ('.$titleAppendix.')';
        }
        $titleAlias=JFilterOutput::stringURLSafe($title);
        //prejmenujeme clanek
        $db->setQuery("UPDATE #__content SET title=".$db->quote($title).',alias='.$db->quote($titleAlias).' WHERE id='.$db->quote($articleId).' LIMIT 1;');
        return $db->query();
      }return false; 
    } 
     
    
    /**
     *  Funkce pro vytvoření nového článku
     */         
    public function newArticle($title,$categoryId,$allowEdit=true,$content="TODO"){
      $user=&JFactory::getUser();
      $articleArr=array('id'=>0,'title'=>$title,'catid'=>$categoryId,'created_by'=>$user->get('id'),'access'=>2,'state'=>1,'articletext'=>$content);
      //zjistíme přístupová práva
      $currentUserGroups=$user->getAuthorisedGroups();
      
      JLoader::import('config', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_sewebar_vyuka' . DS . 'models' );
      $configModel=JModel::getInstance('Config','sewebarModel');
      JLoader::import('config', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_sewebar_vyuka' . DS . 'models' );
      $adminModel=JModel::getInstance('Admin','sewebarModel');
      $adminUserGroups=$adminModel->getUserGroups($configModel->getConfig('PARENT_USERGROUP_ID'));
                                              
      $userGroups=array();
      if (count($adminUserGroups)>0){
        foreach ($adminUserGroups as $userGroup){
        	if (in_array($userGroup->id,$currentUserGroups)){
            $userGroups[]=$userGroup->id;
          }
        }
      }
      
      $articleArr['rules']=array();
      $articleArr['rules']['core.edit']=array();
      $articleArr['rules']['core.delete']=array();
      $articleArr['rules']['core.edit.state']=array();
      if (count($userGroups)>0){
        foreach ($userGroups as $userGroupId){
          $articleArr['rules']['core.edit'][$userGroupId]=$allowEdit;
          $articleArr['rules']['core.delete'][$userGroupId]=true; 	
        }
      }
      
      $db = & JFactory::getDBO();
      $titleAppendix=0;
      $db->setQuery("SELECT id FROM #__content WHERE catid=".$db->quote($categoryId).' AND title='.$db->quote($title).' LIMIT 1;');
      while ($db->loadObject()){
        $titleAppendix++;
        $db->setQuery("SELECT id FROM #__content WHERE catid=".$db->quote($categoryId).' AND title='.$db->quote($title.' ('.$titleAppendix.')').' LIMIT 1;');  
      }
      if ($titleAppendix>0){
        $articleArr['title']=$title.' ('.$titleAppendix.')';
      }
      
      //použijeme importovaný model z com_conent
      JLoader::import('article', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_content' . DS . 'models' );
      $contentModel = JModel::getInstance('Article', 'ContentModel' );
      return $contentModel->save($articleArr);
    }
    
    
    
    
  }
?>
