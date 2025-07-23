<?php
/**
 * Main function to handle women's basketball records display
 * Processes GET parameters for stat type and level (game/season/career/team stats)
 */
function wbbRecords(){
    // Database name constant
    $db_name = "kcmsathl_wvsu";
    
    // Get request parameters
    $requestedStat = array_key_exists('STAT', $_GET) ? $_GET['STAT'] : NULL;
	$requestedLevel = array_key_exists('LEVEL', $_GET) ? $_GET['LEVEL'] : NULL;
?>
<table width="100%">
    <tr>
        <td valign="top" width="85%">
<?php
    // Only process if both stat and level are provided
    if ($requestedLevel != NULL && $requestedStat != NULL){
        // Determine stat type and set table
        $statType = explode("_", $requestedStat);
        $table = "wbb_boxscores";
        
        // Read records from both current and old databases
        $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $requestedLevel);
        $oldRecords = readOldDatabase($db_name, "wbb_oldrecords", $requestedStat, $requestedLevel);

        // Handle different stat/level combinations
        if ($requestedLevel == "GAM" || $requestedLevel == "TMG"){
            if (in_array($requestedStat, ["fgp", "fgp3", "ftp"])) {
                printCurrentRecordsDatabase($currentRecords, $oldRecords, "wbb", 1);
            } else {
                printCurrentRecordsDatabase($currentRecords, $oldRecords, "wbb", FALSE);
            }
        } 
        elseif ($requestedLevel == "TMS"){
            if ($requestedStat == "fgp"){
				// Calculate team season field goal percentage
				$currentRecords = readCurrentDatabase(
					$db_name,
					$table,
					$requestedStat,
					$_GET['LEVEL'],
					"SELECT 
						Name,
						FieldGoalsMade,
						FieldGoalsAttempted,
						FieldGoalPercentage AS Value,
						SeasonsActive,
						Season,
						IsActive 
					 FROM (
						SELECT 
							Name,
							SUM(CASE WHEN `Stat` = 'fgm' THEN Value ELSE 0 END) AS FieldGoalsMade,
							SUM(CASE WHEN `Stat` = 'fga' THEN Value ELSE 0 END) AS FieldGoalsAttempted,
							ROUND(
								SUM(CASE WHEN `Stat` = 'fgm' THEN Value ELSE 0 END) / 
								SUM(CASE WHEN `Stat` = 'fga' THEN Value ELSE 0 END), 
								3
							) AS FieldGoalPercentage,
							SeasonsActive,
							Season,
							IsActive 
						FROM `wbb_boxscores` AS boxscores
						LEFT OUTER JOIN wbb_athletes AS athletes 
							ON boxscores.AthleteID = athletes.AthleteID
						RIGHT OUTER JOIN wbb_games AS games 
							ON boxscores.GameID = games.GameID
						WHERE `Name` = 'TEAM'
						GROUP BY Name, Season
						ORDER BY FieldGoalPercentage DESC
					) AS x"
				);
                printSeasonDatabase($currentRecords, $oldRecords, "wbb", 1);
            }
			elseif ($requestedStat == "fgp3") {
				$currentRecords = readCurrentDatabase(
					$db_name,
					$table,
					$requestedStat,
					$_GET['LEVEL'],
					"SELECT 
						Name,
						FieldGoalsMade,
						FieldGoalsAttempted,
						FieldGoalPercentage AS Value,
						SeasonsActive,
						Season,
						IsActive 
					 FROM (
						SELECT 
							Name,
							SUM(CASE WHEN `Stat` = 'fgm3' THEN Value ELSE 0 END) AS FieldGoalsMade,
							SUM(CASE WHEN `Stat` = 'fga3' THEN Value ELSE 0 END) AS FieldGoalsAttempted,
							ROUND(
								SUM(CASE WHEN `Stat` = 'fgm3' THEN Value ELSE 0 END) / 
								SUM(CASE WHEN `Stat` = 'fga3' THEN Value ELSE 0 END), 
								3
							) AS FieldGoalPercentage,
							SeasonsActive,
							Season,
							IsActive 
						FROM `wbb_boxscores` AS boxscores
						LEFT OUTER JOIN wbb_athletes AS athletes 
							ON boxscores.AthleteID = athletes.AthleteID
						RIGHT OUTER JOIN wbb_games AS games 
							ON boxscores.GameID = games.GameID
						WHERE `Name` = 'TEAM'
						GROUP BY Name, Season
						ORDER BY FieldGoalPercentage DESC
					) AS x"
				);
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", 1); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "ftp") {
				$currentRecords = readCurrentDatabase(
					$db_name,
					$table,
					$requestedStat,
					$_GET['LEVEL'],
					"SELECT 
						Name,
						FieldGoalsMade,
						FieldGoalsAttempted,
						FieldGoalPercentage AS Value,
						SeasonsActive,
						Season,
						IsActive 
					 FROM (
						SELECT 
							Name,
							SUM(CASE WHEN `Stat` = 'ftm' THEN Value ELSE 0 END) AS FieldGoalsMade,
							SUM(CASE WHEN `Stat` = 'fta' THEN Value ELSE 0 END) AS FieldGoalsAttempted,
							ROUND(
								SUM(CASE WHEN `Stat` = 'ftm' THEN Value ELSE 0 END) / 
								SUM(CASE WHEN `Stat` = 'fta' THEN Value ELSE 0 END), 
								3
							) AS FieldGoalPercentage,
							SeasonsActive,
							Season,
							IsActive 
						FROM `wbb_boxscores` AS boxscores
						LEFT OUTER JOIN wbb_athletes AS athletes 
							ON boxscores.AthleteID = athletes.AthleteID
						RIGHT OUTER JOIN wbb_games AS games 
							ON boxscores.GameID = games.GameID
						WHERE `Name` = 'TEAM'
						GROUP BY Name, Season
						ORDER BY FieldGoalPercentage DESC
					) AS x"
				);
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", 1); // TRUE is for decimal precision
			}
			elseif ($requestedStat == "points_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "treb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "oreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "dreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "ast_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "blk_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "stl_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "def_points"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='points_def' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
				$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",$requestedStat,$_GET['LEVEL'],"ASC");
				//echo "Old Records ASC";
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2,"ASC");
			}
			elseif ($requestedStat == "def_fgp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm_def' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='fga_def' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='fgm_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga_def' THEN Value ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
				$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",$requestedStat,$_GET['LEVEL'],"ASC");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",1,"ASC");
			}
			elseif ($requestedStat == "def_fgp3"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3_def' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='fga3_def' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='fgm3_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3_def' THEN Value ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
				$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",$requestedStat,$_GET['LEVEL'],"ASC");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",1,"ASC");
			}
			elseif ($requestedStat == "def_treb"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
				$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",$requestedStat,$_GET['LEVEL'],"ASC");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2,"ASC");
			}
			elseif ($requestedStat == "reb_margin"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)as 'Minutes', ROUND((SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "scor_margin"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)as 'Minutes', ROUND((SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='points_def' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "gp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name, SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",0);
			}
			elseif ($requestedStat == "min_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			else
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", FALSE);
		}
        elseif ($requestedLevel == "SEA"){
            if ($requestedStat == "fgp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", 1); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "fgp3"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", 1); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "ftp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", 1); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "points_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "treb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "oreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "dreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "ast_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "blk_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "stl_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Points', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "min_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "gp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name, SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
				printSeasonDatabase($currentRecords,$oldRecords,"wbb",0);
			}
			else
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", FALSE);
		}
        elseif ($requestedLevel == "CAR"){
            if ($requestedStat == "fgp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
				printCareerDatabase($currentRecords, $oldRecords, "wbb", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "fgp3"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 30");
				printCareerDatabase($currentRecords, $oldRecords, "wbb", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "ftp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
				printCareerDatabase($currentRecords, $oldRecords, "wbb", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "points_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "treb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "oreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "dreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "ast_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "blk_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "stl_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "min_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",2);
			}
			elseif ($requestedStat == "gp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name, SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
				printCareerDatabase($currentRecords,$oldRecords,"wbb",0);
			}
			elseif($requestedStat == "def_points")
				printSeasonDatabase($currentRecords, $oldRecords, "wbb", FALSE, "ASC");
			else
				printCareerDatabase($currentRecords, $oldRecords, "wbb", FALSE);
        }
	}
    else {
        echo "<h2>Please select Category.</h2>";
    }
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
                                <option <?php if ($requestedLevel == "GAM") echo "selected"; ?> value="wbb_GAM">Individual Game Highs</option>
                                <option <?php if ($requestedLevel == "SEA") echo "selected"; ?> value="wbb_SEA">Individual Season Highs</option>
                                <option <?php if ($requestedLevel == "CAR") echo "selected"; ?> value="wbb_CAR">Career Highs</option>
                                <option <?php if ($requestedLevel == "TMG") echo "selected"; ?> value="wbb_TMG">Team Game Highs</option>
                                <option <?php if ($requestedLevel == "TMS") echo "selected"; ?> value="wbb_TMS">Team Season Highs</option>
                            </select>
                        </td>
                    </tr>
                    <tr><td><hr></td></tr>
                    <tr>
                        <td>
<?php							if ($requestedLevel == NULL){ ?>
                            <label for="indexStat">Select Stat</label>
                            <select name="indexStat" id="indexStat">
                                <option value=""></option>
                                
                                <?php 
	}
	else if ($requestedLevel == "GAM" || $requestedLevel == "TMG"){
								?>
									<label for="indexStat">Select Stat</label>
									<select name="indexStat" id="indexStat">
									<option value=""></option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage (Minimum 8 Made)</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage (Minimum 6 Made)</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage (Minimum 10 Made)</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
									</select>
											  </td>
											  </tr>
												<hr>
											  <tr>
											  <td>                                    
                                <?php 
		}
	elseif ($requestedLevel == "CAR"){ ?>
												  <label for="indexStat">Select Stat</label>
													<select name="indexStat" id="indexStat">
													<option value=""></option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points_avg">Highest Scoring Average (Minimum 20 Games)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage (Minimum 54 Made)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage (Minimum 30 Made)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage (Minimum 54 Made)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb_avg">Highest Total Rebound Average (Minimum 50 Games)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb">Most Offensive Rebounds</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb_avg">Highest Offensive Rebounds Per Game (Minimum 50 Games)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb">Most Defensive Rebounds</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb_avg">Highest Defense Rebounds Per Game (Minimum 50 Games)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast_avg">Highest Assist Per Game (Minimum 50 Games)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl_avg">Highest Steals Per Game (Minimum 50 Games)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk_avg">Highest Blocked Shots Per Game (Minimum 50 Games)</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gp">Most Games Played</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gs") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gs">Most Games Started</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min">Most Minutes Played</option>
														<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min_avg">Highest Minutes Played Per Game</option>
												  </select>
											</td>
														  </tr>
																<hr>
														  <tr>
														  <td>
															  
                                <?php 
		}
	elseif ($requestedLevel == "SEA"){ ?>
                                    <label for="indexStat">Select Stat</label>
										<select name="indexStat" id="indexStat">
										<option value=""></option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points_avg">Highest Scoring Average (Minimum 20 Games)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage (Minimum 50 Made)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage (Minimum 50 Made)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage (Minimum 50 Made)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb_avg">Highest Total Rebound Average (Minimum 20 Games)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb">Most Offensive Rebounds</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb_avg">Highest Offensive Rebounds Per Game (Minimum 20 Games)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb">Most Defensive Rebounds</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb_avg">Highest Defense Rebounds Per Game (Minimum 20 Games)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast_avg">Highest Assist Per Game (Minimum 20 Games)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl_avg">Highest Steals Per Game (Minimum 20 Games)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk_avg">Highest Blocked Shots Per Game (Minimum 20 Games)</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gp">Most Games Played</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gs") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gs">Most Games Started</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min">Most Minutes Played</option>
											<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min_avg">Highest Minutes Played Per Game</option>
									  </select>
								</td>
											  </tr>
													<hr>
											  <tr>
											  <td>
                                <?php 
		}
	elseif ($requestedLevel == "TMS"){ ?>
                                    <label for="indexStat">Select Stat</label>
									<select name="indexStat" id="indexStat">
									<option value=""></option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points_avg">Highest Scoring Average</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb_avg">Highest Total Rebound Average</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb">Most Offensive Rebounds</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb_avg">Highest Offensive Rebounds Per Game</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb">Most Defensive Rebounds</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb_avg">Highest Defense Rebounds Per Game</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast_avg">Highest Assist Per Game</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl_avg">Highest Steals Per Game</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk_avg">Highest Blocked Shots Per Game</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gp">Most Games Played</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min">Most Minutes Played</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min_avg">Highest Minutes Played Per Game</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_points">Fewest Points Allowed</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_fgp">Lowest Field Goal Percentage Allowed</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_fgp3">Lowest 3-Point Field Goal Percentage Allowed</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_treb">Fewest Rebounds Allowed</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "reb_margin") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-reb_margin">Highest Rebounding Margin</option>
										<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "scor_margin") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-scor_margin">Highest Scoring Margin</option>
								  </select>
							</td>
										  </tr>
											<hr>
										  <tr>
										  <td>
                                <?php }
											  ?>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<?php
} // End of wbbRecords() function

