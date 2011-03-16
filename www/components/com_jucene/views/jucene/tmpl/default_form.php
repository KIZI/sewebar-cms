<?php defined('_JEXEC') or die('Restricted access'); ?>

<form id="juceneSearchForm" action="<?php echo JRoute::_( 'index.php?option=com_jucene' );?>" method="post" name="jucene">
	<table class="contentpaneopen<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
		<tr>
			<td nowrap="nowrap">
				<label for="search_searchword">
					<?php echo JText::_( 'QUERY' ); ?>:
				</label>
			</td>
			<td nowrap="nowrap">
				<textarea name="searchword" id="search_searchword" class="inputbox ui-autocomplete" ><?php echo trim(str_replace("-",":",$this->query)); ?></textarea>
			</td>
			<td width="100%" nowrap="nowrap">
				<button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_( 'Search' );?></button>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<label for="ordering">
					<?php echo JText::_( 'Ordering' );?>:
				</label>
				<?php echo $this->lists['ordering'];?>
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<label for="sorting">
					<?php echo JText::_( 'Sorting' );?>:
				</label>
				<?php echo $this->lists['sorting'];?>
			</td>
		</tr>
	</table>
	
	<table class="searchintro<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
	<tr>
		<td colspan="3" >
			<br />
			<?php echo JText::_( 'JUCENEQUERY' ) .' <b>'. $this->escape(str_replace("-",":",$this->query)) .'</b>'; ?>
		</td>
	</tr>
	<tr>
		<td>
			<br />
			<?php echo $this->result.", ".(time()-$_SESSION['jucene_timer'])." s"; ?>
		</td>
	</tr>
</table>

<br />
<?php if($this->total > 0) : ?>
<div align="center">
	<div style="float: right;">
		<label for="limit">
			<?php echo JText::_( 'Display Num' ); ?>
		</label>
		<?php echo $this->pagination->getLimitBox( ); ?>
	</div>
	<div>
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>
</div>
<?php endif; ?>

<input type="hidden" name="task"   value="search" />
</form>
