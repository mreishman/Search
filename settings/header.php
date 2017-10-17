<?php
require_once('../setup/setupProcessFile.php');
$URI = $_SERVER['REQUEST_URI'];
require_once("../core/php/customCSS.php");
echo loadSentryData($sendCrashInfoJS); ?>
<script src="../core/js/settings.js?v=<?php echo $cssVersion?>"></script>


<?php require_once("../core/php/template/menu.php"); ?>

<?php if(strpos($URI, 'main.php') !== false): ?>
	<div id="menu2">
		<a id="mainSettingsMenu2" onclick="goToUrl('#settingsMainVars');" class="active" > Main Settings </a>
		<a id="watchListSettingsMenu2" onclick="goToUrl('#settingsMainWatch');" > WatchList </a>
		<a id="menuSettingsMenu2" onclick="goToUrl('#settingsMenuVars');" > Menu Settings </a>
	</div>
<?php endif; ?>
<?php if((strpos($URI, 'whatsNew.php') !== false) || (strpos($URI, 'update.php') !== false) || (strpos($URI, 'changeLog.php') !== false)): ?>
	<div id="menu2">
		<a <?php if(strpos($URI, 'update.php') !== false): ?> class='active' <?php else: ?>  onclick="goToUrl('./update.php');"  <?php endif;?> > Update </a>
		<!-- 
		<a <?php if(strpos($URI, 'whatsNew.php') !== false): ?> class='active' <?php else: ?>  onclick="goToUrl('./whatsNew.php');"  <?php endif;?> > What's New? </a>
		-->
		<a <?php if(strpos($URI, 'changeLog.php') !== false): ?> class='active' <?php else: ?>  onclick="goToUrl('./changeLog.php');"  <?php endif;?> > Changelog </a>
	</div>
<?php endif; 

$baseUrlImages = "../core/";
if(file_exists('../local/layout.php'))
{
	$baseUrlImages = "../local/";
	//there is custom information, use this
	require_once('../local/layout.php');
	$baseUrlImages .= $currentSelectedTheme."/";
}

?>

<script type="text/javascript">
	
	var baseUrl = "<?php echo $baseUrlImages;?>";

</script>