function editwbbrecords(){
	$table = "wbb_boxscores";
	$db_name = "kcmsathl_wvsu";
	if (array_key_exists('STAT',$_GET))
        $requestedStat = $_GET['STAT'];
	else
		$requestedStat = NULL;
    if (array_key_exists('LEVEL',$_GET))
        $requestedLevel = $_GET['LEVEL'];
	else
		$requestedLevel = NULL;
	?>
	<table width="100%">
	<tr>
	<?php
	if (array_key_exists('SPORT',$_GET)) { ?>
		<td valign="top" width="50%">
			<table width="500" border="0">
		  		<tbody>
		  			<tr>
			  			<td>
				  <label for="adminLevel">Make a selection:</label>
				<select name="adminLevel" id="adminLevel">
					<option value="NoSelection"></option>
					<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="wbb_GAM">Individual Game Highs</option>
					<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="wbb_SEA">Individual Season Highs</option>
					<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="wbb_CAR">Career Highs</option>
					<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="wbb_TMG">Team Game Highs</option>
					<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="wbb_TMS">Team Season Highs</option>
					<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATH") echo "selected" ?> value="wbb_ATH">Active Athletes</option>
					<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATR") echo "selected" ?> value="wbb_ATR">Retired Athletes</option>
				  <option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "VGM") echo "selected" ?> value="wbb_VGM">View Uploaded Games</option>
				  </select>
							<br>
			  <?php
    } 
	if ($requestedLevel == "VGM") {
        getGamesBySeasonForSport($_GET['SPORT']);
    }
	if($requestedStat != NULL)
	{
		if ($requestedLevel != NULL && $requestedStat != NULL && $requestedLevel != "ATH"){
			$currentRecords = readCurrentDatabase($db_name,"wbb_boxscores",$requestedStat,$_GET['LEVEL']);
			$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",$requestedStat,$_GET['LEVEL']);
		if($requestedLevel == "TMS"){
			if ($requestedStat == "fgp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x");
			}
			elseif ($requestedStat == "fgp3"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x");
			}
			elseif ($requestedStat == "ftp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x");
			}
			elseif ($requestedStat == "points_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "treb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "oreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "dreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "ast_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "blk_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "stl_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "def_points"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='points_def' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
			}
			elseif ($requestedStat == "def_fgp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm_def' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='fga_def' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='fgm_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga_def' THEN Value ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
			}
			elseif ($requestedStat == "def_fgp3"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3_def' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='fga3_def' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='fgm3_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3_def' THEN Value ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
			}
			elseif ($requestedStat == "def_treb"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame ASC) as x");
			}
			elseif ($requestedStat == "reb_margin"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)as 'Minutes', ROUND((SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "scor_margin"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='reb_def' THEN Value ELSE 0 END)as 'Minutes', ROUND((SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='points_def' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
			elseif ($requestedStat == "min_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x");
			}
		}
		elseif ($requestedLevel == "SEA" || $requestedLevel == "TMS"){
			if ($requestedStat == "fgp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
			}
			elseif ($requestedStat == "fgp3"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
			}
			elseif ($requestedStat == "ftp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
			}
			elseif ($requestedStat == "points_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
			elseif ($requestedStat == "treb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
			elseif ($requestedStat == "oreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
			elseif ($requestedStat == "dreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
			elseif ($requestedStat == "ast_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
			elseif ($requestedStat == "blk_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
			elseif ($requestedStat == "stl_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Points', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
			elseif ($requestedStat == "min_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
			}
		}
		elseif ($requestedLevel == "CAR"){
			if ($requestedStat == "fgp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 54");
			}
			elseif ($requestedStat == "fgp3"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 30");
			}
			elseif ($requestedStat == "ftp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 54");
			}
			elseif ($requestedStat == "points_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "treb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "oreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "dreb_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "ast_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "blk_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "stl_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "min_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
			}
			elseif ($requestedStat == "gp"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name, SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
			}
		}
		elseif ($requestedLevel == "GAM"){
			if ($requestedStat == "fgp")
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 8");
			if ($requestedStat == "fgp3")
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 6");
			if ($requestedStat == "ftp")
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 10");
		}
	}
			$db_name = "kcmsathl_wvsu";
			$table = "wbb_boxscores";
			// Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
			$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",$requestedStat,$_GET['LEVEL']);
			echo "<hr>";
			echo ("Manual Record Editing - ".$_GET['LEVEL'].": ".$_GET['STAT']);
			$oldResultsString = readOldDatabase("kcmsathl_WVSU","wbb_oldrecords",$_GET['STAT'],$_GET['LEVEL']);
			$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wbb", $requestedLevel, 1);
			if ($requestedLevel == "CAR")
				editCareerDatabase($oldResultsString,TRUE);
			else if ($requestedLevel == "TMS" || $requestedLevel == "SEA")
				editSeasonDatabase($oldResultsString,TRUE);
			else
				editCurrentDatabase($oldResultsString, TRUE); ?>
			<td>
				Records Code <br>
				<input type="textarea" value = "<?php echo htmlspecialchars($recordsCode)?>">
			</td>
		<?php
		}
	if ($requestedLevel == "GAM"){
		//------------------------Start Overall Records Code-----------------
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Scoring</h1></td></tr>";
		//------------Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores','points',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",'points',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Points</center></h2>".$newRecordsCode."</td>";
		
		//--------------------------New Header: Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Field Goals</h1></td></tr>";
		
		//----------Field Goals Made-------------
		$recordCat = "fgm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------Field Goals Attempted-------------
		$recordCat = "fga";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Field Goal Percentage-------------
		$recordCat = "fgp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,Opponent,Date,`Home/Away`,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,Opponent,Date,`Home/Away`,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID RIGHT OUTER JOIN NCAA_RPI as RPI on RPI.RPI = games.RPI WHERE `Name`!='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 8");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Percentage (Minimum 8 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: 3-Point Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>3-Point Field Goals</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "fgm3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fga3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>3-Point Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "fgp3";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,Opponent,Date,`Home/Away`,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,Opponent,`Home/Away`,Date,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID RIGHT OUTER JOIN NCAA_RPI as RPI on RPI.RPI = games.RPI WHERE `Name`!='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 6");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Percentage (Minimum 6 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Free Throws-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Free Throws</h1></td></tr>";
		
		//----------Free Throws Made-------------
		$recordCat = "ftm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throws Made</center></h2>".$newRecordsCode."</td>";
		
		//----------Free Throws Attempted-------------
		$recordCat = "fta";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Free Throws Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Free Throw Percentage-------------
		$recordCat = "ftp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,Opponent,Date,`Home/Away`,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,Opponent,Date,`Home/Away`,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID RIGHT OUTER JOIN NCAA_RPI as RPI on RPI.RPI = games.RPI WHERE `Name`!='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 10");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throw Percentage (Minimum 10 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Rebounds-------------
		$recordCat = "treb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Rebounds</center></h2>".$newRecordsCode."</td>";

		//----------Assists-------------
		$recordCat = "ast";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Assists</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Blocks-------------
		$recordCat = "blk";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Blocked Shots</center></h2>".$newRecordsCode."</td>";
		
		//----------Steals-------------
		$recordCat = "stl";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Steals</center></h2>".$newRecordsCode."</td></tr>";
		
		//------------------------End Overall Records Code-----------------
		$recordsCode = $recordsCode."</tr></table>";
		
		?>
	<label for="adminStat">Select Stat</label>
	<select name="adminStat" id="adminStat">
	<option value=""></option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage (Minimum 8 Made)</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage (Minimum 6 Made)</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage (Minimum 10 Made)</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
	</select>
			  </td>
			  </tr>
										<hr>
			  <tr>
			  <td>
				  <h4>Records Code</h4>
				  <textarea rows="10" cols="150" readonly wrap="soft"><?php echo htmlspecialchars($recordsCode)?></textarea>
		<?php
	}
	elseif ($requestedLevel == "TMG"){
		//------------------------Start Overall Records Code-----------------
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Scoring</h1></td></tr>";
		//------------Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores','points',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",'points',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Points</center></h2>".$newRecordsCode."</td>";
		
		//--------------------------New Header: Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Field Goals</h1></td></tr>";
		
		//----------Field Goals Made-------------
		$recordCat = "fgm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------Field Goals Attempted-------------
		$recordCat = "fga";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Field Goal Percentage-------------
		$recordCat = "fgp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,Opponent,Date,`Home/Away`,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,Opponent,`Home/Away`,Date,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID RIGHT OUTER JOIN NCAA_RPI as RPI on RPI.RPI = games.RPI WHERE `Name`='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 35");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goal Percentage (Minimum 35 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: 3-Point Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>3-Point Field Goals</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "fgm3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fga3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>3-Point Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "fgp3";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,Opponent,Date,`Home/Away`,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,Opponent,`Home/Away`,Date,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID RIGHT OUTER JOIN NCAA_RPI as RPI on RPI.RPI = games.RPI WHERE `Name`='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 12");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Percentage (Minimum 12 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Free Throws-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Free Throws</h1></td></tr>";
		
		//----------Free Throws Made-------------
		$recordCat = "ftm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throws Made</center></h2>".$newRecordsCode."</td>";
		
		//----------Free Throws Attempted-------------
		$recordCat = "fta";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Free Throws Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Free Throw Percentage-------------
		$recordCat = "ftp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,Opponent,Date,`Home/Away`,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,Opponent,Date,`Home/Away`,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID RIGHT OUTER JOIN NCAA_RPI as RPI on RPI.RPI = games.RPI WHERE `Name`='TEAM' GROUP BY Name,Season,boxscores.GameID ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throw Percentage (Minimum 20 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Rebounds-------------
		$recordCat = "treb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Rebounds</center></h2>".$newRecordsCode."</td>";

		//----------Assists-------------
		$recordCat = "ast";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Assists</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Blocks-------------
		$recordCat = "blk";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Blocked Shots</center></h2>".$newRecordsCode."</td>";
		
		//----------Steals-------------
		$recordCat = "stl";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Steals</center></h2>".$newRecordsCode."</td></tr>";
		
		//------------------------End Overall Records Code-----------------
		$recordsCode = $recordsCode."</tr></table>";
		
		
		?>
	<label for="adminStat">Select Stat</label>
	<select name="adminStat" id="adminStat">
	<option value=""></option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage (Minimum 8 Made)</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage (Minimum 6 Made)</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage (Minimum 10 Made)</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
		<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
	</select>
			  </td>
			  </tr>
										<hr>
			  <tr>
			  <td>
				  <h4>Records Code</h4>
				  <textarea rows="10" cols="150" readonly wrap="soft"><?php echo htmlspecialchars($recordsCode)?></textarea>
		<?php
	}
	elseif ($requestedLevel == "SEA"){
		//------------------------Start Overall Records Code-----------------
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Scoring</h1></td></tr>";
		
		//------------Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores','points',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",'points',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Points</center></h2>".$newRecordsCode."</td>";
		
		//-----------Average Points--------------
		$recordCat = "points_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Scoring Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Field Goals</h1></td></tr>";
		
		//----------Field Goals Made-------------
		$recordCat = "fgm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------Field Goals Attempted-------------
		$recordCat = "fga";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Field Goal Percentage-------------
		$recordCat = "fgp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 60");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Percentage (Minimum 60 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: 3-Point Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>3-Point Field Goals</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "fgm3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fga3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>3-Point Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "fgp3";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 40");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Percentage (Minimum 40 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Free Throws-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Free Throws</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "ftm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throws Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fta";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Free Throws Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "ftp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throw Percentage (Minimum 50 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Rebounding-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Rebounding</h1></td></tr>";
		
		//----------Rebounds-------------
		$recordCat = "treb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Rebound Average-------------
		$recordCat = "treb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Rebounding Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Defensive Rebounds-------------
		$recordCat = "dreb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Defensive Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Defensive Rebound Average-------------
		$recordCat = "dreb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Defensive Rebounding Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Offensive Rebounds-------------
		$recordCat = "oreb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Offensive Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Offensive Rebound Average-------------
		$recordCat = "oreb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Offensive Rebounding Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Assists-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Assists</h1></td></tr>";
		
		//----------Assists-------------
		$recordCat = "ast";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Assists</center></h2>".$newRecordsCode."</td>";
		
		//----------Assist Average-------------
		$recordCat = "ast_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Assists Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Blocked Shots-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Blocked Shots</h1></td></tr>";
		
		//----------Blocks-------------
		$recordCat = "blk";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Blocked Shots</center></h2>".$newRecordsCode."</td>";
		
		//----------Block Average-------------
		$recordCat = "blk_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Blocked Shots Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Steals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Steals</h1></td></tr>";
		
		//----------Steals-------------
		$recordCat = "stl";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Steals</center></h2>".$newRecordsCode."</td>";
		
		//----------Steal Average-------------
		$recordCat = "stl_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Steals Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Participation-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Participation</h1></td></tr>";
		
		//----------Games Played-------------
		$recordCat = "gp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name, SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Games Played</center></h2>".$newRecordsCode."</td>";
		
		//----------Minutes Played-------------
		$recordCat = "min";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Minutes Played</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Minutes Per Game-------------
		$recordCat = "min_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Average Minutes Played Per Game</center></h2>".$newRecordsCode."</td>";
		
		//------------------------End Overall Records Code-----------------
		$recordsCode = $recordsCode."</tr></table>";
		
			?>
		<label for="adminStat">Select Stat</label>
		<select name="adminStat" id="adminStat">
		<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points_avg">Highest Scoring Average (Minimum 20 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage (Minimum 50 Made)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage (Minimum 50 Made)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage (Minimum 50 Made)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb_avg">Highest Total Rebound Average (Minimum 20 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb">Most Offensive Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb_avg">Highest Offensive Rebounds Per Game (Minimum 20 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb">Most Defensive Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb_avg">Highest Defense Rebounds Per Game (Minimum 20 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast_avg">Highest Assist Per Game (Minimum 20 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl_avg">Highest Steals Per Game (Minimum 20 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk_avg">Highest Blocked Shots Per Game (Minimum 20 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gp">Most Games Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gs") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gs">Most Games Started</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min">Most Minutes Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min_avg">Highest Minutes Played Per Game</option>
	  </select>
	</td>
	</tr>
	<hr>
	<tr>
	<td>
		<h4>Records Code</h4>
		  <textarea rows="10" cols="150" readonly wrap="soft"><?php echo htmlspecialchars($recordsCode)?></textarea>
	<?php
	}
	elseif ($requestedLevel == "TMS"){
		//------------------------Start Overall Records Code-----------------
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Scoring</h1></td></tr>";
		
		//------------Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores','points',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",'points',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Points</center></h2>".$newRecordsCode."</td>";
		
		//-----------Average Points--------------
		$recordCat = "points_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Scoring Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Field Goals</h1></td></tr>";
		
		//----------Field Goals Made-------------
		$recordCat = "fgm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------Field Goals Attempted-------------
		$recordCat = "fga";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Field Goal Percentage-------------
		$recordCat = "fgp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 60");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Percentage (Minimum 60 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: 3-Point Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>3-Point Field Goals</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "fgm3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fga3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>3-Point Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "fgp3";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 40");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Percentage (Minimum 40 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Free Throws-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Free Throws</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "ftm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throws Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fta";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Free Throws Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "ftp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throw Percentage (Minimum 50 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Rebounding-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Rebounding</h1></td></tr>";
		
		//----------Rebounds-------------
		$recordCat = "treb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Rebound Average-------------
		$recordCat = "treb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Rebounding Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Defensive Rebounds-------------
		$recordCat = "dreb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Defensive Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Defensive Rebound Average-------------
		$recordCat = "dreb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Defensive Rebounding Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Offensive Rebounds-------------
		$recordCat = "oreb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Offensive Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Offensive Rebound Average-------------
		$recordCat = "oreb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Offensive Rebounding Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Assists-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Assists</h1></td></tr>";
		
		//----------Assists-------------
		$recordCat = "ast";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Defensive Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Assist Average-------------
		$recordCat = "ast_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Assists Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Blocked Shots-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Blocked Shots</h1></td></tr>";
		
		//----------Blocks-------------
		$recordCat = "blk";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Blocked Shots</center></h2>".$newRecordsCode."</td>";
		
		//----------Block Average-------------
		$recordCat = "blk_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Blocked Shots Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Steals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Steals</h1></td></tr>";
		
		//----------Blocks-------------
		$recordCat = "stl";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Steals</center></h2>".$newRecordsCode."</td>";
		
		//----------Block Average-------------
		$recordCat = "stl_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Steals Average (Minimum 20 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Participation-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Participation</h1></td></tr>";
		
		//----------Games Played-------------
		$recordCat = "gp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name, SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Games Played</center></h2>".$newRecordsCode."</td>";
		
		//----------Minutes Played-------------
		$recordCat = "min";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Minutes Played</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Minutes Per Game-------------
		$recordCat = "min_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 20");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Average Minutes Played Per Game</center></h2>".$newRecordsCode."</td>";
		
		//------------------------End Overall Records Code-----------------
		$recordsCode = $recordsCode."</tr></table>";
		
			?>
		<label for="adminStat">Select Stat</label>
		<select name="adminStat" id="adminStat">
		<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points_avg">Highest Scoring Average</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb_avg">Highest Total Rebound Average</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb">Most Offensive Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb_avg">Highest Offensive Rebounds Per Game</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb">Most Defensive Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb_avg">Highest Defense Rebounds Per Game</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast_avg">Highest Assist Per Game</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl_avg">Highest Steals Per Game</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk_avg">Highest Blocked Shots Per Game</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gp">Most Games Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gs") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gs">Most Games Started</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min">Most Minutes Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min_avg">Highest Minutes Played Per Game</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_points">Fewest Points Allowed</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_fgp">Lowest Field Goal Percentage Allowed</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_fgp3">Lowest 3-Point Field Goal Percentage Allowed</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "def_treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-def_treb">Fewest Rebounds Allowed</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "reb_margin") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-reb_margin">Highest Rebounding Margin</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "scor_margin") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-scor_margin">Highest Scoring Margin</option>
	  </select>
	</td>
	</tr>
	<hr>
	<tr>
	<td>
		<h4>Records Code</h4>
		  <textarea rows="10" cols="150" readonly wrap="soft"><?php echo htmlspecialchars($recordsCode)?></textarea>
	<?php
	}
	elseif ($requestedLevel == "CAR"){
		//------------------------Start Overall Records Code-----------------
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Scoring</h1></td></tr>";
		
		//------------Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores','points',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords",'points',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Points</center></h2>".$newRecordsCode."</td>";
		
		//-----------Average Points--------------
		$recordCat = "points_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 50");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Scoring Average (Minimum 30 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Field Goals</h1></td></tr>";
		
		//----------Field Goals Made-------------
		$recordCat = "fgm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------Field Goals Attempted-------------
		$recordCat = "fga";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Field Goal Percentage-------------
		$recordCat = "fgp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Field Goals Percentage (Minimum 50 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: 3-Point Field Goals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>3-Point Field Goals</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "fgm3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fga3";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>3-Point Field Goals Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "fgp3";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='fgm3' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fga3' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>3-Point Field Goals Percentage (Minimum 30 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Free Throws-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Free Throws</h1></td></tr>";
		
		//----------3P Field Goals Made-------------
		$recordCat = "ftm";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throws Made</center></h2>".$newRecordsCode."</td>";
		
		//----------3P Field Goals Attempted-------------
		$recordCat = "fta";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Free Throws Attempted</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------3P Field Goal Percentage-------------
		$recordCat = "ftp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,FieldGoalsMade,FieldGoalsAttempted,FieldGoalPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END) as 'FieldGoalsMade',SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END)as 'FieldGoalsAttempted', ROUND(SUM(CASE WHEN `Stat`='ftm' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='fta' THEN Value ELSE 0 END),3) as FieldGoalPercentage,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY FieldGoalPercentage DESC) as x WHERE x.FieldGoalsMade >= 50");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Free Throw Percentage (Minimum 50 made)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Rebounding-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Rebounding</h1></td></tr>";
		
		//----------Rebounds-------------
		$recordCat = "treb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Rebound Average-------------
		$recordCat = "treb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='treb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Rebounding Average (Minimum 30 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Defensive Rebounds-------------
		$recordCat = "dreb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Defensive Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Defensive Rebound Average-------------
		$recordCat = "dreb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='dreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Defensive Rebounding Average (Minimum 30 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Offensive Rebounds-------------
		$recordCat = "oreb";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Offensive Rebounds</center></h2>".$newRecordsCode."</td>";
		
		//----------Offensive Rebound Average-------------
		$recordCat = "oreb_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='oreb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Offensive Rebounding Average (Minimum 30 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Assists-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Assists</h1></td></tr>";
		
		//----------Assists-------------
		$recordCat = "ast";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Assists</center></h2>".$newRecordsCode."</td>";
		
		//----------Assist Average-------------
		$recordCat = "ast_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='ast' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Assists Average (Minimum 30 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Blocked Shots-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Blocked Shots</h1></td></tr>";
		
		//----------Blocks-------------
		$recordCat = "blk";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Blocked Shots</center></h2>".$newRecordsCode."</td>";
		
		//----------Block Average-------------
		$recordCat = "blk_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='blk' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Blocked Shots Average (Minimum 30 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Steals-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Steals</h1></td></tr>";
		
		//----------Blocks-------------
		$recordCat = "stl";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Steals</center></h2>".$newRecordsCode."</td>";
		
		//----------Block Average-------------
		$recordCat = "stl_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='stl' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td><h2><center>Steals Average (Minimum 30 Games)</center></h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Participation-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Participation</h1></td></tr>";
		
		//----------Games Played-------------
		$recordCat = "gp";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name, SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Games Played</center></h2>".$newRecordsCode."</td>";
		
		//----------Minutes Played-------------
		$recordCat = "min";
		$currentRecords = readCurrentDatabase($db_name,'wbb_boxscores',"$recordCat",$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td><h2><center>Minutes Played</center></h2>".$newRecordsCode."</td></tr>";
		
		//----------Minutes Per Game-------------
		$recordCat = "min_avg";
		$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Minutes,MinutesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)as 'Minutes', ROUND(SUM(CASE WHEN `Stat`='min' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='min' THEN 1 ELSE 0 END),3) as MinutesPerGame,SeasonsActive,Season,IsActive FROM `wbb_boxscores` AS boxscores LEFT OUTER JOIN wbb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wbb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY MinutesPerGame DESC) as x WHERE x.GamesPlayed >= 30");
		$oldRecords = readOldDatabase($db_name,"wbb_oldrecords","$recordCat",$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'wbb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td><h2><center>Average Minutes Played Per Game</center></h2>".$newRecordsCode."</td>";
		
		//------------------------End Overall Records Code-----------------
		$recordsCode = $recordsCode."</tr></table>";
		
			?>
		<label for="adminStat">Select Stat</label>
		<select name="adminStat" id="adminStat">
		<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-points_avg">Highest Scoring Average (Minimum 50 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm">Most Field Goals Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga">Most Field Goals Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp">Highest Field Goal Percentage (Minimum 50 Made)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgm3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgm3">Most 3-Point Field Goals Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fga3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fga3">Most 3-Point Field Goals Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fgp3") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fgp3">Highest 3-Point Field Goal Percentage (Minimum 30 Made)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftm") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftm">Most Free Throws Made</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "fta") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-fta">Most Free Throws Attempted</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ftp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ftp">Highest Free Throw Percentage (Minimum 50 Made)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb">Most Total Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "treb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-treb_avg">Highest Total Rebound Average (Minimum 30 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb">Most Offensive Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "oreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-oreb_avg">Highest Offensive Rebounds Per Game (Minimum 30 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb">Most Defensive Rebounds</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "dreb_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-dreb_avg">Highest Defense Rebounds Per Game (Minimum 30 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "ast_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-ast_avg">Highest Assist Per Game (Minimum 30 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl">Most Steals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "stl_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-stl_avg">Highest Steals Per Game (Minimum 30 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk">Most Blocked Shots</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "blk_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-blk_avg">Highest Blocked Shots Per Game (Minimum 30 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gp") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gp">Most Games Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "gs") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-gs">Most Games Started</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min">Most Minutes Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "min_avg") echo "selected" ?> value="wbb_<?php echo $_GET['LEVEL']?>-min_avg">Highest Minutes Played Per Game</option>
	  </select>
</td>
			  </tr>
					<hr>
			  <tr>
			  <td>
				  <h4>Records Code</h4>
				  <textarea rows="10" cols="150" readonly wrap="soft"><?php echo htmlspecialchars($recordsCode)?></textarea>
	<?php
	}
	elseif($requestedLevel == "ATH"){
			echo "Active Athlete Administration";
			athleteAdministration("wbb","A");
		}
		elseif($requestedLevel == "ATR"){
			echo "Retired Athlete Administration";
			athleteAdministration("wbb","R");
		}
		else{
			echo "<h2>Please select Stat Category.</h2>";
			?>
			</td>
		  <?php
			}
}

function uploadWbbBox($ArrayRead){
	$db_name = "wbb";
	//Format Date for use in database
	$date = $ArrayRead['venue']['@attributes']['date'];
	$time = $ArrayRead['venue']['@attributes']['time'];
	$time_input = strtotime($date);
	$formattedDate = date('Y-m-d',$time_input);
	$locationOfGame = NULL;
	
	if($ArrayRead["team"][0]["@attributes"]["code"] == 1439){ // Look for good guys by NCAA RPI number. Should eliminate variance on how the team name is typed.
		$teamArray = $ArrayRead['team'][0];
		$homeAway = "A";
		$opponentName = $ArrayRead['team'][1]['@attributes']['name'];
		$opponent = $ArrayRead['team'][1]['@attributes']['code'];
		$opponentTotals = $ArrayRead['team'][1]['totals'];
		$homeScore = $ArrayRead['team'][0]['linescore']['@attributes']['score']; // Not actually Home Team, but the team we are looking for AKA the good guys.
		$awayScore = $ArrayRead['team'][1]['linescore']['@attributes']['score']; // Opponent, regardless of location
	}
	else if ($ArrayRead["team"][1]["@attributes"]["code"] == 1439){
		$teamArray = $ArrayRead['team'][1];
		$homeAway = "H";
		$opponentName = $ArrayRead['team'][0]['@attributes']['name'];
		$opponent = $ArrayRead['team'][0]['@attributes']['code'];
		$opponentTotals = $ArrayRead['team'][0]['totals'];
		$homeScore = $ArrayRead['team'][1]['linescore']['@attributes']['score'];
		$awayScore = $ArrayRead['team'][0]['linescore']['@attributes']['score'];
	}
	if ($homeScore > $awayScore)
	    $result = "W";
	else
	    $result = "L";
	
	$explodedDate = explode("-", $formattedDate);
	$currentMonth = $explodedDate[1];
	$currentYear = $explodedDate[0];
	if ($currentMonth < 5)
		$season = $currentYear-1;
	else
		$season = $currentYear;
	    
	    // Need to Check for duplicate games
	    
	$formattedLocation = checkName($ArrayRead['venue']['@attributes']['location']);
	$GameID = createNewGame ($formattedDate,$time,$homeAway,$opponent,$result,"$homeScore-$awayScore",$ArrayRead['venue']['@attributes']['attend'],$formattedLocation,$season,$db_name);
	
	echo ("<h3>Date of Game: $date | Opponent: $opponentName | RPI: $opponent | GameID: $GameID</h3><h3>Query Results:</h3><br>");
	if(checkOpponent($opponent,$opponentName))
		echo "<h2>Opponent RPI Added to database</h2>";
	
	$teamTotals = $teamArray['totals'];
	createPlayerStatwbb("TEAM","points",$teamTotals['stats']['@attributes']['tp'],$GameID,$season);
	createPlayerStatwbb("TEAM","fgm",$teamTotals['stats']['@attributes']['fgm'],$GameID,$season);
	createPlayerStatwbb("TEAM","fga",$teamTotals['stats']['@attributes']['fga'],$GameID,$season);
	if ($teamTotals['stats']['@attributes']['fga'] > 0)
			createPlayerStatwbb("TEAM","fgp",($teamTotals['stats']['@attributes']['fgm']/$teamTotals['stats']['@attributes']['fga']),$GameID,$season);
		else
			createPlayerStatwbb("TEAM","fgp",0,$GameID,$season);
	createPlayerStatwbb("TEAM","fgm3",$teamTotals['stats']['@attributes']['fgm3'],$GameID,$season);
	createPlayerStatwbb("TEAM","fga3",$teamTotals['stats']['@attributes']['fga3'],$GameID,$season);
	if ($teamTotals['stats']['@attributes']['fga3'] > 0)
			createPlayerStatwbb("TEAM","fgp3",($teamTotals['stats']['@attributes']['fgm3']/$teamTotals['stats']['@attributes']['fga3']),$GameID,$season);
		else
			createPlayerStatwbb("TEAM","fgp3",0,$GameID,$season);
	createPlayerStatwbb("TEAM","ftm",$teamTotals['stats']['@attributes']['ftm'],$GameID,$season);
	createPlayerStatwbb("TEAM","fta",$teamTotals['stats']['@attributes']['fta'],$GameID,$season);
	if ($teamTotals['stats']['@attributes']['fta'] > 0)
			createPlayerStatwbb("TEAM","ftp",($teamTotals['stats']['@attributes']['ftm']/$teamTotals['stats']['@attributes']['fta']),$GameID,$season);
		else
			createPlayerStatwbb("TEAM","ftp",0,$GameID,$season);
	createPlayerStatwbb("TEAM","treb",$teamTotals['stats']['@attributes']['treb'],$GameID,$season);
	createPlayerStatwbb("TEAM","oreb",$teamTotals['stats']['@attributes']['oreb'],$GameID,$season);
	createPlayerStatwbb("TEAM","dreb",$teamTotals['stats']['@attributes']['dreb'],$GameID,$season);
	createPlayerStatwbb("TEAM","ast",$teamTotals['stats']['@attributes']['ast'],$GameID,$season);
	createPlayerStatwbb("TEAM","stl",$teamTotals['stats']['@attributes']['stl'],$GameID,$season);
	createPlayerStatwbb("TEAM","blk",$teamTotals['stats']['@attributes']['blk'],$GameID,$season);
	$colonPosition = strpos($teamTotals['stats']['@attributes']['min'], ":");
		if ($colonPosition != NULL){
			$explodedMins = explode(":",$teamTotals['stats']['@attributes']['min']);
			$mins = $explodedMins[0] + ($explodedMins[1]/60);
			createPlayerStatwbb("TEAM","min",$mins,$GameID,$season);
		}
		else
			createPlayerStatwbb("TEAM","min",$teamTotals['stats']['@attributes']['min'],$GameID,$season);
	createPlayerStatwbb("TEAM","points_def",$opponentTotals['stats']['@attributes']['tp'],$GameID,$season);
	createPlayerStatwbb("TEAM","fgm_def",$opponentTotals['stats']['@attributes']['fgm'],$GameID,$season);
	createPlayerStatwbb("TEAM","fga_def",$opponentTotals['stats']['@attributes']['fga'],$GameID,$season);
	createPlayerStatwbb("TEAM","fgm3_def",$opponentTotals['stats']['@attributes']['fgm3'],$GameID,$season);
	createPlayerStatwbb("TEAM","fga3_def",$opponentTotals['stats']['@attributes']['fga3'],$GameID,$season);
	createPlayerStatwbb("TEAM","reb_def",$opponentTotals['stats']['@attributes']['treb'],$GameID,$season);
	
	$numOfPlayers = sizeof($teamArray['player']);
	
	for($currPlayer = 0; $currPlayer < $numOfPlayers; $currPlayer ++):
	
	$currArray = $teamArray['player'][$currPlayer];
	$athleteName = $currArray['@attributes']['name'];
	
	if ($athleteName == "TEAM" || $currArray['@attributes']['gp'] == 0 || $currArray['@attributes']['uni'] == "TM")
		continue;
	else{
		createPlayerStatwbb("$athleteName","points",$currArray['stats']['@attributes']['tp'],$GameID,$season);
		createPlayerStatwbb("$athleteName","fgm",$currArray['stats']['@attributes']['fgm'],$GameID,$season);
		createPlayerStatwbb("$athleteName","fga",$currArray['stats']['@attributes']['fga'],$GameID,$season);
		if ($currArray['stats']['@attributes']['fgm'] > 7)
			createPlayerStatwbb("$athleteName","fgp",($currArray['stats']['@attributes']['fgm']/$currArray['stats']['@attributes']['fga']),$GameID,$season);
		else
			createPlayerStatwbb("$athleteName","fgp",0,$GameID,$season);
		createPlayerStatwbb("$athleteName","fgm3",$currArray['stats']['@attributes']['fgm3'],$GameID,$season);
		createPlayerStatwbb("$athleteName","fga3",$currArray['stats']['@attributes']['fga3'],$GameID,$season);
		if ($currArray['stats']['@attributes']['fgm3'] > 5)
			createPlayerStatwbb("$athleteName","fgp3",($currArray['stats']['@attributes']['fgm3']/$currArray['stats']['@attributes']['fga3']),$GameID,$season);
		else
			createPlayerStatwbb("$athleteName","fgp3",0,$GameID,$season);
		createPlayerStatwbb("$athleteName","ftm",$currArray['stats']['@attributes']['ftm'],$GameID,$season);
		createPlayerStatwbb("$athleteName","fta",$currArray['stats']['@attributes']['fta'],$GameID,$season);
		if ($currArray['stats']['@attributes']['ftm'] > 9)
			createPlayerStatwbb("$athleteName","ftp",($currArray['stats']['@attributes']['ftm']/$currArray['stats']['@attributes']['fta']),$GameID,$season);
		else
			createPlayerStatwbb("$athleteName","ftp",0,$GameID,$season);
		createPlayerStatwbb("$athleteName","treb",$currArray['stats']['@attributes']['treb'],$GameID,$season);
		createPlayerStatwbb("$athleteName","oreb",$currArray['stats']['@attributes']['oreb'],$GameID,$season);
		createPlayerStatwbb("$athleteName","dreb",$currArray['stats']['@attributes']['dreb'],$GameID,$season);
		createPlayerStatwbb("$athleteName","ast",$currArray['stats']['@attributes']['ast'],$GameID,$season);
		createPlayerStatwbb("$athleteName","stl",$currArray['stats']['@attributes']['stl'],$GameID,$season);
		createPlayerStatwbb("$athleteName","blk",$currArray['stats']['@attributes']['blk'],$GameID,$season);
		$colonPosition = strpos($currArray['stats']['@attributes']['min'], ":");
		if ($colonPosition != NULL){
			$explodedMins = explode(":",$currArray['stats']['@attributes']['min']);
			$mins = $explodedMins[0] + ($explodedMins[1]/60);
			createPlayerStatwbb("$athleteName","min",$mins,$GameID,$season);
		}
		else
			createPlayerStatwbb("$athleteName","min",$currArray['stats']['@attributes']['min'],$GameID,$season);
	}
	
	endfor;
}

function createPlayerStatwbb($name, $stat, $value, $gameID, $season = "", $remarks = ""){
	$config = parse_ini_file('config.ini');
	$wbb_milestones = parse_ini_file('wbb.ini');
	//var_dump($vb_milestones);
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
	if ($name == "TEAM"){
		$athleteID = 0;
		$statType = "TMG";
	}
	else{
		$statType = "GAM";
		$name = checkName($name);
		$athlQuery = "SELECT * FROM `wbb_athletes` WHERE `Name`='$name' AND `IsActive`='1'"; // Search for athlete in database. If found, get Athlete ID, otherwise create a new athlete in the database and assign an ID number to him/her.
		$athlResults = mysqli_query($conn, $athlQuery);
		$finalAthlResult = mysqli_fetch_assoc($athlResults);
		if (mysqli_num_rows($athlResults) > 0)
			$athleteID = $finalAthlResult['AthleteID'];
		else
			$athleteID = createAthlete($name, 'wbb', $season);
	}
	
	//If duplicate is found, update existing record instead of creating an new one. This will reduce the numbers in the records database which will start to get massive quickly.
	$dupQuery = "SELECT * FROM `wbb_boxscores` WHERE `GameID`='$gameID' AND `Stat`='$stat' AND `AthleteID`='$athleteID' ORDER BY `EntryID` DESC";
	$dupResults = mysqli_query($conn, $dupQuery);
	$finalDupResult = mysqli_fetch_assoc($dupResults);
	if(mysqli_num_rows($dupResults)>0)
		$duplicateResult = $finalDupResult['EntryID'];
	else
		$duplicateResult = -1;
	//Initialize all variables...just for shits and giggles.
	
	
	if ($duplicateResult < 0){ 
		if ($name != "TEAM"){
			switch ($stat){
					//Check for Season and Career Milestones
					
				case ('points'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $wbb_milestones['sea_points']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_points']){
						if ($newSeasonTotal > $wbb_milestones['sea_points'])
							echo "<h4>***** Season Milestone Achieved for $name in Points: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Points: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
					$newCareerTotal = $careerTotal + $value;
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if ($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_points'][0]) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_points'][0]) {
						if ($newCareerTotal > $wbb_milestones['car_points'][0])
							echo "<h4>***** Career Milestone Achieved for $name in Points: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Points: $newCareerTotal -----<br>";
					}
					elseif ($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_points'][1]) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_points'][1]){
						if ($newCareerTotal > $wbb_milestones['car_points'][1])
							echo "<h4>***** Career Milestone Achieved for $name in Points: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Points: $newCareerTotal -----<br>";
						
					}
					elseif($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_points'][2]) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_points'][2]){
						if ($newCareerTotal > $wbb_milestones['car_points'][2])
							echo "<h4>***** Career Milestone Achieved for $name in Points: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Points: $newCareerTotal -----<br>";
					}
						
					// echo "DEBUG: $name Points - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
					break;
					
				case ('fgm3'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $wbb_milestones['sea_fgm3']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_fgm3']){
						if ($newSeasonTotal > $wbb_milestones['sea_fgm3'])
							echo "<h4>***** Season Milestone Achieved for $name in 3-Point Field Goals: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in 3-Point Field Goals: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
					$newCareerTotal = $careerTotal + $value;
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_fgm3']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_fgm3'])){
						if ($newCareerTotal > $wbb_milestones['car_fgm3'])
							echo "<h4>***** Career Milestone Achieved for $name in 3-Point Field Goals: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in 3-Point Field Goals: $newCareerTotal -----<br>";
					}
					// echo "DEBUG: $name 3-Point Field Goals - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
					break;
					
				case ('treb'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $wbb_milestones['sea_treb']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_treb']){
						if ($newSeasonTotal > $wbb_milestones['sea_treb'])
							echo "<h4>***** Season Milestone Achieved for $name in Rebounds: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Rebounds: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
					$newCareerTotal = $careerTotal + $value;
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_treb']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_treb'])){
						if ($newCareerTotal > $wbb_milestones['car_treb'])
							echo "<h4>***** Career Milestone Achieved for $name in Rebounds: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Rebounds: $newCareerTotal -----<br>";
					}
					// echo "DEBUG: $name Rebounds - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
					break;
					
				case ('ast'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $wbb_milestones['sea_ast']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_ast']){
						if ($newSeasonTotal > $wbb_milestones['sea_ast'])
							echo "<h4>***** Season Milestone Achieved for $name in Assists: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Assists: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
					$newCareerTotal = $careerTotal + $value;
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_ast']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_ast'])){
						if ($newCareerTotal > $wbb_milestones['car_ast'])
							echo "<h4>***** Career Milestone Achieved for $name in Assists: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Assists: $newCareerTotal -----<br>";
					}
					// echo "DEBUG: $name Assists - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
					break;
					
				case ('stl'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $wbb_milestones['sea_stl']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_stl']){
						if ($newSeasonTotal > $wbb_milestones['sea_stl'])
							echo "<h4>***** Season Milestone Achieved for $name in Steals: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Steals: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
					$newCareerTotal = $careerTotal + $value;
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_stl']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_stl'])){
						if ($newCareerTotal > $wbb_milestones['car_stl'])
							echo "<h4>***** Career Milestone Achieved for $name in Steals: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Steal: $newCareerTotal -----<br>";
					}
					// echo "DEBUG: $name Steals - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
					break;
					
				case ('blk'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $wbb_milestones['sea_blk']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_blk']){
						if ($newSeasonTotal > $wbb_milestones['sea_blk'])
							echo "<h4>***** Season Milestone Achieved for $name in Steals: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Steals: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
					$newCareerTotal = $careerTotal + $value;
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($newCareerTotal != NULL && ($careerTotal / $wbb_milestones['car_blk']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_blk'])){
						if ($newCareerTotal > $wbb_milestones['car_blk'])
							echo "<h4>***** Career Milestone Achieved for $name in Blocks: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Blocks: $newCareerTotal -----<br>";
					}
					// echo "DEBUG: $name Blocks - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
					break;
					
				case ('attack_k'):
					$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $vb_milestones['sea_attack_k']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_attack_k']){
						if ($newSeasonTotal > $vb_milestones['sea_attack_k'])
							echo "<h4>***** Season Milestone Achieved for $name in Kills: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Kills: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
					$newCareerTotal = $careerTotal + $value;
					if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_attack_k'][0]) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_attack_k'][0]){
						if ($newCareerTotal > $vb_milestones['car_attack_k'])
							echo "<h4>***** Career Milestone Achieved for $name in Kills: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Kills: $newCareerTotal -----<br>";
					}
					//// echo "DEBUG: $name Kills - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;
					
				default:
					break;
			}
		}
	$sql = "INSERT INTO `wbb_boxscores`(`Value`, `AthleteID`, `GameID`, `Remarks`, `Stat`, `StatType`) VALUES ('$value','$athleteID','$gameID','$remarks','$stat','$statType')";

		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
	}
	else{
		$sql = "UPDATE `wbb_boxscores` SET `Value`='$value', `Remarks`='$remarks' WHERE `EntryID`='$duplicateResult'";
		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
		if ($name != "TEAM"){
			switch ($stat){
					//Check for Season and Career Milestones
				case ('points'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					if (($seasonTotal / $wbb_milestones['sea_points']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_points']){
						if ($seasonTotal > $wbb_milestones['sea_points'])
							echo "<h4>***** Season Milestone Achieved for $name in Points: $seasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Points: $seasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if ($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_points'][0]) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_points'][0]) {
						if ($careerTotal > $wbb_milestones['car_points'][0])
							echo "<h4>***** Career Milestone Achieved for $name in Points: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Points: $careerTotal -----<br>";
					}
					elseif ($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_points'][1]) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_points'][1]){
						if ($careerTotal > $wbb_milestones['car_points'][1])
							echo "<h4>***** Career Milestone Achieved for $name in Points: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Points: $careerTotal -----<br>";
						
					}
					elseif($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_points'][2]) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_points'][2]){
						if ($careerTotal > $wbb_milestones['car_points'][2])
							echo "<h4>***** Career Milestone Achieved for $name in Points: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Points: $careerTotal -----<br>";
					}
						
					// echo "DEBUG: $name Points - SEA: $seasonTotal | CAR: $careerTotal<br>";
					break;
					
				case ('fgm3'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					if (($seasonTotal / $wbb_milestones['sea_fgm3']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_fgm3']){
						if ($seasonTotal > $wbb_milestones['sea_fgm3'])
							echo "<h4>***** Season Milestone Achieved for $name in 3-Point Field Goals: $seasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in 3-Point Field Goals: $seasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_fgm3']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_fgm3'])){
						if ($careerTotal > $wbb_milestones['car_fgm3'])
							echo "<h4>***** Career Milestone Achieved for $name in 3-Point Field Goals: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in 3-Point Field Goals: $careerTotal -----<br>";
					}
					// echo "DEBUG: $name 3-Point Field Goals - SEA: $seasonTotal | CAR: $careerTotal<br>";
					break;
					
				case ('treb'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					if (($seasonTotal / $wbb_milestones['sea_treb']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_treb']){
						if ($seasonTotal > $wbb_milestones['sea_treb'])
							echo "<h4>***** Season Milestone Achieved for $name in Rebounds: $seasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Rebounds: $seasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_treb']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_treb'])){
						if ($careerTotal > $wbb_milestones['car_treb'])
							echo "<h4>***** Career Milestone Achieved for $name in Rebounds: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Rebounds: $careerTotal -----<br>";
					}
					// echo "DEBUG: $name Rebounds - SEA: $seasonTotal | CAR: $careerTotal<br>";
					break;
					
				case ('ast'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					if (($seasonTotal / $wbb_milestones['sea_ast']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_ast']){
						if ($seasonTotal > $wbb_milestones['sea_ast'])
							echo "<h4>***** Season Milestone Achieved for $name in Assists: $seasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Assists: $seasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_ast']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_ast'])){
						if ($careerTotal > $wbb_milestones['car_ast'])
							echo "<h4>***** Career Milestone Achieved for $name in Assists: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Assists: $careerTotal -----<br>";
					}
					// echo "DEBUG: $name Assists - SEA: $seasonTotal | CAR: $careerTotal<br>";
					break;
					
				case ('stl'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					if (($seasonTotal / $wbb_milestones['sea_stl']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_stl']){
						if ($seasonTotal > $wbb_milestones['sea_stl'])
							echo "<h4>***** Season Milestone Achieved for $name in Steals: $seasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Steals: $seasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_stl']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_stl'])){
						if ($careerTotal > $wbb_milestones['car_stl'])
							echo "<h4>***** Career Milestone Achieved for $name in Steals: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Steal: $careerTotal -----<br>";
					}
					// echo "DEBUG: $name Steals - SEA: $seasonTotal | CAR: $careerTotal<br>";
					break;
					
				case ('blk'):
					$seasonTotal = findSeasonStats($athleteID,'wbb',$stat,$season,'wbb_boxscores');
					if (($seasonTotal / $wbb_milestones['sea_blk']) * 100 > $wbb_milestones['percent'] && $seasonTotal < $wbb_milestones['sea_blk']){
						if ($seasonTotal > $wbb_milestones['sea_blk'])
							echo "<h4>***** Season Milestone Achieved for $name in Steals: $seasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Steals: $seasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'wbb_boxscores');
//					var_dump($careerTotal);
					//var_dump ($vb_milestones['car_attack_k']);
					if (($careerTotal != NULL && ($careerTotal / $wbb_milestones['car_blk']) * 100 > $wbb_milestones['percent'] && $careerTotal < $wbb_milestones['car_blk'])){
						if ($careerTotal > $wbb_milestones['car_blk'])
							echo "<h4>***** Career Milestone Achieved for $name in Blocks: $careerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Blocks: $careerTotal -----<br>";
					}
					// echo "DEBUG: $name Blocks - SEA: $seasonTotal | CAR: $careerTotal<br>";
					break;
					
				default:
					break;
			}
		}
	}
}

?>