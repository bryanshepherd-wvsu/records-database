<?php

function vbRecords()
{
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
		$table = "vb_boxscores";
		
		// Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
			$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
			$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL']);
		
		if ($requestedLevel == "GAM"){
			if ($requestedStat == "attack_k"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='attack_k3' OR `Stat`='attack_k4' OR `Stat`='attack_k5' AND `Name`!='TEAM' AND `StatType`='GAM' ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			if ($requestedStat == "attack_k3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_k4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_k5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "attack_pct"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT IsActive,AttackPercentage as Value,AthleteID,GameID FROM (SELECT boxscores.AthleteID,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive,boxscores.GameID FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID LEFT OUTER JOIN `NCAA_RPI` as r on games.RPI=r.RPI WHERE `Name`!='TEAM' GROUP BY boxscores.GameID ORDER BY AttackPercentage DESC) as x");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 1); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "attack_ta"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`!='TEAM' AND `StatType`='GAM' AND (`Stat`='attack_ta3' OR `Stat`='attack_ta4' OR `Stat`='attack_ta5') ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_ta3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_ta4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_ta5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "serve_sa3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "serve_sa4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "serve_sa5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "set_a"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`!='TEAM' AND `StatType`='GAM' AND (`Stat`='set_a3' OR `Stat`='set_a4' OR `Stat`='set_a5') ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "set_a3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "set_a4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "set_a5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "defense_dig3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "defense_dig4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "defense_dig5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "block_tb"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE (`Stat`='block_tb3' OR `Stat`='block_tb4' OR `Stat`='block_tb5') AND `Name`!='TEAM' AND `StatType`='GAM' ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "block_tb3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "block_tb4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "block_tb5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			else
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE);
		} // End Game Stats
		
		elseif ($requestedLevel == "TMG"){
			if ($requestedStat == "attack_k3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_k4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_k5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "attack_pct"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT IsActive,AttackPercentage as Value,AthleteID,GameID FROM (SELECT boxscores.AthleteID,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive,boxscores.GameID FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID LEFT OUTER JOIN `NCAA_RPI` as r on games.RPI=r.RPI WHERE `Name`='TEAM' GROUP BY boxscores.GameID ORDER BY AttackPercentage DESC) as x");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 1); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "attack_ta"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='attack_ta3' OR `Stat`='attack_ta4' OR `Stat`='attack_ta5' AND `Name`='TEAM' AND `StatType`='TMG' ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_ta3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_ta4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "attack_ta5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "serve_sa"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='serve_sa3' OR `Stat`='serve_sa4' OR `Stat`='serve_sa5' AND `Name`='TEAM' AND `StatType`='TMG' ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "serve_sa3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "serve_sa4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "serve_sa5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "set_a"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='set_a3' OR `Stat`='set_a4' OR `Stat`='set_a5' AND `Name`='TEAM' AND `StatType`='TMG' ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "set_a3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "set_a4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "set_a5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "block_tb"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='block_tb3' OR `Stat`='block_tb4' OR `Stat`='block_tb5' AND `Name`='TEAM' AND `StatType`='TMG' ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "block_tb3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "block_tb4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "block_tb5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			}
			elseif($requestedStat == "defense_dig"){
				$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='defense_dig3' OR `Stat`='defense_dig4' OR `Stat`='defense_dig5' AND `Name`='TEAM' AND `StatType`='TMG' ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "defense_dig3") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "defense_dig4") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "defense_dig5") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			else
				printCurrentRecordsDatabase($currentRecords, $oldRecords, "vb", FALSE);
			
		}
		//---------------------------------------------------------------------------------------
		elseif ($requestedLevel == "SEA"){
			if ($requestedStat == "attack_kperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY KillsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "attack_pct"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AttackPercentage DESC) as x WHERE x.TotalAttacks >= 100");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "set_aperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "serve_saperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AcesPerGame DESC) as x WHERE x.SetsPlayed >= 25");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "serve_pct"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY ServePercentage DESC) as x WHERE x.Serves >= 100");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "defense_tapct"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY ReceptionPercentage DESC) as x WHERE x.Receives >= 100");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "block_tbperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY BlocksPerGame DESC) as x WHERE x.SetsPlayed >= 25");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "misc_ptsperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "defense_digperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY DigsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
				}
			}
			elseif ($requestedStat == "total_sp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
				printSeasonDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "total_mp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
				printSeasonDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts"){
				 $currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
				 printSeasonDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			 }
			else
				printSeasonDatabase($currentRecords, $oldRecords, "vb", FALSE);
		}
		
		 elseif ($requestedLevel == "TMS"){
			if ($requestedStat == "attack_kperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,SeasonsActive,Season,IsActive, ROUND(Kills/SetsPlayed,3) as Value FROM ( SELECT Name, SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', SeasonsActive, SetsPlayed, games.Season, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID RIGHT OUTER JOIN (SELECT SUM(Sets) as SetsPlayed,Season FROM vb_games WHERE 1 GROUP BY Season) setsSum on setsSum.Season = games.Season WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY Kills DESC) as x ORDER BY Value DESC");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			 elseif ($requestedStat == "block_tb"){
				 printSeasonDatabase($currentRecords,$oldRecords,"vb",2);
			 }
			elseif ($requestedStat == "attack_pct"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AttackPercentage DESC) as x");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "set_aperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,ROUND(Assists / SetsPlayed, 3) AS Value,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'set_a' THEN VALUE ELSE 0 END) AS 'Assists', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT SUM(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "serve_saperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,ServiceAces,ROUND(ServiceAces / SetsPlayed, 3) AS Value,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'serve_sa' THEN VALUE ELSE 0 END) AS 'ServiceAces', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT SUM(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "serve_pct"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID  WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY ServePercentage DESC) as x");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "defense_tapct"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY ReceptionPercentage DESC) as x");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "block_tbperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,TotalBlocks,ROUND(TotalBlocks / SetsPlayed, 3) AS Value,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'block_tb' THEN VALUE ELSE 0 END) AS 'TotalBlocks', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT SUM(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "misc_ptsperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,ROUND(Points / SetsPlayed, 3) AS Value,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'misc_pts' THEN VALUE ELSE 0 END) AS 'Points', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT SUM(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "defense_digperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,ROUND(Digs / SetsPlayed, 3) AS Value,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'defense_dig' THEN VALUE ELSE 0 END) AS 'Digs', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT SUM(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
					printSeasonDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "total_sp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed as Value,Digs,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'defense_dig' THEN VALUE ELSE 0 END) AS 'Digs', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT SUM(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
				printSeasonDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "total_mp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed as Value,Digs,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'defense_dig' THEN VALUE ELSE 0 END) AS 'Digs', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT COUNT(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
				printSeasonDatabase($currentRecords, $oldRecords, "vb", FALSE); // TRUE is for outputting decimal precision
			}
			elseif ($requestedStat == "misc_pts"){
				 $currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL']);
				 printSeasonDatabase($currentRecords, $oldRecords, "vb", 2); // TRUE is for outputting decimal precision
			 }
			else
				printSeasonDatabase($currentRecords, $oldRecords, "vb", FALSE);
		 }
		
		elseif ($requestedLevel == "CAR"){
			if ($requestedStat == "attack_kperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY KillsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
				}
			elseif ($requestedStat == "attack_pct"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AttackPercentage DESC) as x WHERE x.TotalAttacks >= 400");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "set_aperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AssistsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "serve_saperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AcesPerGame DESC) as x WHERE x.SetsPlayed >= 60");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "serve_pct"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY ServePercentage DESC) as x WHERE x.Serves >= 100");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "defense_tapct"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY ReceptionPercentage DESC) as x WHERE x.Receives >= 100");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "block_tbperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY BlocksPerGame DESC) as x WHERE x.SetsPlayed >= 60");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "misc_ptsperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY PointsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
					printCareerDatabase($currentRecords,$oldRecords,"vb",1);
			}
			elseif ($requestedStat == "misc_pts"){
					printCareerDatabase($currentRecords,$oldRecords,"vb",2);
			}
			elseif ($requestedStat == "defense_digperset"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY DigsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
					printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "total_sp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name ORDER BY `Value` DESC");
				printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE);
			}
			elseif ($requestedStat == "total_mp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name ORDER BY `Value` DESC");
				printCareerDatabase($currentRecords,$oldRecords,"vb",TRUE); // TRUE is for outputting decimal precision
			}
			else
				printCareerDatabase($currentRecords,$oldRecords,"vb",FALSE);
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
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="vb_GAM">Individual Match Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="vb_SEA">Individual Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="vb_CAR">Career Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="vb_TMG">Team Match Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="vb_TMS">Team Season Highs</option>
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
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Total Kills</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Total Assists</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Total Digs</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k3">Most Kills (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta3">Most Attacks (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts3">Most Points (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa3">Most Aces (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a3">Most Assists (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig3">Most Digs (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb3">Most Total Blocks (3 sets)</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k4">Most Kills (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta4">Most Attacks (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts4">Most Points (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa4">Most Aces (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a4">Most Assists (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig4">Most Digs (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb4">Most Total Blocks (4 sets)</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k5">Most Kills (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta5">Most Attacks (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts5">Most Points (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa5">Most Aces (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a5">Most Assists (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig5">Most Digs (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb5">Most Total Blocks (5 sets)</option>
				<?php
		}
		else if ($requestedLevel == "CAR"){
		?>
			<label for="indexStat">Select Stat</label>
			<select name="indexStat" id="indexStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Kills</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_kperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_kperset">Most Kills Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage (Minimum 400 attempts)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_aperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_aperset">Most Assists Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_saperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_saperset">Most Aces Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_ta">Most Service Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_pct">Highest Service Percent (Minimum 100 serves)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_tapct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_tapct">Highest Reception Percent (Minimum 100 receptions)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tbperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tbperset">Highest Blocks Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_ptsperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_ptsperset">Most Points Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Digs</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_digperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_digperset">Most Digs Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_sp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_sp">Most Sets Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_mp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_mp">Most Matches Played</option>
			</select>
	  </td>
</tr>
<?php
	}
		else if ($requestedLevel == "TMS" || $requestedLevel == "SEA"){
		?>
			<label for="indexStat">Select Stat</label>
			<select name="indexStat" id="indexStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Kills</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_kperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_kperset">Most Kills Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage (Minimum 100 attempts)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_aperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_aperset">Most Assists Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_saperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_saperset">Most Aces Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_ta">Most Service Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_pct">Highest Service Percent (Minimum 100 serves)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_tapct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_tapct">Highest Reception Percent (Minimum 100 receptions)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tbperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tbperset">Highest Blocks Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_ptsperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_ptsperset">Most Points Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Digs</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_digperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_digperset">Most Digs Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_sp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_sp">Most Sets Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_mp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_mp">Most Matches Played</option>
			</select>
	  </td>
</tr>
<?php
	}
}

function editvbrecords()
{
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
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="vb_GAM">Individual Match Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="vb_SEA">Individual Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="vb_CAR">Career Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="vb_TMG">Team Match Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="vb_TMS">Team Season Highs</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "UGM") echo "selected" ?> value="vb_UGM">Uploaded Games</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATH") echo "selected" ?> value="vb_ATH">Active Athletes</option>
						<option <?php if (array_key_exists('LEVEL',$_GET) && $_GET['LEVEL'] == "ATR") echo "selected" ?> value="vb_ATR">Retired Athletes</option>
					  </select>
				  </td>
				  <td>
				  <?php
		}
	if ($requestedLevel == "TMG"){
		
		//------------------------Start Overall Records Code-----------------
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Kills</h1></td></tr>";
		
		//------------Total Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_k',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='attack_k3' OR `Stat`='attack_k4' OR `Stat`='attack_k5' AND `Name`='TEAM' AND `StatType`='TMG' ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Kills</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Kills (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Kills (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Kills (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------Attack Percentage---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT IsActive,AttackPercentage as Value,Name,Date,Opponent,`Home/Away` FROM (SELECT SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive,Date,Opponent,athletes.Name,`Home/Away` FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID LEFT OUTER JOIN `NCAA_RPI` as r on games.RPI=r.RPI WHERE `Name`='TEAM' GROUP BY boxscores.GameID ORDER BY AttackPercentage DESC) as x");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Attack Percentage</h2>".$newRecordsCode."</td>";
		
		//--------------------------New Header: Attack Attempts-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Attack Attempts</h1></td></tr>";
		
		//------------Total Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`='TEAM' AND `StatType`='TMG' AND (`Stat`='attack_ta3' OR `Stat`='attack_ta4' OR `Stat`='attack_ta5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Attacks</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Attacks (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Attacks (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Attacks (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Points-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Points</h1></td></tr>";
		
		//------------Total Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','misc_pts',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='misc_pts3' OR `Stat`='misc_pts4' OR `Stat`='misc_pts5' AND `Name`='TEAM' AND `StatType`='TMG' ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Points</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Points (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Points (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Points (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Assists-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Assists</h1></td></tr>";
		
		//------------Total Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','set_a',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`='TEAM' AND `StatType`='TMG' AND (`Stat`='set_a3' OR `Stat`='set_a4' OR `Stat`='set_a5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Assists</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Assists (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Assists (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Assists (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Digs-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Digs</h1></td></tr>";
		
		//------------Total Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','defense_dig',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`='TEAM' AND `StatType`='TMG' AND (`Stat`='defense_dig3' OR `Stat`='defense_dig4' OR `Stat`='defense_dig5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Digs</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Digs (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Digs (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Digs (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Blocks-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Blocks</h1></td></tr>";
		
		//------------Block Solos---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_bs',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_bs',$_GET['LEVEL'],"DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Block Solos</h2>".$newRecordsCode."</td>";
		
		//------------Block Assists---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_ba',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_ba',$_GET['LEVEL'],"DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Block Assists</h2>".$newRecordsCode."</td></tr>";
		
		//------------Total Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_tb',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`='TEAM' AND `StatType`='TMG' AND (`Stat`='block_tb3' OR `Stat`='block_tb4' OR `Stat`='block_tb5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Blocks</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Total Blocks (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Blocks (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Total Blocks (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Aces-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Aces</h1></td></tr>";
		
		//------------Total Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_sa',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`='TEAM' AND `StatType`='TMG' AND (`Stat`='serve_sa3' OR `Stat`='serve_sa4' OR `Stat`='serve_sa5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Aces</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Aces (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Aces (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Aces (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------------------End Overall Records Code-----------------
		$recordsCode = $recordsCode."</tr></table>";
		?>
		  <label for="adminStat">Select Stat</label>
			<select name="adminStat" id="adminStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Total Kills</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Total Assists</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Total Digs</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k3">Most Kills (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta3">Most Attacks (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts3">Most Points (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa3">Most Aces (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a3">Most Assists (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig3">Most Digs (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb3">Most Total Blocks (3 sets)</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k4">Most Kills (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta4">Most Attacks (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts4">Most Points (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa4">Most Aces (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a4">Most Assists (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig4">Most Digs (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb4">Most Total Blocks (4 sets)</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k5">Most Kills (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta5">Most Attacks (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts5">Most Points (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa5">Most Aces (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a5">Most Assists (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig5">Most Digs (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb5">Most Total Blocks (5 sets)</option>
				<?php if ($requestedStat == NULL){
				?>
			</td>
		</tr>
	</tbody>
</table>
<hr>
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
	}
	if ($requestedLevel == "GAM"){
		//------------------------Start Overall Records Code-----------------
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Kills</h1></td></tr>";
		
		//------------Total Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_k',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='attack_k3' OR `Stat`='attack_k4' OR `Stat`='attack_k5' AND `Name`!='TEAM' AND `StatType`='GAM' ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Kills</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Kills (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Kills (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Kills---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Kills (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------Attack Percentage---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT IsActive,AttackPercentage as Value,AthleteID,GameID FROM (SELECT boxscores.AthleteID,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive,boxscores.GameID FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID LEFT OUTER JOIN `NCAA_RPI` as r on games.RPI=r.RPI WHERE `Name`!='TEAM' GROUP BY boxscores.GameID ORDER BY AttackPercentage DESC) as x");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_pct',$_GET['LEVEL'],"DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],1);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Attack Percentage</h2>".$newRecordsCode."</td>";
		
		//--------------------------New Header: Attack Attempts-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Attack Attempts</h1></td></tr>";
		
		//------------Total Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`!='TEAM' AND `StatType`='GAM' AND (`Stat`='attack_ta3' OR `Stat`='attack_ta4' OR `Stat`='attack_ta5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Attacks</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Attacks (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Attacks (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Attacks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Attacks (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Points-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Points</h1></td></tr>";
		
		//------------Total Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','misc_pts',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Stat`='misc_pts3' OR `Stat`='misc_pts4' OR `Stat`='misc_pts5' AND `Name`!='TEAM' AND `StatType`='GAM' ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Points</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Points (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Points (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Points (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Assists-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Assists</h1></td></tr>";
		
		//------------Total Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','set_a',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`!='TEAM' AND `StatType`='GAM' AND (`Stat`='set_a3' OR `Stat`='set_a4' OR `Stat`='set_a5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Assists</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Assists (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Assists (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Points---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Assists (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Digs-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Digs</h1></td></tr>";
		
		//------------Total Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','defense_dig',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`!='TEAM' AND `StatType`='GAM' AND (`Stat`='defense_dig3' OR `Stat`='defense_dig4' OR `Stat`='defense_dig5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Digs</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Digs (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Digs (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Digs---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Digs (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Blocks-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Blocks</h1></td></tr>";
		
		//------------Block Solos---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_bs',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_bs',$_GET['LEVEL'],"DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Block Solos</h2>".$newRecordsCode."</td>";
		
		//------------Block Assists---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_ba',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_ba',$_GET['LEVEL'],"DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Block Assists</h2>".$newRecordsCode."</td></tr>";
		
		//------------Total Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_tb',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`!='TEAM' AND `StatType`='GAM' AND (`Stat`='block_tb3' OR `Stat`='block_tb4' OR `Stat`='block_tb5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Blocks</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Total Blocks (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Blocks (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Blocks---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='block_tb' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Total Blocks (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//--------------------------New Header: Aces-------------------
		$recordsCode = $recordsCode."<tr><td colspan=\"2\" align=\"center\"><h1>Aces</h1></td></tr>";
	
		//------------Total Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_sa',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL'],"DESC","SELECT * FROM `vb_oldrecords` WHERE `Name`!='TEAM' AND `StatType`='GAM' AND (`Stat`='serve_sa3' OR `Stat`='serve_sa4' OR `Stat`='serve_sa5') ORDER BY `Value` DESC");
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Aces</h2>".$newRecordsCode."</td>";
		
		//------------3-Set Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa3',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Aces (3-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------4-Set Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa4',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Aces (4-Sets)</h2>".$newRecordsCode."</td>";
		
		//------------5-Set Aces---------------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa5',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Aces (5-Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------------------End Overall Records Code-----------------
		$recordsCode = $recordsCode."</tr></table>";
			?>
			<label for="adminStat">Select Stat</label>
			<select name="adminStat" id="adminStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Total Kills</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Total Assists</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Total Digs</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k3">Most Kills (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta3">Most Attacks (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts3">Most Points (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa3">Most Aces (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a3">Most Assists (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig3">Most Digs (3 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb3") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb3">Most Total Blocks (3 sets)</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k4">Most Kills (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta4">Most Attacks (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts4">Most Points (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa4">Most Aces (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a4">Most Assists (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig4">Most Digs (4 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb4") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb4">Most Total Blocks (4 sets)</option>
			<option value=""></option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k5">Most Kills (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta5">Most Attacks (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts5">Most Points (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa5">Most Aces (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a5">Most Assists (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig5">Most Digs (5 sets)</option>
				<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb5") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb5">Most Total Blocks (5 sets)</option>
				<?php if ($requestedStat == NULL){
				?>
				
		</td>
</tr>
	</tbody>
</table>
	<hr>
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
				
		}
	else if ($requestedLevel == "CAR"){
		$db_name = "kcmsathl_wvsu";
		
		//------------Total Kills----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_k',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Attack</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Kills</h2>".$newRecordsCode;

		//------------Kills Per Set----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_kperset',$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY KillsPerGame DESC) as x WHERE SetsPlayed > 60");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_kperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'], TRUE);
		$recordsCode = $recordsCode."</td><td align=\"center\"><h2>Kills Per Set (Minimum 60 Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------Attack Attempts----------------
		$recordsCode=$recordsCode."<tr><td align=\"center\"><h2>Attack Attempts</h2>";
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'vb',$_GET['LEVEL'],FALSE);
		$recordsCode = $recordsCode.$newRecordsCode."</td><td align=\"center\"><h2>Attack Percentage (Minimum 400 Attempts)</h2>";
		
		//------------Attack Percentage----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AttackPercentage DESC) as x WHERE x.TotalAttacks >= 400");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode.$newRecordsCode."</td></tr>";
		
		//------------Total Assists----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','set_a',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Assists</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Assists</h2>".$newRecordsCode;
		
		//------------Assists Per Set---------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AssistsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_aperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'], TRUE);
		$recordsCode = $recordsCode."</td><td align=\"center\"><h2>Assists Per Set (Minimum 60 Sets)</h2>".$newRecordsCode;
		
		//------------Service Aces-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_sa',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Serving</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Service Aces</h2>".$newRecordsCode;
		
		//------------Service Aces Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AcesPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_saperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Service Aces Per Set (Minimum 60 Sets)</h2>".$newRecordsCode;
		
		//------------Service Attempts-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Service Attempts</h2>".$newRecordsCode;
		
		//------------Serving Efficiency---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY ServePercentage DESC) as x WHERE x.Serves >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Serving Efficiency (Minimum 100 Serves)</h2>".$newRecordsCode;
		
		//------------Total Points-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','misc_pts',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Points</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Points</h2>".$newRecordsCode;
		
		//------------Points Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY PointsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_ptsperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Points Per Set (Minimum 60 Sets)</h2>".$newRecordsCode;
		
		//------------Total Digs-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','defense_dig',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Defense</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Digs</h2>".$newRecordsCode;
		
		//------------Digs Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY DigsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_digperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Digs Per Set (Minimum 60 Sets)</h2>".$newRecordsCode;
		
		//------------Block Solos-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_bs',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Block Solos</h2>".$newRecordsCode;
		
		//------------Block Assists---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_ba',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Block Assists</h2>".$newRecordsCode;
		
		//------------Total Blocks---------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_tb',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Blocks</h2>".$newRecordsCode;
		
		//------------Blocks Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY BlocksPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tbperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Blocks Per Set (Minimum 60 Blocks)</h2>".$newRecordsCode;
		
		//------------Reception Percent---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY ReceptionPercentage DESC) as x WHERE x.Receives >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_tapct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<tr valign=\"center\"><td align=\"center\"><h2>Reception Percentage (Minimum 100 Receptions)</h2>".$newRecordsCode."<td align=\"center\"></td></tr>";
		
		//------------Sets Played-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','total_sp',$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'total_sp',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Participation</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Sets Played</h2>".$newRecordsCode;
		
		//------------Matches Played---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed as Value,Digs,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'defense_dig' THEN VALUE ELSE 0 END) AS 'Digs', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT COUNT(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'total_mp',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Matches Played</h2>".$newRecordsCode."</tr></table>";
		$recordsCode = $recordsCode."</tr></table>";
		?>
			<label for="adminStat">Select Stat</label>
			<select name="adminStat" id="adminStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Kills</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_kperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_kperset">Most Kills Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage (Minimum 400 attempts)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_aperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_aperset">Most Assists Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_saperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_saperset">Most Aces Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_ta">Most Service Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_pct">Highest Service Percent (Minimum 100 serves)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_tapct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_tapct">Highest Reception Percent (Minimum 100 receptions)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tbperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tbperset">Highest Blocks Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_ptsperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_ptsperset">Most Points Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Digs</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_digperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_digperset">Most Digs Per Set (Minimum 60 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_sp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_sp">Most Sets Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_mp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_mp">Most Matches Played</option>
			</select>
<?php if ($requestedStat == NULL){
	  ?>
				
		</td>
</tr>
	</tbody>
</table>
	<hr>
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
	}
	else if ($requestedLevel == "TMS"){
		$db_name = "kcmsathl_wvsu";
		//------------Total Kills----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_k',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Attack</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Kills</h2>".$newRecordsCode;

		//------------Kills Per Set----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_kperset',$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY KillsPerGame DESC) as x");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_kperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'], TRUE);
		$recordsCode = $recordsCode."</td><td align=\"center\"><h2>Kills Per Set (Minimum 25 Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------Attack Attempts----------------
		$recordsCode=$recordsCode."<tr><td align=\"center\"><h2>Attack Attempts</h2>";
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'vb',$_GET['LEVEL'],FALSE);
		$recordsCode = $recordsCode.$newRecordsCode."</td><td align=\"center\"><h2>Attack Percentage (Minimum 100 Attempts)</h2>";
		
		//------------Attack Percentage----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AttackPercentage DESC) as x WHERE x.TotalAttacks >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode.$newRecordsCode."</td></tr>";
		
		//------------Total Assists----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','set_a',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Assists</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Assists</h2>".$newRecordsCode;
		
		//------------Assists Per Set---------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_aperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'], TRUE);
		$recordsCode = $recordsCode."</td><td align=\"center\"><h2>Assists Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Service Aces-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_sa',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Serving</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Service Aces</h2>".$newRecordsCode;
		
		//------------Service Aces Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AcesPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_saperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Service Aces Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Service Attempts-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Service Attempts</h2>".$newRecordsCode;
		
		//------------Serving Efficiency---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY ServePercentage DESC) as x WHERE x.Serves >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Serving Efficiency (Minimum 100 Serves)</h2>".$newRecordsCode;
		
		//------------Total Points-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','misc_pts',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Points</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Points</h2>".$newRecordsCode;
		
		//------------Points Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_ptsperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Points Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Total Digs-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','defense_dig',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Defense</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Digs</h2>".$newRecordsCode;
		
		//------------Digs Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY DigsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_digperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Digs Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Block Solos-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_bs',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Block Solos</h2>".$newRecordsCode;
		
		//------------Block Assists---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_ba',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Block Assists</h2>".$newRecordsCode;
		
		//------------Total Blocks---------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_tb',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Blocks</h2>".$newRecordsCode;
		
		//------------Blocks Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY BlocksPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tbperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Blocks Per Set (Minimum 25 Blocks)</h2>".$newRecordsCode;
		
		//------------Reception Percent---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY ReceptionPercentage DESC) as x WHERE x.Receives >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_tapct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<tr valign=\"center\"><td align=\"center\"><h2>Reception Percentage (Minimum 100 Receptions)</h2>".$newRecordsCode."<td align=\"center\"></td></tr>";
		
		//------------Sets Played-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed as Value,Digs,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'defense_dig' THEN VALUE ELSE 0 END) AS 'Digs', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT COUNT(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'total_sp',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Participation</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Sets Played</h2>".$newRecordsCode;
		
		//------------Matches Played---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed as Value,Digs,SeasonsActive,Season,IsActive FROM(SELECT Name, SUM(CASE WHEN `Stat` = 'defense_dig' THEN VALUE ELSE 0 END) AS 'Digs', SeasonsActive, games.Season, SetsPlayed, IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games AS games ON boxscores.GameID = games.GameID RIGHT OUTER JOIN( SELECT COUNT(Sets) AS SetsPlayed, Season FROM vb_games WHERE 1 GROUP BY Season ) setsSum ON setsSum.Season = games.Season WHERE `Name` = 'TEAM' GROUP BY NAME , Season) AS X ORDER BY Value DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'total_mp',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Matches Played</h2>".$newRecordsCode."</tr></table>";
		?>
			<label for="adminStat">Select Stat</label>
			<select name="adminStat" id="adminStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Kills</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_kperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_kperset">Most Kills Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage (Minimum 100 attempts)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_aperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_aperset">Most Assists Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_saperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_saperset">Most Aces Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_ta">Most Service Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_pct">Highest Service Percent (Minimum 100 serves)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_tapct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_tapct">Highest Reception Percent (Minimum 100 receptions)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tbperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tbperset">Highest Blocks Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_ptsperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_ptsperset">Most Points Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Digs</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_digperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_digperset">Most Digs Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_sp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_sp">Most Sets Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_mp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_mp">Most Matches Played</option>
			</select>
<?php if ($requestedStat == NULL){
	  ?>
				
		</td>
</tr>
	</tbody>
</table>
	<hr>
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
	}
	else if ($requestedLevel == "SEA"){
		$db_name = "kcmsathl_wvsu";
		//------------Total Kills----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_k',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_k',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = "<table width=\"100%\"><tr><td colspan=\"2\" align=\"center\"><h1>Attack</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Kills</h2>".$newRecordsCode;

		//------------Kills Per Set----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_kperset',$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY KillsPerGame DESC) as x");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_kperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'], TRUE);
		$recordsCode = $recordsCode."</td><td align=\"center\"><h2>Kills Per Set (Minimum 25 Sets)</h2>".$newRecordsCode."</td></tr>";
		
		//------------Attack Attempts----------------
		$recordsCode=$recordsCode."<tr><td align=\"center\"><h2>Attack Attempts</h2>";
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','attack_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'vb',$_GET['LEVEL'],FALSE);
		$recordsCode = $recordsCode.$newRecordsCode."</td><td align=\"center\"><h2>Attack Percentage (Minimum 100 Attempts)</h2>";
		
		//------------Attack Percentage----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AttackPercentage DESC) as x WHERE x.TotalAttacks >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'attack_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode($currentRecords,$oldRecords,'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode.$newRecordsCode."</td></tr>";
		
		//------------Total Assists----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','set_a',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_a',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Assists</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Assists</h2>".$newRecordsCode;
		
		//------------Assists Per Set---------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'set_aperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'], TRUE);
		$recordsCode = $recordsCode."</td><td align=\"center\"><h2>Assists Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Service Aces-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_sa',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_sa',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Serving</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Service Aces</h2>".$newRecordsCode;
		
		//------------Service Aces Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AcesPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_saperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Service Aces Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Service Attempts-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','serve_ta',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Service Attempts</h2>".$newRecordsCode;
		
		//------------Serving Efficiency---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY ServePercentage DESC) as x WHERE x.Serves >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Serving Efficiency (Minimum 100 Serves)</h2>".$newRecordsCode;
		
		//------------Total Points-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','misc_pts',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_pts',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],2);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Points</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Points</h2>".$newRecordsCode;
		
		//------------Points Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'misc_ptsperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Points Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Total Digs-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','defense_dig',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_dig',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Defense</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Digs</h2>".$newRecordsCode;
		
		//------------Digs Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY DigsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_digperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Digs Per Set (Minimum 25 Sets)</h2>".$newRecordsCode;
		
		//------------Block Solos-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_bs',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_ta',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Block Solos</h2>".$newRecordsCode;
		
		//------------Block Assists---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_ba',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'serve_pct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Block Assists</h2>".$newRecordsCode;
		
		//------------Total Blocks---------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','block_tb',$_GET['LEVEL']);
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tb',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Total Blocks</h2>".$newRecordsCode;
		
		//------------Blocks Per Set---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY BlocksPerGame DESC) as x WHERE x.SetsPlayed >= 25");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'block_tbperset',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Blocks Per Set (Minimum 25 Blocks)</h2>".$newRecordsCode;
		
		//------------Reception Percent---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores',$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY ReceptionPercentage DESC) as x WHERE x.Receives >= 100");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'defense_tapct',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL'],TRUE);
		$recordsCode = $recordsCode."<tr valign=\"center\"><td align=\"center\"><h2>Reception Percentage (Minimum 100 Receptions)</h2>".$newRecordsCode."<td align=\"center\"></td></tr>";
		
		//------------Sets Played-----------------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','total_sp',$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'total_sp',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<tr valign=\"top\" align=\"center\"><td colspan=\"2\"><h1>Participation</h1></td></tr><tr valign=\"top\" align=\"center\"><td align=\"center\"><h2>Sets Played</h2>".$newRecordsCode;
		
		//------------Matches Played---------
		$currentRecords = readCurrentDatabase($db_name,'vb_boxscores','total_mp',$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
		$oldRecords = readOldDatabase($db_name,"vb_oldrecords",'total_mp',$_GET['LEVEL']);
		$newRecordsCode = outputRecordsCode ($currentRecords, $oldRecords, 'vb',$_GET['LEVEL']);
		$recordsCode = $recordsCode."<td align=\"center\"><h2>Matches Played</h2>".$newRecordsCode."</tr></table>";
		?>
			<label for="adminStat">Select Stat</label>
			<select name="adminStat" id="adminStat">
			<option value=""></option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_k") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_k">Most Kills</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_kperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_kperset">Most Kills Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_ta">Most Attack Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "attack_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-attack_pct">Highest Attack Percentage (Minimum 100 attempts)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_a") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_a">Most Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "set_aperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-set_aperset">Most Assists Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_sa") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_sa">Most Service Aces</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_saperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_saperset">Most Aces Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_ta") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_ta">Most Service Attempts</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "serve_pct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-serve_pct">Highest Service Percent (Minimum 100 serves)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_tapct") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_tapct">Highest Reception Percent (Minimum 100 receptions)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_bs") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_bs">Most Block Solos</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_ba") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_ba">Most Block Assists</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tb") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tb">Most Total Blocks</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "block_tbperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-block_tbperset">Highest Blocks Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_pts") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_pts">Most Points</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "misc_ptsperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-misc_ptsperset">Most Points Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_dig") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_dig">Most Digs</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "defense_digperset") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-defense_digperset">Most Digs Per Set (Minimum 25 Sets)</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_sp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_sp">Most Sets Played</option>
			<option <?php if (array_key_exists('STAT',$_GET) && $_GET['STAT'] == "total_mp") echo "selected" ?> value="vb_<?php echo $_GET['LEVEL']?>-total_mp">Most Matches Played</option>
			</select>
	  </td>
</tr>
	</tbody>
</table>
<table>
	<tbody>
	<tr>
		<td>
		<?php
			
		if ($requestedLevel == "TMS")
			echo "<br>Team Season Records Code<br>";
		else if ($requestedLevel == "SEA")
			echo "<br>Individual Season Records Code<br>";
		else if ($requestedLevel == "CAR")
			echo "<br>Career Records Code<br>";
		else if ($requestedLevel == "TMG")
			echo "<br>Team Match Records Code<br>";
		else if ($requestedLevel == "GAM")
			echo "<br>Individual Match Records Code<br>";
		?>
			<textarea rows="10" cols="150" readonly wrap="soft"><?php echo htmlspecialchars($recordsCode)?></textarea>
		</td>
	</tr>
<?php
}
	
	if($requestedStat != NULL)
	{
		if ($requestedLevel != NULL && $requestedStat != NULL){
			$db_name = "kcmsathl_wvsu";
			$table = "vb_boxscores";
		// Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
			$currentRecords = readCurrentDatabase($db_name,"vb_boxscores",$requestedStat,$_GET['LEVEL']);
			$oldRecords = readOldDatabase($db_name,"vb_oldrecords",$requestedStat,$_GET['LEVEL']);
			
			if ($requestedLevel == "GAM" || $requestedLevel == "TMG"){
				if ($requestedLevel == "GAM"){
					if ($requestedStat == "attack_k3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_k4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_k5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_ta3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_ta4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_ta5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "misc_pts3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "misc_pts4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "misc_pts5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "serve_sa3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "serve_sa4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "serve_sa5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "set_a3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "set_a4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "set_a5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "defense_dig3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "defense_dig4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "defense_dig5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`!='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
				} // End Game Stats
				elseif ($requestedLevel == "TMG"){
					if ($requestedStat == "attack_k3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_k4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_k5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_k' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_ta3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_ta4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "attack_ta5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='attack_ta' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "misc_pts3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "misc_pts4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "misc_pts5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='misc_pts' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "serve_sa3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "serve_sa4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "serve_sa5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='serve_sa' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "set_a3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "set_a4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "set_a5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='set_a' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "defense_dig3") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='3' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "defense_dig4") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='4' AND `Value`>0 ORDER BY `Value` DESC");
					}
					elseif ($requestedStat == "defense_dig5") {
						$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT * FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='defense_dig' AND `Name`='TEAM' AND `Sets`='5' AND `Value`>0 ORDER BY `Value` DESC");
					}
				}
			}
			elseif ($requestedLevel == "SEA"){
			if ($requestedStat == "attack_kperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY KillsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
				}
			}
			elseif ($requestedStat == "attack_pct"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AttackPercentage DESC) as x WHERE x.TotalAttacks >= 100");
				}
			}
			elseif ($requestedStat == "set_aperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
				}
			}
			elseif ($requestedStat == "serve_saperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY AcesPerGame DESC) as x WHERE x.SetsPlayed >= 25");
				}
			}
			elseif ($requestedStat == "serve_pct"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY ServePercentage DESC) as x WHERE x.Serves >= 100");
				}
			}
			elseif ($requestedStat == "defense_tapct"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY ReceptionPercentage DESC) as x WHERE x.Receives >= 100");
				}
			}
			elseif ($requestedStat == "block_tbperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY BlocksPerGame DESC) as x WHERE x.SetsPlayed >= 25");
				}
			}
			elseif ($requestedStat == "misc_ptsperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
				}
			}
			elseif ($requestedStat == "defense_digperset"){
				if ($requestedLevel == "SEA"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name,Season ORDER BY DigsPerGame DESC) as x WHERE x.SetsPlayed >= 25");
				}
			}
			elseif ($requestedStat == "total_sp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
			}
			elseif ($requestedStat == "total_mp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
			}
		}
		 	elseif ($requestedLevel == "TMS"){
			 if ($requestedStat == "attack_kperset"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY KillsPerGame DESC) as x");
				}
			}
			elseif ($requestedStat == "attack_pct"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AttackPercentage DESC) as x");
				}
			}
			elseif ($requestedStat == "set_aperset"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AssistsPerGame DESC) as x");
				}
			}
			elseif ($requestedStat == "serve_saperset"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY AcesPerGame DESC) as x");
				}
			}
			elseif ($requestedStat == "serve_pct"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY ServePercentage DESC) as x");
				}
			}
			elseif ($requestedStat == "defense_tapct"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY ReceptionPercentage DESC) as x");
				}
			}
			elseif ($requestedStat == "block_tbperset"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY BlocksPerGame DESC) as x");
				}
			}
			elseif ($requestedStat == "misc_ptsperset"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY PointsPerGame DESC) as x");
				}
			}
			elseif ($requestedStat == "defense_digperset"){
				if ($requestedLevel == "TMS"){
					$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`='TEAM' GROUP BY Name,Season ORDER BY DigsPerGame DESC) as x");
				}
			}
			elseif ($requestedStat == "total_sp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,(SELECT SUM(`Sets`) FROM `vb_games` WHERE 1) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
			}
			elseif ($requestedStat == "total_mp") {
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,(SELECT COUNT(`Sets`) FROM `vb_games` WHERE 1) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`='TEAM' AND `Value`>0 GROUP BY Name,Season ORDER BY `Value` DESC");
			}
		 }
			elseif ($requestedLevel == "CAR"){
			if ($requestedStat == "attack_kperset"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Kills,KillsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills', ROUND(SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as KillsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY KillsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
			}
		elseif ($requestedStat == "attack_pct"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,TotalAttacks,AttackErrors,AttackPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END) as 'TotalAttacks',SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)as 'Kills',SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END)as 'AttackErrors', ROUND((SUM(CASE WHEN `Stat`='attack_k' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='attack_e' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='attack_ta' THEN Value ELSE 0 END),3) as AttackPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AttackPercentage DESC) as x WHERE x.TotalAttacks >= 400");
		}
		elseif ($requestedStat == "set_aperset"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Assists,AssistsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)as 'Assists', ROUND(SUM(CASE WHEN `Stat`='set_a' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AssistsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AssistsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		}
		elseif ($requestedStat == "serve_saperset"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Aces,AcesPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Aces', ROUND(SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as AcesPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY AcesPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		}
		elseif ($requestedStat == "serve_pct"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ServiceErrors,Serves,ServePercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END) as 'ServiceErrors',SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)as 'Serves', ROUND((SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='serve_se' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='serve_ta' THEN Value ELSE 0 END),3) as ServePercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY ServePercentage DESC) as x WHERE x.Serves >= 100");
		}
		elseif ($requestedStat == "defense_tapct"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,ReceptionErrors,Receives,ReceptionPercentage as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END) as 'ReceptionErrors',SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)as 'Receives', ROUND((SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END)-SUM(CASE WHEN `Stat`='defense_re' THEN Value ELSE 0 END))/SUM(CASE WHEN `Stat`='defense_ta' THEN Value ELSE 0 END),3) as ReceptionPercentage,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY ReceptionPercentage DESC) as x WHERE x.Receives >= 100");
		}
		elseif ($requestedStat == "block_tbperset"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Blocks,BlocksPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='serve_sa' THEN Value ELSE 0 END)as 'Blocks', ROUND(SUM(CASE WHEN `Stat`='block_tb' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as BlocksPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY BlocksPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		}
		elseif ($requestedStat == "misc_ptsperset"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Points,PointsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END) as 'Points', ROUND(SUM(CASE WHEN `Stat`='misc_pts' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as PointsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY PointsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		}
		elseif ($requestedStat == "defense_digperset"){
				$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SetsPlayed,Digs,DigsPerGame as Value,SeasonsActive,Season,IsActive FROM (SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'SetsPlayed',SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)as 'Digs', ROUND(SUM(CASE WHEN `Stat`='defense_dig' THEN Value ELSE 0 END)/SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END),3) as DigsPerGame,SeasonsActive,Season,IsActive FROM `vb_boxscores` AS boxscores LEFT OUTER JOIN vb_athletes AS athletes on boxscores.AthleteID = athletes.AthleteID RIGHT OUTER JOIN vb_games as games on boxscores.GameID = games.GameID WHERE `Name`!='TEAM' GROUP BY Name ORDER BY DigsPerGame DESC) as x WHERE x.SetsPlayed >= 60");
		}
		elseif ($requestedStat == "total_sp") {
			$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN Value ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name ORDER BY `Value` DESC");
		}
		elseif ($requestedStat == "total_mp") {
			$currentRecords = readCurrentDatabase($db_name,$table,$requestedStat,$_GET['LEVEL'],"SELECT Name,SUM(CASE WHEN `Stat`='gp' THEN 1 ELSE 0 END) as 'Value',SeasonsActive,Season,IsActive FROM `vb_boxscores` as b LEFT OUTER JOIN vb_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN vb_athletes as a on b.AthleteID=a.AthleteID WHERE `Name`!='TEAM' AND `Value`>0 GROUP BY Name ORDER BY `Value` DESC");
		}
		}
			$recordsCode = outputRecordsCode($currentRecords, $oldRecords, "vb", $requestedLevel, FALSE);
			
			echo ("Manual Record Editing - ".$_GET['LEVEL'].": ".$_GET['STAT']);
			$oldResultsString = readOldDatabase("kcmsathl_WVSU","vb_oldrecords",$_GET['STAT'],$_GET['LEVEL']);
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
	}
	elseif($requestedLevel == "ATH"){
		echo "Active Athlete Administration";
		athleteAdministration("vb","A");
	}
	elseif($requestedLevel == "ATR"){
		echo "Retired Athlete Administration";
		athleteAdministration("vb","R");
	}
	else{
		echo "<h2>Please select Stat Category.</h2>";
		?>
		</td>
	  <?php
	}
}

