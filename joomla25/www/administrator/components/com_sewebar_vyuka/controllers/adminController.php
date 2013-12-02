<?php

jimport( 'joomla.application.component.controller' );
 
class adminController extends JController{
  var $document;
  
  /**
   *  akce zobrazující výchozí stránku administračního rozhraní této komponenty
   */     
  public function info(){     
    $view=&$this->getView('Info',$this->document->getType()); 
    $view->display();
  }
  
  /**
   *  Akce pro administraci skupin uživatelů
   */     
  public function usergroups(){    
    $configModel=& $this->getModel('Config','sewebarModel');
    $adminModel=& $this->getModel('Admin','sewebarModel');         
    $parentUserGroupId=$configModel->getConfig("PARENT_USERGROUP_ID");    
    $parentUserGroup=$adminModel->getUserGroup($parentUserGroupId);     
    if (!$parentUserGroup){                          
      //neexistuje nastavena hlavni kategorie - potrebujeme ji vybrat
      $this->setRedirect(JRoute::_("index.php?option=com_sewebar_vyuka&task=selectParentUserGroup",false));
      return;
    }else{
      $view=&$this->getView('Usergroups',$this->document->getType());
      $view->setModel($adminModel,true);   
      $view->assignRef("parentUserGroup",$parentUserGroup);
      $view->assign("parentUserGroupId",$parentUserGroupId);
      $view->setModel($configModel,false);
      $view->display();
    }
  } 
  
