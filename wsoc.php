<?php

function wsocRecords(){
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
		$table = "wsoc_boxscores";
		
		// Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
			$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
			$oldRecords = readOldDatabase($db_name,"fb_oldrecords",$requestedStat,$_GET['LEVEL']);
		
		if ($requestedLevel == "GAM" || $requestedLevel == "TMG"){
			if ($requestedStat == "") 
					printCurrentRecordsDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
				else
					printCurrentRecordsDatabase($currentRecords, $oldRecords, "wsoc", FALSE);
		}
		if ($requestedLevel == "SEA" || $requestedLevel == "TMS"){
			if ($requestedStat == "goals_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,GoalsScored,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)as 'GoalsScored', ROUND(SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY GoalsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,GoalsScored,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)as 'GoalsScored', ROUND(SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY GoalsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "assists_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "points_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,points,pointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'points', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as pointsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY pointsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,points,pointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'points', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as pointsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY pointsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "shots_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,shots,shotsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)as 'shots', ROUND(SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as shotsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY shotsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,shots,shotsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)as 'shots', ROUND(SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as shotsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY shotsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "sog_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,sog,sogPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)as 'sog', ROUND(SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as sogPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY sogPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,sog,sogPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)as 'sog', ROUND(SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as sogPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY sogPerGame DESC) as x WHERE x.GamesPlayed > 4 AND Value > 0");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "saves_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) as x WHERE x.GamesPlayed > 4 AND savesPerGame > 0");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) as x WHERE x.GamesPlayed > 4 AND savesPerGame > 0");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "saves_avg"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ShotsFaced,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END) as 'ShotsFaced',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) as x WHERE x.saves > 4 AND savesPerGame > 0");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ShotsFaced,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END) as 'ShotsFaced',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) as x WHERE x.saves > 4");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "goals_against_avg"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END) as 'GoalsAllowed',SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)as 'GoalieMinutes', ROUND((SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)*90),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID GROUP BY Season ORDER BY GoalsPerGame ASC;");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GoalsAllowed,GoalieMinutes,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END) as 'GoalsAllowed',SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)as 'GoalieMinutes', ROUND((SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)*90),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY GoalsPerGame ASC) as x WHERE x.GoalieMinutes > 4");
				}
				printSeasonDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
				else
					printSeasonDatabase($currentRecords, $oldRecords, "wsoc", FALSE);
		}
		if ($requestedLevel == "CAR"){
			if ($requestedStat == "goals_pergame"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,GoalsScored,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)as 'GoalsScored', ROUND(SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY GoalsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "assists_pergame"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AssistsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "points_pergame"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,points,pointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'points', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as pointsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY pointsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "shots_pergame"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,shots,shotsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)as 'shots', ROUND(SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as shotsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY shotsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "sog_pergame"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,sog,sogPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)as 'sog', ROUND(SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as sogPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY sogPerGame DESC) as x WHERE x.GamesPlayed > 4 AND GamesPlayed > 0");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "saves_pergame"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY savesPerGame DESC) as x WHERE x.GamesPlayed > 4 AND savesPerGame > 0");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "saves_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ShotsFaced,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END) as 'ShotsFaced',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY savesPerGame DESC) as x WHERE x.saves > 4");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "goals_against_avg"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GoalsAllowed,GoalieMinutes,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END) as 'GoalsAllowed',SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)as 'GoalieMinutes', ROUND((SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)*90),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY GoalsPerGame ASC) as x WHERE x.GoalieMinutes > 4");
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", TRUE); // TRUE is for outputting decimal precision
			}
			else
				printCareerDatabase($currentRecords, $oldRecords, "wsoc", FALSE);
		}
	}
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
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="wsoc_GAM">Individual Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="wsoc_SEA">Individual Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="wsoc_CAR">Career Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="wsoc_TMG">Team Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="wsoc_TMS">Team Season Highs</option>
					  </select>
				  </td>
				  </tr>
				  <tr>
				  <td>
					  
				  <?php
		if (($requestedLevel == "GAM" || $requestedLevel == "TMG") && $requestedLevel != "ATH"){
			?>
			<label for="indexStat">Select Stat</label>
			<select name="indexStat" id="indexStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves">Most Saves</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals">Most Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "assists") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-assists">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shots") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shots">Most Shots</option>
				<?php
		}
		else if ($requestedLevel == "TMS" || $requestedLevel == "CAR" || $requestedLevel == "SEA"){
		?>
			<label for="indexStat">Select Stat</label>
			<select name="indexStat" id="indexStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals">Most Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals_pergame">Most Goals Per Game (Min. 5 games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "assists") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-assists">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "assists_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-assists_pergame">Most Assists Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-points_pergame">Most Points Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shots") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shots">Most Shots</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shots_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shots_pergame">Most Shots Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sog") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-sog">Most Shots On Goal</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sog_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-sog_pergame">Most Shots On Goal Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pengoals") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-pengoals">Most Penalty Shot Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "penshots") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-penshots">Most Penalty Shot Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "type_gw") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-type_gw">Most Game-Winning Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves">Most Saves</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves_pergame">Most Saves Per Game (Min. 5 games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves_avg") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves_avg">Highest Save Percent (Min. 5 saves)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals_against_avg") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals_against_avg">Lowest Goals Against Avg (Min. 5 minutes)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "win") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-win">Most Wins</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "loss") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-loss">Most Losses</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tie") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-tie">Most Ties</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shutouts") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shutouts">Most Shutouts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "played") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-played">Most Games Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "starts") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-starts">Most Games Started</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "minutes") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-minutes">Most Minutes Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goalie_played") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goalie_played">Most Games Played As Goalie</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goalie_starts") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goalie_starts">Most Games Started As Goalie</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goalie_minutes") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goalie_minutes">Most Minutes Played As Goalie</option>
			</select>
	  </td>
