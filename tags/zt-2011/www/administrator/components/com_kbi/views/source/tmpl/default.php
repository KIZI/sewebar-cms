<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
	<div class="col100">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Details' ); ?></legend>

			<table class="admintable">
			<tbody>
				<tr>
					<td width="20%" class="key">
						<label for="name">
							<?php echo JText::_( 'Name' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="name" id="name" size="50" value="<?php echo isset($this->row->name) ? htmlspecialchars($this->row->name) : '' ;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="type">
							<?php echo JText::_( 'Type' ); ?>:
						</label>
					</td>
					<td width="80%">
						<?php echo $this->lists['types']; ?>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="metod">
							<?php echo JText::_( 'Method' ); ?>:
						</label>
					</td>
					<td width="80%">
						<?php echo $this->lists['methods']; ?>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="name">
							<?php echo JText::_( 'URL' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="url" id="url" size="100" value="<?php echo isset($this->row->url) ? htmlspecialchars($this->row->url) : '' ;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="params">
							<?php echo JText::_( 'Custom Parameters' ); ?>:
						</label>
					</td>
					<td width="80%">
						<textarea class="inputbox" name="params" id="params" cols="80" rows="7" style="width:90%"><?php echo isset($this->row->params) ? htmlspecialchars($this->row->params, ENT_NOQUOTES) : '' ;?></textarea>
					</td>
				</tr>
				<?php if(isset($this->source) && ($this->source instanceof ISynchronable)):?>
				<tr>
					<td width="20%" class="key">
						<label for="documents">
							<?php echo JText::_( 'Documents' ); ?>:
						</label>
					</td>
					<td width="80%">
						<a href="<?php echo JRoute::_("index.php?option={$option}&controller=documents&id[]={$this->row->id}") ?>"><?php echo JText::_( 'Documents' ); ?></a>
					</td>
				</tr>
				<?php endif ?>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="controller" value="sources" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="id" value="<?php echo isset($this->row->id) ? $this->row->id : ''; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>