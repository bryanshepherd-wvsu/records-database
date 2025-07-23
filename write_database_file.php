<?php
include("database.php");

function writeToDatabaseWithSCFile ($filename,$sport,$stat,$level){
$records = readRecordFile("$filename");
	$s = 0;
	$t = readCurrentDatabase("kcmsathl_WVSU_$sport","GameRecords",$stat,$level);
	if ($sport == "mbb")
	    $db_name = "kcmsathl_WVSU_mbb";
	else if ($sport == "wbb")
	    $db_name = "kcmsathl_WVSU_wbb";
	while (sizeof($records) > $s) {
		$t ++;
		createNewRecord ($records[$s][0][0],$records[$s][0][1],$records[$s][0][2],$records[$s][0][3], $records[$s][0][4],$sport,$stat,$db_name);
		$s ++;
	} 
}

function writeToDatabaseWithXMLFile ($filename, $sport){
	$xmlfile = file_get_contents($filename); 
	// Convert xml string into an object 
	$new = simplexml_load_string($xmlfile); 
	// Convert into json 
	$con = json_encode($new); 
	// Convert into associative array 
	$ArrayRead = json_decode($con, true);
	
	switch ($sport) {
		case "fb":
			include_once("fb.php");
			echo "Uploading Football XML...<br>";
			uploadFbBox($ArrayRead);
			break;
		case "wsoc":
			include_once("wsoc.php");
			echo "Uploading Women's Soccer XML...<br>";
			uploadWsocBox($ArrayRead);
			break;
		case "vb":
			include_once("vb.php");
			echo "Uploading Women's Volleyball XML...<br>";
			uploadVbBox($ArrayRead);
			break;
		case "mbb":
			include_once("mbb.php");
			echo "Uploading Men's Basketball XML...<br>";
			uploadMbbBox($ArrayRead);
			break;
		case "wbb":
			include_once("wbb.php");
			echo "Uploading Women's Basketball XML...<br>";
			uploadWbbBox($ArrayRead);
			break;
		case "bs":
			include_once("bs.php");
			echo "Uploading Baseball XML...<br>";
			uploadBsBox($ArrayRead);
			break;
		case "sb":
			include_once("sb.php");
			echo "Uploading Softball XML...<br>";
			uploadSbBox($ArrayRead);
			break;
	}
	
	/*
	// First, display game identifying information from the XML file. Then go through the team stats and find those records.
	// Finally, go through the individual stat performances and loop through. Need to set minimums for all of the stats that we look for to reduce clutter.
	// Likely will look at current records and see what the smallest value is and make those be the mimum value to add them to the database.
	// Also, should probably add a confirmation step at some point in the future to show the parsed file and results that will be added to the database, before actually committing the changes.
	if ($sport == "mbb" || $sport == "wbb"){
    	// Team Stats are simple to pull.
    	$tem_assist = $teamArray['totals']['stats']['@attributes']['ast'];
    	$tem_block = $teamArray['totals']['stats']['@attributes']['blk'];
    	$tem_fg3a = $teamArray['totals']['stats']['@attributes']['fga3'];
    	$tem_fg3m = $teamArray['totals']['stats']['@attributes']['fgm3'];
    	$tem_fg3pct = round(($tem_fg3m/$tem_fg3a),3);
    	$tem_fga = $teamArray['totals']['stats']['@attributes']['fga'];
    	$tem_fgm = $teamArray['totals']['stats']['@attributes']['fgm'];
    	$tem_fgpct = round(($tem_fgm/$tem_fga),3);
    	$tem_fta = $teamArray['totals']['stats']['@attributes']['fta'];
    	$tem_ftm = $teamArray['totals']['stats']['@attributes']['ftm'];
    	$tem_ftpct = round(($tem_ftm/$tem_fta),3);
    	$tem_points = $teamArray['totals']['stats']['@attributes']['tp'];
    	$tem_rebound = $teamArray['totals']['stats']['@attributes']['treb'];
    	$tem_steal = $teamArray['totals']['stats']['@attributes']['stl'];
    	
    	//Minimums Check. If it reaches the minimum, then we will upload to the database. If not, we won't. Either way, we output status.
    	$dupCheck = -1;
    	//Team Assists Check
    	if($tem_assist >= 15){
    		$dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'Assists');
    		if ($dupCheck >= 0){
    		    echo "Attempting update of record number $dupCheck... <br>";
    		    updateRecord($dupCheck, $tem_assist, "TEAM", $formattedDate, $opponent, '', $sport, "Assists");
    		}
    		else{
    		  createNewRecord($tem_assist, "TEAM", $formattedDate, $opponent, "",$sport, "Assists");
    		  echo "***Team assists added to database: $tem_assist (Rank)<br>";
    		  findRanking("TEAM", $sport, "Assists", $formattedDate);
    		}
    	}
    	else 
    		echo "Team Assists do not meet minimums for this game. ($tem_assist < 15) <br>";
    	$teamStatsArray['AST'] = $tem_assist;
    	
    	// Team Blocks Check
    	if ($tem_block >= 4){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'Blocks');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_block, "TEAM", $formattedDate, $opponent, '', $sport, "Blocks");
    	    }
    	    else{
    	        createNewRecord($tem_block, "TEAM", $formattedDate, $opponent, "",$sport, "Blocks");
    	        echo "***Team blocks added to database: $tem_block (Rank)<br>";
    	        findRanking("TEAM", $sport, "Blocks", $formattedDate);
    	    }
    	}
    	else
    		echo "Team Blocks do not meet minimums for this game. ($tem_block < 4) <br>";
    	$teamStatsArray['BLK'] = $tem_block;
    	
    	// Team FG 3 point attempts check
    	if ($tem_fg3a >= 23){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FG3A'); 
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_fg3a, "TEAM", $formattedDate, $opponent, '', $sport, "FG3A");
    	    }
    	    else{
    	        createNewRecord($tem_fg3a, "TEAM", $formattedDate, $opponent, "",$sport, "FG3A");
    	        echo "***Team three-point attempts added to database: $tem_fg3a (Rank)<br>";
    	        findRanking("TEAM", $sport, "FG3A", $formattedDate);
    	    }
    	}
    	else
    		echo "Team Three-Point Attempts do not meet minimums for this game. ($tem_fg3a < 23) <br>";
    	$teamStatsArray['FG3A'] = $tem_fg3a;
    	
    	//Team FG 3 point makes check
    	if ($tem_fg3m >= 8){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FG3M');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_fg3m, "TEAM", $formattedDate, $opponent, '', $sport, "FG3M");
    	    }
    	    else{
    	        createNewRecord($tem_fg3m, "TEAM", $formattedDate, $opponent, "",$sport, "FG3M");
    	        echo "***Team three-point makes added to database: $tem_fg3m (Rank)<br>";
    	        findRanking("TEAM", $sport, "FG3M", $formattedDate);
    	    }
    	}
    	else
    		echo "Team Three-Point Makes do not meet minimums for this game. ($tem_fg3m < 8) <br>";
    	$teamStatsArray['FG3M'] = $tem_fg3m;
    	
    	//Team FG 3 point percentage check
    	if ($tem_fg3a >= 7){
    		if ($tem_fg3pct >= 0.278){
    		    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FG3P');
    		    if ($dupCheck >= 0){
    		        echo "Attempting update of record number $dupCheck... <br>";
    		        updateRecord($dupCheck, $tem_fg3pct, "TEAM", $formattedDate, $opponent, "$tem_fg3m-$tem_fg3a", $sport, "FG3P");
    		    }
    		    else{
    		        createNewRecord($tem_fg3pct, "TEAM", $formattedDate, $opponent, "$tem_fg3m-$tem_fg3a",$sport, "FG3P");
    		        echo "***Team three-point percentage added to database: $tem_fg3pct (Rank)<br>";
    		        findRanking("TEAM", $sport, "FG3P", $formattedDate);
    		    }
    		}
    		else 
    			echo "Team Three-Point Percentage does not meet minimums for this game. ($tem_fg3pct < 0.278) <br>";
    		
    	}
    	else
    		echo "Team Three-Point Attempts do not meet minimums for percentage check this game. ($tem_fg3m < 7) <br>";
    	$teamStatsArray['FG3P'] = $tem_fg3pct;
    	
    	// Team Field Goal attempts check
    	if ($tem_fga >= 66){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FGA');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_fga, "TEAM", $formattedDate, $opponent, '', $sport, "FGA");
    	    } 
    	    else{
    	        createNewRecord($tem_fga, "TEAM", $formattedDate, $opponent, "",$sport, "FGA");
    	        echo "***Team field goal attempts added to database: $tem_fga (Rank)<br>";
    	        findRanking("TEAM", $sport, "FGA", $formattedDate);
    	    }
    	}
    	else
    		echo "Team Field Goal Attempts do not meet minimums for this game. ($tem_fga < 66) <br>";
    	$teamStatsArray['FGA'] = $tem_fga;
    	
    	// Team Field Goals Made Check
    	if ($tem_fgm >= 29){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FGM');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_fgm, "TEAM", $formattedDate, $opponent, '', $sport, "FGM");
    	    }
    	    else{
    	        createNewRecord($tem_fgm, "TEAM", $formattedDate, $opponent, "",$sport, "FGM");
    	        echo "***Team field goals made added to database: $tem_fgm (Rank)<br>";
    	        findRanking("TEAM", $sport, "FGM", $formattedDate);
    	    }
    	}
    	else
    		echo "Team Field Goal Makes do not meet minimums for this game. ($tem_fgm < 29	) <br>";
    	$teamStatsArray['FGM'] = $tem_fgm;
    	
    	// Team Field Goal Percentage Check
    	if ($tem_fga >= 40){
    		if ($tem_fgpct >= 0.460){
    		    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FGP');
    		    if ($dupCheck >= 0){
    		        echo "Attempting update of record number $dupCheck... <br>";
    		        updateRecord($dupCheck, $tem_fgpct, "TEAM", $formattedDate, $opponent, "$tem_fgm-$tem_fga", $sport, "FGP");
    		    }
    		    else{
    		        createNewRecord($tem_fgpct, "TEAM", $formattedDate, $opponent, "$tem_fgm-$tem_fga",$sport, "FGP");
    		        echo "***Team field goal percentage added to database: $tem_fgpct (Rank)<br>";
    		        findRanking("TEAM", $sport, "FGP", $formattedDate);
    		    }
    		}
    		else 
    			echo "Team Field Goal Percentage does not meet minimums for this game. ($tem_fgpct < 0.460) <br>";
    		
    	}
    	else
    		echo "Team Field Goal Attempts do not meet minimums for percentage check this game. ($tem_fga < 40) <br>";
    	$teamStatsArray['FGP'] = $tem_fgpct;
    	
    	// Team Free Throws attempted check
    		if ($tem_fta >= 24){
    		    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FTA');
    		    if ($dupCheck >= 0){
    		      echo "Attempting update of record number $dupCheck... <br>";
    		      updateRecord($dupCheck, $tem_fta, "TEAM", $formattedDate, $opponent, '', $sport, "FTA");
    		}
    		else{
    		    createNewRecord($tem_fta, "TEAM", $formattedDate, $opponent, "",$sport, "FTA");
    		    echo "***Team field goal attempts added to database: $tem_fta (Rank)<br>";
    		    findRanking("TEAM", $sport, "FTA", $formattedDate);
    		}
    	}
    	else
    		echo "Team Free Throw Attempts do not meet minimums for this game. ($tem_fta < 24) <br>";
    	$teamStatsArray['FTA'] = $tem_fta;
    		
    	// Team Free Throws Made Check
    	if ($tem_ftm >= 18){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FTM');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_ftm, "TEAM", $formattedDate, $opponent, '', $sport, "FTM");
    	    }
    	    else{
    	        createNewRecord($tem_ftm, "TEAM", $formattedDate, $opponent, "",$sport, "FTM");
    	        echo "***Team free throws made added to database: $tem_ftm (Rank)<br>";
    	        findRanking("TEAM", $sport, "FTM", $formattedDate);
    	    }
    	}
    	else
    		echo "Team Free Throw Makes do not meet minimums for this game. ($tem_ftm < 18) <br>";
    	$teamStatsArray['FTM'] = $tem_ftm;
    	
    	// Team Free Throw Percentage Check
    	if ($tem_fta >= 12){
    		if ($tem_ftpct >= 0.739){
    		    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'FTP');
    		    if ($dupCheck >= 0){
    		        echo "Attempting update of record number $dupCheck... <br>";
    		        updateRecord($dupCheck, $tem_ftpct, "TEAM", $formattedDate, $opponent, "$tem_ftm-$tem_fta", $sport, "FTP");
    		    }
    		    else{
    		        createNewRecord($tem_ftpct, "TEAM", $formattedDate, $opponent, "$tem_ftm-$tem_fta",$sport, "FTP");
    		        echo "***Team free throw percentage added to database: $tem_ftpct (Rank)<br>";
    		        findRanking("TEAM", $sport, "FTP", $formattedDate);
    		    }
    		}
    		else 
    			echo "Team Free Throw Percentage does not meet minimums for this game. ($tem_ftpct < 0.739) <br>";
    		
    	}
    	else
    		echo "Team Free Throw Attempts do not meet minimums for percentage check this game. ($tem_ftm < 12) <br>";
    	$teamStatsArray['FTP'] = $tem_ftpct;
    	
    	// Team Points Check
    	if($tem_points >= 83){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'Points');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_points, "TEAM", $formattedDate, $opponent, '', $sport, "Points");
    	    }
    	    else{
    	        createNewRecord($tem_points, "TEAM", $formattedDate, $opponent, "",$sport, "Points");
    	        echo "***Team Points added to database: $tem_points (Rank)<br>";
    	        findRanking("TEAM", $sport, "Points", $formattedDate);
    	    }
    	}
    	else 
    		echo "Team Points do not meet minimums for this game. ($tem_points < 83) <br>";
    	$teamStatsArray['PTS'] = $tem_points;
    	
    	// Team Rebounds Check
    	if($tem_rebound >= 40){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'Rebounds');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_rebound, "TEAM", $formattedDate, $opponent, '', $sport, "Rebounds");
    	    }
    	    else{
    	        createNewRecord($tem_rebound, "TEAM", $formattedDate, $opponent, "",$sport, "Rebounds");
    	        echo "***Team rebounds added to database: $tem_rebound (Rank)<br>";
    	        findRanking("TEAM", $sport, "Rebounds", $formattedDate);
    	    }
    	}
    	else 
    		echo "Team Rebounds do not meet minimums for this game. ($tem_rebound < 40) <br>";
		$teamStatsArray['OREB'] = $teamArray['totals']['stats']['@attributes']['oreb'];
		$teamStatsArray['DREB'] = $teamArray['totals']['stats']['@attributes']['dreb'];
		$teamStatsArray['TREB'] = $teamArray['totals']['stats']['@attributes']['treb'];
    	
    	//Team Steals Check
    	if($tem_steal >= 7){
    	    $dupCheck = checkDuplicateRecords('GameRecords', 'TEAM', $formattedDate, $sport, 'Steals');
    	    if ($dupCheck >= 0){
    	        echo "Attempting update of record number $dupCheck... <br>";
    	        updateRecord($dupCheck, $tem_steal, "TEAM", $formattedDate, $opponent, '', $sport, "Steals");
    	    }
    	    else{
    	        createNewRecord($tem_steal, "TEAM", $formattedDate, $opponent, "",$sport, "Steals");
    	        echo "***Team steals added to database: $tem_steal (Rank)<br>";
    	        findRanking("TEAM", $sport, "Steals", $formattedDate);
    	    }
    	}
    	else 
    		echo "Team Steals do not meet minimums for this game. ($tem_steal < 7) <br>";
    	echo "<hr>";
    	$teamStatsArray['STL'] = $tem_steal;
    	$teamStatsArray['opponent'] = $opponentOfGame;
    	$teamStatsArray['tog'] = $time;
    	$teamStatsArray['locationOfGame'] = checkName($locationOfGame);
    	$teamStatsArray['date'] = $formattedDate;
    	$teamStatsArray['pf'] = $teamArray['totals']['stats']['@attributes']['pf'];
    	$teamStatsArray['tf'] = $teamArray['totals']['stats']['@attributes']['tf'];
    	$teamStatsArray['dq'] = $teamArray['totals']['stats']['@attributes']['dq'];
    	$teamStatsArray['turns'] = $teamArray['totals']['stats']['@attributes']['to'];
    	$teamStatsArray['min'] = $teamArray['totals']['stats']['@attributes']['min'];
    	$teamStatsArray['season'] = $season;
    	createPlayerBoxBB('TEAM',$sport,$teamStatsArray); //Send box score array to the SQL table
    	
    	//Individual Stats will require a loop.
    	$playerArray = $teamArray['player'];
    	for($p=0; $p < sizeof($playerArray); $p++):
    	
    	//Check to see if the player's name is last name first or first name first based on if there's a comma there. If there is, convert the name to first name first.
    	$formattedName = "";
    	$isLastNameFirst = strpos($playerArray[$p]['@attributes']['name'],",");
    	if (!$isLastNameFirst){
    		$playerName = $playerArray[$p]['@attributes']['name'];	
    	}
    	else{
    		$nameReformat = explode(",",$playerArray[$p]['@attributes']['name']);
    		$playerName = $nameReformat[1]." ".$nameReformat[0];
    	}
    	
    	if($playerArray[$p]['@attributes']['gp'] == 0){ // If the player wasn't marked as playing in the game, we can stop looking through their stats because they have none.
    		echo $playerName." did not play this game. <br><hr>";
    		continue; // Finish this loop and go to the next one.
    	}
    	if ($playerArray[$p]['@attributes']['name'] == 'TEAM'){ // We already generated team numbers and we aren't going to add a "TEAM" player box score for historical purposes...so skip it.
    	    continue;
    	}
    	echo "Checking $playerName...<br>";
    	
    	//Creating Box Score Array for the player.
    	$statsArray = array();
    	$statsArray['opponent'] = $opponentOfGame;
    	$statsArray['tog'] = $time;
    	$statsArray['locationOfGame'] = checkName($locationOfGame);
    	$statsArray['date'] = $formattedDate;
    	$statsArray['oreb'] = $playerArray[$p]['stats']['@attributes']['oreb'];
    	$statsArray['dreb'] = $playerArray[$p]['stats']['@attributes']['dreb'];
    	$statsArray['pf'] = $playerArray[$p]['stats']['@attributes']['pf'];
    	$statsArray['tf'] = $playerArray[$p]['stats']['@attributes']['tf'];
    	$statsArray['dq'] = $playerArray[$p]['stats']['@attributes']['dq'];
    	$statsArray['turns'] = $playerArray[$p]['stats']['@attributes']['to'];
    	$statsArray['season'] = $season;
    	
    	//Game Record Checks
    	$statsArray['gp'] = $playerArray[$p]['@attributes']['gp'];
    	if (array_key_exists('gs',$playerArray[$p]['@attributes']))
    	   $statsArray['gs'] = 1;
    	else
    	    $statsArray['gs'] = 0;
    	$statsArray['min'] = $playerArray[$p]['stats']['@attributes']['min'];
    	$FoundStats = FALSE; // Constant boolean for if a stat is found. If all stats are checked and this variable doesn't change, post a notice that nothing was found.
    	for ($s = 0; $s < 14; $s ++){
    	    $statCheck = NULL;
    	    $remarks = "";
    		//echo "Checking Stat $s<br>";
    		switch ($s){
    			case 0:
    				$currStat = $playerArray[$p]['stats']['@attributes']['fgm'];
    				$statsArray['fgm'] = $currStat;
    				if($currStat > 5){
    					echo "$playerName FGM: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'FGM',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "FGM";		
    					$FoundStats = TRUE;
    				}
    				break;
    			case 1:
    				$currStat = $playerArray[$p]['stats']['@attributes']['fga'];
    				$statsArray['fga'] = $currStat;
    				if ($currStat > 5){
    					echo "$playerName FGA: $currStat. | ";
    					$prevSeasonHigh = findSeasonHigh($playerName,'FGA',$sport);
    					echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "FGA";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 2:
    				$fga = $playerArray[$p]['stats']['@attributes']['fga'];
    				$fgm = $playerArray[$p]['stats']['@attributes']['fgm'];
    				$currStat = round(($fgm/$fga), 3);
    				$statsArray['fgp'] = $currStat;
    				if ($fga >= 8){ // Minimum 8 attempts to even look at percentage.
    					if ($currStat >= 0.571) {
    						echo "$playerName FG%: $currStat. | ";
    						$prevSeasonHigh = findSeasonHigh($playerName,$currStat,$sport);
    						echo "Previous Season High: $prevSeasonHigh<br>";
    						$statCheck = "FGP";
    						$remarks = "$fgm-$fga";
    						$FoundStats = TRUE;
    					}
    				}
    				break;
    			case 3:
    				$currStat = $playerArray[$p]['stats']['@attributes']['fgm3'];
    				$statsArray['fg3m'] = $currStat;
    				if ($currStat >= 4){
    					echo "$playerName FGM3: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'FG3M',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "FG3M";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 4:
    				$currStat = $playerArray[$p]['stats']['@attributes']['fga3'];
    				$statsArray['fg3a'] = $currStat;
    				if ($currStat >= 8){
    					echo "$playerName FGA3: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'FG3A',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "FG3A";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 5:
    				$fga3 = $playerArray[$p]['stats']['@attributes']['fga3'];
    				$fgm3 = $playerArray[$p]['stats']['@attributes']['fgm3'];
    				$currStat = round(($fgm3/$fga3), 3);
    				$statsArray['fg3p'] = $currStat;
    				if ($fga3 >= 7){ // Minimum 8 attempts to even look at percentage.
    					if ($currStat >= 0.470) { // Minimum three point percentage
    						echo "$playerName FG3%: $currStat. | ";
                            $prevSeasonHigh = findSeasonHigh($playerName,$currStat,$sport);
                            echo "Previous Season High: $prevSeasonHigh<br>";
    						$statCheck = "FG3P";
    						$remarks = "$fgm3-$fga3";
    						$FoundStats = TRUE;
    					}
    				}
    				break;
    			case 6:
    				$currStat = $playerArray[$p]['stats']['@attributes']['ftm'];
    				$statsArray['ftm'] = $currStat;
    				if ($currStat >= 7){
    					echo "$playerName FTM: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'FTM',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "FTM";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 7:
    				$currStat = $playerArray[$p]['stats']['@attributes']['fta'];
    				$statsArray['fta'] = $currStat;
    				if ($currStat >= 9){
    					echo "$playerName FTA: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'FTA',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "FTA";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 8:
    				$fta = $playerArray[$p]['stats']['@attributes']['fta'];
    				$ftm = $playerArray[$p]['stats']['@attributes']['ftm'];
    				$currStat = round(($ftm/$fta), 3);
    				$statsArray['ftp'] = $currStat;
    				if ($fta >= 6){ // Minimum 8 attempts to even look at percentage.
    					if ($currStat >= 0.833) {
    						echo "$playerName FT%: $currStat. | ";
                            $prevSeasonHigh = findSeasonHigh($playerName,$currStat,$sport);
                            echo "Previous Season High: $prevSeasonHigh<br>";
    						$statCheck = "FTP";
    						$remarks = "$ftm-$fta";
    						$FoundStats = TRUE;
    					}
    				}
    				break;
    			case 9:
    				$currStat = $playerArray[$p]['stats']['@attributes']['ast'];
    				$statsArray['ast'] = $currStat;
    				if ($currStat >= 5){
    					echo "$playerName AST: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'AST',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "Assists";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 10:
    				$currStat = $playerArray[$p]['stats']['@attributes']['treb'];
    				if ($currStat >= 10){
    					echo "$playerName REB: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,$currStat,$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "Rebounds";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 11:
    				$currStat = $playerArray[$p]['stats']['@attributes']['tp'];
    				$statsArray['pts'] = $currStat;
    				if ($currStat >= 23){
    					echo "$playerName PTS: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'PTS',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "Points";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 12:
    				$currStat = $playerArray[$p]['stats']['@attributes']['blk'];
    				$statsArray['blk'] = $currStat;
    				if ($currStat >= 16){
    					echo "$playerName BLK: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'BLK',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "Blocks";
    					$FoundStats = TRUE;
    				}
    				break;
    			case 13:
    				$currStat = $playerArray[$p]['stats']['@attributes']['stl'];
    				$statsArray['stl'] = $currStat;
    				if ($currStat >= 3){
    					echo "$playerName STL: $currStat. | ";
                        $prevSeasonHigh = findSeasonHigh($playerName,'STL',$sport);
                        echo "Previous Season High: $prevSeasonHigh<br>";
    					$statCheck = "Steals";
    					$FoundStats = TRUE;
    				}
    				break;
    		}
    		$formattedName = checkName($playerName);
    		if ($statCheck != NULL){
    	        $dupCheck = checkDuplicateRecords ('GameRecords', $playerName, $formattedDate, $sport, $statCheck);
    	        if ($dupCheck >= 0){
    	            updateRecord($dupCheck, $currStat, $formattedName, $formattedDate, $opponent, $remarks, $sport, $statCheck);
    	        }
    	        else 
    	            createNewRecord($currStat, $formattedName, $formattedDate, $opponent, $remarks, $sport, $statCheck);
                findRanking($formattedName, $sport, $statCheck, $formattedDate);
    		}
    	} // End player stat loop. This is for one player's stats
    	createPlayerBoxBB($formattedName,$sport,$statsArray);
    	if (!$FoundStats)
    		echo "No stats found above minimums for $playerName.<hr>";
    	else
    		echo "<hr>";
    	
    	endfor; // Finish Player Loop. This loops through ALL players on the team. All stats that meet specs should've been printed to the webpage and uploaded to database.
	}
	*/
	
	
	
	//var_dump($ArrayRead);
}
?>