</tr>
<?php
	}
}

function editwsocrecords(){
	
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
		<?php if (array_key_exists('SPORT',$_GET)) { ?>
			<td valign="top" width="50%">
				<table width="500" border="0">
					
			  <tbody>
			  <tr>
				  <td>
					  <label for="adminLevel">Make a selection:</label>
				  	<select name="adminLevel" id="adminLevel">
						<option value="NoSelection"></option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="wsoc_GAM">Individual Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="wsoc_SEA">Individual Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="wsoc_CAR">Career Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="wsoc_TMG">Team Game Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="wsoc_TMS">Team Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATH") echo "selected" ?> value="wsoc_ATH">Active Athletes</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATH") echo "selected" ?> value="wsoc_ATR">Retired Athletes</option>
					  </select>
				  </td>
				  <td>
				  <?php
		}
		if (($requestedLevel == "GAM" || $requestedLevel == "TMG") && $requestedLevel != "ATH"){
			?>
		<label for="adminStat">Select Stat</label>
		<select name="adminStat" id="adminStat">
		<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves">Most Saves</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals">Most Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "assists") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-assists">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shots") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shots">Most Shots</option>
		</select>
				<?php
		//-------------------------Start Records Code for Pasting-------------------------------
			$recordsCode = "<table width=\"100%\">";
			
		//--------------------------Points------------------------------------------------------
			$currentRecords = readCurrentDatabase($db_name, 'wsoc_boxscores','points',$_GET['LEVEL']);
			$oldRecords = Array();
			$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'wsoc',$_GET['LEVEL']);
			$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Points</h2>".$newRecordsCode."</td>";
		
		//-------------------------Goals----------------------------------------------------------
			$currentRecords = readCurrentDatabase($db_name, 'wsoc_boxscores','goals',$_GET['LEVEL']);
			$oldRecords = Array();
			$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'wsoc',$_GET['LEVEL']);
			$recordsCode = $recordsCode."<td align=\"center\"><h2>Goals</h2>".$newRecordsCode."</td></tr>";
			
		//-------------------------Assists----------------------------------------------------------
			$currentRecords = readCurrentDatabase($db_name, 'wsoc_boxscores','assists',$_GET['LEVEL']);
			$oldRecords = Array();
			$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'wsoc',$_GET['LEVEL']);
			$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Assists</h2>".$newRecordsCode."</td>";
			
		//-------------------------Shots----------------------------------------------------------
			$currentRecords = readCurrentDatabase($db_name, 'wsoc_boxscores','shots',$_GET['LEVEL']);
			$oldRecords = Array();
			$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'wsoc',$_GET['LEVEL']);
			$recordsCode = $recordsCode."<td align=\"center\"><h2>Shots</h2>".$newRecordsCode."</td></tr>";
			
		//-------------------------Saves----------------------------------------------------------
			$currentRecords = readCurrentDatabase($db_name, 'wsoc_boxscores','saves',$_GET['LEVEL']);
			$oldRecords = Array();
			$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'wsoc',$_GET['LEVEL']);
			$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Saves</h2>".$newRecordsCode."</td></tr></table>";
			
		//------------------------Display Code to Paste-------------------------------------------
			?>
			<table>
	<tbody>
		<tr>
			<td>
				<h4>Records Code</h4>
				<textarea rows="10" cols="75" readonly wrap="soft"><?php echo htmlspecialchars($recordsCode)?></textarea>
			</td>
	  </tr>
	</tbody>
