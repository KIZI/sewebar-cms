<?php defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'LM Server' ); ?></legend>

			<table class="admintable">
			<tbody>
				<tr>
					<td width="20%" class="key">
						<label>
							<?php echo JText::_( 'Name' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" disabled="disabled" readonly="readonly" size="50" value="<?php echo $this->row->name;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label>
							<?php echo JText::_( 'URL' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" disabled="disabled" readonly="readonly" size="50" value="<?php echo $this->row->url;?>" />
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Configuration' ); ?></legend>

			<table class="admintable">
			<tbody>
				<tr>
					<td width="20%" class="key">
						<label for="name">
							<?php echo JText::_( 'Name' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="name" id="name" size="50" value="New LM Miner" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="name">
							<?php echo JText::_( 'Name' ); ?>:
						</label>
					</td>
					<td width="80%">
						<input class="inputbox" type="text" name="name" id="name" disabled="disabled" readonly="readonly" size="50" value="<?php echo $this->row->name;?>" />
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="type">
							<?php echo JText::_( 'DB Type' ); ?>:
						</label>
					</td>
					<td width="80%">
						<?php echo $this->lists['types']; ?>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="url">
							<?php echo JText::_( 'DB conf (JSON)' ); ?>:
						</label>
					</td>
					<td width="80%">
						<textarea class="text_area" name="db" id="db" cols="80" rows="7" style="width:90%"><?php echo json_encode(array(
							'server' => 'localhost',
							'database' => 'barbora',
							'username' => 'lisp',
							'password' => 'lisp'
						))?></textarea>
					</td>
				</tr>
				<tr>
					<td width="20%" class="key">
						<label for="url">
							<?php echo JText::_( 'Data Dictionary' ); ?>:
						</label>
					</td>
					<td width="80%">
						<textarea class="text_area" name="dataDictionary" id="dataDictionary" cols="80" rows="30" style="width:90%"><?php echo file_get_contents('/Volumes/Data/svn/sewebar/trunk/joomla/www/components/com_arbuilder/assets/barboraForLMImport.pmml');?></textarea>
					</td>
				</tr>
			</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="controller" value="registerlmserver" />
	<input type="hidden" name="option" value="com_kbi" />
	<input type="hidden" name="id" value="<?php echo isset($this->row->id) ? $this->row->id : '' ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>