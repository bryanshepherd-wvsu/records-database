<?php
/*************************************************
SQL Override List
***** All Purpose Yards = SELECT NAME, SUM( CASE WHEN Stat IN('rush_yds', 'rcv_yds') THEN VALUE ELSE 0 END ) AS VALUE, SeasonsActive, IsActive, Season FROM `fb_boxscores_offense` AS b LEFT OUTER JOIN fb_athletes AS a ON b.AthleteID = a.AthleteID RIGHT OUTER JOIN fb_games AS g ON b.GameID = g.GameID WHERE `Name` != 'TEAM' GROUP BY NAME , Season ORDER BY VALUE DESC;



*************************************************/

function fbRecords(){
	
	$db_name = "kcmsathl_wvsu";
	
	if (array_key_exists('STAT',$_GET))
	   $requestedStat = $_GET['STAT'];
    else
        $requestedStat = NULL;
        if (array_key_exists('LEVEL',$_GET))
            $requestedLevel = $_GET['LEVEL'];
		else
			$requestedLevel = NULL;
	?><table width="100%">
		<tr>
			<td valign="top" width="85%">
			<?php
	if ($requestedLevel != NULL && $requestedStat != NULL){
		// Determine which table to read. Applicable for football, baseball and softball only. All other sports will be read from one table.
		$statType = explode("_",$requestedStat);
		if ($statType[0] == "rush" || $statType[0] == "pass" || $statType[0] == "rcv")
			$table = "fb_boxscores_offense";
		else if ($statType [0] == "punt" || $statType [0] == "ko" || $statType [0] == "fg" || $statType [0] == "pat" || $statType [0] == "kr" || $statType [0] == "pr")
			$table = "fb_boxscores_specteams";
		else if ($statType [0] == "scoring")
			$table = "fb_boxscores_scoring";
		else
			$table = "fb_boxscores_defense";
		
		// Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
			$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
			$oldRecords = readOldDatabase($db_name,"fb_oldrecords",$requestedStat,$_GET['LEVEL']);
		// Game Highs. Shouldn't need many custom scripts.
			if ($requestedLevel == "GAM" || $requestedLevel == "TMG"){
				if ($requestedStat == "rush_avg" || $requestedStat == "pass_avg" || $requestedStat == "pass_eff" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp" || $requestedStat == "rcv_avg" || $requestedStat == "punt_avg" || $requestedStat == "pr_avg" || $requestedStat == "ir_avg" || $requestedStat == "fr_avg" || $requestedStat == "kr_avg") 
					printCurrentRecordsDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
				else
					printCurrentRecordsDatabase($currentRecords, $oldRecords, "fb", 0); // FALSE truncates decimals, even though all values are stored as thousandths.
			}//End Game Highs
		
		//Career Numbers. Will need several Custom scripts.
			elseif ($requestedLevel == "CAR"){
				if($requestedStat == "pass_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingCaught,PassingAttempts,(PassingAverage * 100) as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END) as 'PassingCaught',SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END)as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END),3) as PassingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY PassingAverage DESC) as x WHERE PassingAttempts > 29 ");
						printCareerDatabase($currentRecords, $oldRecords, "fb", 2); // 2 = Decimal to Tenth of a position
					}
				else if ($requestedStat == "rcv_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceivingYards,PassesCaught,YardsPerCatch as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='rcv_yds' THEN Value ELSE 0 END) as 'ReceivingYards',SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END)as 'PassesCaught', ROUND(SUM(CASE WHEN `Stat`='rcv_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END),3) as YardsPerCatch,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY YardsPerCatch DESC) as x WHERE PassesCaught > 19 ");
						printCareerDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "rcv_nopergame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceivingYards,PassesCaught,YardsPerCatch as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END) as 'ReceivingYards',SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END)as 'PassesCaught', ROUND(SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END),3) as YardsPerCatch,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY YardsPerCatch DESC) as x WHERE PassesCaught > 9 ");
						printCareerDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
				else if ($requestedStat == "rush_avg" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(AVG(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
					printCareerDatabase($currentRecords, $oldRecords, "fb", TRUE); // TRUE is for outputting decimal precision
				}
				else if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long" || $requestedStat == "kr_long" || $requestedStat == "fr_long" || $requestedStat == "ir_long" || $requestedStat == "pr_long"){
					if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long")
						$requestedTable = "offense";
					else if ($requestedStat == "kr_long" || $requestedStat == "pr_long" || $requestedStat == "fg_long")
						$requestedTable = "specteams";
					else if ($requestedStat == "fr_long" || $requestedStat == "ir_long")
						$requestedTable = "defense";
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(MAX(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_".$requestedTable."` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
					printCareerDatabase($currentRecords, $oldRecords, "fb", FALSE); // TRUE is for outputting decimal precision
				}
				else
					printCareerDatabase($currentRecords, $oldRecords, "fb", FALSE); // FALSE truncates decimals, even though all values are stored as thousandths.
			} // End Career
		
		//Season Numbers. Several Customs.
			elseif ($requestedLevel == "SEA" || $requestedLevel == "TMS"){
				if ($requestedLevel == "TMS"){
					if ($requestedStat == "rush_pergame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,RushingYards,RushingAttempts,RushingAverage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='rush_yds' THEN Value ELSE 0 END) as 'RushingYards',SUM(CASE WHEN `Stat`='rush_att' THEN 1 ELSE 0 END)as 'RushingAttempts', ROUND(SUM(CASE WHEN `Stat`='rush_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='rush_att' THEN 1 ELSE 0 END),3) as RushingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY RushingAverage DESC) as x WHERE RushingAttempts > 7 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // 2 = Decimal to Tenth of a position
					}
					elseif ($requestedStat == "pass_eff"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT IsActive,Name,SeasonsActive,Season,PassingYards,Touchdowns,Completions,Interceptions,PassAttempts,ROUND((((8.4*PassingYards) + (330*Touchdowns) + (100*Completions) - (200*Interceptions))/PassAttempts),3) as Value FROM(SELECT NAME,SUM(CASE WHEN `Stat` = 'pass_yds' THEN VALUE ELSE 0 END) AS PassingYards,SUM(CASE WHEN `Stat` = 'pass_td' THEN VALUE ELSE 0 END) AS Touchdowns,SUM(CASE WHEN `Stat` = 'pass_comp' THEN VALUE ELSE 0 END) AS Completions,SUM(CASE WHEN `Stat` = 'pass_int' THEN VALUE ELSE 0 END) AS Interceptions,SUM(CASE WHEN `Stat` = 'pass_att' THEN VALUE ELSE 0 END) AS PassAttempts, SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games AS games ON boxscores.GameID = games.GameID WHERE `Name` = 'TEAM' GROUP BY NAME,Season) AS X WHERE PassAttempts > 29 ORDER BY Value DESC");
						printSeasonDatabase($currentRecords,$oldRecords,"fb",2);
					}
					elseif($requestedStat == "pass_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingCaught,PassingAttempts,(PassingAverage * 100) as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END) as 'PassingCaught',SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END)as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END),3) as PassingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY PassingAverage DESC) as x WHERE PassingAttempts > 29 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // 2 = Decimal to Tenth of a position
					}
					elseif($requestedStat == "pass_int"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,IsActive,SUM(CASE WHEN `Stat`= 'pass_int' THEN Value ELSE 0 END) as Value,Season FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID GROUP BY Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // 0 = Truncate Decimal
					}
					elseif ($requestedStat == "pass_pergame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,PassingAttempts,PassingAverage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='pass_att' THEN 1 ELSE 0 END)as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_att' THEN 1 ELSE 0 END),3) as PassingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY PassingAverage DESC) as x WHERE PassingAttempts > 7 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // 2 = Decimal to Tenth of a position
					}
					elseif($requestedStat == "totoff_yards"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,IsActive,SUM(CASE WHEN `Stat`= 'pass_yds' OR `Stat`='rush_yds' THEN Value ELSE 0 END) as Value,Season FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Season,Name ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // 0 = Truncate Decimal
					}
					elseif($requestedStat == "totoff_avggame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,PassingAttempts,PassingAverage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_yds' OR `Stat` = 'rush_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='rush_att' THEN 1 ELSE 0 END)as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_yds' OR `Stat` = 'rush_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_att' THEN 1 ELSE 0 END),3) as PassingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Season ORDER BY PassingAverage DESC) as x");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // 0 = Truncate Decimal
					}
					else
					printSeasonDatabase($currentRecords, $oldRecords, "fb", 0);
				
				}
				else if ($requestedLevel == "SEA") {
					if ($requestedStat == "pass_eff"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT IsActive,Name,SeasonsActive,Season,PassingYards,Touchdowns,Completions,Interceptions,PassAttempts,ROUND((((8.4*PassingYards) + (330*Touchdowns) + (100*Completions) - (200*Interceptions))/PassAttempts),3) as Value FROM(SELECT NAME,SUM(CASE WHEN `Stat` = 'pass_yds' THEN VALUE ELSE 0 END) AS PassingYards,SUM(CASE WHEN `Stat` = 'pass_td' THEN VALUE ELSE 0 END) AS Touchdowns,SUM(CASE WHEN `Stat` = 'pass_comp' THEN VALUE ELSE 0 END) AS Completions,SUM(CASE WHEN `Stat` = 'pass_int' THEN VALUE ELSE 0 END) AS Interceptions,SUM(CASE WHEN `Stat` = 'pass_att' THEN VALUE ELSE 0 END) AS PassAttempts, SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games AS games ON boxscores.GameID = games.GameID WHERE `Name` != 'TEAM' GROUP BY NAME,Season) AS X WHERE PassAttempts > 29 ORDER BY Value DESC");
						printSeasonDatabase($currentRecords,$oldRecords,"fb",2);
					}
					elseif ($requestedStat == "rush_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,RushingYards,RushingAttempts,RushingAverage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='rush_yds' THEN Value ELSE 0 END) as 'RushingYards',SUM(CASE WHEN `Stat`='rush_att' THEN Value ELSE 0 END)as 'RushingAttempts', ROUND(SUM(CASE WHEN `Stat`='rush_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='rush_att' THEN Value ELSE 0 END),3) as RushingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY RushingAverage DESC) as x WHERE RushingAttempts > 29 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					elseif($requestedStat == "rush_pergame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,RushingYards,RushingAttempts,RushingAverage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='rush_yds' THEN Value ELSE 0 END) as 'RushingYards',SUM(CASE WHEN `Stat`='rush_att' THEN 1 ELSE 0 END)as 'RushingAttempts', ROUND(SUM(CASE WHEN `Stat`='rush_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='rush_att' THEN 1 ELSE 0 END),3) as RushingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY RushingAverage DESC) as x WHERE RushingAttempts > 7 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "pass_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingCaught,PassingAttempts,(PassingAverage * 100) as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END) as 'PassingCaught',SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END)as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END),3) as PassingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PassingAverage DESC) as x WHERE PassingAttempts > 29 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "pass_peratt"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,PassingAttempts,YardsPerPassAtt as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END)as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END),3) as YardsPerPassAtt,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY YardsPerPassAtt DESC) as x WHERE PassingAttempts > 29 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "pass_percomp"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,PassesCaught,PassingAttempts,YardsPerPassComp as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END)as 'PassingAttempts',SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END)as 'PassesCaught', ROUND(SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END),3) as YardsPerPassComp,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY YardsPerPassComp DESC) as x WHERE PassingAttempts > 29 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "pass_pergame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,GamesPlayed,YardsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END)as 'GamesPlayed', ROUND(SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END),3) as YardsPerGame,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY YardsPerGame DESC) as x WHERE GamesPlayed > 7 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "rcv_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceivingYards,PassesCaught,YardsPerCatch as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='rcv_yds' THEN Value ELSE 0 END) as 'ReceivingYards',SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END)as 'PassesCaught', ROUND(SUM(CASE WHEN `Stat`='rcv_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END),3) as YardsPerCatch,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY YardsPerCatch DESC) as x WHERE PassesCaught > 9 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "rcv_nopergame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceivingYards,PassesCaught,YardsPerCatch as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END) as 'ReceivingYards',SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END)as 'PassesCaught', ROUND(SUM(CASE WHEN `Stat`='rcv_no' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END),3) as YardsPerCatch,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY YardsPerCatch DESC) as x WHERE PassesCaught > 7 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if($requestedStat == "pass_eff"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,PassTouchdowns,PassingCompletions,Interceptions,PassingAttempts,SeasonsActive,Season,IsActive, ROUND(((PassingYards*8.4)+(PassingCompletions*100)+(PassTouchdowns*330)-(Interceptions*200))/PassingAttempts,3) as Value FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_comp' THEN Value ELSE 0 END) as 'PassingCompletions',SUM(CASE WHEN `Stat`='pass_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='pass_td' THEN Value ELSE 0 END) as 'PassTouchdowns',SUM(CASE WHEN `Stat`='pass_int' THEN Value ELSE 0 END) as 'Interceptions',SUM(CASE WHEN `Stat`='pass_att' THEN Value ELSE 0 END) as 'PassingAttempts',SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season) as x WHERE PassingAttempts > 29 ORDER BY Value DESC");
							printSeasonDatabase($currentRecords,$oldRecords,"fb",1);
					}
					else if ($requestedStat == "all_purpose_yards"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SeasonsActive,Season,IsActive, SUM(CASE WHEN `Stat` IN ('rcv_yds','rush_yds','fr_yds','ir_yds','kr_yds','pr_yds') THEN Value ELSE 0 END) as Value FROM (SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_offense` WHERE `Stat`='rush_yds' OR `Stat`='rcv_yds' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_defense` WHERE `Stat`='ir_yds' OR `Stat`='fr_yds' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_specteams` WHERE `Stat`='kr_yds' OR `Stat`='pr_yds') as X LEFT OUTER JOIN fb_athletes AS athletes on x.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on x.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY `Value` DESC ");
						printSeasonDatabase($currentRecords,$oldRecords,"fb",0);
					}
					else if ($requestedStat == "all_purpose_pergame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,APYards,GamesPlayed,APAvg as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SeasonsActive,Season,IsActive, SUM(CASE WHEN `Stat` IN ('rcv_yds','rush_yds','fr_yds','ir_yds','kr_yds','pr_yds') THEN Value ELSE 0 END) as APYards, SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END) as GamesPlayed, ROUND(SUM(CASE WHEN `Stat` IN ('rcv_yds','rush_yds','fr_yds','ir_yds','kr_yds','pr_yds') THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END),1) as APAvg FROM (SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_offense` WHERE `Stat`='rush_yds' OR `Stat`='rcv_yds' OR `Stat`='gp' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_defense` WHERE `Stat`='ir_yds' OR `Stat`='fr_yds' OR `Stat`='gp' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_specteams` WHERE `Stat`='kr_yds' OR `Stat`='pr_yds' OR `Stat`='gp') as X LEFT OUTER JOIN fb_athletes AS athletes on x.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on x.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season) as y WHERE GamesPlayed > 7 ORDER BY Value DESC;");
						printSeasonDatabase($currentRecords,$oldRecords,"fb",2);
					}
					elseif($requestedStat == "totoff_td"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SeasonsActive,Season,IsActive, SUM(CASE WHEN `Stat` IN ('rcv_td','rush_td','fr_td','ir_td','kr_td','pr_td','pass_td') THEN Value ELSE 0 END) as Value FROM (SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_offense` WHERE `Stat`='rush_td' OR `Stat`='pass_td' OR `Stat`='rcv_td' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_defense` WHERE `Stat`='ir_td' OR `Stat`='fr_td' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_specteams` WHERE `Stat`='kr_td' OR `Stat`='pr_td') as X LEFT OUTER JOIN fb_athletes AS athletes on x.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on x.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY `Value` DESC ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // 0 = Truncate Decimal
					}
					elseif($requestedStat == "totoff_avggame"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,PassingAttempts,PassingAverage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_yds' OR `Stat` = 'rush_yds' OR `Stat`='rcv_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END)as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_yds' OR `Stat` = 'rush_yds' OR `Stat`='rcv_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END),3) as PassingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PassingAverage DESC) as x WHERE PassingAttempts > 7");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 2); // 0 = Truncate Decimal
					}
					elseif($requestedStat == "totoff_avggain"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PassingYards,PassingAttempts,PassingAverage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pass_yds' OR `Stat` = 'rush_yds' OR `Stat`='rcv_yds' THEN Value ELSE 0 END) as 'PassingYards',SUM(CASE WHEN `Stat`='pass_att' OR `Stat`='rush_att' OR `Stat`='rcv_att' THEN Value ELSE 0 END) as 'PassingAttempts', ROUND(SUM(CASE WHEN `Stat`='pass_yds' OR `Stat` = 'rush_yds' OR `Stat`='rcv_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pass_att' OR `Stat`='rush_att' OR `Stat`='rcv_no' THEN Value ELSE 0 END),2) as PassingAverage,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PassingAverage DESC) as x WHERE PassingAttempts > 49");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // 0 = Truncate Decimal
					}
					else if ($requestedStat == "points"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,Touchdowns,FieldGoalsMade,PATKicks,((Touchdowns*6)+(FieldGoalsMade*3)+PATKicks) as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SeasonsActive,Season,IsActive, SUM(CASE WHEN `Stat` IN ('rcv_td','rush_td','fr_td','ir_td','kr_td','pr_td') THEN Value ELSE 0 END) as Touchdowns,SUM(CASE WHEN `Stat`='fg_made' THEN Value ELSE 0 END) as FieldGoalsMade,SUM(CASE WHEN `Stat`='pat_kickmade' THEN Value ELSE 0 END) as PATKicks FROM (SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_offense` WHERE `Stat`='rush_td' OR `Stat`='rcv_td' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_defense` WHERE `Stat`='ir_td' OR `Stat`='fr_td' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_specteams` WHERE `Stat`='kr_td' OR `Stat`='pr_td' OR `Stat`='fg_made' OR `Stat`='pat_kickmade') as x LEFT OUTER JOIN fb_athletes AS athletes on x.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on x.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season) as y ORDER BY Value DESC;");
						printSeasonDatabase($currentRecords,$oldRecords,"fb",0);
					}
					else if ($requestedStat == "touchdowns"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,Touchdowns as Value,FieldGoalsMade,PATKicks,((Touchdowns*6)+(FieldGoalsMade*3)+PATKicks) as Points,SeasonsActive,Season,IsActive FROM (SELECT Name,SeasonsActive,Season,IsActive, SUM(CASE WHEN `Stat` IN ('rcv_td','rush_td','fr_td','ir_td','kr_td','pr_td') THEN Value ELSE 0 END) as Touchdowns,SUM(CASE WHEN `Stat`='fg_made' THEN Value ELSE 0 END) as FieldGoalsMade,SUM(CASE WHEN `Stat`='pat_kickmade' THEN Value ELSE 0 END) as PATKicks FROM (SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_offense` WHERE `Stat`='rush_td' OR `Stat`='rcv_td' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_defense` WHERE `Stat`='ir_td' OR `Stat`='fr_td' UNION SELECT `AthleteID`,`Value`,`GameID`,`Stat`,`StatType` FROM `fb_boxscores_specteams` WHERE `Stat`='kr_td' OR `Stat`='pr_td' OR `Stat`='fg_made' OR `Stat`='pat_kickmade') as x LEFT OUTER JOIN fb_athletes AS athletes on x.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on x.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season) as y ORDER BY Value DESC;");
						printSeasonDatabase($currentRecords,$oldRecords,"fb",0);
					}
					else if ($requestedStat == "pat_kickpct"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PATsMade,PATsAttempted,PATPct as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pat_kickmade' THEN Value ELSE 0 END) as 'PATsMade',SUM(CASE WHEN `Stat`='pat_kickatt' THEN Value ELSE 0 END)as 'PATsAttempted', ROUND(SUM(CASE WHEN `Stat`='pat_kickmade' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pat_kickatt' THEN Value ELSE 0 END),3) as PATPct,SeasonsActive,Season,IsActive FROM `fb_boxscores_specteams` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PATPct DESC) as x WHERE PATsAttempted > 4 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "fg_pct"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,KicksMade,KicksAttempted,FGPct as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fg_made' THEN Value ELSE 0 END) as 'KicksMade',SUM(CASE WHEN `Stat`='fg_att' THEN Value ELSE 0 END)as 'KicksAttempted', ROUND(SUM(CASE WHEN `Stat`='fg_made' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fg_att' THEN Value ELSE 0 END),3) as FGPct,SeasonsActive,Season,IsActive FROM `fb_boxscores_specteams` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FGPct DESC) as x WHERE KicksAttempted > 4 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "punt_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PuntYards,TotalPunts,AvgPunt as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='punt_yds' THEN Value ELSE 0 END) as 'PuntYards',SUM(CASE WHEN `Stat`='punt_no' THEN Value ELSE 0 END)as 'TotalPunts', ROUND(SUM(CASE WHEN `Stat`='punt_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='punt_no' THEN Value ELSE 0 END),3) as AvgPunt,SeasonsActive,Season,IsActive FROM `fb_boxscores_specteams` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AvgPunt DESC) as x WHERE TotalPunts > 4 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "pr_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PuntYards,TotalPunts,AvgPunt as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='pr_yds' THEN Value ELSE 0 END) as 'PuntYards',SUM(CASE WHEN `Stat`='pr_no' THEN Value ELSE 0 END)as 'TotalPunts', ROUND(SUM(CASE WHEN `Stat`='pr_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='pr_no' THEN Value ELSE 0 END),3) as AvgPunt,SeasonsActive,Season,IsActive FROM `fb_boxscores_specteams` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AvgPunt DESC) as x WHERE TotalPunts > 4 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "pr_td"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']," SELECT * FROM `fb_boxscores_specteams` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' AND `Stat`='pr_td' AND Value > 0 GROUP BY Name,Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "kr_avg"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PuntYards,TotalPunts,AvgPunt as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='kr_yds' THEN Value ELSE 0 END) as 'PuntYards',SUM(CASE WHEN `Stat`='kr_no' THEN Value ELSE 0 END)as 'TotalPunts', ROUND(SUM(CASE WHEN `Stat`='kr_yds' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='kr_no' THEN Value ELSE 0 END),3) as AvgPunt,SeasonsActive,Season,IsActive FROM `fb_boxscores_specteams` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AvgPunt DESC) as x WHERE TotalPunts > 4 ");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 1); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "kr_td"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']," SELECT * FROM `fb_boxscores_specteams` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' AND `Stat`='kr_td' AND Value > 0 GROUP BY Name,Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "fr_td"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']," SELECT * FROM `fb_boxscores_defense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' AND `Stat`='fr_td' AND Value > 0 GROUP BY Name,Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "fr_no"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']," SELECT * FROM `fb_boxscores_defense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' AND `Stat`='fr_no' AND Value > 0 GROUP BY Name,Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "fr_yds"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `fb_boxscores_defense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' AND `Stat`='fr_yds' AND Value > 0 GROUP BY Name,Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "ir_td"){
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']," SELECT * FROM `fb_boxscores_defense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' AND `Stat`='ir_td' AND Value > 0 GROUP BY Name,Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", 0); // TRUE is for outputting decimal precision
					}
					else if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long" || $requestedStat == "kr_long" || $requestedStat == "fr_long" || $requestedStat == "ir_long" || $requestedStat == "pr_long"){
						if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long")
							$requestedTable = "offense";
						else if ($requestedStat == "kr_long" || $requestedStat == "pr_long" || $requestedStat == "fg_long")
							$requestedTable = "specteams";
						else if ($requestedStat == "fr_long" || $requestedStat == "ir_long")
							$requestedTable = "defense";
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(MAX(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_".$requestedTable."` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
						printSeasonDatabase($currentRecords, $oldRecords, "fb", FALSE); // TRUE is for outputting decimal precision
					}
					else
						printSeasonDatabase($currentRecords, $oldRecords, "fb", FALSE); // FALSE truncates decimals, even though all values are stored as thousandths.
					}
				}// End Season
			} // End Null Level and Stat
	else
		echo "<h2>Please select Category.</h2>";
?>
		</td>
		<td valign="top" width="15%">
				<table border="0">
			  <tbody>
				  <tr>
				  <td>
					  <label for="indexLevel">Make a selection:</label>
				  	<select name="indexLevel" id="indexLevel">
						<option value="NoSelection"></option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="fb_GAM">Individual Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="fb_SEA">Individual Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="fb_CAR">Career Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="fb_TMG">Team Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="fb_TMS">Team Season Highs</option>
					  </select>
				  </td>
				  </tr>
				  <tr>
				  <td>
					  <label for="indexStat">Select Stat</label>
						<select name="indexStat" id="indexStat">
						<option value=""></option>
				  <?php
		if ($requestedLevel != NULL && $requestedLevel != "ATH"){
			if ($requestedLevel == "SEA" || $requestedLevel == "TMS"){
				?>
<option>-----Rushing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_att">Most Carries</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_yds">Most Net Yards Rushing</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_avg">Highest Average Yards Per Rush (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_pergame">Highest Average Rushing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_td">Most Rushing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_long">Longest Rush from Scrimmage</option>
							<option>-----Passing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_att">Most Passing Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_comp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_comp">Most Passing Completions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_yds">Most Passing Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_avg">Highest Completion Rate (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_eff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_eff">Highest Passing Efficiency (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_peratt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_peratt">Highest Average per Pass (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_percomp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_percomp">Highest Average per Completion (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_pergame">Highest Average Passing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_td">Most Passing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_int") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_int">Most Interceptions Thrown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_long">Longest Pass Completion</option>
							<option>-----Receiving-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_no">Most Passes Caught</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_yds">Most Receiving Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_td">Most Touchdown Receptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_avg">Highest Average Yards Per Catch (Minimum 10 receptions)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_nopergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_nopergame">Most Receptions Per Game (Minumum 8 Games)</option>
							<option>-----Total Offense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_yards">Total Offensive Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_td">Total TDs Responsible For</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggame">Avg. Yards Per Game</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggain") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggain">Avg. Yards Per Play</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_yards">Most All-Purpose Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_pergame">Most All-Purpose Yards Per Game</option>
							<option>-----Scoring-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-points">Most Points Scored</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "touchdowns") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-touchdowns">Most Touchdowns Scored</option>
							<option>-----Extra Points-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickmade") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickmade">Most PAT Kicks Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickatt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickatt">Most PAT Kick Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickpct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickpct">Highest PAT percent (Minimum 5 attempts)</option>
							<option>-----Field Goals-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_att">Most Field Goals Attempted</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_made") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_made">Most Field Goals Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_pct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_pct">Highest Field Goal Percent (minimum 5 attempts)</option>
							<option>-----Punting-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_no">Most Punts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_yds">Most Yards Punting</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_blkd">Most Punts Had Blocked</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_avg">Highest Average Yards Per Punt</option>
							<option>-----Returns-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_no">Most Punt Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_yds">Most Punt Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_td">Most Punt Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_avg">Highest Average Gain Per Punt Return (minimum 5 returns)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_no">Most Kick Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_yds">Most Kick Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_td">Most Kick Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_avg">Highest Average Yards Per Kick Return (minimum 5 returns)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_no">Most Fumble Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_yds">Most Fumble Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_td">Most Fumble Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_no">Most Interceptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_yds">Most Interception Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_td">Most Interception Returns for Touchdown</option>
							<option>-----Defense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tot_tack") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tot_tack">Most Tackles</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sacks") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-sacks">Most Quarterback Sacks</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tfl") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tfl">Most Tackles For Loss</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ff">Most Fumbles Forced</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr">Most Fumbles Recovered</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "brup") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-brup">Most Pass Breakups</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-blkd">Most Blocked Kicks</option>
			  </select>
			<?php
					  }
			else if ($requestedLevel == "CAR"){ ?>
						<option>-----Rushing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_att">Most Carries</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_yds">Most Net Yards Rushing</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_avg">Highest Average Yards Per Rush (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_pergame">Highest Average Rushing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_td">Most Rushing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_long">Longest Rush from Scrimmage</option>
							<option>-----Passing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_att">Most Passing Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_comp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_comp">Most Passing Completions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_yds">Most Passing Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_avg">Highest Completion Rate (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_eff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_eff">Highest Passing Efficiency (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_peratt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_peratt">Highest Average per Pass (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_percomp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_percomp">Highest Average per Completion (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_pergame">Highest Average Passing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_td">Most Passing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_int") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_int">Most Interceptions Thrown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_long">Longest Pass Completion</option>
							<option>-----Receiving-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_no">Most Passes Caught</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_yds">Most Receiving Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_td">Most Touchdown Receptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_avg">Highest Average Yards Per Catch (Minimum 20 receptions)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_nopergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_nopergame">Most Receptions Per Game (Minumum 10 Games)</option>
							<option>-----Total Offense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_yards">Total Offensive Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_td">Total TDs Responsible For</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggame">Avg. Yards Per Game</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggain") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggain">Avg. Yards Per Play</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_yards">Most All-Purpose Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_pergame">Most All-Purpose Yards Per Game</option>
							<option>-----Scoring-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-points">Most Points Scored</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "touchdowns") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-touchdowns">Most Touchdowns Scored</option>
							<option>-----Extra Points-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickmade") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickmade">Most PAT Kicks Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickatt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickatt">Most PAT Kick Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickpct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickpct">Highest PAT percent (Minimum 5 attempts)</option>
							<option>-----Field Goals-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_att">Most Field Goals Attempted</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_made") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_made">Most Field Goals Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_pct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_pct">Highest Field Goal Percent (minimum 5 attempts)</option>
							<option>-----Punting-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_no">Most Punts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_yds">Most Yards Punting</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_avg">Highest Average Yards Per Punt</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_inside20") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_inside20">Most Punts downed inside 20</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_long">Longest Punt</option>
							<option>-----Returns-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_long">Longest Punt Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_long">Longest Kick Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_long">Longest Interception Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_long">Longest Fumble Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_no">Most Punt Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_yds">Most Punt Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_avg">Highest Average Gain Per Punt Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_no">Most Kick Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_yds">Most Kick Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_avg">Highest Average Yards Per Kick Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_no">Most Fumble Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_yds">Most Fumble Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_avg">Highest Average Yards Per Fumble Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_no">Most Interceptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_yds">Most Interception Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_avg">Highest Average Gain Per Interception Return</option>
							<option>-----Defense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tot_tack") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tot_tack">Most Tackles</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sacks") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-sacks">Most Quarterback Sacks</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tfl") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tfl">Most Tackles For Loss</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ff">Most Fumbles Forced</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr">Most Fumbles Recovered</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "brup") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-brup">Most Pass Breakups</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-blkd">Most Blocked Kicks</option>
		<?php
			}
			else {
			?>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_att">Most Carries</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_yds">Most Net Yards Rushing</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_avg">Highest Average Gain Per Rush</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_td">Most Rushing Touchdowns</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_long">Longest Rush from Scrimmage</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_att">Most Passing Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_comp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_comp">Most Passing Completions</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_yds">Most Passing Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_avg">Highest Completion Rate</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_peratt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_peratt">Highest Average per Attempt</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_percomp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_percomp">Highest Average per Completion</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_td">Most Passing Touchdowns</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_int") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_int">Most Interceptions Thrown</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_eff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_eff">Highest Passing Efficiency</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_long">Longest Pass Completion</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_no">Most Passes Caught</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_yds">Most Receiving Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_avg">Highest Average Per Reception</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_td">Most Touchdown Receptions</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_long">Longest Pass Reception</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_plays") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_plays">Total Offensive Plays</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_yards">Total Offensive Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggain") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggain">Avg. Gained Per Play</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_yards">Most All-Purpose Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_att">Most All-Purpose Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-points">Most Points Scored</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "touchdowns") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-touchdowns">Most Touchdowns Scored</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickmade") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickmade">Most Extra Points Scored By Kicking</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickatt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickatt">Most Extra Points Attempted by Kicking</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kick_pts") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kick_pts">Most Points Scored By Kicking</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_att">Most Field Goals Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_made") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_made">Most Field Goals Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_long">Longest Field Goal Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_no">Most Punts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_yds">Most Yards Punting</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_avg">Highest Average Yards Per Punt</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_inside20") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_inside20">Most Punts downed inside 20</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_long">Longest Punt</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_long">Longest Punt Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_long">Longest Kick Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_long">Longest Interception Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_long">Longest Fumble Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_no">Most Punt Returns</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_yds">Most Punt Return Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_avg">Highest Average Gain Per Punt Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_no">Most Kick Returns</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_yds">Most Kick Return Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_avg">Highest Average Yards Per Kick Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_no">Most Fumble Returns</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_yds">Most Fumble Return Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_avg">Highest Average Yards Per Fumble Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_no">Most Interceptions</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_yds">Most Interception Return Yards</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_avg">Highest Average Gain Per Interception Return</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tot_tack") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tot_tack">Most Tackles</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sacks") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-sacks">Most Quarterback Sacks</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tfl") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tfl">Most Tackles For Loss</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ff">Most Fumbles Forced</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr">Most Fumbles Recovered</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "brup") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-brup">Most Pass Breakups</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-blkd">Most Blocked Kicks</option>
		  </select>
	  </td>
</tr>
	  <?php
				}
	}
}

function editfbRecords(){
	if (array_key_exists('STAT',$_GET))
        $requestedStat = $_GET['STAT'];
        else
            $requestedStat = NULL;
    if (array_key_exists('LEVEL',$_GET))
        $requestedLevel = $_GET['LEVEL'];
        else
            $requestedLevel = NULL;
	?><table width="100%">
	<tr>
		<?php if (array_key_exists('SPORT',$_GET)){ ?>
			<td valign="top" width="50%">
				<table width="500" border="0">
					
			  <tbody>
			  <tr>
				  <td>
					  <label for="adminLevel">Make a selection:</label>
				  	<select name="adminLevel" id="adminLevel">
						<option value="NoSelection"></option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="fb_GAM">Individual Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="fb_SEA">Individual Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="fb_CAR">Career Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="fb_TMG">Team Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="fb_TMS">Team Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATH") echo "selected" ?> value="fb_ATH">Active Athletes</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATR") echo "selected" ?> value="fb_ATR">Retired Athletes</option>
					  </select>
				  </td>
				  <td>
					  <label for="adminStat">Select Stat</label>
				  	<select name="adminStat" id="adminStat">
						<option value=""></option>
				  <?php
		if ($requestedLevel != NULL && ($requestedLevel != "ATH" && $requestedLevel != "ATR")){
			if ($requestedLevel == "SEA"){
				?>
<option>-----Rushing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_att">Most Carries</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_yds">Most Net Yards Rushing</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_avg">Highest Average Yards Per Rush (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_pergame">Highest Average Rushing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_td">Most Rushing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_long">Longest Rush from Scrimmage</option>
							<option>-----Passing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_att">Most Passing Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_comp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_comp">Most Passing Completions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_yds">Most Passing Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_avg">Highest Completion Rate (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_eff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_eff">Highest Passing Efficiency (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_peratt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_peratt">Highest Average per Pass (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_percomp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_percomp">Highest Average per Completion (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_pergame">Highest Average Passing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_td">Most Passing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_int") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_int">Most Interceptions Thrown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_long">Longest Pass Completion</option>
							<option>-----Receiving-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_no">Most Passes Caught</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_yds">Most Receiving Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_td">Most Touchdown Receptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_avg">Highest Average Yards Per Catch (Minimum 10 receptions)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_nopergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_nopergame">Most Receptions Per Game (Minumum 8 Games)</option>
							<option>-----Total Offense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_yards">Total Offensive Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_td">Total TDs Responsible For</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggame">Avg. Yards Per Game</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggain") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggain">Avg. Yards Per Play</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_yards">Most All-Purpose Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_pergame">Most All-Purpose Yards Per Game</option>
							<option>-----Scoring-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-points">Most Points Scored</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "touchdowns") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-touchdowns">Most Touchdowns Scored</option>
							<option>-----Extra Points-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickmade") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickmade">Most PAT Kicks Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickatt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickatt">Most PAT Kick Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickpct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickpct">Highest PAT percent (Minimum 5 attempts)</option>
							<option>-----Field Goals-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_att">Most Field Goals Attempted</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_made") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_made">Most Field Goals Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_pct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_pct">Highest Field Goal Percent (minimum 5 attempts)</option>
							<option>-----Punting-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_no">Most Punts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_yds">Most Yards Punting</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_blkd">Most Punts Had Blocked</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_avg">Highest Average Yards Per Punt</option>
							<option>-----Returns-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_no">Most Punt Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_yds">Most Punt Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_td">Most Punt Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_avg">Highest Average Gain Per Punt Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_no">Most Kick Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_yds">Most Kick Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_td">Most Kick Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_avg">Highest Average Yards Per Kick Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_no">Most Fumble Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_yds">Most Fumble Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_td">Most Fumble Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_no">Most Interceptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_yds">Most Interception Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_td">Most Interception Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_avg">Highest Average Gain Per Interception Return</option>
							<option>-----Defense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tot_tack") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tot_tack">Most Tackles</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tot_tack") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tot_tack">Most Unassisted Tackles</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sacks") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-sacks">Most Quarterback Sacks</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tfl") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tfl">Most Tackles For Loss</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ff">Most Fumbles Forced</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr">Most Fumbles Recovered</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "brup") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-brup">Most Pass Breakups</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-blkd">Most Blocked Kicks</option>
		<?php
						}
			else if ($requestedLevel == "CAR"){ ?>
<option>-----Rushing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_att">Most Carries</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_yds">Most Net Yards Rushing</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_avg">Highest Average Yards Per Rush (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_pergame">Highest Average Rushing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_td">Most Rushing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_long">Longest Rush from Scrimmage</option>
							<option>-----Passing-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_att">Most Passing Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_comp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_comp">Most Passing Completions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_yds">Most Passing Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_avg">Highest Completion Rate (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_eff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_eff">Highest Passing Efficiency (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_peratt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_peratt">Highest Average per Pass (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_percomp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_percomp">Highest Average per Completion (Minimum 30 Attempts)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_pergame">Highest Average Passing Yards Per Game (Minimum 8 Games)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_td">Most Passing Touchdowns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_int") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_int">Most Interceptions Thrown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_long">Longest Pass Completion</option>
							<option>-----Receiving-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_no">Most Passes Caught</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_yds">Most Receiving Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_td">Most Touchdown Receptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_avg">Highest Average Yards Per Catch (Minimum 20 receptions)</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_nopergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_nopergame">Most Receptions Per Game (Minumum 10 Games)</option>
							<option>-----Total Offense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_yards">Total Offensive Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_td">Total TDs Responsible For</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggame">Avg. Yards Per Game</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggain") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggain">Avg. Yards Per Play</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_yards">Most All-Purpose Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_pergame">Most All-Purpose Yards Per Game</option>
							<option>-----Scoring-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-points">Most Points Scored</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "touchdowns") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-touchdowns">Most Touchdowns Scored</option>
							<option>-----Extra Points-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickmade") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickmade">Most PAT Kicks Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickatt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickatt">Most PAT Kick Attempts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickpct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickpct">Highest PAT percent (Minimum 5 attempts)</option>
							<option>-----Field Goals-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_att">Most Field Goals Attempted</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_made") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_made">Most Field Goals Made</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_pct") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_pct">Highest Field Goal Percent (minimum 5 attempts)</option>
							<option>-----Punting-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_no">Most Punts</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_yds">Most Yards Punting</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_avg">Highest Average Yards Per Punt</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_inside20") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_inside20">Most Punts downed inside 20</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_long">Longest Punt</option>
							<option>-----Returns-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_long">Longest Punt Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_long">Longest Kick Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_long">Longest Interception Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_long">Longest Fumble Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_no">Most Punt Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_yds">Most Punt Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_td">Most Punt Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_avg">Highest Average Gain Per Punt Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_no">Most Kick Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_yds">Most Kick Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_td">Most Kick Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_avg">Highest Average Yards Per Kick Return</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_no">Most Fumble Returns</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_yds">Most Fumble Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_td">Most Fumble Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_no">Most Interceptions</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_yds">Most Interception Return Yards</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_td">Most Interception Returns for Touchdown</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_avg">Highest Average Gain Per Interception Return</option>
							<option>-----Defense-----</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tot_tack") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tot_tack">Most Tackles</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tackua") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tackua">Most Unassisted Tackles</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tacka") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tacka">Most Assisted Tackles</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sacks") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-sacks">Most Quarterback Sacks</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sack_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-sack_yds">Most Yards from Sacks</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tfl") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tfl">Most Tackles For Loss</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tfl_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tfl_yds">Most Yards from TFLs</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ff">Most Fumbles Forced</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr">Most Fumbles Recovered</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_def") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_def">Most Passes Defended</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "qbh") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-qbh">Most Quarterback Hurries</option>
<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-blkd">Most Blocked Kicks</option>
		<?php
			}
			else {
						?>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_att">Most Carries</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_yds">Most Net Yards Rushing</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_avg">Highest Average Yards Per Rush (Minimum 30 Attempts)</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_pergame") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_pergame">Highest Average Rushing Yards Per Game (Minimum 8 Games)</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_td">Most Rushing Touchdowns</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rush_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rush_long">Longest Rush from Scrimmage</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_att">Most Passing Attempts</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_comp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_comp">Most Passing Completions</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_yds">Most Passing Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_avg">Highest Completion Rate</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_peratt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_peratt">Highest Average per Attempt</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_percomp") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_percomp">Highest Average per Completion</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_td">Most Passing Touchdowns</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_int") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_int">Most Interceptions Thrown</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_eff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_eff">Highest Passing Efficiency</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pass_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pass_long">Longest Pass Completion</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_no">Most Passes Caught</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_yds">Most Receiving Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_avg">Highest Average Per Reception</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_td") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_td">Most Touchdown Receptions</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "rcv_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-rcv_long">Longest Pass Reception</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_plays") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_plays">Total Offensive Plays</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_yards">Total Offensive Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "totoff_avggain") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-totoff_avggain">Avg. Gained Per Play</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_yards") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_yards">Most All-Purpose Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "all_purpose_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-all_purpose_att">Most All-Purpose Attempts</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-points">Most Points Scored</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "touchdowns") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-touchdowns">Most Touchdowns Scored</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickmade") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickmade">Most Extra Points Scored By Kicking</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pat_kickatt") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pat_kickatt">Most Extra Points Attempted by Kicking</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kick_pts") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kick_pts">Most Points Scored By Kicking</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_att") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_att">Most Field Goals Attempted</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_made") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_made">Most Field Goals Made</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fg_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fg_long">Longest Field Goal Made</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_no">Most Punts</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_yds">Most Yards Punting</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_avg">Highest Average Yards Per Punt</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_inside20") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_inside20">Most Punts downed inside 20</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "punt_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-punt_long">Longest Punt</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_long">Longest Punt Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_long">Longest Kick Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_long">Longest Interception Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_long") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_long">Longest Fumble Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_no">Most Punt Returns</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_yds">Most Punt Return Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-pr_avg">Highest Average Gain Per Punt Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_no">Most Kick Returns</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_yds">Most Kick Return Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "kr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-kr_avg">Highest Average Yards Per Kick Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_no">Most Fumble Returns</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_yds">Most Fumble Return Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr_avg">Highest Average Yards Per Fumble Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_no") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_no">Most Interceptions</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_yds") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_yds">Most Interception Return Yards</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ir_avg") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ir_avg">Highest Average Gain Per Interception Return</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tot_tack") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tot_tack">Most Tackles</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sacks") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-sacks">Most Quarterback Sacks</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tfl") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-tfl">Most Tackles For Loss</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ff") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-ff">Most Fumbles Forced</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fr") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-fr">Most Fumbles Recovered</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "brup") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-brup">Most Pass Breakups</option>
						<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blkd") echo "selected" ?> value="fb_<?php echo $_GET['LEVEL']?>-blkd">Most Blocked Kicks</option>
					  </select>
				  </td>
		  </tr>
			<?php
							}
			}
		}
	if($requestedStat != NULL)
	{
		if ($requestedLevel != NULL && $requestedStat != NULL){
		// Determine which table to read. Applicable for football, baseball and softball only. All other sports will be read from one table.
		$statType = explode("_",$requestedStat);
		if ($statType[0] == "rush" || $statType[0] == "pass" || $statType[0] == "rcv")
			$table = "fb_boxscores_offense";
		else if ($statType [0] == "punt" || $statType [0] == "ko" || $statType [0] == "fg" || $statType [0] == "pat" || $statType [0] == "kr" || $statType [0] == "pr")
			$table = "fb_boxscores_specteams";
		else if ($statType [0] == "scoring")
			$table = "fb_boxscores_scoring";
		else
			$table = "fb_boxscores_defense";
		$db_name = "kcmsathl_wvsu";
		// Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
			$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
			var_dump($currentRecords);
			$oldRecords = readOldDatabase($db_name,"fb_oldrecords",$requestedStat,$_GET['LEVEL']);
		// Game Highs. Shouldn't need many custom scripts.
			if ($requestedLevel == "GAM" || $requestedLevel == "TMG"){
				if ($requestedStat == "rush_avg" || $requestedStat == "pass_avg" || $requestedStat == "pass_eff" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp" || $requestedStat == "rcv_avg" || $requestedStat == "punt_avg" || $requestedStat == "pr_avg" || $requestedStat == "ir_avg" || $requestedStat == "fr_avg" || $requestedStat == "kr_avg") ;
					//$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, TRUE); // TRUE is for outputting decimal precision
				else ;
					//$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, FALSE); // FALSE truncates decimals, even though all values are stored as thousandths.
			}//End Game Highs
		//Career Numbers. Will need several Custom scripts.
			elseif ($requestedLevel == "CAR"){
				if ($requestedStat == "rush_avg" || $requestedStat == "pass_avg" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp" || $requestedStat == "rcv_avg"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(AVG(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, TRUE); // TRUE is for outputting decimal precision
				}
				else if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long" || $requestedStat == "kr_long" || $requestedStat == "fr_long" || $requestedStat == "ir_long" || $requestedStat == "pr_long"){
					if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long")
						$requestedTable = "offense";
					else if ($requestedStat == "kr_long" || $requestedStat == "pr_long" || $requestedStat == "fg_long")
						$requestedTable = "specteams";
					else if ($requestedStat == "fr_long" || $requestedStat == "ir_long")
						$requestedTable = "defense";
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(MAX(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_".$requestedTable."` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, FALSE); // TRUE is for outputting decimal precision
				}
				else
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, FALSE); // FALSE truncates decimals, even though all values are stored as thousandths.
			} // End Career
		//Season Numbers. Several Customs.
			elseif ($requestedLevel == "SEA" || $requestedLevel == "TMS"){
				if ($requestedLevel == "TMS"){
					if ($requestedStat == "rush_avg" || $requestedStat == "pass_avg" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp" || $requestedStat == "rcv_avg"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(AVG(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
						//var_dump($currentrecords);
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, TRUE); // TRUE is for outputting decimal precision
				}
				if ($requestedStat == "rush_avg" || $requestedStat == "pass_avg" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp" || $requestedStat == "rcv_avg"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(AVG(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`='TEAM' GROUP BY Name ORDER BY Value DESC");
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, TRUE); // TRUE is for outputting decimal precision
				}
				else if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long" || $requestedStat == "kr_long" || $requestedStat == "fr_long" || $requestedStat == "ir_long" || $requestedStat == "pr_long"){
					if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long")
						$requestedTable = "offense";
					else if ($requestedStat == "kr_long" || $requestedStat == "pr_long" || $requestedStat == "fg_long")
						$requestedTable = "specteams";
					else if ($requestedStat == "fr_long" || $requestedStat == "ir_long")
						$requestedTable = "defense";
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(MAX(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_".$requestedTable."` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`='TEAM' GROUP BY Name ORDER BY Value DESC");
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, FALSE); // TRUE is for outputting decimal precision
				}
				else
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, FALSE);
				}
				else {
				if ($requestedStat == "rush_avg" || $requestedStat == "pass_avg" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp" || $requestedStat == "rcv_avg"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(AVG(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, TRUE); // TRUE is for outputting decimal precision
				}
				if ($requestedStat == "rush_avg" || $requestedStat == "pass_avg" || $requestedStat == "pass_peratt" || $requestedStat == "pass_percomp" || $requestedStat == "rcv_avg"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(AVG(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_offense` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, TRUE); // TRUE is for outputting decimal precision
				}
				else if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long" || $requestedStat == "kr_long" || $requestedStat == "fr_long" || $requestedStat == "ir_long" || $requestedStat == "pr_long"){
					if ($requestedStat == "rush_long" || $requestedStat == "pass_long" || $requestedStat == "rcv_long")
						$requestedTable = "offense";
					else if ($requestedStat == "kr_long" || $requestedStat == "pr_long" || $requestedStat == "fg_long")
						$requestedTable = "specteams";
					else if ($requestedStat == "fr_long" || $requestedStat == "ir_long")
						$requestedTable = "defense";
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ROUND(MAX(Value),3) AS Value,SeasonsActive,Season,IsActive FROM `fb_boxscores_".$requestedTable."` AS boxscores LEFT OUTER JOIN fb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN fb_games as games on boxscores.GameID = games.GameID WHERE `Stat`='$requestedStat' AND `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
					//$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, FALSE); // TRUE is for outputting decimal precision
				}
				//else
					//$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "fb", $requestedLevel, FALSE); // FALSE truncates decimals, even though all values are stored as thousandths.
				}
			}// End Season
		} // End Null Level and Stat
		
		$recordsCode = "";
		echo ("Manual Record Editing - ".$_GET['LEVEL'].": ".$_GET['STAT']);
		$oldResultsString = readOldDatabase("kcmsathl_WVSU","fb_oldrecords",$_GET['STAT'],$_GET['LEVEL']);
		if ($requestedLevel == "CAR")
			editCareerDatabase($oldResultsString,TRUE);
		else if ($requestedLevel == "TMS" || $requestedLevel == "SEA")
			editSeasonDatabase($oldResultsString,TRUE);
		else
			editCurrentDatabase($oldResultsString, TRUE); ?>
		<td>
			Records Code <br>
			<input type="textarea" value = '<?php echo $recordsCode?>'>
		</td>
