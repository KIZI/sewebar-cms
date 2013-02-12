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
    $usersModel=&$this->getModel('Users','dbconnectModel');
    
    $name=trim(JRequest::getString('name'));          
    $username=trim(JRequest::getString('username'));
    $password=JRequest::getString('password');
    $password2=JRequest::getString('password2');
    $email=trim(JRequest::getString('email'));
    $sent=JRequest::getVar('sent','no');
        
    $errors='';
    if ($sent=='ok'){     
      if (strlen($username)<5){
        $errors.='<li>'.JText::_('MINIMAL_USERNAME_LENGTH').'</li>';
      }elseif (!($usersModel->checkUsername($username))){
        $errors.='<li>'.JText::_('REGISTRATION_USERNAME_ERROR').'</li>';
      }
      if (!($usersModel->checkEmail($email))){
        $errors.='<li>'.JText::_('REGISTRATION_EMAIL_ERROR').'</li>';
      }
      if (strlen($password)<5){
        $errors.='<li>'.JText::_('MINIMAL_PASSWORD_LENGTH').'</li>';
      }
      if ($password!=$password2){
        $errors.='<li>'.JText::_('PASSWORDS_ARE_DIFFERENT').'</li>';
      }
      if ($errors!=''){
        $errors='<ul>'.$errors.'</ul>';
      }else{
        //nemáme žádné chyby => zaregistrujeme uživatele
        $usersModel->registerUser($name,$username,$password,$email);
        $view=&$this->getView('IziInfo',$this->document->getType(),'iziView');
        $view->assign('title',JText::_('USER_ACCOUNT_CREATED'));
        $view->assign('info',JText::_('USER_ACCOUNT_CREATED_INFO'));
        $view->link=JRoute::_('index.php?option=com_dbconnect&controller=user&task=login&tmpl=component');
        $view->display();
        return;
      }
    }
    
    $view=&$this->getView('IziRegister',$this->document->getType());
    $view->assign('username',$username);
    $view->assign('email',$email);
    $view->assign('name',$name);
    $view->assign('errorMessage',$errors);
    $view->display();
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
