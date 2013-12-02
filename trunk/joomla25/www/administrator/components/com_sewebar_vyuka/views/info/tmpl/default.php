<?php 
/**
* @package helloworld02
* @version 1.1
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


defined('_JEXEC') or die('Restricted access');

  echo '<h1>'.JText::_('SEWEBAR_ADMINISTRATION').'</h1>';
  
  echo '<p>Pomocí této komponenty lze administrovat skupiny a uživatelské účty pro výuku...</p>';
  
  echo '<ul>
          <li><a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=usergroups').'">Administrace skupin uživatelů</a></li>
          <li><a href="'.JRoute::_('index.php?option=com_sewebar_vyuka&task=users').'">Administrace uživatelů</a></li>
        </ul>';
  
?>