<?php
	}
	elseif($requestedLevel == "ATH"){
		echo "Active Athlete Administration";
		athleteAdministration("fb","A");
	}
	elseif($requestedLevel == "ATR"){
		echo "Retired Athlete Administration";
		athleteAdministration("fb","R");
	}

	else{
		echo "<h2>Please select Stat Category.</h2>";
?>
		</td>
		
				  <?php
		}
}

function uploadFbBox($ArrayRead)
{
	
	$db_name = "fb";
	

	//Format Date for use in database
	
	$date = $ArrayRead['venue']['@attributes']['date'];
	$time = $ArrayRead['venue']['@attributes']['start'];
	$time_input = strtotime($date);
	$formattedDate = date('Y-m-d',$time_input);
	$locationOfGame = NULL;
	
	if($ArrayRead["team"][0]["@attributes"]["code"] == 1439){ // Look for good guys by NCAA RPI number. Should eliminate variance on how the team name is typed.
		$teamArray = $ArrayRead['team'][0];
		$oppArray = $ArrayRead['team'][1];
		$homeAway = "A";
		$opponentName = $ArrayRead['team'][1]['@attributes']['name'];
		$opponent = $ArrayRead['team'][1]['@attributes']['code'];
		$homeScore = $ArrayRead['team'][0]['linescore']['@attributes']['score']; // Not actually Home Team, but the team we are looking for AKA the good guys.
		$awayScore = $ArrayRead['team'][1]['linescore']['@attributes']['score']; // Opponent, regardless of location
	}
	else if ($ArrayRead["team"][1]["@attributes"]["code"] == 1439){
		$teamArray = $ArrayRead['team'][1];
		$oppArray = $ArrayRead['team'][0];
		$homeAway = "H";
		$opponentName = $ArrayRead['team'][0]['@attributes']['name'];
		$opponent = $ArrayRead['team'][0]['@attributes']['code'];
		$homeScore = $ArrayRead['team'][1]['linescore']['@attributes']['score'];
		$awayScore = $ArrayRead['team'][0]['linescore']['@attributes']['score'];
	}
	if ($homeScore > $awayScore)
	    $result = "W";
	else
	    $result = "L";
	    $explodedDate = explode("-", $formattedDate);
	    $currentYear = $explodedDate[0];
		$season = $currentYear;
	    
	    // Need to Check for duplicate games
	    
	$formattedLocation = checkName($ArrayRead['venue']['@attributes']['location']);
	$GameID = createNewGame ($formattedDate,$time,$homeAway,$opponent,$result,"$homeScore-$awayScore",$ArrayRead['venue']['@attributes']['attend'],$formattedLocation,$season,$db_name);
	
	echo ("<h3>Date of Game: $date | Opponent: $opponentName | RPI: $opponent | GameID: $GameID</h3><h3>Query Results:</h3><br>");
	if(checkOpponent($opponent,$opponentName))
		echo "<h2>Opponent RPI Added to database</h2>";
	
	$teamTotals = $teamArray['totals'];
	
	createPlayerStatFB("TEAM","points",$teamArray['linescore']['@attributes']['score'],"scoring",$GameID);
	createPlayerStatFB("TEAM","totoff_plays",$teamTotals['@attributes']['totoff_plays'],"offense",$GameID);
	createPlayerStatFB("TEAM","totoff_yards",$teamTotals['@attributes']['totoff_yards'],"offense",$GameID);
	$totoff_avg = $teamTotals['@attributes']['totoff_yards'] / $teamTotals['@attributes']['totoff_plays'];
	createPlayerStatFB("TEAM","totoff_avg",$totoff_avg,"offense",$GameID);
	createPlayerStatFB("TEAM","firstdowns_no",$teamTotals['firstdowns']['@attributes']['no'],"offense",$GameID);
	createPlayerStatFB("TEAM","rush_att",$teamTotals['rush']['@attributes']['att'],"offense",$GameID);
	createPlayerStatFB("TEAM","rush_yds",$teamTotals['rush']['@attributes']['yds'],"offense",$GameID);
	createPlayerStatFB("TEAM","rush_td",$teamTotals['rush']['@attributes']['td'],"offense",$GameID);
	createPlayerStatFB("TEAM","rush_long",$teamTotals['rush']['@attributes']['long'],"offense",$GameID);
	createPlayerStatFB("TEAM","rush_avg",$teamTotals['rush']['@attributes']['yds']/$teamTotals['rush']['@attributes']['att'],"offense",$GameID);
	createPlayerStatFB("TEAM","pass_comp",$teamTotals['pass']['@attributes']['comp'],"offense",$GameID);
	createPlayerStatFB("TEAM","pass_att",$teamTotals['pass']['@attributes']['att'],"offense",$GameID);
	createPlayerStatFB("TEAM","pass_yds",$teamTotals['pass']['@attributes']['yds'],"offense",$GameID);
	createPlayerStatFB("TEAM","pass_td",$teamTotals['pass']['@attributes']['td'],"offense",$GameID);
	$pass_avg = $teamTotals['pass']['@attributes']['comp'] / $teamTotals['pass']['@attributes']['att'];
	createPlayerStatFB("TEAM","pass_avg",$pass_avg,"offense",$GameID);
	$pass_peratt = $teamTotals['pass']['@attributes']['yds'] / $teamTotals['pass']['@attributes']['att'];
	createPlayerStatFB("TEAM","pass_peratt",$pass_peratt,"offense",$GameID);
	$pass_percomp = $teamTotals['pass']['@attributes']['yds'] / $teamTotals['pass']['@attributes']['comp'];
	createPlayerStatFB("TEAM","pass_percomp",$pass_percomp,"offense",$GameID);
	$pass_eff = ((8.4 * $teamTotals['pass']['@attributes']['yds']) + (330 * $teamTotals['pass']['@attributes']['td']) + (100 * $teamTotals['pass']['@attributes']['comp']) - (200 * $teamTotals['pass']['@attributes']['int'])) / $teamTotals['pass']['@attributes']['att'];
	createPlayerStatFB("TEAM","pass_eff",$pass_eff,"offense",$GameID);
	if (array_key_exists("scoring",$teamTotals)){
		if (array_key_exists("td",$teamTotals['scoring']['@attributes']))
			createPlayerStatFB("TEAM","touchdowns",$teamTotals['scoring']['@attributes']['td'],"scoring",$GameID);
	}
	createPlayerStatFB("TEAM","tot_tack",$teamTotals['defense']['@attributes']['tot_tack'],"defense",$GameID);
	if (!array_key_exists("tflua",$teamTotals['defense']['@attributes']))
		$tflua = 0;
	else $tflua = $teamTotals['defense']['@attributes']['tflua'];
	if (!array_key_exists("tfla",$teamTotals['defense']['@attributes']))
		$tfla = 0;
	else $tfla = $teamTotals['defense']['@attributes']['tfla'];
	createPlayerStatFB("TEAM","tfl",($tflua+($tfla/2)),"defense",$GameID);
	createPlayerStatFB("TEAM","sacks",$teamTotals['defense']['@attributes']['sacks'],"defense",$GameID);
	if (array_key_exists('ff', $teamTotals['defense']['@attributes']))
	createPlayerStatFB("TEAM","ff",$teamTotals['defense']['@attributes']['ff'],"defense",$GameID);
	if (array_key_exists('fr', $teamTotals['defense']['@attributes']))
		createPlayerStatFB("TEAM","fr",$teamTotals['defense']['@attributes']['fr'],"defense",$GameID);
	if (array_key_exists('brup', $teamTotals['defense']['@attributes']))
		createPlayerStatFB("TEAM","brup",$teamTotals['defense']['@attributes']['brup'],"defense",$GameID);
	if (array_key_exists('blkd', $teamTotals['defense']['@attributes']))
		createPlayerStatFB("TEAM","blkd",$teamTotals['defense']['@attributes']['blkd'],"defense",$GameID);
	
	createPlayerStatFB("TEAM","kr_no",$teamTotals['kr']['@attributes']['no'],"specteams",$GameID);
	createPlayerStatFB("TEAM","kr_yds",$teamTotals['kr']['@attributes']['yds'],"specteams",$GameID);
	$kr_avg = $teamTotals['kr']['@attributes']['yds'] / $teamTotals['kr']['@attributes']['no'];
	createPlayerStatFB("TEAM","kr_avg",$kr_avg,"specteams",$GameID);
	if (array_key_exists('pr',$teamTotals)){
		createPlayerStatFB("TEAM","pr_no",$teamTotals['pr']['@attributes']['no'],"specteams",$GameID);
		createPlayerStatFB("TEAM","pr_yds",$teamTotals['pr']['@attributes']['yds'],"specteams",$GameID);
		$pr_yds = $teamTotals['pr']['@attributes']['yds'];
		$pr_no = $teamTotals['pr']['@attributes']['no'];
		if ($teamTotals['pr']['@attributes']['no'] == 0)
			$pr_avg = 0;
		else
			$pr_avg = $teamTotals['pr']['@attributes']['yds'] / $teamTotals['pr']['@attributes']['no'];
		createPlayerStatFB("TEAM","pr_avg",$pr_avg,"specteams",$GameID);
	}
	else{
		$pr_yds = 0;
		$pr_no = 0;
	}
	if (array_key_exists('ir', $teamTotals)){
		createPlayerStatFB("TEAM","ir_no",$teamTotals['ir']['@attributes']['no'],"defense",$GameID);
		createPlayerStatFB("TEAM","ir_yds",$teamTotals['ir']['@attributes']['yds'],"defense",$GameID);
		$ir_yds = $teamTotals['ir']['@attributes']['yds'];
		$ir_no = $teamTotals['ir']['@attributes']['no'];
		if ($teamTotals['ir']['@attributes']['no'] == 0)
			$ir_avg = 0;
		else
			$ir_avg = $teamTotals['ir']['@attributes']['yds'] / $teamTotals['ir']['@attributes']['no'];
		createPlayerStatFB("TEAM","ir_avg",$ir_avg,"defense",$GameID);
	}
	else{
		$ir_yds = 0;
		$ir_no = 0;
	}
	
	if (array_key_exists('fr', $teamTotals)){
		createPlayerStatFB("TEAM","fr_no",$teamTotals['fr']['@attributes']['no'],"defense",$GameID);
		createPlayerStatFB("TEAM","fr_yds",$teamTotals['fr']['@attributes']['yds'],"defense",$GameID);
		$fr_yds = $teamTotals['fr']['@attributes']['yds'];
		$fr_no = $teamTotals['fr']['@attributes']['no'];
		if ($teamTotals['fr']['@attributes']['no'] == 0)
			$fr_avg = 0;
		else
			$fr_avg = $teamTotals['fr']['@attributes']['yds'] / $teamTotals['fr']['@attributes']['no'];
		createPlayerStatFB("TEAM","fr_avg",$fr_avg,"defense",$GameID);
	}
	else{
		$fr_yds = 0;
		$fr_no = 0;
	}
	
	$allPurposeYards = $teamTotals['rush']['@attributes']['yds'] + $teamTotals['rcv']['@attributes']['yds'] + $pr_yds + $teamTotals['kr']['@attributes']['yds'] + $ir_yds + $fr_yds;
	createPlayerStatFB("TEAM","all_purpose_yards",$allPurposeYards,"offense",$GameID);
	
	$allPurposeAttempts = $teamTotals['rush']['@attributes']['att'] + $teamTotals['rcv']['@attributes']['no'] + $pr_no + $teamTotals['kr']['@attributes']['no'] + $ir_no + $fr_no;
	createPlayerStatFB("TEAM","all_purpose_att",$allPurposeAttempts,"offense",$GameID);
	
	//Opposing Team Stats
	
	
	//End Team Stats
	
	$numOfPlayers = sizeof($teamArray['player']);
	
	for($currPlayer = 0; $currPlayer < $numOfPlayers; $currPlayer ++):
	
	$currArray = $teamArray['player'][$currPlayer];
	$athleteName = $currArray['@attributes']['name'];
	
	if ($athleteName == "TEAM")
		continue;
	
	if ($currArray['@attributes']['gp'] == 1){
		createPlayerStatFB("$athleteName","gp",1,"offense",$GameID);
		createPlayerStatFB("$athleteName","gp",1,"defense",$GameID);
		createPlayerStatFB("$athleteName","gp",1,"specteams",$GameID);
	}
	if (array_key_exists('rush',$currArray)){
		createPlayerStatFB("$athleteName","rush_att",$currArray['rush']['@attributes']['att'],"offense",$GameID);
		createPlayerStatFB("$athleteName","rush_yds",$currArray['rush']['@attributes']['yds'],"offense",$GameID,$season,$currArray['rush']['@attributes']['att']." carries");
		createPlayerStatFB("$athleteName","rush_td",$currArray['rush']['@attributes']['td'],"offense",$GameID);
		createPlayerStatFB("$athleteName","rush_long",$currArray['rush']['@attributes']['long'],"offense",$GameID);
		if ($currArray['rush']['@attributes']['att'] >= 10)
			createPlayerStatFB("$athleteName","rush_avg",$currArray['rush']['@attributes']['yds']/$currArray['rush']['@attributes']['att'],"offense",$GameID,$season,$currArray['rush']['@attributes']['att']."-".$currArray['rush']['@attributes']['yds']);
	}
	if (array_key_exists('pass', $currArray)){
		createPlayerStatFB("$athleteName","pass_att",$currArray['pass']['@attributes']['att'],"offense",$GameID);
		createPlayerStatFB("$athleteName","pass_comp",$currArray['pass']['@attributes']['comp'],"offense",$GameID);
		createPlayerStatFB("$athleteName","pass_int",$currArray['pass']['@attributes']['int'],"offense",$GameID);
		createPlayerStatFB("$athleteName","pass_yds",$currArray['pass']['@attributes']['yds'],"offense",$GameID);
		createPlayerStatFB("$athleteName","pass_td",$currArray['pass']['@attributes']['td'],"offense",$GameID);
		createPlayerStatFB("$athleteName","pass_long",$currArray['pass']['@attributes']['long'],"offense",$GameID);
		if ($currArray['pass']['@attributes']['att'] >=10)
			createPlayerStatFB("$athleteName","pass_avg",$currArray['pass']['@attributes']['comp']/$currArray['pass']['@attributes']['att'],"offense",$GameID,$season,$currArray['pass']['@attributes']['comp']."-".$currArray['pass']['@attributes']['att']);
		if ($currArray['pass']['@attributes']['att'] >=10){
			$pass_eff = ((8.4 * $currArray['pass']['@attributes']['yds']) + (330 * $currArray['pass']['@attributes']['td']) + (100 * $currArray['pass']['@attributes']['comp']) - (200 * $currArray['pass']['@attributes']['int'])) / $currArray['pass']['@attributes']['att'];
			createPlayerStatFB("$athleteName","pass_eff",$pass_eff,"offense",$GameID);
		}
		if ($currArray['pass']['@attributes']['att'] >=10){
			$pass_peratt = $currArray['pass']['@attributes']['yds'] / $currArray['pass']['@attributes']['att'];
			createPlayerStatFB("$athleteName","pass_peratt",$pass_peratt,"offense",$GameID,$season,$currArray['pass']['@attributes']['att']."-".$currArray['pass']['@attributes']['yds']);
		}
		if ($currArray['pass']['@attributes']['comp'] >=5){
			$pass_percomp = $currArray['pass']['@attributes']['yds'] / $currArray['pass']['@attributes']['comp'];
			createPlayerStatFB("$athleteName","pass_percomp",$pass_percomp,"offense",$GameID,$season,$currArray['pass']['@attributes']['comp']."-".$currArray['pass']['@attributes']['yds']);
		}
	}
	if (array_key_exists('rcv', $currArray)){
		createPlayerStatFB("$athleteName","rcv_no",$currArray['rcv']['@attributes']['no'],"offense",$GameID);
		createPlayerStatFB("$athleteName","rcv_yds",$currArray['rcv']['@attributes']['yds'],"offense",$GameID,$season,$currArray['rcv']['@attributes']['no']." receptions");
		createPlayerStatFB("$athleteName","rcv_td",$currArray['rcv']['@attributes']['td'],"offense",$GameID);
		createPlayerStatFB("$athleteName","rcv_long",$currArray['rcv']['@attributes']['long'],"offense",$GameID);
		if ($currArray['rcv']['@attributes']['no'] >=3)
			createPlayerStatFB("$athleteName","rcv_avg",$currArray['rcv']['@attributes']['yds']/$currArray['rcv']['@attributes']['no'],"offense",$GameID,$season,$currArray['rcv']['@attributes']['no']."-".$currArray['rcv']['@attributes']['yds']);
	}
	if (array_key_exists('punt', $currArray)){
		createPlayerStatFB("$athleteName","punt_no",$currArray['punt']['@attributes']['no'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","punt_yds",$currArray['punt']['@attributes']['yds'],"specteams",$GameID,$season,$currArray['punt']['@attributes']['no']." punts");
		createPlayerStatFB("$athleteName","punt_long",$currArray['punt']['@attributes']['long'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","punt_avg",$currArray['punt']['@attributes']['avg'],"specteams",$GameID,$season,$currArray['punt']['@attributes']['no']."-".$currArray['punt']['@attributes']['yds']);
		createPlayerStatFB("$athleteName","punt_inside20",$currArray['punt']['@attributes']['inside20'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","punt_blkd",$currArray['punt']['@attributes']['blkd'],"specteams",$GameID);
	}
	if (array_key_exists('pat', $currArray)){
		if(array_key_exists('kickatt',$currArray['pat']['@attributes']))
			createPlayerStatFB("$athleteName","pat_kickatt",$currArray['pat']['@attributes']['kickatt'],"specteams",$GameID);
		if(array_key_exists('kickmade',$currArray['pat']['@attributes']))
			createPlayerStatFB("$athleteName","pat_kickmade",$currArray['pat']['@attributes']['kickmade'],"specteams",$GameID);
	}
	if (array_key_exists('fg', $currArray)){
		createPlayerStatFB("$athleteName","fg_made",$currArray['fg']['@attributes']['made'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","fg_att",$currArray['fg']['@attributes']['att'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","fg_long",$currArray['fg']['@attributes']['long'],"specteams",$GameID);
	}
	if (array_key_exists('defense', $currArray)){
		if (array_key_exists('tackua', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['tackua'] > 0)
			createPlayerStatFB("$athleteName","tackua",$currArray['defense']['@attributes']['tackua'],"defense",$GameID);
		if (array_key_exists('tacka', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['tacka'] > 0)
			createPlayerStatFB("$athleteName","tacka",$currArray['defense']['@attributes']['tacka'],"defense",$GameID);
		if (array_key_exists('tflua', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['tflua'] > 0)
			createPlayerStatFB("$athleteName","tflua",$currArray['defense']['@attributes']['tflua'],"defense",$GameID);
		if (array_key_exists('tfla', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['tfla'] > 0)
			createPlayerStatFB("$athleteName","tfla",$currArray['defense']['@attributes']['tfla'],"defense",$GameID);
		if (array_key_exists('tflyds', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['tflyds'] > 0)
			createPlayerStatFB("$athleteName","tflyds",$currArray['defense']['@attributes']['tflyds'],"defense",$GameID);
		if (array_key_exists('sacks', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['sacks']> 0)
			createPlayerStatFB("$athleteName","sack",$currArray['defense']['@attributes']['sacks'],"defense",$GameID);
		if (array_key_exists('sackyds', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['sackyds']> 0)
			createPlayerStatFB("$athleteName","sackyds",$currArray['defense']['@attributes']['sackyds'],"defense",$GameID);
		if (array_key_exists('ff', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['ff']> 0)
			createPlayerStatFB("$athleteName","ff",$currArray['defense']['@attributes']['ff'],"defense",$GameID);
		if (array_key_exists('fr', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['fr']> 0)
			createPlayerStatFB("$athleteName","fr",$currArray['defense']['@attributes']['fr'],"defense",$GameID);
		if (array_key_exists('brup', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['brup']> 0)
			createPlayerStatFB("$athleteName","brup",$currArray['defense']['@attributes']['brup'],"defense",$GameID);
		if (array_key_exists('blkd', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['blkd']> 0)
			createPlayerStatFB("$athleteName","blkd",$currArray['defense']['@attributes']['blkd'],"defense",$GameID);
		if (array_key_exists('qbh', $currArray['defense']['@attributes']) && $currArray['defense']['@attributes']['qbh']> 0)
			createPlayerStatFB("$athleteName","qbh",$currArray['defense']['@attributes']['qbh'],"defense",$GameID);
	}
	if(array_key_exists('kr',$currArray)){
		createPlayerStatFB("$athleteName","kr_no",$currArray['kr']['@attributes']['no'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","kr_yds",$currArray['kr']['@attributes']['yds'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","kr_td",$currArray['kr']['@attributes']['td'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","kr_long",$currArray['kr']['@attributes']['long'],"specteams",$GameID);
		if($currArray['kr']['@attributes']['no'] > 0)
			createPlayerStatFB("$athleteName","kr_avg",$currArray['kr']['@attributes']['yds']/$currArray['kr']['@attributes']['no'],"specteams",$GameID);
	}
	if(array_key_exists('pr',$currArray)){
		createPlayerStatFB("$athleteName","pr_no",$currArray['pr']['@attributes']['no'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","pr_yds",$currArray['pr']['@attributes']['yds'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","pr_td",$currArray['pr']['@attributes']['td'],"specteams",$GameID);
		createPlayerStatFB("$athleteName","pr_long",$currArray['pr']['@attributes']['long'],"specteams",$GameID);
		if($currArray['pr']['@attributes']['no'] > 0)
			createPlayerStatFB("$athleteName","pr_avg",$currArray['pr']['@attributes']['yds']/$currArray['pr']['@attributes']['no'],"specteams",$GameID);
	}
	if(array_key_exists('ir',$currArray)){
		createPlayerStatFB("$athleteName","ir_no",$currArray['ir']['@attributes']['no'],"defense",$GameID);
		createPlayerStatFB("$athleteName","ir_yds",$currArray['ir']['@attributes']['yds'],"defense",$GameID);
		createPlayerStatFB("$athleteName","ir_td",$currArray['ir']['@attributes']['td'],"defense",$GameID);
		createPlayerStatFB("$athleteName","ir_long",$currArray['ir']['@attributes']['long'],"defense",$GameID);
		if($currArray['ir']['@attributes']['no'] > 0)
			createPlayerStatFB("$athleteName","ir_avg",$currArray['ir']['@attributes']['yds']/$currArray['ir']['@attributes']['no'],"defense",$GameID);
	}
	if(array_key_exists('fr',$currArray)){
		createPlayerStatFB("$athleteName","fr_no",$currArray['fr']['@attributes']['no'],"defense",$GameID);
		createPlayerStatFB("$athleteName","fr_yds",$currArray['fr']['@attributes']['yds'],"defense",$GameID);
		createPlayerStatFB("$athleteName","fr_td",$currArray['fr']['@attributes']['td'],"defense",$GameID);
		createPlayerStatFB("$athleteName","fr_long",$currArray['fr']['@attributes']['long'],"defense",$GameID);
		if($currArray['fr']['@attributes']['no'] > 0)
			createPlayerStatFB("$athleteName","fr_avg",$currArray['fr']['@attributes']['yds']/$currArray['fr']['@attributes']['no'],"defense",$GameID);
	}
	endfor;
}

function createPlayerStatFB($name, $stat, $value, $table, $gameID, $season = "", $remarks = "")
{
	$config = parse_ini_file('config.ini');
	$fb_milestones = parse_ini_file('fb.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
	if ($name == "TEAM"){
		$athleteID = 0;
		$statType = "TMG";
	}
	else{
		$statType = "GAM";
		$name = checkName($name);
		$athlQuery = "SELECT * FROM `fb_athletes` WHERE `Name`='$name' AND `IsActive`='1'"; // Search for athlete in database. If found, get Athlete ID, otherwise create a new athlete in the database and assign an ID number to him/her.
		$athlResults = mysqli_query($conn, $athlQuery);
		$finalAthlResult = mysqli_fetch_assoc($athlResults);
		if (mysqli_num_rows($athlResults) > 0)
			$athleteID = $finalAthlResult['AthleteID'];
		else
			$athleteID = createAthlete($name, 'fb', $season);
	}
	
	//If duplicate is found, update existing record instead of creating an new one. This will reduce the numbers in the records database which will start to get massive quickly.
	$dupQuery = "SELECT * FROM `fb_boxscores_".$table."` WHERE `GameID`='$gameID' AND `Stat`='$stat' AND `AthleteID`='$athleteID' ORDER BY `EntryID` DESC";
	$dupResults = mysqli_query($conn, $dupQuery);
	$finalDupResult = mysqli_fetch_assoc($dupResults);
	if(mysqli_num_rows($dupResults)>0)
		$duplicateResult = $finalDupResult['EntryID'];
	else
		$duplicateResult = -1;
	
	
	//Prepare to insert into SQL datatbase
	// Insert Career check into this area. Season Check is already implemented.
	// Also need to add a comparative function in here at this point that shows if a player has cracked the Top 10 in either game, season, or career stats.
	// The backbone of which has already been written and is sitting in the database_old file.
	//var_dump($fb_milestones);
	//echo "<br><br>";
	
	if ($duplicateResult < 0){ // New Stat being uploaded.
		//Season Milestone Check. Does check before adding new stats. 
		if ($name != "TEAM"){
			switch ($stat){
				case ('rush_yds'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_rush_yds']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_rush_yds']){
						$newSeasonTotal = $seasonTotal + $value;
						if ($newSeasonTotal > $fb_milestones['sea_rush_yds'])
							echo "<h4>***** Season Milestone Achieved for $name in Rushing Yards: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Rushing Yards: $newSeasonTotal -----</h5>";
					}
					break;

				case ('pass_yds'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if ($seasonTotal < $fb_milestones['sea_pass_yds'][0]){
						if (($seasonTotal / $fb_milestones['sea_pass_yds'][0]) * 100 > $fb_milestones['percent']){
							$newSeasonTotal = $seasonTotal + $value;
							if ($newSeasonTotal > $fb_milestones['sea_pass_yds'][0])
								echo "<h4>***** Season Milestone Achieved for $name in Passing Yards: $newSeasonTotal *****</h4>";
							else
								echo "<h5>----- Season Milestone Approaching for $name in Passing Yards: $newSeasonTotal -----</h5>";
						}
					}
					elseif ($seasonTotal < $fb_milestones['sea_pass_yds'][1]){
						if (($seasonTotal / $fb_milestones['sea_pass_yds'][1]) * 100 > $fb_milestones['percent']){
							$newSeasonTotal = $seasonTotal + $value;
							if ($newSeasonTotal > $fb_milestones['sea_pass_yds'][1])
								echo "<h4>***** Season Milestone Achieved for $name in Passing Yards: $newSeasonTotal *****</h4>";
							else
								echo "<h5>----- Season Milestone Approaching for $name in Passing Yards: $newSeasonTotal -----</h5>";
						}
					}
					break;

				case ('rcv_yds'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_rcv_yds']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_rcv_yds']){
						$newSeasonTotal = $seasonTotal + $value;
						if ($newSeasonTotal > $fb_milestones['sea_rcv_yds'])
							echo "<h4>***** Season Milestone Achieved for $name in Receiving Yards: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Receiving Yards: $newSeasonTotal -----</h5>";
					}
					break;
				case ('tot_tack'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_tot_tack']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_tot_tack']){
						$newSeasonTotal = $seasonTotal + $value;
						if ($newSeasonTotal > $fb_milestones['sea_tot_tack'])
							echo "<h4>***** Season Milestone Achieved for $name in Tackles: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Tackles: $newSeasonTotal -----</h5>";
					}
					break;

				case('sacks'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_sacks']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_sacks']){
						$newSeasonTotal = $seasonTotal + $value;
						if ($newSeasonTotal > $fb_milestones['sea_sacks'])
							echo "<h4>***** Season Milestone Achieved for $name in Sacks: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Sacks: $newSeasonTotal -----</h5>";
					}
					break;

				case('tfl'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_tfl']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_tfl']){
						$newSeasonTotal = $seasonTotal + $value;
						if ($newSeasonTotal > $fb_milestones['sea_tfl'])
							echo "<h4>***** Season Milestone Achieved for $name in TFLs (Tackles For Loss): $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in TFLs (Tackles For Loss): $newSeasonTotal -----</h5>";
					}
					break;

				case('fg_made'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_fg_made']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_fg_made']){
						$newSeasonTotal = $seasonTotal + $value;
						if ($newSeasonTotal > $fb_milestones['sea_fg_made'])
							echo "<h4>***** Season Milestone Achieved for $name in Field Goals Made: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Field Goals Made: $newSeasonTotal -----</h5>";
					}
					break;

				default:
					break;
			}
	}
		$sql = "INSERT INTO `fb_boxscores_".$table."`(`Value`, `AthleteID`, `GameID`, `Remarks`, `Stat`, `StatType`) VALUES ('$value','$athleteID','$gameID','$remarks','$stat','$statType')";

		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
	}
	else{
		$sql = "UPDATE `fb_boxscores_".$table."` SET `Value`='$value', `Remarks`='$remarks' WHERE `EntryID`='$duplicateResult'";
		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
		if ($name != "TEAM"){ //Season Milestone Check. Does check after updating old stats.
			switch ($stat){
				case ('rush_yds'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_rush_yds']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_rush_yds']){
						$newSeasonTotal = $seasonTotal;
						if ($newSeasonTotal > $fb_milestones['sea_rush_yds'])
							echo "<h4>***** Season Milestone Achieved for $name in Rushing Yards: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Rushing Yards: $newSeasonTotal -----</h5>";
					}
					break;

				case ('pass_yds'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if ($seasonTotal < $fb_milestones['sea_pass_yds'][0]){
						if (($seasonTotal / $fb_milestones['sea_pass_yds'][0]) * 100 > $fb_milestones['percent']){
							$newSeasonTotal = $seasonTotal;
							if ($newSeasonTotal > $fb_milestones['sea_pass_yds'][0])
								echo "<h4>***** Season Milestone Achieved for $name in Passing Yards: $newSeasonTotal *****</h4>";
							else
								echo "<h5>----- Season Milestone Approaching for $name in Passing Yards: $newSeasonTotal -----</h5>";
						}
					}
					elseif ($seasonTotal < $fb_milestones['sea_pass_yds'][1]){
						if (($seasonTotal / $fb_milestones['sea_pass_yds'][1]) * 100 > $fb_milestones['percent']){
							$newSeasonTotal = $seasonTotal;
							if ($newSeasonTotal > $fb_milestones['sea_pass_yds'][1])
								echo "<h4>***** Season Milestone Achieved for $name in Passing Yards: $newSeasonTotal *****</h4>";
							else
								echo "<h5>----- Season Milestone Approaching for $name in Passing Yards: $newSeasonTotal -----</h5>";
						}
					}
					break;

				case ('rcv_yds'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_rcv_yds']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_rcv_yds']){
						$newSeasonTotal = $seasonTotal;
						if ($newSeasonTotal > $fb_milestones['sea_rcv_yds'])
							echo "<h4>***** Season Milestone Achieved for $name in Receiving Yards: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Receiving Yards: $newSeasonTotal -----</h5>";
					}
					break;
				case ('tot_tack'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_tot_tack']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_tot_tack']){
						$newSeasonTotal = $seasonTotal;
						if ($newSeasonTotal > $fb_milestones['sea_tot_tack'])
							echo "<h4>***** Season Milestone Achieved for $name in Tackles: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Tackles: $newSeasonTotal -----</h5>";
					}
					break;

				case('sacks'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_sacks']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_sacks']){
						$newSeasonTotal = $seasonTotal;
						if ($newSeasonTotal > $fb_milestones['sea_sacks'])
							echo "<h4>***** Season Milestone Achieved for $name in Sacks: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Sacks: $newSeasonTotal -----</h5>";
					}
					break;

				case('tfl'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_tfl']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_tfl']){
						$newSeasonTotal = $seasonTotal;
						if ($newSeasonTotal > $fb_milestones['sea_tfl'])
							echo "<h4>***** Season Milestone Achieved for $name in TFLs (Tackles For Loss): $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in TFLs (Tackles For Loss): $newSeasonTotal -----</h5>";
					}
					break;

				case('fg_made'):
					$seasonTotal = findSeasonStats($athleteID,'fb',$stat,$season,'fb_boxscores_offense');
					if (($seasonTotal / $fb_milestones['sea_fg_made']) * 100 > $fb_milestones['percent'] && $seasonTotal < $fb_milestones['sea_fg_made']){
						$newSeasonTotal = $seasonTotal;
						if ($newSeasonTotal > $fb_milestones['sea_fg_made'])
							echo "<h4>***** Season Milestone Achieved for $name in Field Goals Made: $newSeasonTotal *****</h4>";
						else
							echo "<h5>----- Season Milestone Approaching for $name in Field Goals Made: $newSeasonTotal -----</h5>";
					}
					break;

				default:
					break;
			}
		}
	}
}