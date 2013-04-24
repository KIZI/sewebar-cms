<?php
/**
 * @package Quasar Template - RocketTheme
 * @version 1.0 September 11, 2011
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2010 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

require_once(JPATH_LIBRARIES.'/gantry/gantry.php');
$gantry->init();
gantry_import('core.utilities.gantryjformfieldaccessor');

?>
<div class="reset-complete<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=reset.complete'); ?>" method="post" class="form-validate">

		<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
		<p><?php echo JText::_($fieldset->label); ?></p>		<fieldset>
			<dl>
			<?php
				foreach ($this->form->getFieldset($fieldset->name) as $name => $field):
			    $fa = new GantryJFormFieldAccessor($field);
			    if ($fa->getType() == "password") $fa->addClass('inputbox');
			?>
				<dt><?php echo $field->label; ?></dt>
				<dd><?php echo $field->input; ?></dd>
			<?php endforeach; ?>
			</dl>
		</fieldset>
		<?php endforeach; ?>
		
		<div>
			<div class="readon"><button type="submit" class="button"><?php echo JText::_('JSUBMIT'); ?></button></div>
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>