var title = $("title").text();
var currentPage;
var logs = {};
var titles = {};
var lastLogs = {};
var fresh = true;
var flasher;
var updating = false;
var startedPauseOnNonFocus = false;
var polling = false;
var counterForPoll = 0;
var arrayOfData1 = null;
var arrayOfData2 = null;
var arrayToUpdate = [];
var arrayOfDataMain = null;
var pollTimer = null;
var dataFromUpdateCheck = null;
var timeoutVar = null;
var pollSkipCounter = 0;
var counterForPollForceRefreshAll = 0;
var filesNew;
var pausePoll = false;
var refreshPauseActionVar;
var userPaused = false;
var refreshing = false;
var percent = 0;
var firstLoad = true;
var timer;

function escapeHTML(unsafeStr)
{
	try
	{
		return unsafeStr.toString()
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/\"/g, "&quot;")
		.replace(/\'/g, "&#39;")
		.replace(/\//g, "&#x2F;");
	}
	catch(e)
	{
		eventThrowException(e);
	}
	
}

function resize() 
{
	try
	{
	    var targetHeight = window.innerHeight - $("#menu").outerHeight() - $("#title").outerHeight();
		if(enablePollTimeLogging !== "false")
		{
			targetHeight -= 25;
		}
		if($("#main").outerHeight() !== targetHeight)
		{
			$("#main").outerHeight(targetHeight);
		}
		if($("#main").css("bottom") !== $("#title").outerHeight() + "px")
		{
			$("#main").css("bottom", $("#title").outerHeight() + "px");
		}
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function installUpdates()
{
	try
	{
	    displayLoadingPopup();
		//reset vars in post request
		var urlForSend = '../core/php/resetUpdateFilesToDefault.php?format=json'
		var data = {status: "" };
		$.ajax(
		{
			url: urlForSend,
			dataType: "json",
			data: data,
			type: "POST",
			complete: function(data)
			{
				//set thing to check for updated files. 	
				timeoutVar = setInterval(function(){verifyChange();},3000);
			}
		});
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function verifyChange()
{
	try
	{
	    var urlForSend = '../update/updateActionCheck.php?format=json'
		var data = {status: "" };
		$.ajax(
		{
			url: urlForSend,
			dataType: "json",
			data: data,
			type: "POST",
			success(data)
			{
				if(data == 'finishedUpdate')
				{
					clearInterval(timeoutVar);
					actuallyInstallUpdates();
				}
			}
		});
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function actuallyInstallUpdates()
{
	try
	{
    	$("#settingsInstallUpdate").submit();
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function checkForUpdateMaybe()
{
	try
	{
    	if (autoCheckUpdate == true)
		{
			if(daysSinceLastCheck > (daysSetToUpdate - 1))
			{
				daysSinceLastCheck = -1;
				checkForUpdateDefinitely();
			}
		}
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function checkForUpdateDefinitely(showPopupForNoUpdate = false)
{
	try
	{
    	if(!updating)
		{
			updating = true;
			if(showPopupForNoUpdate)
			{
				displayLoadingPopup(baseUrl+"img/");
			}
			$.getJSON('core/php/settingsCheckForUpdateAjax.php', {}, function(data) 
			{
				if((data.version == "1" && updateNoticeMeter == "every")|| data.version == "2" | data.version == "3")
				{
					//Update needed
					if(dontNotifyVersion != data.versionNumber)
					{

						if(popupSettingsArray.versionCheck != "false")
						{
							dataFromUpdateCheck = data;
							timeoutVar = setInterval(function(){updateUpdateCheckWaitTimer();},3000);
						}
						else
						{
							location.reload();
						}
					}
				}
				else if (data.version == "0")
				{
					if(showPopupForNoUpdate)
					{
						showPopup();
						document.getElementById("popupContentInnerHTMLDiv").innerHTML = "<div class='settingsHeader' >No Update Needed</div><br><div style='width:100%;text-align:center;padding-left:10px;padding-right:10px;'>You are on the most current version</div><div class='link' onclick='hidePopup();' style='margin-left:165px; margin-right:50px;margin-top:25px;'>Okay!</div></div>";
					}
				}
				else
				{
					//error?
					showPopup();
					document.getElementById("popupContentInnerHTMLDiv").innerHTML = "<div class='settingsHeader' >Error when checking for update</div><br><div style='width:100%;text-align:center;padding-left:10px;padding-right:10px;'>An error occured while trying to check for updates. Make sure you are connected to the internet and settingsCheckForUpdate.php has sufficient rights to write / create files. </div><div class='link' onclick='hidePopup();' style='margin-left:165px; margin-right:50px;margin-top:5px;'>Okay!</div></div>";
				}
				
			});
			updating = false;
		}
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function updateUpdateCheckWaitTimer()
{
	try
	{
		$.getJSON("core/php/configStaticCheck.php", {}, function(data) 
		{
			if(currentVersion != data)
			{
				clearInterval(timeoutVar);
				showPopupForUpdate(dataFromUpdateCheck);
			}
		});
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function showUpdateCheckPopup(data)
{
	try
	{
		showPopup();
		var textForInnerHTML = "<div class='settingsHeader' >New Version Available!</div><br><div style='width:100%;text-align:center;padding-left:10px;padding-right:10px;'>Version "+escapeHTML(data.versionNumber)+" is now available!</div><div class='link' onclick='installUpdates();' style='margin-left:74px; margin-right:50px;margin-top:25px;'>Update Now</div><div onclick='saveSettingFromPopupNoCheckMaybe();' class='link'>Maybe Later</div><br><div style='width:100%; padding-left:45px; padding-top:5px;'><input id='dontShowPopuForThisUpdateAgain'";
		if(dontNotifyVersion == data.versionNumber)
		{
			textForInnerHTML += " checked "
		}
		dontNotifyVersion = data.versionNumber;
		textForInnerHTML += "type='checkbox'>Don't notify me about this update again</div></div>";
		document.getElementById("popupContentInnerHTMLDiv").innerHTML = textForInnerHTML;
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function saveSettingFromPopupNoCheckMaybe()
{
	try
	{
    	if(document.getElementById("dontShowPopuForThisUpdateAgain").checked)
		{
			var urlForSend = "core/php/settingsSaveAjax.php?format=json";
			var data = {dontNotifyVersion: dontNotifyVersion };
			$.ajax({
					  url: urlForSend,
					  dataType: "json",
					  data: data,
					  type: "POST",
			complete: function(data){
				hidePopup();
	  	},
			});
		}
		else
		{
		hidePopup();
		}
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function scanDir(arrayOfFolders, idOfScan, scanFor, arrayOfFiles = [], total = 1, count = 0)
{
	if(arrayOfFolders.constructor !== Array)
	{
		document.getElementById("newSearch").style.display = "none";
		var blank = $("#storage .container").html();
		var item = blank;
		item = item.replace(/{{id}}/g, idOfScan);
		item = item.replace(/{{folder}}/g, arrayOfFolders);
		item = item.replace(/{{search}}/g, scanFor);
		var main = $("#main");
		main.append(item);
		arrayOfFolders = [arrayOfFolders];
	}
	try
	{
		var directory = arrayOfFolders[0]; 
		var urlForSend = "core/php/getDirInfo.php?format=json";
		var data = {arrayOfFolders, idOfScan, scanFor, arrayOfFiles, total, count};
		(function(_data){
			$.ajax({
				url: urlForSend,
				dataType: "json",
				data: {dir: directory},
				type: "POST",
				success(data)
				{
					parseDirectoryData(_data['arrayOfFolders'], _data['scanFor'], _data['idOfScan'], _data['arrayOfFiles'], _data['total'], _data['count'], data);
				}
			});	
		}(data));
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function parseDirectoryData(arrayOfFolders, scanFor, idOfScan, arrayOfFiles, total, count, data)
{
	if(document.getElementById(idOfScan+'Progress'))
	{
		count++;
		total += data['folders'].length;
		var currentPercent = ((100*(count/total))/2).toFixed(2);
		document.getElementById(idOfScan+'Progress').value = currentPercent/100;
		document.getElementById(idOfScan+'ProgressTxt').innerHTML = currentPercent;
		arrayOfFolders = arrayOfFolders.concat(data['folders']);
		arrayOfFiles = arrayOfFiles.concat(data['files']);
		arrayOfFolders.shift();
		if(arrayOfFolders.length > 0)
		{
			scanDir(arrayOfFolders, idOfScan, scanFor, arrayOfFiles, total, count);
		}
		else
		{
			arrayOfFiles.sort();
			loopThroughFiles(scanFor, "" ,idOfScan, -1, arrayOfFiles);
		
		}
	}
}

function loopThroughFiles(pattern, file, id, count = -1, arrayOfFiles)
{
	if(document.getElementById(id+'Progress'))
	{
		var total = arrayOfFiles.length;
		count++;
		var currentPercent = (((100*(count/total))/2)+50).toFixed(2);
		document.getElementById(id+'Progress').value = currentPercent/100;
		document.getElementById(id+'ProgressTxt').innerHTML = currentPercent;
		if(count < arrayOfFiles.length)
		{
			phpGrep(pattern,arrayOfFiles[count],id, count, arrayOfFiles);
		}
		else
		{
			console.log("END");
		}
	}
}

function phpGrep(pattern, file, id, count, files)
{
	try
	{
		var urlForSend = "core/php/phpGrep.php?format=json";
		var objectSent = new Array();
		var data = {file, pattern, id, count, files};
		(function(_data){
			$.ajax({
				url: urlForSend,
				dataType: "json",
				data: {file, pattern, id, count},
				type: "POST",
				success(data)
				{
					styleReturnedData(data, _data);
					loopThroughFiles(_data['pattern'], _data['file'], _data['id'], _data['count'],  _data['files']);
				}
			});	
		}(data));
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function styleReturnedData(data, otherData)
{
	if(data)
	{
		var idToAttach = otherData['id']+"FoundThings";
		var dataKeys = Object.keys(data);
		for (var i = dataKeys.length - 1; i >= 0; i--) 
		{
			var tableOutput = "<table style='width: 100%; border-spacing: 0;' ><tr><th colspan=\"2\" style='background-color: #333; line-height: 250%; border: 1px solid white;'>"+dataKeys[i]+"("+data[dataKeys[i]]["data"].length+") [expand]</th></tr>";
			for (var j = 0; j < data[dataKeys[i]]["data"].length; j++)
			{

				for (var k = 0; k < data[dataKeys[i]]["data"][j].length; k++)
				{
					if( (data[dataKeys[i]]["positionArray"][j][0]+k) === (data[dataKeys[i]]["positionArray"][j][1]))
					{
						tableOutput += "<tr style='background-color: #7c7;'>";
					}
					else
					{
						tableOutput += "<tr>";
					}
					tableOutput += "<td style='text-align: right; background-color: #555; width: 100px;' >" + ((data[dataKeys[i]]["positionArray"][j][0])+k) + "</td><td style='white-space: pre-wrap;'>" + escapeHTML(data[dataKeys[i]]["data"][j][k]) + "</td></tr>";
				}
				if(j != (data[dataKeys[i]]["data"].length-1))
				{
					tableOutput += "<td style='text-align: right; background-color: #555;' >...</td><td></td>";
				}
				
			}
			tableOutput += "</table>";
			$("#"+idToAttach).append(tableOutput);
		}
	}
}

function showGrepPopup()
{
	scanDir("/var/www/html/app/", 'test', 'loading-mask');
}

function deleteSearch(searchToRemove)
{
	$(searchToRemove).remove();

	//check if no more searches, add back buttons
	if($(".containerMain").length === 2)
	{
		document.getElementById("newSearch").style.display = "block";
	}
}

$(document).ready(function()
{
	resize();
	window.onresize = resize;
	window.onfocus = focus;

	//checkForUpdateMaybe();

});