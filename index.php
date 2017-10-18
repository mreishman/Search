<?php
require_once('core/php/commonFunctions.php');

$baseUrl = "core/";
if(file_exists('local/layout.php'))
{
	$baseUrl = "local/";
	//there is custom information, use this
	require_once('local/layout.php');
	$baseUrl .= $currentSelectedTheme."/";
}
if(!file_exists($baseUrl.'conf/config.php'))
{
	$partOfUrl = clean_url($_SERVER['REQUEST_URI']);
	$url = "http://" . $_SERVER['HTTP_HOST'] .$partOfUrl ."setup/welcome.php";
	header('Location: ' . $url, true, 302);
	exit();
}
require_once($baseUrl.'conf/config.php');
require_once('core/conf/config.php');
require_once('core/php/configStatic.php');
require_once('core/php/loadVars.php');
require_once('core/php/updateCheck.php');

$daysSince = calcuateDaysSince($configStatic['lastCheck']);

if($pollingRateType == 'Seconds')
{
	$pollingRate *= 1000;
}
if($backgroundPollingRateType == 'Seconds')
{
	$backgroundPollingRate *= 1000;
}

$locationForStatusIndex = "";
if($locationForStatus != "")
{
	$locationForStatusIndex = $locationForStatus;
}
elseif (is_dir("../status"))
{
	$locationForStatusIndex = "../status/";
}
elseif (is_dir("../Status"))
{
	$locationForStatusIndex = "../Status/";
}

$locationForMonitorIndex = "";
if($locationForMonitor != "")
{
	$locationForMonitorIndex = $locationForMonitor;
}
elseif (is_dir("../monitor"))
{
	$locationForMonitorIndex = "../monitor/";
}
elseif (is_dir("../Monitor"))
{
	$locationForMonitorIndex = "../Monitor/";
}

?>
<!doctype html>
<head>
	<title>Search | Index</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>img/favicon.png" />
	<script src="core/js/jquery.js"></script>
	<?php
		echo loadSentryData($sendCrashInfoJS);
	?>
</head>
<body>
	<?php require_once("core/php/customCSS.php");
	if($enablePollTimeLogging != "false"): ?>
		<div id="loggTimerPollStyle" style="width: 100%;background-color: black;text-align: center; line-height: 200%;" ><span id="loggingTimerPollRate" >### MS /<?php echo $pollingRate; ?> MS</span> | <span id="loggSkipCount" >0</span>/<?php echo $pollForceTrue; ?> | <span id="loggAllCount" >0</span>/<?php echo $pollRefreshAll; ?></div>
	<?php endif; 
	require_once('core/php/template/menu.php');?>
	
	<div style="z-index: 5;" id="main">

		<div style="height: 100px;">
		</div>
		<?php
		$showNewSearch = false; 
		if(is_dir("savedSearches/")):
			$dir = "savedSearches/";
			$dir = array_diff(scandir($dir), array('..', '.'));
			if(count($dir) > 0):
				//show saved stuff
			else:
				$showNewSearch = true;
			endif;
		else:
			$showNewSearch = true;
		endif;
		?>
		<div style="display: <?php if($showNewSearch){ echo "none" }else{ echo "block" }?> " id='newSearch' onclick='showGrepPopup();'>New Search</div>
	</div>

	<div id="storage">
		<div class="container">
			<div style="width: 80%; margin-left: 10%; background-color: white; min-height: 200px; padding: 5px; margin-bottom: 40px;border: 1px solid black; " id="{{id}}" class="scanBar">
				<div>
					<progress style="color: white; background: #000000; width: 100%;" id="{{id}}Progress" value="0" max="1"></progress>
				</div>
				<div style="color: black; width: 100%; text-align: left;" id="{{id}}Title">
					<h3><span id="{{id}}Folder">{{folder}}</span> - "<span id="{{id}}Search">{{search}}</span>" <span id="{{id}}ProgressTxt" >--</span>%<div style="float: right;"><img onclick="deleteSearch({{id}});" src="core/img/trashCan2.png" style="width: 25px; height: 25px; margin-top: -4px; cursor: pointer;"></div></h3>
				</div>
				<div id="{{id}}FoundThings" style="background-color: grey; height: 400px; border: 1px solid black; margin-top: 10px; overflow-y: scroll;">
				</div>
			</div>
		</div>
	</div>

	<form id="settingsInstallUpdate" action="update/updater.php" method="post" style="display: none"></form>
	<script>

		<?php
		echo "var autoCheckUpdate = ".$autoCheckUpdate.";";
		echo "var dateOfLastUpdate = '".$configStatic['lastCheck']."';";
		echo "var daysSinceLastCheck = '".$daysSince."';";
		echo "var daysSetToUpdate = '".$autoCheckDaysUpdate."';";
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var enableLogging = "<?php echo $enableLogging; ?>";
		var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray); ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var baseUrl = "<?php echo $baseUrl;?>";

	</script>
	<?php readfile('core/html/popup.html') ?>
	<script src="core/js/main.js?v=<?php echo $cssVersion?>"></script>

	<nav id="context-menu" class="context-menu">
	  <ul id="context-menu-items" class="context-menu__items">
	  </ul>
	</nav>


</body>