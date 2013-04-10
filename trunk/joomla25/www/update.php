<?php
require_once dirname(__FILE__) . '/Updater.php';

$paths = array('.', '../../tools/IZI Miner/');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SEWEBAR Dev Updater</title>
</head>
<body>
<?php foreach($paths as $path): ?>
    <?php $updater = new Updater($path); ?>
	Updating <i><?php print realpath($path) ?></i>...<br>
	<?php print $updater->update() ?>
	<br /><hr /><br />
<?php endforeach; ?>
</body>
</html>