function uploadVbBox($ArrayRead)
{
	$db_name = "vb";
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
	$sets = $ArrayRead['status']['@attributes']['game'];
	    
	    // Need to Check for duplicate games
	    
	$formattedLocation = checkName($ArrayRead['venue']['@attributes']['location']);
	$GameID = createNewGameVB ($formattedDate,$time,$homeAway,$opponent,$result,"$homeScore-$awayScore",$ArrayRead['venue']['@attributes']['attend'],$formattedLocation,$season,$db_name,$sets);
	
	echo ("<h3>Date of Game: $date | Opponent: $opponentName | RPI: $opponent | GameID: $GameID</h3><h3>Query Results:</h3><br>");
	if(checkOpponent($opponent,$opponentName))
		echo "<h2>Opponent RPI Added to database</h2>";
	
	$teamTotals = $teamArray['totals'];
	createPlayerStatVb("TEAM","attack_k",$teamTotals['attack']['@attributes']['k'],$GameID,$season);
	createPlayerStatVb("TEAM","attack_e",$teamTotals['attack']['@attributes']['e'],$GameID,$season);
	createPlayerStatVb("TEAM","attack_ta",$teamTotals['attack']['@attributes']['ta'],$GameID,$season);
	createPlayerStatVb("TEAM","set_a",$teamTotals['set']['@attributes']['a'],$GameID,$season);
	if (array_key_exists('e',$teamTotals['set']['@attributes']))
		createPlayerStatVb("TEAM","set_e",$teamTotals['set']['@attributes']['e'],$GameID,$season);
	if (array_key_exists('ta',$teamTotals['set']['@attributes']))
		createPlayerStatVb("TEAM","set_ta",$teamTotals['set']['@attributes']['ta'],$GameID,$season);
	createPlayerStatVb("TEAM","serve_sa",$teamTotals['serve']['@attributes']['sa'],$GameID,$season);
	createPlayerStatVb("TEAM","serve_se",$teamTotals['serve']['@attributes']['se'],$GameID,$season);
	if (array_key_exists('ta',$teamTotals['serve']['@attributes']))
		createPlayerStatVb("TEAM","serve_ta",$teamTotals['serve']['@attributes']['ta'],$GameID,$season);
	createPlayerStatVb("TEAM","defense_dig",$teamTotals['defense']['@attributes']['dig'],$GameID,$season);
	createPlayerStatVb("TEAM","defense_re",$teamTotals['defense']['@attributes']['re'],$GameID,$season);
	if (array_key_exists('ta',$teamTotals['defense']['@attributes']))
	createPlayerStatVb("TEAM","defense_ta",$teamTotals['defense']['@attributes']['ta'],$GameID,$season);
	createPlayerStatVb("TEAM","block_bs",$teamTotals['block']['@attributes']['bs'],$GameID,$season);
	createPlayerStatVb("TEAM","block_ba",$teamTotals['block']['@attributes']['ba'],$GameID,$season);
	createPlayerStatVb("TEAM","block_be",$teamTotals['block']['@attributes']['be'],$GameID,$season);
	createPlayerStatVb("TEAM","block_tb",$teamTotals['block']['@attributes']['tb'],$GameID,$season);
	createPlayerStatVb("TEAM","misc_bhe",$teamTotals['misc']['@attributes']['bhe'],$GameID,$season);
	createPlayerStatVb("TEAM","misc_pts",$teamTotals['misc']['@attributes']['pts'],$GameID,$season);
	
	$numOfPlayers = sizeof($teamArray['player']);
	
	for($currPlayer = 0; $currPlayer < $numOfPlayers; $currPlayer ++):
	
	$currArray = $teamArray['player'][$currPlayer];
	$athleteName = $currArray['@attributes']['name'];
	
	echo "<hr>";
	if ($athleteName == "TEAM" || $currArray['@attributes']['gp'] == 0)
		continue;
	else{
		createPlayerStatVb("$athleteName","gp",$currArray['@attributes']['gp'],$GameID);
		createPlayerStatVb("$athleteName","attack_k",$currArray['attack']['@attributes']['k'],$GameID,$season);
		createPlayerStatVb("$athleteName","attack_e",$currArray['attack']['@attributes']['e'],$GameID,$season);
		createPlayerStatVb("$athleteName","attack_ta",$currArray['attack']['@attributes']['ta'],$GameID,$season);
		createPlayerStatVb("$athleteName","set_a",$currArray['set']['@attributes']['a'],$GameID,$season);
		if (array_key_exists('e',$currArray['set']['@attributes']))
			createPlayerStatVb("$athleteName","set_e",$currArray['set']['@attributes']['e'],$GameID,$season);
		if (array_key_exists('ta',$currArray['set']['@attributes']))
			createPlayerStatVb("$athleteName","set_ta",$currArray['set']['@attributes']['ta'],$GameID,$season);
		createPlayerStatVb("$athleteName","serve_sa",$currArray['serve']['@attributes']['sa'],$GameID,$season);
		createPlayerStatVb("$athleteName","serve_se",$currArray['serve']['@attributes']['se'],$GameID,$season);
		if (array_key_exists('ta',$currArray['serve']['@attributes']))
			createPlayerStatVb("$athleteName","serve_ta",$currArray['serve']['@attributes']['ta'],$GameID,$season);
		createPlayerStatVb("$athleteName","defense_dig",$currArray['defense']['@attributes']['dig'],$GameID,$season);
		createPlayerStatVb("$athleteName","defense_re",$currArray['defense']['@attributes']['re'],$GameID,$season);
		if (array_key_exists('ta',$currArray['defense']['@attributes']))
			createPlayerStatVb("$athleteName","defense_ta",$currArray['defense']['@attributes']['ta'],$GameID,$season);
		createPlayerStatVb("$athleteName","block_bs",$currArray['block']['@attributes']['bs'],$GameID,$season);
		createPlayerStatVb("$athleteName","block_ba",$currArray['block']['@attributes']['ba'],$GameID,$season);
		createPlayerStatVb("$athleteName","block_be",$currArray['block']['@attributes']['be'],$GameID,$season);
		createPlayerStatVb("$athleteName","block_tb",$currArray['block']['@attributes']['tb'],$GameID,$season);
		createPlayerStatVb("$athleteName","misc_bhe",$currArray['misc']['@attributes']['bhe'],$GameID,$season);
		createPlayerStatVb("$athleteName","misc_pts",$currArray['misc']['@attributes']['pts'],$GameID,$season);
		
	echo "<hr>";
	}
	
	endfor;
}

