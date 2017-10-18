<form onsubmit="checkWatchList()" id="settingsMainWatch" action="../core/php/settingsSave.php" method="post">
<div class="settingsHeader">
	Saved Searches
	<div class="settingsHeaderButtons">
		<a onclick="resetWatchListVars();" id="resetChangesSettingsHeaderButton" style="display: none;" class="linkSmall" > Reset Current Changes</a>
		<?php if ($setupProcess == "preStart" || $setupProcess == "finished"): ?>
			<a class="linkSmall" onclick="saveAndVerifyMain('settingsMainWatch');" >Save Changes</a>
		<?php else: ?>
			<button  onclick="displayLoadingPopup();">Save Changes</button>
		<?php endif; ?>
	</div>
</div>
<div class="settingsDiv" >	
	<ul id="settingsUl">
		<?php
		$i = 0;
		foreach($config['watchList'] as $key => $item): $i++;
		?>
			<li id="rowNumber<?php echo $i; ?>" >
				<a 
					class="deleteIconPosition"
					onclick="deleteRowFunctionPopup(
						<?php echo $i; ?>,
						true,
						'<?php echo $key; ?>')"
				>
					<img src="<?php echo $baseUrlImages;?>img/trashCan.png" height="15px;" >
				</a>
				File #<?php if($i < 10){echo "0";} ?><?php echo $i; ?>: 
				<span> <?php echo $key; ?> </span>
			</li>

		<?php endforeach; ?>
		<div id="newRowLocationForWatchList">
		</div>
	</ul>
</div>
<div id="hidden" style="display: none">
	<input id="numberOfRows" type="text" name="numberOfRows" value="<?php echo $i;?>">
</div>	
</form>
<?php $folderCount = $i; ?>