</table>
					  <?php
		}
		else if ($requestedLevel == "TMS" || $requestedLevel == "CAR" || $requestedLevel == "SEA"){
		?>
		<label for="adminStat">Select Stat</label>
		<select name="adminStat" id="adminStat">
		<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals">Most Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals_pergame">Most Goals Per Game (Min. 5 games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "assists") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-assists">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "assists_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-assists_pergame">Most Assists Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-points">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "points_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-points_pergame">Most Points Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shots") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shots">Most Shots</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shots_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shots_pergame">Most Shots Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sog") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-sog">Most Shots On Goal</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "sog_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-sog_pergame">Most Shots On Goal Per Game (Min. 5 Games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "pengoals") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-pengoals">Most Penalty Shot Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "penshots") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-penshots">Most Penalty Shot Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "type_gw") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-type_gw">Most Game-Winning Goals</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves">Most Saves</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves_pergame") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves_pergame">Most Saves Per Game (Min. 5 games)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "saves_avg") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-saves_avg">Highest Save Percent (Min. 5 saves)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goals_against_avg") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goals_against_avg">Lowest Goals Against Avg (Min. 5 minutes)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "win") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-win">Most Wins</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "loss") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-loss">Most Losses</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "tie") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-tie">Most Ties</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "shutouts") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-shutouts">Most Shutouts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "played") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-played">Most Games Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "starts") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-starts">Most Games Started</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "minutes") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-minutes">Most Minutes Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goalie_played") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goalie_played">Most Games Played As Goalie</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goalie_starts") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goalie_starts">Most Games Started As Goalie</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "goalie_minutes") echo "selected" ?> value="wsoc_<?php echo $_GET['LEVEL']?>-goalie_minutes">Most Minutes Played As Goalie</option>
		</select>
	  </td>