function createPlayerStatVb($name, $stat, $value, $gameID, $season = "", $remarks = "")
{
	$config = parse_ini_file('config.ini');
	$vb_milestones = parse_ini_file('vb.ini');
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
		$athlQuery = "SELECT * FROM `vb_athletes` WHERE `Name`='$name' AND `IsActive`='1'"; // Search for athlete in database. If found, get Athlete ID, otherwise create a new athlete in the database and assign an ID number to him/her.
		$athlResults = mysqli_query($conn, $athlQuery);
		$finalAthlResult = mysqli_fetch_assoc($athlResults);
		if (mysqli_num_rows($athlResults) > 0)
			$athleteID = $finalAthlResult['AthleteID'];
		else
			$athleteID = createAthlete($name, 'vb', $season);
	}
	
	//If duplicate is found, update existing record instead of creating an new one. This will reduce the numbers in the records database which will start to get massive quickly.
	$dupQuery = "SELECT * FROM `vb_boxscores` WHERE `GameID`='$gameID' AND `Stat`='$stat' AND `AthleteID`='$athleteID' ORDER BY `EntryID` DESC";
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
					//echo "DEBUG: $name Kills - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;

				case ('set_a'):
					$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $vb_milestones['sea_set_a']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_set_a']){
						if ($newSeasonTotal > $vb_milestones['sea_set_a'])
							echo "<h4>***** Season Milestone Achieved for $name in Assists: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Assists: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
					$newCareerTotal = $careerTotal + $value;
					if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_set_a']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_set_a'] && $careerTotal != NULL){
						if ($newCareerTotal > $vb_milestones['car_set_a'])
							echo "<h4>***** Career Milestone Achieved for $name in Assists: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Assists: $newCareerTotal -----<br>";
					}
					//echo "DEBUG: $name Assists - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;

				case ('serve_sa'):
					$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $vb_milestones['sea_serve_sa']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_serve_sa']){
						if ($newSeasonTotal > $vb_milestones['sea_serve_sa'])
							echo "<h4>***** Season Milestone Achieved for $name in Aces: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Aces: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
					$newCareerTotal = $careerTotal + $value;
					if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_serve_sa']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_serve_sa'] && $careerTotal != NULL){
						if ($newCareerTotal > $vb_milestones['car_serve_sa'])
							echo "<h4>***** Career Milestone Achieved for $name in Aces: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Aces: $newCareerTotal -----<br>";
					}
					//echo "DEBUG: $name Aces - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;
					
				case ('defense_dig'):
					$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $vb_milestones['sea_defense_dig']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_defense_dig']){
						if ($newSeasonTotal > $vb_milestones['sea_defense_dig'])
							echo "<h4>***** Season Milestone Achieved for $name in Digs: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Digs: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
					$newCareerTotal = $careerTotal + $value;
					if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_defense_dig']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_defense_dig'] && $careerTotal != NULL){
						if ($newCareerTotal > $vb_milestones['car_defense_dig'])
							echo "<h4>***** Career Milestone Achieved for $name in Digs: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Digs: $newCareerTotal -----<br>";
					}
					//echo "DEBUG: $name Digs - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;

				case('block_bs'):
					$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $vb_milestones['sea_block_bs']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_block_bs']){
						if ($newSeasonTotal > $vb_milestones['sea_block_bs'])
							echo "<h4>***** Season Milestone Achieved for $name in Block Solos: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Block Solos: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
					$newCareerTotal = $careerTotal + $value;
					if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_block_bs']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_block_bs'] && $careerTotal != NULL){
						if ($newCareerTotal > $vb_milestones['car_block_bs'])
							echo "<h4>***** Career Milestone Achieved for $name in Block Solos: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Block Solos: $newCareerTotal -----<br>";
					}
					//echo "DEBUG: $name Block Solos - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;

				case('block_ba'):
					$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $vb_milestones['sea_block_ba']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_block_ba']){
						if ($newSeasonTotal > $vb_milestones['sea_block_ba'])
							echo "<h4>***** Season Milestone Achieved for $name in Block Assists: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Block Assists: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
					$newCareerTotal = $careerTotal + $value;
					if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_block_ba']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_block_ba'] && $careerTotal != NULL){
						if ($newCareerTotal > $vb_milestones['car_block_ba'])
							echo "<h4>***** Career Milestone Achieved for $name in Block Assists: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Block Assists: $newCareerTotal -----<br>";
					}
					//echo "DEBUG: $name Blk Ast - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;

				case('block_tb'):
					$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
					$newSeasonTotal = $seasonTotal + $value;
					if (($seasonTotal / $vb_milestones['sea_block_tb']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_block_tb']){
						if ($newSeasonTotal > $vb_milestones['sea_block_tb'])
							echo "<h4>***** Season Milestone Achieved for $name in Total Blocks: $newSeasonTotal *****</h4>";
						else
							echo "----- Season Milestone Approaching for $name in Total Blocks: $newSeasonTotal -----<br>";
					}
					$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
					$newCareerTotal = $careerTotal + $value;
					if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_block_tb']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_block_tb'] && $careerTotal != NULL){
						if ($newCareerTotal > $vb_milestones['car_block_tb'])
							echo "<h4>***** Career Milestone Achieved for $name in Total Blocks: $newCareerTotal *****</h4>";
						else
							echo "----- Career Milestone Approaching for $name in Total Blocks: $newCareerTotal -----<br>";
					}
					//echo "DEBUG: $name Ttl Blks - SEA: $seasonTotal/$newSeasonTotal | CAR: $careerTotal/$newCareerTotal<br><br>";
					break;

				default:
					break;
			}
		}
	$sql = "INSERT INTO `vb_boxscores`(`Value`, `AthleteID`, `GameID`, `Remarks`, `Stat`, `StatType`) VALUES ('$value','$athleteID','$gameID','$remarks','$stat','$statType')";

		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
	}
	else{
		$sql = "UPDATE `vb_boxscores` SET `Value`='$value', `Remarks`='$remarks' WHERE `EntryID`='$duplicateResult'";
		if(!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
			echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
		if ($name != "TEAM"){
			switch ($stat){
						//Check for Season and Career Milestones
					case ('attack_k'):
						$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
						if (($seasonTotal / $vb_milestones['sea_attack_k']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_attack_k']){
							if ($seasonTotal > $vb_milestones['sea_attack_k'])
								echo "<h4>***** Season Milestone Achieved for $name in Kills: $seasonTotal *****</h4>";
							else
								echo "----- Season Milestone Approaching for $name in Kills: $seasonTotal -----<br>";
						}
						$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
	//					var_dump($careerTotal);
						//var_dump ($vb_milestones['car_attack_k']);
						if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_attack_k'][0]) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_attack_k'][0]){
							if ($careerTotal > $vb_milestones['car_attack_k'])
								echo "<h4>***** Career Milestone Achieved for $name in Kills: $careerTotal *****</h4>";
							else
								echo "----- Career Milestone Approaching for $name in Kills: $careerTotal -----<br>";
						}
						//echo "DEBUG: $name Kills - SEA: $seasonTotal | CAR: $careerTotal<br>";
						break;

					case ('set_a'):
						$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
						if (($seasonTotal / $vb_milestones['sea_set_a']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_set_a']){
							if ($seasonTotal > $vb_milestones['sea_set_a'])
								echo "<h4>***** Season Milestone Achieved for $name in Assists: $seasonTotal *****</h4>";
							else
								echo "----- Season Milestone Approaching for $name in Assists: $seasonTotal -----<br>";
						}
						$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
						if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_set_a']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_set_a'] && $careerTotal != NULL){
							if ($careerTotal > $vb_milestones['car_set_a'])
								echo "<h4>***** Career Milestone Achieved for $name in Assists: $careerTotal *****</h4>";
							else
								echo "----- Career Milestone Approaching for $name in Assists: $careerTotal -----<br>";
						}
						//echo "DEBUG: $name Assists - SEA: $seasonTotal | CAR: $careerTotal<br><br>";
						break;

					case ('serve_sa'):
						$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
						if (($seasonTotal / $vb_milestones['sea_serve_sa']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_serve_sa']){
							if ($seasonTotal > $vb_milestones['sea_serve_sa'])
								echo "<h4>***** Season Milestone Achieved for $name in Aces: $seasonTotal *****</h4>";
							else
								echo "----- Season Milestone Approaching for $name in Aces: $seasonTotal -----<br>";
						}
						$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
						if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_serve_sa']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_serve_sa'] && $careerTotal != NULL){
							if ($careerTotal > $vb_milestones['car_serve_sa'])
								echo "<h4>***** Career Milestone Achieved for $name in Aces: $careerTotal *****</h4>";
							else
								echo "----- Career Milestone Approaching for $name in Aces: $careerTotal -----<br>";
						}
						//echo "DEBUG: $name Aces - SEA: $seasonTotal | CAR: $careerTotal<br><br>";
						break;

					case ('defense_dig'):
						$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
						if (($seasonTotal / $vb_milestones['sea_defense_dig']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_defense_dig']){
							if ($seasonTotal > $vb_milestones['sea_defense_dig'])
								echo "<h4>***** Season Milestone Achieved for $name in Digs: $seasonTotal *****</h4>";
							else
								echo "----- Season Milestone Approaching for $name in Digs: $seasonTotal -----<br>";
						}
						$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
						if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_defense_dig']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_defense_dig'] && $careerTotal != NULL){
							if ($careerTotal > $vb_milestones['car_defense_dig'])
								echo "<h4>***** Career Milestone Achieved for $name in Digs: $careerTotal *****</h4>";
							else
								echo "----- Career Milestone Approaching for $name in Digs: $careerTotal -----<br>";
						}
						//echo "DEBUG: $name Digs - SEA: $seasonTotal | CAR: $careerTotal<br><br>";
						break;

					case('block_bs'):
						$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
						if (($seasonTotal / $vb_milestones['sea_block_bs']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_block_bs']){
							if ($seasonTotal > $vb_milestones['sea_block_bs'])
								echo "<h4>***** Season Milestone Achieved for $name in Block Solos: $seasonTotal *****</h4>";
							else
								echo "----- Season Milestone Approaching for $name in Block Solos: $seasonTotal -----<br>";
						}
						$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
						if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_block_bs']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_block_bs'] && $careerTotal != NULL){
							if ($careerTotal > $vb_milestones['car_block_bs'])
								echo "<h4>***** Career Milestone Achieved for $name in Block Solos: $careerTotal *****</h4>";
							else
								echo "----- Career Milestone Approaching for $name in Block Solos: $careerTotal -----<br>";
						}
						//echo "DEBUG: $name Block Solos - SEA: $seasonTotal | CAR: $careerTotal<br><br>";
						break;

					case('block_ba'):
						$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
						if (($seasonTotal / $vb_milestones['sea_block_ba']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_block_ba']){
							if ($seasonTotal > $vb_milestones['sea_block_ba'])
								echo "<h4>***** Season Milestone Achieved for $name in Block Assists: $seasonTotal *****</h4>";
							else
								echo "----- Season Milestone Approaching for $name in Block Assists: $seasonTotal -----<br>";
						}
						$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
						if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_block_ba']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_block_ba'] && $careerTotal != NULL){
							if ($careerTotal > $vb_milestones['car_block_ba'])
								echo "<h4>***** Career Milestone Achieved for $name in Block Assists: $careerTotal *****</h4>";
							else
								echo "----- Career Milestone Approaching for $name in Block Assists: $careerTotal -----<br>";
						}
						//echo "DEBUG: $name Blk Ast - SEA: $seasonTotal | CAR: $careerTotal<br><br>";
						break;

					case('block_tb'):
						$seasonTotal = findSeasonStats($athleteID,'vb',$stat,$season,'vb_boxscores');
						if (($seasonTotal / $vb_milestones['sea_block_tb']) * 100 > $vb_milestones['percent'] && $seasonTotal < $vb_milestones['sea_block_tb']){
							if ($seasonTotal > $vb_milestones['sea_block_tb'])
								echo "<h4>***** Season Milestone Achieved for $name in Total Blocks: $seasonTotal *****</h4>";
							else
								echo "----- Season Milestone Approaching for $name in Total Blocks: $seasonTotal -----<br>";
						}
						$careerTotal = findCareerStats($athleteID,$stat,'vb_boxscores');
						if ($careerTotal != NULL && ($careerTotal / $vb_milestones['car_block_tb']) * 100 > $vb_milestones['percent'] && $careerTotal < $vb_milestones['car_block_tb'] && $careerTotal != NULL){
							if ($careerTotal > $vb_milestones['car_block_tb'])
								echo "<h4>***** Career Milestone Achieved for $name in Total Blocks: $careerTotal *****</h4>";
							else
								echo "----- Career Milestone Approaching for $name in Total Blocks: $careerTotal -----<br>";
						}
						//echo "DEBUG: $name Ttl Blks - SEA: $seasonTotal | CAR: $careerTotal<br><br>";
						break;

					default:
						break;
				}
		}
	}
	$dbInfo = [
    'host' => 'localhost',
    'dbname' => 'kcmsathl_wvsu', // This is the relevant sport database
    'username' => 'root',
    'password' => '',
	'port' => '3306'
	];