  /**
   *  Akce pro odstranění skupiny uživatelů
   */     
  public function deleteUserGroup(){
    $adminModel=& $this->getModel("Admin","sewebarModel");
    $groupId=JRequest::getInt('group');
    $userGroup=$adminModel->getUserGroup($groupId);
    if (!$userGroup){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    if ($_POST["potvrzeni"]=="ok"){
      $adminModel->deleteUserGroup($groupId);
      $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=usergroups',false));
    }else{
      //zobrazime vyzvu k potvrzeni
      $view=&$this->getView("DeleteUserGroup",$this->document->getType());
      $view->assign("userGroup",$userGroup);
      $view->display();
    }
  }
  
  /**
   *  Akce pro vypsání uživatelů v dané kategorii
   */     
  public function usersInGroup(){        
    $adminModel=&$this->getModel('Admin','sewebarModel');
    $groupId=JRequest::getInt('group');
    $userGroup=$adminModel->getUserGroup($groupId);
    if (!$userGroup){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    $view=&$this->getView("UsersInGroup",$this->document->getType());
    $view->assign('userGroup',$userGroup);
    $view->setModel($adminModel,true);
    $view->display();
  }
  
  /**
   *  Akce pro odstranění skupiny uživatelů
   */     
  public function removeUserFromGroup(){
    $adminModel=& $this->getModel("Admin","sewebarModel");
    $groupId=JRequest::getInt('group');
    $userId=JRequest::getInt('user');
    
    $adminModel->removeUserFromGroup($userId,$groupId);
    $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=usersInGroup&group='.$groupId,false));
  }  
  
  /**
   *  Akce pro přidání uživatelského účtu do vybrané skupiny
   */     
  public function addUserIntoGroup(){
    $adminModel=& $this->getModel("Admin","sewebarModel");
    $groupId=JRequest::getInt('group');
    $userGroup=$adminModel->getUserGroup($groupId);
    if (!$userGroup){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    $userId=JRequest::getInt('user');
    if ($userId>0){
      $user=$adminModel->getUser($userId);
    }
    if ($user){
      //máme skupinu i uživatele => přidáme ho
      $adminModel->addUserIntoGroup($userId,$groupId);
      $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=usergroups',false));  
    }else{
      //zobrazime vyber uzivatelu,ktere je mozne pridat
      $configModel=& $this->getModel("Config","sewebarModel");
      $view=& $this->getView("AddUsersIntoGroup",$this->document->getType());
      $view->setModel($adminModel,true);
      $view->assignRef("userGroup",$userGroup);
      $view->assign("parentUserGroupId",$configModel->getConfig("PARENT_USERGROUP_ID"));
      $view->display();
    }
  } 
  
  /**
   *  Akce pro vybrání hlavní skupiny uživatelů
   */     
  public function selectParentUserGroup(){        
    $adminModel=& $this->getModel("Admin","sewebarModel");
    $configModel=&$this->getModel("Config","sewebarModel");
    $rootGroupId=$configModel->getConfig("ROOT_USERGROUP_ID");
    
    //kontrola, jestli existuje root user group
    $adminModel->checkRootGroupExists($rootGroupId,$configModel);
    
    $groupId=JRequest::getInt("group");
    if ($groupId>0){
      $userGroup=$adminModel->getUserGroup($groupId);
    }
    
    $newGroupTitle=JRequest::getString("newGroupTitle","");
                               
    $parentGroupId=0;
    if ((@$userGroup)&&($userGroup->parent_id==$rootGroupId)){
      //mame vybranou platnou kategorii - nastavime ji
      $parentGroupId=$userGroup->id;            
    }elseif(($newGroupTitle!="")&&(@$_POST["potvrzeni"]=="ok")){
      $groupId=$adminModel->addUserGroup($rootGroupId,$newGroupTitle);
      if ($groupId){
        $parentGroupId=$groupId;
      }
    }
    //ulozime nastaveni
    if ($parentGroupId>0){                      
      $configModel->setConfig("PARENT_USERGROUP_ID",$parentGroupId);
      $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=usergroups',false));
      return; 
    }
  
    //zobrazime vyber z moznych kategorii
    $view=& $this->getView("SelectParentUserGroup",$this->document->getType());
    $view->setModel($adminModel,true);
    $view->assign("rootGroupId",$rootGroupId);
    $view->display();
  } 
  
  /**
   *  Funkce pro přidání uživatelských skupin 
   */     
  public function addUserGroups(){
    if ((trim(@$_POST["groups"])!="")&&(@$_POST["potvrzeni"]=="ok")){
      //potrebujeme vytvorit skupiny
      $groupsArr=explode(PHP_EOL,$_POST["groups"]);
      if (count($groupsArr)>0){
        $configModel=& $this->getModel('Config','sewebarModel');
        $adminModel=& $this->getModel('Admin','sewebarModel');         
        $parentUserGroupId=$configModel->getConfig("PARENT_USERGROUP_ID");    
        $parentUserGroup=$adminModel->getUserGroup($parentUserGroupId);
        if ($parentUserGroup){
          //máme nějaké skupiny => zkusíme je přidat
          foreach ($groupsArr as $groupTitle) {
          	$adminModel->addUserGroup($parentUserGroupId,$groupTitle);
          }
        } 
      }
    }
    $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=usergroups',false));
  }
  
  /**
   *  Akce pro administraci uživatelů aktuálně spravovaných touto komponentou
   */     
  public function users(){
    $configModel=& $this->getModel('Config','sewebarModel');
    $adminModel=& $this->getModel('Admin','sewebarModel');         
    $parentUserGroupId=$configModel->getConfig("PARENT_USERGROUP_ID");    
    $parentUserGroup=$adminModel->getUserGroup($parentUserGroupId);     
    if (!$parentUserGroup){                          
      //neexistuje nastavena hlavni kategorie - potrebujeme ji vybrat
      $this->setRedirect(JRoute::_("index.php?option=com_sewebar_vyuka&task=selectParentUserGroup",false));
      return;
    }else{
      $view=&$this->getView('Users',$this->document->getType());
      $view->setModel($adminModel,true);   
      $view->assignRef("parentUserGroup",$parentUserGroup);
      $view->display();
    }
  }
  
  
  /**
   *  Akce pro přidání uživatelských účtů do parent user group
   */     
  public function addUsers(){               
    $configModel=& $this->getModel('Config','sewebarModel');
    $adminModel=& $this->getModel('Admin','sewebarModel');         
    $parentUserGroupId=$configModel->getConfig("PARENT_USERGROUP_ID");    
    $parentUserGroup=$adminModel->getUserGroup($parentUserGroupId);     
    if (($parentUserGroup)&&(trim(@$_POST["users"])!="")){
      //máme zadaná data pro uživ. účty - pokusíme se projít jednotlivé řádky
      $rows=split(PHP_EOL,trim($_POST["users"]));
      if (count($rows)>0)
        foreach ($rows as $row) {
        	$rowArr=split(";",$row);
          if (count($rowArr)>=3){
            //mame zadany radek, ktery by mohl byt platny
            $name=trim($rowArr[0],'\'"');
            $username=trim($rowArr[1],'\'"');
            $email=trim($rowArr[2],'\'"');       
            if (($name!='')&&($username!='')&&(JMailHelper::isEmailAddress($email)===true)){
              //data jsou platna -> vytvorime uzivatele
              $adminModel->addUser($name,$username,$email,$parentUserGroupId);
            }
            if (trim(@$rowArr[3])!=""){
              //mame uzivatele zaroven pridat do skupiny
              $adminModel->addUserIntoGroupByNames($username,$email,trim($rowArr[3]),$parentUserGroupId);
            }
          }
        }
    } 
    $this->setRedirect(JRoute::_("index.php?option=com_sewebar_vyuka&task=users",false));
  }
  
    
  /**
   *  Akce pro vybrání skupiny pro uživatele
   */     
  public function selectUsersUserGroup(){
    $adminModel=& $this->getModel("Admin","sewebarModel");
    $configModel=& $this->getModel("Config","sewebarModel");
    $userId=JRequest::getInt('user');
    $user=$adminModel->getUser($userId);
    if (!$user){
      JError::raiseError(500,JText::_('FORBIDDEN'));
      return;
    }
    
    $parentUserGroup=$adminModel->getUserGroup($configModel->getConfig("PARENT_USERGROUP_ID"));
    if (!$parentUserGroup){
      $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=usergroups',false));
    }
    $groupId=JRequest::getInt('group');
    if ($groupId>0){
      $group=$adminModel->getUserGroup($groupId);
    }
    if ($group){
      //máme skupinu i uživatele => přidáme ho
      $adminModel->removeUserFromGroupsChildren($userId,$parentUserGroup->id);
      $adminModel->addUserIntoGroup($userId,$groupId);
      $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=users',false));  
    }else{
      //zobrazime vyber uzivatelu,ktere je mozne pridat
      $configModel=& $this->getModel("Config","sewebarModel");
      $view=& $this->getView("SelectUsersUserGroup",$this->document->getType());
      $view->setModel($adminModel,true);
      $view->assignRef("user",$user);    
      $view->assignRef("parentUserGroup",$parentUserGroup);
      $view->display();
    }
  }
  
  /**
   *  Akce pro odebrání dílčích podskupin, do kterých je uživatel zařazen
   */     
  public function removeUsersUserGroup(){
    $userId=JRequest::getInt("user");
    $adminModel=$this->getModel("Admin","sewebarModel");
    $configModel=$this->getModel("Config","sewebarModel");
    $adminModel->removeUserFromGroupsChildren($userId,$configModel->getConfig("PARENT_USERGROUP_ID"));
    $this->setRedirect(JRoute::_('index.php?option=com_sewebar_vyuka&task=users',false));
  }
  
  
  
  
  
  /**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{                                        
		parent::__construct( $default );
		$this->document =& JFactory::getDocument();
	}

}
?>
