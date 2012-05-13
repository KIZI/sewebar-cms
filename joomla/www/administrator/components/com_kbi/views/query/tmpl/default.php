<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Details' ); ?></legend>

			<table class="admintable">
			<tbody>
				<tr>
					<td width="20%" class="key">
						<label>
							<?php echo JText::_( 'ID' ); ?>:
						</label>
					</td>
					<td width="80%">
						<span><?php echo isset($this->row->id) ? htmlspecialchars($this->row->id) : '' ;?></span>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="name">
							<?php echo JText::_( 'Name' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="name" id="name" size="50" value="<?php echo $this->row->name;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="delimiter">
							<?php echo JText::_( 'Parameter delimiter' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="delimiter" id="delimiter" size="50" value="<?php echo $this->row->delimiter;?>" />
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
		<?php if (!empty($this->ardesigner)) :?>
		<fieldset class="adminform">
			<legend><?php echo JText::_('Query Designer'); ?>:</legend>
			<table cellspacing="1" class="admintable">
				<?php /*<tr>
					<td valign="top" class="key">
						<span class="editlinktip hasTip" title="Query Designer::Opens query designer. Feature List, Data Dictionary Query and XSLTs of query must be saved."><?php echo JText::_('Open Designer'); ?></span>
					</td>
					<td valign="top">
						<?php echo $this->ardesigner; ?>
					</td>
				</tr>
				*/?>
				<tr>
					<td valign="top" class="key">
						<span class="editlinktip hasTip" title="Feature List::Features configuration for the designer."><?php echo JText::_('Feature List'); ?></span>
					</td>
					<td valign="top">
						<textarea class="text_area" name="featurelist" id="featurelist" cols="80" rows="20" style="width:90%"><?php echo $this->row->featurelist;?></textarea>
					</td>
				</tr>
				<?php /*
				<tr>
					<td valign="top" class="key">
						<span class="editlinktip hasTip" title="Data Dictionary::Data dictionary for the designer."><?php echo JText::_('Data Dictionary'); ?></span>
					</td>
					<td valign="top">
						<textarea class="text_area" name="dictionaryquery" id="dictionaryquery" cols="80" rows="20" style="width:90%"><?php echo $this->row->dictionaryquery;?></textarea>
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						<span class="editlinktip hasTip" title="..."><?php echo JText::_('Data Dictionary XSLT'); ?></span>
					</td>
					<td valign="top">
						<textarea class="text_area" name="dictionaryqueryxsl" id="dictionaryqueryxsl" cols="80" rows="20" style="width:90%"><?php echo $this->row->dictionaryqueryxsl;?></textarea>
					</td>
				</tr>
				*/?>
			</table>
		</fieldset>
		<?php endif ?>
	</div>
	<div class="col width-50">
		<fieldset class="adminform">
			<legend><?php echo JText::_('Query'); ?>:</legend>
			<table class="admintable" width="100%">
				<tr>
					<td valign="top" class="key">
						<span class="editlinktip hasTip" title="Query::The query itself with parameters."><?php echo JText::_('Query'); ?></span>
					</td>
					<td valign="top">
						<div><textarea class="text_area" style="width: 100%" name="query" id="query" cols="80" rows="20"><?php echo $this->row->query;?></textarea></div>
					</td>
				</tr>
				<tr>
					<td valign="top" class="key">
						<span class="editlinktip hasTip" title="Parameters XSLT::If non-empty, defines the transformation to be aplied on query before execution."><?php echo JText::_('XSLT'); ?></span>
					</td>
					<td valign="top">
						<div><textarea class="text_area" style="width: 100%" name="paramsxsl" id="paramsxsl" cols="80" rows="20"><?php echo $this->row->paramsxsl;?></textarea></div>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="controller" value="queries" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="id" value="<?php echo isset($this->row->id) ? $this->row->id : '' ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>