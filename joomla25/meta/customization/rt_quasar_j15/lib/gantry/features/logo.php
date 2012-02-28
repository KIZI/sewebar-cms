<?php
/**
 * @package     gantry
 * @subpackage  features
 * @version		2.0.12 February 12, 2010
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeaturelogo extends GantryFeature {
	var $_feature_name = 'logo';
    
	function render($position="") {
	    ob_start();
	    ?>
			<div class="rt-block">
    	    	<a href="<?php echo JURI::base(); ?>" id="rt-logo"></a>
    		</div>
	    <?php
	    return ob_get_clean();
	}
}