<?php
$baseUrl = "../core/";
if(file_exists('../local/layout.php'))
{
	$baseUrl = "../local/";
	//there is custom information, use this
	require_once('../local/layout.php');
	$baseUrl .= $currentSelectedTheme."/";
}
require_once($baseUrl.'conf/config.php');
require_once('setupProcessFile.php');

function clean_url($url) {
    $parts = parse_url($url);
    return $parts['path'];
}


if($setupProcess != "step1")
{
	$partOfUrl = clean_url($_SERVER['REQUEST_URI']);
	$partOfUrl = substr($partOfUrl, 0, strpos($partOfUrl, 'setup'));
	$url = "http://" . $_SERVER['HTTP_HOST'] .$partOfUrl ."setup/director.php";
	header('Location: ' . $url, true, 302);
	exit();
}
$counterSteps = 1;
while(file_exists('step'.$counterSteps.'.php'))
{
	$counterSteps++;
}
$counterSteps--;
require_once('../core/php/loadVars.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Welcome!</title>
	<link rel="stylesheet" type="text/css" href="../core/template/theme.css">
	<script src="../core/js/jquery.js"></script>
	<?php readfile('../core/html/popup.html') ?>	
	<style type="text/css">
		#settingsMainWatch .settingsHeader{
			display: none;
		}
		#widthForWatchListSection{
			width: 100% !important;
		}
	</style>
</head>
<body>
<div style="width: 90%; margin: auto; margin-right: auto; margin-left: auto; display: block; height: auto; margin-top: 15px;" >
	<div class="settingsHeader">
		<h1>Step 1 of <?php echo $counterSteps; ?></h1>
	</div>

	<p style="padding: 10px;">Main Settings:</p>
	<?php require_once('../core/php/template/mainVars.php'); ?>
	<table style="width: 100%; padding-left: 20px; padding-right: 20px;" ><tr><th style="text-align: right;" >
		<?php if($counterSteps == 1): ?>
			<a onclick="updateStatus('finished');" class="link">Finish</a>
		<?php else: ?>
			<a onclick="updateStatus('step2');" class="link">Continue</a>
		<?php endif; ?>
	</th></tr></table>
	<br>
	<br>
</div>
</body>
<form id="defaultVarsForm" action="../core/php/settingsSave.php" method="post"></form>
<script type="text/javascript">
	function defaultSettings()
	{
		//change setupProcess to finished
		location.reload();
	}

	function customSettings()
	{
		//change setupProcess to page2
		document.getElementById('settingsMainWatch').action = "../core/php/settingsSave.php";
		document.getElementById('settingsMainWatch').submit();
	}

	var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray) ?>');
	var fileArray = JSON.parse('<?php echo json_encode($config['watchList']) ?>');
	var countOfWatchList = <?php echo $i; ?>;
	var countOfAddedFiles = 0;
	var countOfClicks = 0;
	var locationInsert = "newRowLocationForWatchList";
	var logTrimType = "<?php echo $logTrimType; ?>";


</script>
<script src="../core/js/settingsMain.js?v=<?php echo $cssVersion?>"></script>
<script src="stepsJavascript.js?v=<?php echo $cssVersion?>"></script>
</html>