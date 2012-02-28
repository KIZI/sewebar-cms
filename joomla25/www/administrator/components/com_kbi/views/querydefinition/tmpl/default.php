<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" id="oksSelectorForm" method="post" enctype="multipart/form-data" name="adminForm">
	<div class="col100">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Source and Tranformation' ); ?></legend>

			<table class="admintable">
			<tbody>
				<tr>
					<td valign="top" align="right" class="key">
						<label for="name">
							<?php echo JText::_( 'Name' ); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="name" id="name" size="50" value="<?php echo !empty($this->row) ? $this->row->name : ''?>" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" class="key">
						<label for="sources">
							<?php echo JText::_( 'Source' ); ?>:
						</label>
					</td>
					<td>
						<?php echo $this->lists['sources']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top" align="right" class="key">
						<label for="queries">
							<?php echo JText::_( 'Query' ); ?>:
						</label>
					</td>
					<td>
						<?php echo $this->lists['queries']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="xslt">
							<?php echo JText::_( 'XSLT' ); ?>:
						</label>
					</td>
					<td >
						<?php echo $this->lists['xslt']; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="icon">
							<?php echo JText::_( 'Icon URL' ); ?>:
						</label>
					</td>
					<td >
						<input class="inputbox" type="text" name="icon" id="icon" size="50" value="<?php echo !empty($this->row) ? $this->row->icon : ''?>" />
					</td>
				</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="controller" value="querydefinitions" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="id" value="<?php echo isset($this->row->id) ? $this->row->id : '' ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>