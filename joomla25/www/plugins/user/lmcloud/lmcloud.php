<?php
/**
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Example User Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	User.example
 * @since		1.5
 */
class plgUserLmcloud extends JPlugin
{
  /**
   *  Funkce pro zakódování hesla pro použití v rámci LMCloud serveru
   */     
  public static function encodePassword($username,$password){
    $str=substr($password,0,2).$username.$password;
    return sha1($str);
  }

	/**
	 * Example store user method
	 *
	 * Method is called after user data is stored in the database
	 *
	 * @param	array		$user		Holds the new user data.
	 * @param	boolean		$isnew		True if a new user is stored.
	 * @param	boolean		$success	True if user was succesfully stored in the database.
	 * @param	string		$msg		Message.
	 *
	 * @return	void
	 * @since	1.6
	 * @throws	Exception on error.
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		$app = JFactory::getApplication();

		// convert the user parameters passed to the event
		// to a format the external application

		$args = array();
		$args['username']	= $user['username'];
		$args['email']		= $user['email'];
		$args['fullname']	= $user['name'];
		$args['password']	= $user['password'];

		if ($isnew) {
			// Call a function in the external app to create the user
			// ThirdPartyApp::createUser($user['id'], $args);
		}
		else {
			// Call a function in the external app to update the user
			// ThirdPartyApp::updateUser($user['id'], $args);
		}
	}

	/**
	 * Example store user method
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param	array		$user	Holds the user data.
	 * @param	boolean		$succes	True if user was succesfully stored in the database.
	 * @param	string		$msg	Message.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function onUserAfterDelete($user, $succes, $msg)
	{
		$app = JFactory::getApplication();

		// only the $user['id'] exists and carries valid information

		// Call a function in the external app to delete the user
		// ThirdPartyApp::deleteUser($user['id']);
	}

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param	array	$user		Holds the user data.
	 * @param	array	$options	Extra options.
	 *
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function onUserLogin($user, $options)
	{
    //načtení ID uživatele podle uživatelského jména 
    $db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('id');
		$query->from('#__users');
		$query->where('username=' . $db->Quote($user['username']));
    $db->setQuery($query);
		$userObject = $db->loadObject();
    
    if ($userObject){
      $username=$this->params->get('servername','').'_'.$userObject->id;
      //uložení přihlašovacích údajů do JSession    
      $session =& JFactory::getSession();
      $session->set('user',array('username'=>$username,'password'=>self::encodePassword($user['username'],$user['password'])),'sewebar');
      return true;
    }
    
    return false;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param	array	$user	Holds the user data.
	 *
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function onUserLogout($user){
		
    $session =& JFactory::getSession();
    $session->clear('user','sewebar');

		return false;
	}
}
