<?php
jimport( 'joomla.application.component.controller' );

/**
 *  Controller pro zpřístupnění funkcí aplikace prostřednictvím GET/POST požadavků 
 */  
class UserController extends JController{
  var $document;

  /**
   *  Akce pro vytvoření nového článku
   */
  public function register(){
    $userId=JRequest::getInt('user',-1);
    if (!($userId>=0)){
      $user =& JFactory::getUser();
      $userId=$user->get('id');    
    }
    
  }
  
  /**
   *  Akce pro přihlášení uživatele
   */
  public function login(){
  
  }      

  /**
   *  Akce pro odhlášení uživatele   
   *  po odhlášení uživatele je uživatel přesměrován na view   
   */
  public function logout(){
    
  }      

  /**
   *  Konstruktor
   */     
  public function __construct( $default = array()){                                        
		parent::__construct( $default );
		$this->document =& JFactory::getDocument();
	}

}
?>