$tables = ['vb_boxscores'];
	$potentialRecords = searchTop10Stat($athleteID, $stat, $dbInfo, $tables, $gameID, $season);
	
}

function createNewGameVB ($date,$time,$ha,$opp,$result,$score,$attend,$site,$season,$sport,$sets)
{
    $config = parse_ini_file('config.ini');
	
	$db_name = "kcmsathl_wvsu";
    
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    
	$dup_check = checkDuplicateGames($sport, $date, $opp, $time);
	
	if ($dup_check == -1){
		$query = "INSERT INTO `".$sport."_games`(`Date`, `Time`, `Home/Away`,`RPI`, `W/L`, `Score`, `Attendance`, `Site`, `Season`, `Sets`) VALUES ('$date','$time','$ha','$opp','$result','$score','$attend','$site','$season','$sets')";
		if(mysqli_query($conn, $query)) {
		 echo "Game successfully created.";
		$dup_check = checkDuplicateGames($sport, $date, $opp, $time);
		 }
		 else{
		 echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
		 }
	}
	else{
		$query = "UPDATE `".$sport."_games` SET `Date`='$date',`Time`='$time',`Home/Away`='$ha',`RPI`='$opp',`W/L`='$result',`Score`='$score',`Attendance`='$attend',`Site`='$site',`Season`='$season',`Sets`='$sets' WHERE `GameID`='$dup_check'";
		if(mysqli_query($conn, $query)) {
		 echo "GameID $dup_check successfully updated.";
		 }
		 else{
		 echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn)."<br>";
		 }
	}
   // echo $query;
	return ($dup_check);
    
}

