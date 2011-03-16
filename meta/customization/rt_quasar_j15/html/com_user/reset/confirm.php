<?php
/**
 * @package   Quasar Template - RocketTheme
 * @version   @VERSION@ @BUILD_DATE@
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - @COPYRIGHT_YEAR@ RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Quasar Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="rt-joomla <?php echo $this->params->get('pageclass_sfx')?>">
	
	<div class="user">

		<h1 class="rt-pagetitle">
			<?php echo JText::_('Confirm your Account'); ?>
		</h1>

		<p>
			<?php echo JText::_('RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?>
		</p>

		<form action="<?php echo JRoute::_( 'index.php?option=com_user&task=confirmreset' ); ?>" method="post" class="josForm form-validate">
		<fieldset>
			<legend><?php echo JText::_('Confirm your Account'); ?></legend><br />
			
			<div>
				<label for="token" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TEXT'); ?>"><?php echo JText::_('Token'); ?>:</label>
				<input id="token" name="token" type="text" class="required" size="36" />
			</div>
			<br />
			<div class="readon">
				<button type="submit" class="button"><?php echo JText::_('Submit'); ?></button>
			</div>
			
		</fieldset>
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	
	</div>
</div>