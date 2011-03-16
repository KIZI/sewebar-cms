<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" >	
	<fieldset class="adminform">
			<legend><?php echo JText::_( 'Upload PMML document' ); ?></legend>
	
			<table class="admintable">
			<tbody>
				<tr>
					<td width="20%" class="key">
						<label for="document">
							<?php echo JText::_( 'PMML' ); ?>:
						</label>
					</td>
					<td width="80%">
						<textarea name="document" id="document" rows="100" cols="80"><?php print $this->document ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	
	<input type="hidden" name="controller" value="documents" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->source->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php //echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php //echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
