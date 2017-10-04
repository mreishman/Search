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
		echo loadVisibilityJS(baseURL());
	?>
</head>
<body>
	<?php require_once("core/php/customCSS.php");
	if($enablePollTimeLogging != "false"): ?>
		<div id="loggTimerPollStyle" style="width: 100%;background-color: black;text-align: center; line-height: 200%;" ><span id="loggingTimerPollRate" >### MS /<?php echo $pollingRate; ?> MS</span> | <span id="loggSkipCount" >0</span>/<?php echo $pollForceTrue; ?> | <span id="loggAllCount" >0</span>/<?php echo $pollRefreshAll; ?></div>
	<?php endif; ?>
	<div class="backgroundForMenus" id="menu">
		<div style="display: none;">
			<div onclick="pausePollAction();" class="menuImageDiv">
				<img id="playImage" class="menuImage" src="<?php echo $baseUrl; ?>img/Play.png"
					<?php if($pausePoll !== 'true'):?>
						style="display: none;"
					<?php else: ?>
						style="display: inline-block;"
					<?php endif;?>
				height="30px">
				<img id="pauseImage" class="menuImage" src="<?php echo $baseUrl; ?>img/Pause.png"
					<?php if($pausePoll === 'true'):?>
						style="display: none;"
					<?php else: ?>
						style="display: inline-block;"
					<?php endif;?>
				height="30px">
			</div>
			
			<div onclick="deleteAction();"  class="menuImageDiv">
				<img id="deleteImage" class="menuImage" src="<?php echo $baseUrl; ?>img/trashCanMulti.png" height="30px">
			</div>
			
			<?php if($locationForMonitorIndex != ""): ?>
			<div onclick="window.location.href = '<?php echo $locationForMonitorIndex; ?>'"  class="menuImageDiv">
				<img id="taskmanagerImage" class="menuImage" src="<?php echo $baseUrl; ?>img/task-manager.png" height="30px">
			</div>
			<?php endif; ?>


			<div onclick="window.location.href = './settings/main.php';"  class="menuImageDiv">
				<img data-id="1" id="gear" class="menuImage" src="<?php echo $baseUrl; ?>img/Gear.png" height="30px">
				<?php if($updateNotificationEnabled === "true")
				{
					if($levelOfUpdate == 1)
					{
						echo '<img id="updateImage" src="<?php echo $baseUrl; ?>img/yellowWarning.png" height="15px" style="position: absolute;margin-left: 13px;margin-top: -34px;">';
					} 
					elseif($levelOfUpdate == 2 || $levelOfUpdate == 3)
					{
						echo '<img id="updateImage" src="<?php echo $baseUrl; ?>img/redWarning.png" height="15px" style="position: absolute;margin-left: 13px;margin-top: -34px;">';
					} 
				}?>
			</div>
			<?php if ($locationForStatusIndex != ""):?>
				<div class="menuImage" style="display: inline-block; cursor: pointer; color: white; " onclick="window.location.href='<?php echo $locationForStatusIndex; ?>'" >
					gS
				</div>
			<?php endif; ?>
		</div>
		<table width="100%">
			<tr>
				<td width="33%">
					Menu
				</td>
				<td width="34%" style="text-align: center;">
					New Find | New Grep
				</td>
				<td width="33%">
				</td>
			</tr>
		</table>
	</div>
	
	<div id="main">
		
		
		<div style="width: 80%; margin-left: 10%; padding: 5px; margin-bottom: 40px; " id="header" class="scanBar">
			<div style="width: 40%; color: black; display: inline-block; text-align: center; line-height: 50px; background-color: white; border: 1px solid black;"> 
			New Find
			</div>
			<div style="width: 40%; color: black; display: inline-block; text-align: center; line-height: 50px; float: right; background-color: white; border: 1px solid black;">
			New GREP
			</div>
		</div>

		<div style="width: 80%; margin-left: 10%; background-color: white; min-height: 200px; padding: 5px; margin-bottom: 40px;border: 1px solid black; " id="test" class="scanBar">
			<div>
				<progress style="color: white; background: #000000; width: 100%;" id="testProgress" value="0" max="1">
			</div>
			<div style="color: black; width: 100%; text-align: center;" id="testTitle">
				<h2>/var/www/html/Log-Hog/</h2>
			</div>
			<div style="color: black;" id="testButtons">
				[pause] [delete]
			</div>
			<div id="testFoundThings" style="background-color: grey; height: 400px; border: 1px solid black; margin-top: 10px; overflow-y: scroll;">
			</div>
		</div>

	</div>
	
	<!--

	<div class="menuItem">
			<a class="{{id}}Button {{class}}" onclick="show(this, '{{id}}')">{{title}}</a>
		</div>

	-->


	<div id="storage">
		<div id="{{id}}" class="scanBar">
			<div >
				<progress id="{{id}}Progress" value="0" max="1">
			</div>
			<div id="{{id}}Title">
			</div>
			<div id="{{id}}Buttons">
			</div>
		</div>
	</div>
	
	<div
		class="backgroundForMenus" style="display: none;" id="titleContainer">
		<div id="title">
			&nbsp;
		</div>
		&nbsp;&nbsp;
	</div>
	<form id="settingsInstallUpdate" action="update/updater.php" method="post" style="display: none"></form>
	<script>

		<?php
		echo "var colorArrayLength = ".count($currentSelectedThemeColorValues).";";
		echo "var pausePollOnNotFocus = ".$pauseOnNotFocus.";";
		echo "var autoCheckUpdate = ".$autoCheckUpdate.";";
		echo "var flashTitleUpdateLog = ".$flashTitleUpdateLog.";";
		echo "var dateOfLastUpdate = '".$configStatic['lastCheck']."';";
		echo "var daysSinceLastCheck = '".$daysSince."';";
		echo "var daysSetToUpdate = '".$autoCheckDaysUpdate."';";
		echo "var pollingRate = ".$pollingRate.";";
		echo "var backgroundPollingRate = ".$backgroundPollingRate.";";
		echo "var pausePollFromFile = ".$pausePoll.";";
		echo "var groupByColorEnabled = ".$groupByColorEnabled.";";
		echo "var pollForceTrue = ".$pollForceTrue.";";
		echo "var pollRefreshAll = ".$pollRefreshAll.";";
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var enablePollTimeLogging = "<?php echo $enablePollTimeLogging;?>";
		var enableLogging = "<?php echo $enableLogging; ?>";
		var groupByType = "<?php echo $groupByType; ?>";
		var hideEmptyLog = "<?php echo $hideEmptyLog; ?>";
		var currentFolderColorTheme = "<?php echo $currentFolderColorTheme; ?>";
		var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray); ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var pollRefreshAllBool = "<?php echo $pollRefreshAllBool;?>";
		var pollForceTrueBool = "<?php echo $pollRefreshAllBool;?>";
		var baseUrl = "<?php echo $baseUrl;?>";

	</script>
	<?php readfile('core/html/popup.html') ?>
	<script src="core/js/main.js?v=<?php echo $cssVersion?>"></script>

	<nav id="context-menu" class="context-menu">
	  <ul id="context-menu-items" class="context-menu__items">
	  </ul>
	</nav>


</body>