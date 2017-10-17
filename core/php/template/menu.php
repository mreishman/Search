<?php 

$URI = $_SERVER['REQUEST_URI'];

$menuItems = array();
$count = 0;
$modifier = "./";

if((strpos($URI, 'main.php') !== false) || (strpos($URI, 'about.php') !== false) || ((strpos($URI, 'whatsNew.php') !== false) || (strpos($URI, 'update.php') !== false) || (strpos($URI, 'changeLog.php') !== false)))
{
	$menuItems[$count] = array("title" => "Back" , "action" => "goToUrl('../index.php')");
	$count++;
}
else
{
	$modifier = "./settings/";
	$menuItems[$count] = array("title" => "New Grep" , "action" => "grep()");
	$count++;
}

if(strpos($URI, 'about.php') !== false)
{
	$menuItems[$count] = array("title" => "About" , "action" => "");
	$count++;
}
else
{
	$menuItems[$count] = array("title" => "About" , "action" => "window.location = '".$modifier."about.php'");
	$count++;
}

if(strpos($URI, 'main.php') !== false)
{
	$menuItems[$count] = array("title" => "Settings" , "action" => "");
	$count++;
}
else
{
	$menuItems[$count] = array("title" => "Settings" , "action" => "window.location = '".$modifier."main.php'");
	$count++;
}

if(((strpos($URI, 'whatsNew.php') !== false) || (strpos($URI, 'update.php') !== false) || (strpos($URI, 'changeLog.php') !== false)))
{
	$menuItems[$count] = array("title" => "Update" , "action" => "");
	$count++;
}
else
{
	$menuItems[$count] = array("title" => "Update" , "action" => "window.location = '".$modifier."update.php'");
	$count++;
}

?>
<div onclick="toggleMenu();" id="menuMain" style="width: 100%; height: 100%; background-color: rgba(0,0,0,.5); display: none; position: absolute; z-index: 20; padding-top: 65px;">
	<?php
	foreach ($menuItems as $menuItem => $value): ?>
		<a style="cursor: pointer" onclick="<?php echo $value['action'];?>">
		<div style="display: inline-block; width: 150px; height: 150px; margin: 75px; border: 1px solid white;">
			<?php echo $value['title'];?>
		</div>
		</a>
	<?php endforeach; ?>
</div>
<div style="z-index: 40; border-bottom: 1px dotted grey;" class="backgroundForMenus" id="menu">
	<table width="100%">
		<tr>
			<td width="33%">
				<a onclick="toggleMenu();" >Menu</a>
			</td>
			<td width="34%" style="text-align: center;">
				
			</td>
			<td width="33%">
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	function toggleMenu()
	{
		if(document.getElementById("menuMain").style.display === "block")
		{
			document.getElementById("menuMain").style.display = "none";
		}
		else
		{
			document.getElementById("menuMain").style.display = "block";
		}
	}
</script>