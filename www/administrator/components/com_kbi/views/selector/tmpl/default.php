<?php defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" id="oksSelectorForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<div style="float: right">
			<button type="button" onclick="KbiManager.onOk();window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('Insert') ?></button>
			<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo JText::_('Cancel') ?></button>
		</div>
	</fieldset>
	
	<div class="col100">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'Source and Tranformation' ); ?></legend>

			<table class="admintable">
			<tbody>
				<tr>
					<td class="key">
						<?php echo JText::_( 'Is dynamic' ); ?>:
					</td>
					<td>
						<?php echo $this->lists['dynamic']; ?>
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
						<label for="parameters">
							<?php echo JText::_( 'Query Parameters' ); ?>:
						</label>
					</td>
					<td >
						<textarea class="inputbox" name="parameters" id="parameters" cols="50" rows="5"></textarea>
					</td>
				</tr>
				<tr id="parameter_raw">
					<td class="key">
						<?php echo JText::_( 'Query Parameters (RAW)' ); ?>:
					</td>
					<td >
						<div class="text"></div>
					</td>
				</tr>
				<?php if (!empty($this->ardesigner)) :?>
				<tr>
					<td class="key">
						&nbsp;
					</td>
					<td >
						<?php echo $this->ardesigner; ?>
					</td>
				</tr>
				<?php endif ?>
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
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_( 'form.token' ); ?>
</form>