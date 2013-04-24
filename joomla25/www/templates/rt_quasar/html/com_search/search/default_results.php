<?php
/**
 * @package   Refraction Template - RocketTheme
 * @version   1.0 September 5, 2011
 * @author    RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2011 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Rockettheme Refraction Template uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
// no direct access
defined('_JEXEC') or die;
?>

<dl class="search-results<?php echo $this->pageclass_sfx; ?>">
<ol class="list">
<?php foreach($this->results as $result) : ?>
	<li>
	<div class="result-title">
		<?php if ($result->href) :?>
			<h4>
				<a href="<?php echo JRoute::_($result->href); ?>"<?php if ($result->browsernav == 1) :?> target="_blank"<?php endif;?>>
				<?php echo $this->escape($result->title);?></a>
			</h4>
		<?php else:?>
			<?php echo $this->escape($result->title);?>
		<?php endif; ?>
	</div>
	<?php if ($result->section) : ?>
		<p><?php echo JText::_('Category') ?>:
			<span class="small">
				(<?php echo $this->escape($result->section); ?>)
			</span>
		</p>
	<?php endif; ?>
	<div class="description">
		<?php echo $result->text; ?>
	</div>
	<?php if ($this->params->get('show_date')) : ?>
		<div class="result-created">
			<?php echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created); ?>
		</div>
	<?php endif; ?>
	</li>
<?php endforeach; ?>
</ol>
</dl>

<div class="pagination">
	<?php echo $this->pagination->getPagesLinks(); ?>
</div>
