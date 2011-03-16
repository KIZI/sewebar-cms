<?php	
	$this->loadTemplate('tab');
?>
<form name = "adminForm" action="index2.php" method="post">
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="1">
		<!--<input type="hidden" name="cbInstalled" value="<?php echo $cbInstalled;?>">-->
		<input type="hidden" name="hidemainmenu" value="0" />
	</form>
	