<?php
/**
 * @copyright  Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Example User Plugin
 *
 * @package    Joomla.Plugin
 * @subpackage  User.example
 * @since    1.5
 */
class plgUserLmcloud extends JPlugin
{
  /** @var string $lmUrl - adresa lmcloud serveru */
  private $lmUrl = '';
  private $adminPassword = '';

  // const LM_URL='http://connect-dev.lmcloud.vse.cz/SewebarConnectNext';

  public static function prepareKbi($lmUrl)
  {
    //TODO vazba na totožnou metodu v dbconnectModelConnections
    $configArr = array('type' => 'LISPMINER', 'name' => 'TEST', 'method' => 'POST', 'url' => $lmUrl);
    JLoader::import('KBIntegrator', JPATH_LIBRARIES . DS . 'kbi');
    return KBIntegrator::create($configArr);
  }

  /**
   *  Funkce pro zakódování hesla pro použití v rámci LMCloud serveru
   */
  public static function encodePassword($username, $password)
  {
    $str = substr($password, 0, 2) . $username . $password;
    return sha1($str);
  }

  /**
   * Example store user method
   *
   * Method is called after user data is stored in the database
   *
   * @param  array $user    Holds the new user data.
   * @param  boolean $isnew    True if a new user is stored.
   * @param  boolean $success  True if user was succesfully stored in the database.
   * @param  string $msg    Message.
   *
   * @return  void
   * @since  1.6
   * @throws  Exception on error.
   */
  public function onUserAfterSave($user, $isnew, $success, $msg)
  {
    if (!$success){return;}
    $app = JFactory::getApplication();

    // convert the user parameters passed to the event
    // to a format the external application
    $session =& JFactory::getSession();
    $userData = $session->get('user', array(), 'sewebar');

    $args = array();
    $args['username'] = $user['username'];
    $args['email'] = $user['email'];
    $args['fullname'] = $user['name'];
    $args['password'] = $user['password'];

    if (empty($user['password_clear'])){
      //pokud nedojde ke změně hesla, změnu normálně potvrdíme a ignorujeme
      return;
    }

    $username = $this->prepareUserName($user['id']);
    try {
      if ($isnew) {
        $kbi = self::prepareKbi($this->lmUrl);
        $kbi->registerUser($username, self::encodePassword($user['username'], $user['password_clear']), $user['email']);
      } else {
        $kbi = self::prepareKbi($this->lmUrl);
        if ($username == $userData['username']){
          //jde o toho samého uživatele
          $result=$kbi->updateUser($username, $userData['password'], $username, self::encodePassword($user['username'], $user['password_clear']), $user['email']);
          if ($result){
            $session =& JFactory::getSession();
            $session->set('user', array('username' => $username, 'password' => self::encodePassword($user['username'], $user['password_clear'])), 'sewebar');
          }
        } else {
          $confirmPasswordChangeLink=JRoute::_('index.php?option=com_dbconnect&controller=izi&task=confirmPasswordChange&user='.base64_encode($user['username']).'&code={code}',false);
          //měníme heslo jinému uživateli
          $kbi->updateOtherUser(
            $username,
            $username,
            self::encodePassword($user['username'],
              $user['password_clear']),
            $user['email'],
            $this->params->get('email_from'),
            $confirmPasswordChangeLink
          );
          JError::raiseNotice(JText::_('PASSWORD_CONFIRM_REQUEST_MAIL'));
        }
      }
    } catch (Exception $e) {
      JError::raiseWarning(JText::_('PASSWORD_CHANGE_FAILED'));
    }
  }

  /**
   * Example store user method
   *
   * Method is called after user data is deleted from the database
   *
   * @param  array $user  Holds the user data.
   * @param  boolean $succes  True if user was succesfully stored in the database.
   * @param  string $msg  Message.
   *
   * @return  void
   * @since  1.6
   */
  public function onUserAfterDelete($user, $succes, $msg)
  {
    $app = JFactory::getApplication();

    // only the $user['id'] exists and carries valid information

    //TODO odstranění všech úloh navázaných na konkrétního uživatele

    $kbi = self::prepareKbi($this->lmUrl);
    $session =& JFactory::getSession();
    $userData = $session->get('user', array(), 'sewebar');
    $kbi->deleteUser($userData['username'], $this->adminPassword);
  }

  /**
   * This method should handle any login logic and report back to the subject
   *
   * @param  array $user    Holds the user data.
   * @param  array $options  Extra options.
   *
   * @return  boolean  True on success
   * @since  1.5
   */
  public function onUserLogin($user, $options)
  {
    //načtení ID uživatele podle uživatelského jména 
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('id');
    $query->from('#__users');
    $query->where('username=' . $db->Quote($user['username']));
    $db->setQuery($query);
    $userObject = $db->loadObject();

    if ($userObject) {
      $username = $this->prepareUsername($userObject->id);
      //uložení přihlašovacích údajů do JSession    
      $session =& JFactory::getSession();
      $session->set('user', array('username' => $username, 'password' => self::encodePassword($user['username'], $user['password'])), 'sewebar');
      return true;
    }

    return false;
  }

  /**
   * Private funkce pro vytvoření uživatelského jména pro lmcloud server
   * @param $userId
   * @return string
   */
  private function prepareUserName($userId)
  {
    return $this->params->get('servername', '') . '_' . $userId;
  }

  /**
   * This method should handle any logout logic and report back to the subject
   *
   * @param  array $user  Holds the user data.
   *
   * @return  boolean  True on success
   * @since  1.5
   */
  public function onUserLogout($user)
  {

    $session =& JFactory::getSession();
    $session->clear('user', 'sewebar');

    return false;
  }

  public function __construct(&$subject, $config = array())
  {
    parent::__construct($subject, $config);
    $this->lmUrl = $this->params->get('lm_url');
    $this->adminPassword = $this->params->get('admin_password');
  }
}
