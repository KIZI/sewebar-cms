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
    $username=@$_POST['username'];
    $password=@$_POST['password'];
    $errorMessage='';
    
    if (($username!='')&&($password!='')){
      //máme se pokusit o přihlášení
      $app = JFactory::getApplication();
      if ($app->login(array('username'=>$username,'password'=>$password),array('silent'=>true))){
        $view=&$this->getView('IziLoginOK',$this->document->getType());
        $view->assign('title',JText::_('SUCCESSFULLY_LOGGED_IN'));
        $text=JText::_('SUCCESSFULLY_LOGGED_IN_TEXT');
        $text=str_replace('{:username}',$username,$text);
        $view->assign('text',$text);
        $view->display();
        return;
      }else{
        $errorMessage=JText::_('LOGIN_ERROR');
      }
    }
    
    $view=&$this->getView('IziLogin',$this->document->getType());
    $view->assign('username',$username);
    $view->assign('errorMessage',$errorMessage);
    $view->display();
  }      

  /**
   *  Akce pro odhlášení uživatele   
   *  po odhlášení uživatele je uživatel přesměrován na view   
   */
  public function logout(){
    $app = JFactory::getApplication();
    if ($app->logout()){
      $view=&$this->getView('IziLoginOK',$this->document->getType());
      $view->assign('title',JText::_('SUCCESSFULLY_LOGGED_OUT'));
      $text=JText::_('SUCCESSFULLY_LOGGED_OUT_TEXT');
      $view->assign('text',$text);
      $view->display();
    }else{
      $view=&$this->getView('IziError',$this->document->getType(),'iziView');
      $view->assign('title',JText::_('ERROR'));
      $view->assign('text',JText::_('LOGOUT_ERROR_TEXT'));
      $view->display();
    }
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
