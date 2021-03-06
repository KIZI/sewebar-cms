<?php
/**
 * @package		Gantry Template Framework - RocketTheme
 * @version		@VERSION@ @BUILD_DATE@
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - @COPYRIGHT_YEAR@ RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureBtab extends GantryFeature {
    var $_feature_name = 'btab';

	function render($position="") {
	    ob_start();
	    ?>
		<div class="clear"></div>
		<div class="rt-block">
			<div class="bottomtab"><span class="bottomtab2"><?php echo $this->get('text'); ?></span></div>
		</div>
		<?php
	    return ob_get_clean();
	}
}