<?php	
	require_once('default_tab.php');	
?>

	<form name = "adminForm" action="index2.php" method="post">
		<table>
			<tr>
				<td><?php echo JText::_("Setting");?></td>
				<td><?php echo JText::_("Value");?></td>
				<td><?php echo JText::_("Here will be the tooltip row.");?></td>
			</tr>
			
			
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="1">
		<input type="hidden" name="hidemainmenu" value="0" />		
	</form>