<?php

	$this->loadTemplate('tab');

?>

	<form name = "adminForm" action="index2.php" method="post">
		<table>
			<tr>
				<th><?php echo JText::_("SETTING");?></th>
				<th><?php echo JText::_("VALUE");?></th>
				<th><?php echo JText::_("SETTINGSTOOLTIP");?></th>
			</tr>

			<?php	foreach($this->data as $key => $val):

					?>
			<tr>
				<td><?php echo $key;?></td>
				<td><?php echo $val;?></td>
			</tr>
					<?php
					endforeach;
				?>

		</table>
		<table>
			<tr>
				<th><?php echo JText::_("INDEXINFORMATION");?></th>
				<th><?php echo JText::_("Value");?></th>
				<th><?php echo JText::_("Here will be the tooltip row.");?></th>
			</tr>

			<?php	foreach($this->data as $key => $val):

					?>
			<tr>
				<td><?php echo $key;?></td>
				<td><?php echo $val;?></td>
			</tr>
					<?php
					endforeach;
				?>

		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="1">
		<!--<input type="hidden" name="cbInstalled" value="<?php //echo $cbInstalled;?>">-->
		<input type="hidden" name="hidemainmenu" value="0" />
	</form>