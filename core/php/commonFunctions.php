<?php

function filePermsDisplay($key)
{
	$info = "u---------";
	if(file_exists($key))
	{
		$perms  =  fileperms($key);

		switch ($perms & 0xF000) {
		    case 0xC000: // socket
		        $info = 's';
		        break;
		    case 0xA000: // symbolic link
		        $info = 'l';
		        break;
		    case 0x8000: // regular
		        $info = 'f';
		        break;
		    case 0x6000: // block special
		        $info = 'b';
		        break;
		    case 0x4000: // directory
		        $info = 'd';
		        break;
		    case 0x2000: // character special
		        $info = 'c';
		        break;
		    case 0x1000: // FIFO pipe
		        $info = 'p';
		        break;
		    default: // unknown
		        $info = 'u';
		}

		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
		            (($perms & 0x0800) ? 's' : 'x' ) :
		            (($perms & 0x0800) ? 'S' : '-'));

		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
		            (($perms & 0x0400) ? 's' : 'x' ) :
		            (($perms & 0x0400) ? 'S' : '-'));

		// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		            (($perms & 0x0200) ? 't' : 'x' ) :
		            (($perms & 0x0200) ? 'T' : '-'));
	}
	return $info;
}

function phpGrep($objectSent)
{
	$returnArray = array();
	$grepResults = shell_exec("grep -nHo ".$objectSent['pattern']." ".$objectSent['file']);
	$grepResults = explode(PHP_EOL, $grepResults);
	$defaultPadding = 3; //lines +/- of padding for around found thing.
	if($grepResults)
	{
		$subArray = array();
		$subArray['data'] = array();
		$subArray['positionArray'] = array();
		$file =  file($objectSent['file']);
		foreach ($grepResults as $result)
		{
			$positionArray = explode(":", $result);
			if(count($positionArray) === 3)
			{
				//as expected
				$subSubArray = array();
				for ($i = $defaultPadding; $i > 0; $i--)
				{
					if(isset($file[((int)($positionArray[1])-1-$i)]))
					{ 
						array_push($subSubArray, $file[((int)($positionArray[1])-1-$i)]);
					}
				}

				array_push($subSubArray, $file[((int)($positionArray[1])-1)]);
				
				for ($i = $defaultPadding; $i > 0; $i--)
				{ 
					if(isset($file[((int)($positionArray[1])-1+(4-$i))]))
					{
						array_push($subSubArray, $file[((int)($positionArray[1])-1+(4-$i))]);
					}
				}

				array_push($subArray['data'], $subSubArray);
				array_push($subArray['positionArray'], ((int)($positionArray[1])-1));
			}
			else
			{
				//?????
			}
		}
		//getName
		$name = explode(":", $grepResults[0]);
		$name = $name[0];
		$returnArray[$name] = $subArray;
	}

	return $returnArray;
}

function getDirContents($dir)
{
    $files = scandir($dir);
    $skipFolders = array('.git');
    //$files = array_diff(scandir($dir), array('..', '.'));
    $results = ['files' => array(), 'folders' => array()];
    foreach($files as $key => $value)
    {
    	//if($value != "." && $value != "..")
    	//if(!in_array($value, $skipFolders))
    	//{
	        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
	        if(!is_dir($path))
	        {
	            array_push($results['files'], $path);
	        }
	        else if($value != "." && $value != "..")
	        {
	            array_push($results['folders'], $path);
	        }
	    //}
    }
    return $results;
}

function loadSentryData($sendCrashInfoJS)
{
	if($sendCrashInfoJS === "true")
	{
		return "
		<script>

		function eventThrowException(e)
		{
			//this would send errors, but it needs to be changed for this repo
		}

	</script>";
	}
	return "
	<script>

		function eventThrowException(e)
		{
			//this would send errors, but it is disabled
		}

	</script>";
}

function baseURL()
{
	$baseURL = "";
	$boolBaseURL = file_exists($baseURL."error.php");
	while(!$boolBaseURL)
	{
		$baseURL .= "../";
		$boolBaseURL = file_exists($baseURL."error.php");
	}
	return $baseURL;
}

function clean_url($url)
{
    $parts = parse_url($url);
    return $parts['path'];
}

function loadCSS($baseUrl, $version)
{
	return "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$baseUrl."template/theme.css?v=".$version."\">";
}

function loadVisibilityJS($baseURL)
{
	return "<script src=\"".$baseURL."core/js/visibility.core.js\"></script>
	<script src=\"".$baseURL."core/js/visibility.fallback.js\"></script>
	<script src=\"".$baseURL."core/js/visibility.js\"></script>
	<script src=\"".$baseURL."core/js/visibility.timers.js\"></script>";
}

function calcuateDaysSince($lastCheck)
{
	$today = date('Y-m-d');
	$old_date = $lastCheck;
	$old_date_array = preg_split("/-/", $old_date);
	$old_date = $old_date_array[2]."-".$old_date_array[0]."-".$old_date_array[1];

	$datetime1 = date_create($old_date_array[2]."-".$old_date_array[0]."-".$old_date_array[1]);
	$datetime2 = date_create($today);
	$interval = date_diff($datetime1, $datetime2);
	return $interval->format('%a');
}

function findUpdateValue($newestVersionCount, $versionCount, $newestVersion, $version)
{
	for($i = 0; $i < $newestVersionCount; $i++)
	{
		if($i < $versionCount)
		{
			if($i == 0)
			{
				if($newestVersion[$i] > $version[$i])
				{
					return 3;
				}
				elseif($newestVersion[$i] < $version[$i])
				{
					break;
				}
			}
			elseif($i == 1)
			{
				if($newestVersion[$i] > $version[$i])
				{
					return 2;
				}
				elseif($newestVersion[$i] < $version[$i])
				{
					break;
				}
			}
			else
			{
				if(isset($newestVersion[$i]))
				{
					if($newestVersion[$i] > $version[$i])
					{
						return 1;
					}
					elseif($newestVersion[$i] < $version[$i])
					{
						break;
					}
				}
				else
				{
					break;
				}
			}
		}
		else
		{
			return 1;
		}
	}
	return 0;
}

/*

$baseUrl = "../core/";
if(file_exists('../local/layout.php'))
{
	$baseUrl = "../local/";
	//there is custom information, use this
	require_once('../local/layout.php');
	$baseUrl .= $currentSelectedTheme."/";
}
require_once($baseUrl.'conf/config.php');
require_once('../core/conf/config.php');
require_once('../core/php/configStatic.php');
require_once('../core/php/loadVars.php');

*/
?>