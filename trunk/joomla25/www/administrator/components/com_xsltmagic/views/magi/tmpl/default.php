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
							<?php echo JText::_( 'Name' ); 
      
              ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="name" id="name" size="50" value="<?php echo $this->row->name;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="style">
							<?php echo JText::_( 'Rule' ); ?>:
						</label>
					</td>
					<td width="80%">
						<textarea class="inputbox" name="rule" id="rule" cols="40" rows="5"><?php echo $this->row->rule;?></textarea>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="style">
							<?php echo JText::_( 'Source' ); ?>
						</label>
					</td>
					<td width="80%">
                        <select name="source" id="source">
    					   <?php  echo $this->_xslts->createFileList('',-1,$this->row->source); ?>
                        </select>
                    </td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="controller" value="magic" />
	<input type="hidden" name="modified" value="<?php echo date('Y-m-d H:i:s'); ?>" />
  <input type="hidden" name="option" value="com_xsltmagic" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>