function searchTop10Stat($athleteID, $stat, $dbInfo, $tables, $gameID = null, $season = null) {
    $top10Results = []; // Accumulate all top 10 results (overall)
    $setSpecificResults = []; // Accumulate set-specific top 10 results
    $isTeamQuery = ($athleteID == 0); // Flag for team query

    try {
        // Establish a PDO connection to the relevant sport's database
        $dsn = "mysql:host={$dbInfo['host']};dbname={$dbInfo['dbname']};charset=utf8";
        $pdo = new PDO($dsn, $dbInfo['username'], $dbInfo['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        // 1. Fetch the number of sets for the given game from the vb_games table (used for match-specific queries)
        $setQuery = "SELECT Sets FROM vb_games WHERE GameID = :gameID";
        $stmt = $pdo->prepare($setQuery);
        $stmt->execute(['gameID' => $gameID]);
        $gameData = $stmt->fetch();
        $setCount = $gameData['Sets']; // Number of sets in the game

        

        // 2. Query for Overall Season Stats (No filter by sets, since we're aggregating for the whole season)
        $overallSeasonQuery = "
            SELECT A.Name AS AthleteName, G.Season, SUM(B.Value) as total_stat, G.GameID, G.Sets
            FROM {$tables[0]} B
            JOIN vb_athletes A ON B.AthleteID = A.AthleteID
            JOIN vb_games G ON B.GameID = G.GameID
            WHERE B.Stat = :stat AND G.Season = :season
            GROUP BY A.AthleteID, G.Season
            ORDER BY total_stat DESC
            LIMIT 10
        ";

        // Execute season-level query (No set filtering)
        $stmt = $pdo->prepare($overallSeasonQuery);
        $stmt->execute(['stat' => $stat, 'season' => $season]);
        $overallResults = $stmt->fetchAll();
		

       // Output for Season-Level Records (Restricted to Current Season and Athlete)
if (!empty($overallResults)) {
    
    foreach ($overallResults as $index => $row) {
        if ($row['Season'] == $season && $row['AthleteName'] == $athleteID) { // Restrict to current season and athlete
            $rank = $index + 1;
            $athleteName = $row['AthleteName'];
            $totalStatValue = $row['total_stat'];
			echo "<h3>Season-Level Top 10 Records (Season {$season})</h3>";
            echo "*****SEASON RECORD: {$athleteName} ranked #{$rank} in Season {$season} with {$totalStatValue}<br>";
        }
    }
}


       // Match records query: capturing stats for each match (overall)
		$matchOverallQuery = "
			SELECT A.Name AS AthleteName, G.Season, SUM(B.Value) as total_stat, G.GameID
			FROM vb_boxscores B
			JOIN vb_athletes A ON B.AthleteID = A.AthleteID
			JOIN vb_games G ON B.GameID = G.GameID
			WHERE B.Stat = :stat AND G.Season = :season
			GROUP BY A.AthleteID, G.GameID
			ORDER BY total_stat DESC
			LIMIT 10
		";

		// Execute match query (overall)
		$stmt = $pdo->prepare($matchOverallQuery);
		$stmt->execute(['stat' => $stat, 'season' => $season]);
		$matchOverallResults = $stmt->fetchAll();

		// Output for Match-Level Records (Restricted to Current Game)
if (!empty($matchOverallResults)) {
    
    foreach ($matchOverallResults as $index => $row) {
        if ($row['GameID'] == $gameID) { // Restricting output to the current game
            $rank = $index + 1;
            $athleteName = $row['AthleteName'];
            $totalStatValue = $row['total_stat'];
			echo "<h3>Match-Specific Overall Top 10 Records (Game {$gameID})</h3>";
            echo "*****MATCH RECORD: {$athleteName} ranked #{$rank} with {$totalStatValue} in Game {$gameID}<br>";
        }
    }
}

		// Match records query: capturing stats for each match with set filtering
		$setSpecificMatchQuery = "
			SELECT A.Name AS AthleteName, G.Season, SUM(B.Value) as total_stat, G.GameID, G.Sets
			FROM vb_boxscores B
			JOIN vb_athletes A ON B.AthleteID = A.AthleteID
			JOIN vb_games G ON B.GameID = G.GameID
			WHERE B.Stat = :stat AND G.Season = :season AND G.Sets = :setCount
			GROUP BY A.AthleteID, G.GameID
			ORDER BY total_stat DESC
			LIMIT 10
		";

		// Execute set-specific match query
		$stmt = $pdo->prepare($setSpecificMatchQuery);
		$stmt->execute(['stat' => $stat, 'season' => $season, 'setCount' => $setCount]);
		$setSpecificMatchResults = $stmt->fetchAll();

		// Process and output set-specific match results
		if (!empty($setSpecificMatchResults)) {
			foreach ($setSpecificMatchResults as $index => $row) {
				 if ($row['GameID'] == $gameID) { 
				$rank = $index + 1;
				$athleteName = $row['AthleteName'];
				$totalStatValue = $row['total_stat'];
				$gameID = $row['GameID'];
				$sets = $row['Sets'];
				 echo "<h3>Set-Specific Top 10 Match Records</h3>";
				echo "*****SET-SPECIFIC MATCH RECORD: {$athleteName} ranked #{$rank} with {$totalStatValue} for {$stat} in Game {$gameID} ({$sets}-set match)<br>";
				 }
			}
		}

		// Career query: summing stats across all seasons
			$careerQuery = "
				SELECT A.Name AS AthleteName, SUM(B.Value) as total_stat
				FROM vb_boxscores B
				JOIN vb_athletes A ON B.AthleteID = A.AthleteID
				WHERE B.Stat = :stat AND B.AthleteID != 0
				GROUP BY A.AthleteID
				ORDER BY total_stat DESC
				LIMIT 10
			";

			// Execute career query
			$stmt = $pdo->prepare($careerQuery);
			$stmt->execute(['stat' => $stat]);
			$careerResults = $stmt->fetchAll();

			// Output for Career-Level Records (Restricted to Current Athlete)
if (!empty($careerResults)) {
    
    foreach ($careerResults as $index => $row) {
        if ($row['AthleteName'] == $athleteID) { // Restrict to current athlete
            $rank = $index + 1;
            $athleteName = $row['AthleteName'];
            $totalStatValue = $row['total_stat'];
			echo "<h3>Career-Level Top 10 Records for {$athleteID}</h3>";
            echo "*****CAREER RECORD: {$athleteName} ranked #{$rank} with {$totalStatValue} across all seasons<br>";
        }
    }
}



    } catch (PDOException $e) {
        // Handle potential connection or query errors
        error_log("Database error: " . $e->getMessage());
    }
}





?>