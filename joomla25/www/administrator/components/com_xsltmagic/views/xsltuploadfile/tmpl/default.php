<?php defined('_JEXEC') or die('Restricted access'); ?>

<form  enctype="multipart/form-data" action="index.php" method="post" name="adminForm">
	<div class="col100">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Details' ); ?></legend>
	
			<table class="admintable">
			<tbody>
				<tr>
					<td width="20%" class="key">
						<label for="name">


							<?php 

echo JText::_( 'Choose a file to upload: ' ); ?>
						</label>
					</td>
					<td width="80%">

<input name="uploadedfile" type="file" /><br />

					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="controller" value="xslts" />
	<input type="hidden" name="option" value="com_xsltmagic" />
	<input type="hidden" name="prevName" value="<?php echo $this->row->name; ?>" />
  <input type="hidden" name="url" value="<?php echo $this->row->url;; ?>" />
	<input type="hidden" name="jump" value="<?php echo $this->row->jump; ?>" />
  <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>