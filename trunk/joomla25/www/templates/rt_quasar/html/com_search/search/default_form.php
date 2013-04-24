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

// no direct access
defined('_JEXEC') or die;
$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();
?>

<form id="searchForm" class="search_result" action="<?php echo JRoute::_('index.php?option=com_search');?>" method="post">

<div>
	<fieldset class="word">
		<label for="search_searchword">
			<?php echo JText::_('COM_SEARCH_SEARCH_KEYWORD'); ?>
		</label>
		<div class="input-field-l"><input type="text" name="searchword" id="search_searchword" size="30" maxlength="<?php echo $upper_limit; ?>" value="<?php echo $this->escape($this->origkeyword); ?>" class="inputbox" /></div>
	</fieldset>

	<div class="searchintro<?php echo $this->params->get('pageclass_sfx'); ?>">
		<?php if (!empty($this->searchword)):?>
		<p><?php echo JText::plural('COM_SEARCH_SEARCH_KEYWORD_N_RESULTS', $this->total);?></p>
		<?php endif;?>
	</div>

	<fieldset class="phrases">
		<legend><?php echo JText::_('COM_SEARCH_FOR');?>
		</legend>
			<div class="phrases-box">
			<?php echo $this->lists['searchphrase']; ?>
			</div>
			<div class="ordering-box">
			<label for="ordering" class="ordering">
				<?php echo JText::_('COM_SEARCH_ORDERING');?>
			</label>
			<?php echo $this->lists['ordering'];?>
			</div>
	</fieldset>

	<?php if ($this->params->get('search_areas', 1)) : ?>
		<fieldset class="only">
		<legend><?php echo JText::_('COM_SEARCH_SEARCH_ONLY');?></legend>
		<?php foreach ($this->searchareas['search'] as $val => $txt) :
			$checked = is_array($this->searchareas['active']) && in_array($val, $this->searchareas['active']) ? 'checked="checked"' : '';
		?>
		<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area-<?php echo $val;?>" <?php echo $checked;?> />
			<label for="area-<?php echo $val;?>">
				<?php echo JText::_($txt); ?>
			</label>
		<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

<div class="readon-wrap1">
	<div class="readon1-l"></div>
	<a class="readon-main"><span class="readon1-m"><span class="readon1-r">
		<input type="submit" name="Search" onClick="this.form.submit()" class="button" value="<?php echo JText::_('COM_SEARCH_SEARCH');?>" />
		<input type="hidden" name="task" value="search" />
	</span></span></a>
	</div>

</div>

<div class="display_limit">
<?php if ($this->total > 0) : ?>

	<div class="form-limit">
		<label for="limit">
			<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>
		</label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<p class="counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</p>

<?php endif; ?>
</div>

</form>