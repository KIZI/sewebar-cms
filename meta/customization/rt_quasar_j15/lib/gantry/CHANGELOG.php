<?php
/**
 * RokNavMenu Plugin
 *
 * @package		gantry
 * @version		2.0.12 February 12, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
?>

1. Copyright and disclaimer
----------------


2. Changelog
------------
This is a non-exhaustive changelog for Gantry, inclusive of any alpha, beta, release candidate and final versions.

Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

------- 2.0.12 Release [12-February-2010] -------
# Fixed module collapsing

------- 2.0.11 Release [12-February-2010] -------
+ Added inherited menu item parameters
# Fixed forced layout positions. 

------- 2.0.10 Release [4-February-2010] -------
# Fixed gzipper to only work if direcctories are writeable 

------- 2.0.3 Release [10-January-2010] -------
# Fixed menuitem elemts display on no menu item defined
# Fixed ajax-save issue when template is not default and default template is not a gantry template
# Fixed menu item parameter preference to not be saved in cookie and session
+ Added setby location to parameters to determin where a parameter was set by.

------- 2.0.2 Release [05-January-2010] -------
^ Moved layouts to individual layout classes to allow override
# Fix to show sidebars when component not defined
^ Moved positions cache files to a flatfile db
+ Added check to make sure presets dont get saved in cookies or sessions
+ Added spinner for Apply/Save


------- 2.0.1 Release [02-January-2010] -------
# Fixed per Menu Item saving
# JS fixes for Menu item saving and IE
+ Added copy of language files to Joomla locations if not there

------- 2.0 Release [01-January-2010] -------
+ Per-Menu configuration
+ Custom presets
+ 16 column support added
+ RTL Support (right-to-left languages)
+ Diagnostic status
+ Built-in AJAX support
+ Built-in Gantry GZipper
+ Feature order
+ Component display toggle
+ Page Suffix feature
+ Menu-less pages feature

------- 1.2 Release [11-December-2009] -------
! Changelog Creation