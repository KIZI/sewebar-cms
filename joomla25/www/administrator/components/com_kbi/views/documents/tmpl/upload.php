<form action="index.php" method="post" name="adminForm" enctype="multipart/form-data" >
	
	<fieldset>
		<legend>Upload PMML document</legend>
		<label for="document">PMML</label>
		<input type="file" name="document" id="document" />
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
