<?php
$paths = array('..', 'ardesigner');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SEWEBAR Dev Updater</title>
</head>
<body>
<?php foreach($paths as $path): ?>
	Updating <i><?php print realpath($path) ?></i>...<br>
	<?php print exec("svn update $path 2>&1") ?>
	<br /><br />
<?php endforeach; ?>
</body>
</html>