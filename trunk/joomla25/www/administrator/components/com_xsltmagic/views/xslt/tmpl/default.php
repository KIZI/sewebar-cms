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
						<input class="inputbox" type="text" name="name" id="name" size="50" value="<?php echo $this->row->name;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="style">
							<?php echo JText::_( 'Style' ); ?>:
						</label>
					</td>
					<td width="80%">
						<textarea  name="code" id="code" cols="100" rows="30"><?php echo $this->row->style;?></textarea>
    				        <script>
                            var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
                            lineNumbers: true, 
                            mode: {name: "xml", htmlMode: true}
                            });
                            </script>
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