</tr>
<?php
			
			}
	if($requestedStat != NULL)
	{
		if ($requestedLevel != NULL && $requestedStat != NULL){
			$db_name = "kcmsathl_wvsu";
		// Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
			$currentRecords = readCurrentDatabase($db_name,"wsoc_boxscores",$requestedStat,$_GET['LEVEL']);
			$oldRecords = readOldDatabase($db_name,"wsoc_oldrecords",$requestedStat,$_GET['LEVEL']);
		// Game Highs. Shouldn't need many custom scripts.
			if ($requestedLevel == "GAM" || $requestedLevel == "TMG"){
				if ($requestedStat == "assists"){
					$currentRecords = readCurrentDatabase($db_name,"wsoc_boxscores",$requestedStat,$_GET['LEVEL'],"SELECT * FROM `wsoc_boxscores` as b LEFT OUTER JOIN `wsoc_games` AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN wsoc_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='assists' AND `Value` > 1 AND `Name`!='TEAM' ORDER BY `Value` DESC");
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, FALSE);
				}
				else
					$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, FALSE);
			}
			$table = "wsoc_boxscores";
			if ($requestedStat == "points"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,Points as Value,SeasonsActive,Season,IsActive,Goals,Assists,CONCAT(ROUND(Goals,0),'g, ',ROUND(Assists,0),'a') as Remarks FROM (SELECT Name,ROUND(AVG(Value),3) as PointsPerGame,SeasonsActive,Season,IsActive,SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END) as 'Goals', SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END) as 'Assists', SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END) as 'Points' FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE 1");
				}
			}
			if ($requestedStat == "goals_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' AND `Stat`='goals' GROUP BY Name,Season ORDER BY GoalsperGame DESC) as x WHERE 1");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,GoalsScored,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)as 'GoalsScored', ROUND(SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY GoalsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "CAR"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,GoalsScored,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)as 'GoalsScored', ROUND(SUM(CASE WHEN `Stat`='goals' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY GoalsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
			}
			elseif ($requestedStat == "assists_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' AND `Stat`='assists' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE 1");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "CAR"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='assists' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AssistsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
			}
			elseif ($requestedStat == "points_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' AND `Stat`='points' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE 1");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,points,pointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'points', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as pointsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY pointsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				elseif ($requestedLevel == "CAR"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,points,pointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)as 'points', ROUND(SUM(CASE WHEN `Stat`='points' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as pointsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY pointsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
			}
			elseif ($requestedStat == "shots_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ShotsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as ShotsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' AND `Stat`='shots' GROUP BY Name,Season ORDER BY ShotsPerGame DESC) as x WHERE 1");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,shots,shotsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)as 'shots', ROUND(SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as shotsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY shotsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				else if ($requestedLevel == "CAR"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,shots,shotsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)as 'shots', ROUND(SUM(CASE WHEN `Stat`='shots' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as shotsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY shotsPerGame DESC) as x WHERE x.GamesPlayed > 4");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
			}
			elseif ($requestedStat == "sog_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SogPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as SogPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' AND `Stat`='sog' GROUP BY Name,Season ORDER BY SogPerGame DESC) as x WHERE 1");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,sog,sogPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)as 'sog', ROUND(SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as sogPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY sogPerGame DESC) as x WHERE x.GamesPlayed > 4 AND sogPerGame > 0");
				}
				else if ($requestedLevel == "CAR"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,sog,sogPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)as 'sog', ROUND(SUM(CASE WHEN `Stat`='sog' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as sogPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY sogPerGame DESC) as x WHERE x.GamesPlayed > 4 AND GamesPlayed > 0");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
			}
			elseif ($requestedStat == "saves_pergame"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SavesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,ROUND(AVG(Value),3) as SavesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' AND `Stat`='saves' GROUP BY Name,Season ORDER BY SavesPerGame DESC) as x WHERE 1");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) as x WHERE x.GamesPlayed > 4 AND savesPerGame > 0");
				}
				else if ($requestedLevel == "CAR"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GamesPlayed,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END) as 'GamesPlayed',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='played' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY savesPerGame DESC) as x WHERE x.GamesPlayed > 4 AND savesPerGame > 0");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
			}
			elseif ($requestedStat == "saves_avg"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ShotsFaced,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END) as 'ShotsFaced',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) as x WHERE 1");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ShotsFaced,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END) as 'ShotsFaced',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY savesPerGame DESC) as x WHERE x.saves > 4");
				}
				else if ($requestedLevel == "CAR"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ShotsFaced,saves,savesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END) as 'ShotsFaced',SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)as 'saves', ROUND(SUM(CASE WHEN `Stat`='saves' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_sf' THEN Value ELSE 0 END),3) as savesPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY savesPerGame DESC) as x WHERE x.saves > 4");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
			}
			elseif ($requestedStat == "goals_against_avg"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END) as 'GoalsAllowed',SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)as 'GoalieMinutes', ROUND((SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)*90),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID GROUP BY Season ORDER BY GoalsPerGame ASC;");
				}
				else if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GoalsAllowed,GoalieMinutes,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END) as 'GoalsAllowed',SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)as 'GoalieMinutes', ROUND((SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)*90),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY GoalsPerGame ASC) as x WHERE x.GoalieMinutes > 14");
					
				}
				else if ($requestedLevel == "CAR"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,GoalsAllowed,GoalieMinutes,GoalsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END) as 'GoalsAllowed',SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)as 'GoalieMinutes', ROUND((SUM(CASE WHEN `Stat`='goalie_goalsallowed' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='goalie_minutes' THEN Value ELSE 0 END)*90),3) as GoalsPerGame,SeasonsActive,Season,IsActive FROM `wsoc_boxscores` AS boxscores LEFT OUTER JOIN wsoc_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN wsoc_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY GoalsPerGame ASC) as x WHERE x.GoalieMinutes > 4");
				}
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, TRUE);
		}
			elseif ($requestedStat == "win"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(Value) AS Value,Season FROM `wsoc_boxscores` as b LEFT OUTER JOIN `wsoc_games` AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN wsoc_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='win' AND `Name`!='TEAM' GROUP BY `Season` ORDER BY `Value` DESC");
				}
				else
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, FALSE);
			}
			elseif ($requestedStat == "loss"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(Value) AS Value,Season FROM `wsoc_boxscores` as b LEFT OUTER JOIN `wsoc_games` AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN wsoc_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='loss' AND `Name`!='TEAM' GROUP BY `Season` ORDER BY `Value` DESC");
				}
				else
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, FALSE);
			}
			elseif ($requestedStat == "tie"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(Value) AS Value,Season FROM `wsoc_boxscores` as b LEFT OUTER JOIN `wsoc_games` AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN wsoc_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='tie' AND `Name`!='TEAM' GROUP BY `Season` ORDER BY `Value` DESC");
				}
				else
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
			}
		else
				$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "wsoc", $requestedLevel, FALSE);
	echo ("Manual Record Editing - ".$_GET['LEVEL'].": ".$_GET['STAT']);
		$oldResultsString = readOldDatabase("kcmsathl_WVSU","wsoc_oldrecords",$_GET['STAT'],$_GET['LEVEL']);
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
	}
	elseif($requestedLevel == "ATH"){
		echo "Active Athlete Administration";
		athleteAdministration("wsoc","A");
	}
	elseif($requestedLevel == "ATR"){
		echo "Retired Athlete Administration";
		athleteAdministration("wsoc","R");
	}

	else{
		echo "<h2>Please select Stat Category.</h2>";
		?>
		</td>
		
	  <?php
		}
}
function uploadWsocBox($ArrayRead){
	$db_name = "wsoc";
	//Format Date for use in database
	$date = $ArrayRead['venue']['@attributes']['date'];
	$time = $ArrayRead['venue']['@attributes']['start'];
	$time_input = strtotime($date);
	$formattedDate = date('Y-m-d',$time_input);
	$locationOfGame = NULL;
	
	if($ArrayRead["team"][0]["@attributes"]["code"] == 1439){ // Look for good guys by NCAA RPI number. Should eliminate variance on how the team name is typed.
		$teamArray = $ArrayRead['team'][0];
		$homeAway = "A";
		$opponentName = $ArrayRead['team'][1]['@attributes']['name'];
		$opponent = $ArrayRead['team'][1]['@attributes']['code'];
		$homeScore = $ArrayRead['team'][0]['linescore']['@attributes']['score']; // Not actually Home Team, but the team we are looking for AKA the good guys.
		$awayScore = $ArrayRead['team'][1]['linescore']['@attributes']['score']; // Opponent, regardless of location
	}
	else if ($ArrayRead["team"][1]["@attributes"]["code"] == 1439){
		$teamArray = $ArrayRead['team'][1];
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
	if (array_key_exists('pts',$teamTotals['shots']['@attributes']))
		createPlayerStatWsoc("TEAM","points",$teamTotals['shots']['@attributes']['pts'],$GameID);
	else{
		$points = ($teamTotals['shots']['@attributes']['g'] * 2) + $teamTotals['shots']['@attributes']['a'];
		createPlayerStatWsoc("TEAM","points",$points,$GameID);
	}
	createPlayerStatWsoc("TEAM","goals",$teamTotals['shots']['@attributes']['g'],$GameID);
	createPlayerStatWsoc("TEAM","assists",$teamTotals['shots']['@attributes']['a'],$GameID);
	createPlayerStatWsoc("TEAM","shots",$teamTotals['shots']['@attributes']['sh'],$GameID);
	createPlayerStatWsoc("TEAM","pengoals",$teamTotals['shots']['@attributes']['ps'],$GameID);
	createPlayerStatWsoc("TEAM","penshots",$teamTotals['shots']['@attributes']['psatt'],$GameID);
	createPlayerStatWsoc("TEAM","sog",$teamTotals['shots']['@attributes']['sog'],$GameID);
	createPlayerStatWsoc("TEAM","type_gw",$teamTotals['goaltype']['@attributes']['gw'],$GameID);
	createPlayerStatWsoc("TEAM","type_ua",$teamTotals['goaltype']['@attributes']['ua'],$GameID);
	createPlayerStatWsoc("TEAM","type_fg",$teamTotals['goaltype']['@attributes']['fg'],$GameID);
	createPlayerStatWsoc("TEAM","type_ot",$teamTotals['goaltype']['@attributes']['ot'],$GameID);
	createPlayerStatWsoc("TEAM","type_hat",$teamTotals['goaltype']['@attributes']['hat'],$GameID);
	createPlayerStatWsoc("TEAM","type_gt",$teamTotals['goaltype']['@attributes']['gt'],$GameID);
	createPlayerStatWsoc("TEAM","pen_cards",$teamTotals['penalty']['@attributes']['count'],$GameID);
	createPlayerStatWsoc("TEAM","pen_yellow",$teamTotals['penalty']['@attributes']['yellow'],$GameID);
	createPlayerStatWsoc("TEAM","pen_red",$teamTotals['penalty']['@attributes']['red'],$GameID);
	createPlayerStatWsoc("TEAM","pen_fouls",$teamTotals['penalty']['@attributes']['fouls'],$GameID);
	createPlayerStatWsoc("TEAM","goalie_ga",$teamTotals['goalie']['@attributes']['ga'],$GameID);
	createPlayerStatWsoc("TEAM","saves",$teamTotals['goalie']['@attributes']['saves'],$GameID);
	createPlayerStatWsoc("TEAM","goalie_sf",$teamTotals['goalie']['@attributes']['sf'],$GameID);
	
	$numOfPlayers = sizeof($teamArray['player']);
	
	for($currPlayer = 0; $currPlayer < $numOfPlayers; $currPlayer ++):
	
	$currArray = $teamArray['player'][$currPlayer];
	$athleteName = $currArray['@attributes']['name'];
	
	if ($athleteName == "TEAM" || $currArray['@attributes']['gp'] == 0)
		continue;
	createPlayerStatWsoc("$athleteName","played",$currArray['@attributes']['gp'],$GameID);
	if (array_key_exists("gs",$currArray['@attributes']))
		createPlayerStatWsoc("$athleteName","starts",$currArray['@attributes']['gs'],$GameID);
	
	if (array_key_exists("shots",$currArray)){
		if ($currArray['shots']['@attributes']['g'] > 0)
			createPlayerStatWsoc("$athleteName","goals",$currArray['shots']['@attributes']['g'],$GameID);
		if ($currArray['shots']['@attributes']['a'] > 0)
			createPlayerStatWsoc("$athleteName","assists",$currArray['shots']['@attributes']['a'],$GameID);
		if (array_key_exists('pts',$teamTotals['shots']['@attributes'])){
			if ($currArray['shots']['@attributes']['pts'] > 0)
				createPlayerStatWsoc("$athleteName","points",$currArray['shots']['@attributes']['pts'],$GameID);
		}
		else{
			$points = ($currArray['shots']['@attributes']['g'] * 2) + $currArray['shots']['@attributes']['a'];
			createPlayerStatWsoc("$athleteName","points",$points,$GameID);
		}
		if ($currArray['shots']['@attributes']['sh'] > 0)
			createPlayerStatWsoc("$athleteName","shots",$currArray['shots']['@attributes']['sh'],$GameID);
		if ($currArray['shots']['@attributes']['ps'] > 0)
			createPlayerStatWsoc("$athleteName","pengoals",$currArray['shots']['@attributes']['ps'],$GameID);
		if ($currArray['shots']['@attributes']['psatt'] > 0)
			createPlayerStatWsoc("$athleteName","penshots",$currArray['shots']['@attributes']['psatt'],$GameID);
		if ($currArray['shots']['@attributes']['sog'] > 0)
			createPlayerStatWsoc("$athleteName","sog",$currArray['shots']['@attributes']['sog'],$GameID);
	}
	if (array_key_exists("goaltype",$currArray)){
		if ($currArray['goaltype']['@attributes']['gw'] > 0)
			createPlayerStatWsoc("$athleteName","type_gw",$currArray['goaltype']['@attributes']['gw'],$GameID);
		if ($currArray['goaltype']['@attributes']['ua'] > 0)
			createPlayerStatWsoc("$athleteName","type_ua",$currArray['goaltype']['@attributes']['ua'],$GameID);
		if ($currArray['goaltype']['@attributes']['fg'] > 0)
			createPlayerStatWsoc("$athleteName","type_fg",$currArray['goaltype']['@attributes']['fg'],$GameID);
		if ($currArray['goaltype']['@attributes']['ot'] > 0)
			createPlayerStatWsoc("$athleteName","type_ot",$currArray['goaltype']['@attributes']['ot'],$GameID);
		if ($currArray['goaltype']['@attributes']['hat'] > 0)
			createPlayerStatWsoc("$athleteName","type_hat",$currArray['goaltype']['@attributes']['hat'],$GameID);
		if ($currArray['goaltype']['@attributes']['gt'] > 0)
			createPlayerStatWsoc("$athleteName","type_gt",$currArray['goaltype']['@attributes']['gt'],$GameID);
	}
	if (array_key_exists("penalty",$currArray)){
		if ($currArray['penalty']['@attributes']['count'] > 0)
			createPlayerStatWsoc("$athleteName","pen_count",$currArray['penalty']['@attributes']['count'],$GameID);
		if ($currArray['penalty']['@attributes']['yellow'] > 0)
			createPlayerStatWsoc("$athleteName","pen_yellow",$currArray['penalty']['@attributes']['yellow'],$GameID);
		if ($currArray['penalty']['@attributes']['red'] > 0)
			createPlayerStatWsoc("$athleteName","pen_red",$currArray['penalty']['@attributes']['red'],$GameID);
		if ($currArray['penalty']['@attributes']['fouls'] > 0)
			createPlayerStatWsoc("$athleteName","pen_fouls",$currArray['penalty']['@attributes']['fouls'],$GameID);
	}
	if (array_key_exists("misc",$currArray)){
		createPlayerStatWsoc("$athleteName","minutes",$currArray['misc']['@attributes']['minutes'],$GameID);
	}
	if (array_key_exists("goalie",$currArray)){
		createPlayerStatWsoc("$athleteName","goalie_played",$currArray['goalie']['@attributes']['gp'],$GameID);
		createPlayerStatWsoc("$athleteName","goalie_starts",$currArray['goalie']['@attributes']['gs'],$GameID);
		$goalie_minutes_array = explode(":",$currArray['goalie']['@attributes']['minutes']);
		$goalie_minutes = $goalie_minutes_array[0] + ($goalie_minutes_array[1] / 60);
		createPlayerStatWsoc("$athleteName","goalie_minutes",$goalie_minutes,$GameID);
		createPlayerStatWsoc("$athleteName","goalie_goalsallowed",$currArray['goalie']['@attributes']['ga'],$GameID);
		createPlayerStatWsoc("$athleteName","goalie_sf",$currArray['goalie']['@attributes']['sf'],$GameID);
		createPlayerStatWsoc("$athleteName","saves",$currArray['goalie']['@attributes']['saves'],$GameID);
		if (array_key_exists("win",$currArray['goalie']['@attributes']))
			createPlayerStatWsoc("$athleteName","win","1",$GameID);
		elseif (array_key_exists("loss",$currArray['goalie']['@attributes']))
			createPlayerStatWsoc("$athleteName","loss","1",$GameID);
		elseif (array_key_exists("tie",$currArray['goalie']['@attributes']))
			createPlayerStatWsoc("$athleteName","tie","1",$GameID);
		if (array_key_exists("shutout", $currArray['goalie']['@attributes'])){
			createPlayerStatWsoc("$athleteName","shutouts","1",$GameID);
		}
	}
	endfor;
}

function createPlayerStatWsoc($name, $stat, $value, $gameID, $season = "", $remarks = ""){
	$config = parse_ini_file('config.ini');
	$fb_milestones = parse_ini_file('wsoc.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
	if ($name == "TEAM"){
		$athleteID = 0;
		$statType = "TMG";
	}
	else{
		$statType = "GAM";
		$name = checkName($name);
		$athlQuery = "SELECT * FROM `wsoc_athletes` WHERE `Name`='$name' AND `IsActive`='1'"; // Search for athlete in database. If found, get Athlete ID, otherwise create a new athlete in the database and assign an ID number to him/her.
		$athlResults = mysqli_query($conn, $athlQuery);
		$finalAthlResult = mysqli_fetch_assoc($athlResults);
		if (mysqli_num_rows($athlResults) > 0)
			$athleteID = $finalAthlResult['AthleteID'];
		else
			$athleteID = createAthlete($name, 'wsoc', $season);
	}
	
	//If duplicate is found, update existing record instead of creating an new one. This will reduce the numbers in the records database which will start to get massive quickly.
	$dupQuery = "SELECT * FROM `wsoc_boxscores` WHERE `GameID`='$gameID' AND `Stat`='$stat' AND `AthleteID`='$athleteID' ORDER BY `EntryID` DESC";
	$dupResults = mysqli_query($conn, $dupQuery);
	$finalDupResult = mysqli_fetch_assoc($dupResults);
	if(mysqli_num_rows($dupResults)>0)
		$duplicateResult = $finalDupResult['EntryID'];
	else
		$duplicateResult = -1;
	
	if ($duplicateResult < 0){ 
	$sql = "INSERT INTO `wsoc_boxscores`(`Value`, `AthleteID`, `GameID`, `Remarks`, `Stat`, `StatType`) VALUES ('$value','$athleteID','$gameID','$remarks','$stat','$statType')";

		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
	}
	else{
		$sql = "UPDATE `wsoc_boxscores` SET `Value`='$value', `Remarks`='$remarks' WHERE `EntryID`='$duplicateResult'";
		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
	}
	
}


?>