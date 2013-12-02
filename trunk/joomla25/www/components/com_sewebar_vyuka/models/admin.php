<?php
  jimport( 'joomla.application.component.model' );
              
                     
  class sewebarModelAdmin extends JModel{
  	
    /**
     *  Funkce vracející hodnotu konkrétní položky
     */         
    public function getUser($id){          
      $db=$this->getDBO();                   
      $db->setQuery('SELECT * FROM #__users WHERE id='.$db->quote($id).' LIMIT 1;');
      return $db->loadObject();
    }
    
    /**
     *  Funkce vracející hodnotu konkrétní položky
     */         
    public function getUserGroup($id){
      $db=$this->getDBO();
      $db->setQuery('SELECT * FROM #__usergroups WHERE id="'.$db->getEscaped($id).'" LIMIT 1;');
      return $db->loadObject();
    }    
    
    /**
     *  Funkce vracející uživatelské podkategorie ve vybrané kategorii
     */         
    public function getUserGroups($parentId,$showUsersCount=false){
      $db=$this->getDBO();
      $db->setQuery("SELECT *".($showUsersCount?',(SELECT COUNT(user_id) FROM #__user_usergroup_map WHERE group_id=#__usergroups.id) AS usersCount':'')." FROM #__usergroups WHERE parent_id='".$db->getEscaped($parentId)."' ORDER BY title;");
      return $db->loadObjectList();
    } 
    
    
    /**
     *  Funkce pro přidání uživatelské skupiny do DB - pod zadanou rodičovskou kategorii
     */         
    public function addUserGroup($parentId,$title){    
      $title=trim($title);
      if ($title==""){return false;} 
      $db=$this->getDBO();
      $db->setQuery("SELECT * FROM #__usergroups WHERE id=".$db->quote($parentId)." LIMIT 1;");
      $usergroup=$db->loadObject();
      $db->setQuery("SELECT * FROM #__usergroups WHERE parent_id=".$db->quote($parentId)." AND title=".$db->quote($title)." LIMIT 1;");
      $childgroup=$db->loadObject();
      //test, jestli už neexistuje na dane urovni kategorie se stejnym title
      if ((!$usergroup)||($childgroup)){  
        return false;
      }
      //pridani
      if (isset($usergroup->rgt)){
        //hranice noveho prvku
        $lft=$usergroup->rgt;
        //posuneme hranice u dalsich prvku...
        $db->setQuery("UPDATE #__usergroups SET lft=lft+2 WHERE rgt>'".$usergroup->rgt."';");
        $db->query();
        $db->setQuery("UPDATE #__usergroups SET rgt=rgt+2 WHERE rgt>='".$usergroup->rgt."';");
        $db->query();
        //vlozime novou skupinu
        $db->setQuery("INSERT INTO #__usergroups (parent_id,lft,rgt,title)VALUES('".$usergroup->id."','".$lft."','".($lft+1)."','".$db->getEscaped($title)."');");
        if ($db->query()){     
          return $db->insertid(); 
        }
      }
      return false;
    } 
    
    /**
     *  Funkce pro odmazání skupiny uživatelů
     */         
    public function deleteUserGroup($groupId){
      $db=$this->getDBO();
      $groupId=$db->getEscaped($groupId);
      //nacteme skupinu z DB
      $db->setQuery("SELECT * FROM #__usergroups WHERE id='".$parentId."' LIMIT 1;");
      $usergroup=$db->loadObject();
      if (isset($usergroup->lft)){
        //posuneme hranice
        $db->setQuery("UPDATE #__usergroups SET lft=lft-2 WHERE lft>'".$usergroup->lft."';");
        $db->query();
        $db->setQuery("UPDATE #__usergroups SET rgt=rgt-2 WHERE rgt>'".$usergroup->rgt."';");
        $db->query();
        //smazeme vazby na uzivatelske ucty
        $db->setQuery("DELETE FROM #__user_usergroup_map WHERE group_id='".$groupId."';");
        $db->query();
        //smazeme skupinu
        $db->setQuery("DELETE FROM #__usergroups WHERE id='".$groupId."' LIMIT 1;");
        $db->query();
      }
    } 
    
    /**
     *  Funkce pro přidání uživatelského účtu  
     */         
    public function addUser($jmeno,$username,$email,$mainGroupId){
      $db=$this->getDBO();
      $db->setQuery("SELECT * FROM #__users WHERE username='".$db->getEscaped($usename)."' OR email='".$db->getEscaped($email)."' LIMIT 1;");
      if ($usersRows=$db->loadObjectList()){
        //uživatel už existuje 
        foreach ($usersRows as $user){
        	//pridame uzivatele do hlavni skupiny
          $db->setQuery("SELECT * FROM #__user_usergroup_map WHERE user_id='".$user->id."' AND group_id='".$mainGroupId."' LIMIT 1;");
          if (!($db->loadObject())){
            $db->setQuery("INSERT INTO #__user_usergroup_map (user_id,group_id) VALUES ('".$user->id."','".$mainGroupId."');");
            $db->query();
          }
        }
      }else{
        //pripravime heslo
        jimport( 'joomla.user.helper' );
        $password=JUserHelper::getCryptedPassword($username);
        //můžeme vytvořit uživatele
        $db->setQuery("INSERT INTO #__users (name,username,email,password,registerDate) VALUES (
          '".$db->getEscaped($jmeno)."',
          '".$db->getEscaped($username)."',
          '".$db->getEscaped($email)."',
          '".$db->getEscaped($password)."',
          NOW()
        );");
        $db->query();
        $db->setQuery("SELECT id FROM #__users WHERE username='".$db->getEscaped($username)."' LIMIT 1;");
        if ($user=$db->loadObject()){
          $db->setQuery("INSERT INTO #__user_usergroup_map (user_id,group_id) VALUES ('".$user->id."','".$mainGroupId."');");
          $db->query();
        }
      }
    }
    
    
    /**
     *  Funkce pro přidání uživatelského účtu k dané skupině
     */         
    public function addUserIntoGroup($userId,$groupId){
      $db=$this->getDBO();
      $db->setQuery("SELECT * FROM #__user_usergroup_map WHERE user_id='".$userId."' AND group_id='".$groupId."' LIMIT 1;");
      if (!($db->loadObject())){
        //zatim neexistuje -> muzeme vazbu vytvorit
        $db->setQuery("INSERT INTO #__user_usergroup_map (user_id,group_id) VALUES ('".$userId."','".$groupId."');");
        $db->query();
      }
    }
    
    /**
     *  Funkce pro přidání uživatele do skupiny v závislosti na jménech
     */         
    public function addUserIntoGroupByNames($userName,$userEmail="",$groupTitle,$parentGroupId){ 
      $db=$this->getDBO();
      $db->setQuery("SELECT * FROM #__users WHERE username=".$db->quote($userName)." OR email=".$db->quote($userEmail)." LIMIT 1;");
      $user=$db->loadObject();
      $db->setQuery("SELECT * FROM #__usergroups WHERE title=".$db->quote($groupTitle).' AND parent_id='.$db->quote($parentGroupId)." LIMIT 1;");
      $group=$db->loadObject();
      if (($user)&&($group)){
        $this->addUserIntoGroup($user->id,$group->id);
      }
    } 
    
    /**
     *  Funkce pro odebrání uživatele ze skupiny
     */         
    public function removeUserFromGroup($userId,$groupId){
      $db=$this->getDBO();
      $db->setQuery("DELETE FROM #__user_usergroup_map WHERE user_id='".$db->getEscaped($userId)."' AND group_id='".$db->getEscaped($groupId)."';");
      $db->query();
    } 
    
    /**
     *  Funkce kontrolující, zda existuje skupina uživatelů, která je nastavena jako ROOT této komponenty
     */         
    public function checkRootGroupExists($groupId,$configModel){    
      $db=$this->getDBO();                                                                             //     exit("SELECT * FROM #__usergroups WHERE id=".$db->quote($groupId)." AND parent_id=1 LIMIT 1;");
      $db->setQuery("SELECT * FROM #__usergroups WHERE id=".$db->quote($groupId)." AND parent_id=1 LIMIT 1;");
      if (!$db->loadObject()){       
        //neexistuje -> musime skupinu vytvorit
        $index="";
        while (!($rootGroupId=$this->addUserGroup(1,"SEWEBAR".$index))){
          $index=intval($index)+1;
        }       
        //vytvoreno -> ulozime nastaveni
        $configModel->setConfig("ROOT_USERGROUP_ID",$rootGroupId);
        //pridame prava pro prihlaseni
        $assetRules=JAccess::getAssetRules(1);
        $assetRules->merge('{"core.login.site":{"'.$rootGroupId.'":1}}');
        //ulozime prava do DB
        $db->setQuery("UPDATE #__assets SET rules=".$db->quote((string)$assetRules)." WHERE id=1;");
        $db->query();
      }
    }
    
    /**
     *  Funkce vracející uživatele patřící do zadané kategorie
     *  @param $groupId - id kategorie, do které mají uživatelé patřit
     *  @param $ignoreUsersInSubkategories=false - pokud je true, budou vyloučeni uživatelé, kteří už patří do některé z podkategorií           
     */         
    public function usersInGroup($groupId,$ignoreUsersInSubkategories=false){
      $db=$this->getDBO();
      if ($ignoreUsersInSubkategories>0){
        $whereSql2="AND id NOT IN (SELECT user_id FROM #__user_usergroup_map JOIN #__usergroups ON #__user_usergroup_map.group_id=#__usergroups.id WHERE parent_id=".$db->quote($groupId).")";
      }else{
        $whereSql2='';
      }
      $db->setQuery("SELECT * FROM #__users WHERE id IN (SELECT user_id FROM #__user_usergroup_map WHERE group_id=".$db->quote($groupId).") ".$whereSql2." ORDER BY name;");
      return $db->loadObjectList(); 
    }
    
    /**
     *  Funkce vracející identifikaci skupiny, do které patří uživatel
     */         
    public function getUsersGroup($userId,$parentGroupId){
      $db=$this->getDBO();
      $db->setQuery("SELECT * FROM #__usergroups WHERE parent_id=".$db->quote($parentGroupId)." AND id IN (SELECT group_id FROM #__user_usergroup_map WHERE user_id=".$userId.") LIMIT 1;");
      return $db->loadObject();
    }
    
    public function removeUserFromGroupsChildren($parentGroupId){
      $db=$this->getDBO();
      $db->setQuery("DELETE FROM #__user_usergroup_map WHERE group_id IN (SELECT id FROM #__usergroups WHERE parent_id=".$db->quote($parentGroupId).")");
      $db->query();
    }
    
  }
?>
