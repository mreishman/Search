<?php 
	$menuItems = array("New Grep" => "grep()", "About" => "window.location = './settings/about.php'", "Settings" => "window.location = './settings/main.php';", "Update" => "./settings/update.php");
?>
<div id="menuMain" style="width: 100%; height: 100%; background-color: rgba(0,0,0,.5); display: none; position: absolute; z-index: 20;">
	<?php
	foreach ($menuItems as $menuItem => $value): ?>
		<div style="display: inline-block; width: 150px; height: 150px; margin: 75px; border: 1px solid white;">
			<a onclick="<?php echo $value;?>"><?php echo $menuItem;?></a>
		</div>
	<?php endforeach; ?>
</div>