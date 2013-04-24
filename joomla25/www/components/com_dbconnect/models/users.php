<?php
  
  jimport('joomla.application.component.model');
  
  class dbconnectModelUsers extends JModel{
    
    /**
     *  Funkce pro zaregistrování uživatele
     */         
    public function registerUser($name,$username,$password,$email){
      require_once JPATH_ROOT.DS.'components'.DS.'com_users'.DS.'models'.DS.'registration.php';
      require_once JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'application'.DS.'component'.DS.'helper.php';
      $usersModelRegistration = new UsersModelRegistration();
      jimport('joomla.mail.helper');
      jimport('joomla.user.helper');
      
      $data = array( 'username' => $username,
                     'name' => $name,
                     'email1' => $email,
                     'password1' => $password, // First password field
                     'password2' => $password, // Confirm password field
                     'block' => 0,
                     'sendEmail'=>0,
                     'activation'=>0 );
      $usersModelRegistration->register($data);
    }
    
    /**
     *  Funkce pro kontrolu, jestli je možné použít e-mail k registraci
     */         
    public function checkEmail($email,$ignoreUserId=0){
      if(!preg_match('/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+$/',$email)){
        return false;
      } 
      $db=$this->getDBO();
      $db->setQuery('SELECT id FROM #__users WHERE email='.$db->quote($email).(($ignoreUserId>0)?' AND id!='.$db->quote($ignoreUserId):'').' LIMIT 1;');
      if ($db->loadRow()){
        return false;
      }else{
        return true;
      }
    }
    
    /**
     *  Funkce pro kontrolu, jestli je možné použít zvolené uživatelské jméno pro registraci
     */
    public function checkUsername($username,$ignoreUserId=0){
      $db=$this->getDBO();
      $db->setQuery('SELECT id FROM #__users WHERE username='.$db->quote($username).' AND id!='.$db->quote($ignoreUserId).' LIMIT 1;');
      if ($db->loadRow()){
        return false;
      }else{
        return true;
      }
    }          
    
    
  }
  
?>