<?php

function sbRecords() {
    $db_name = "kcmsathl_wvsu";
    if (array_key_exists('STAT', $_GET)) $requestedStat = $_GET['STAT'];
    else $requestedStat = NULL;
    if (array_key_exists('LEVEL', $_GET)) $requestedLevel = $_GET['LEVEL'];
    else $requestedLevel = NULL;
?><table width="100%">
		<tr>
			<td valign="top" width="85%">
			<?php
    if ($requestedLevel != NULL && $requestedStat != NULL) {
        // Determine which table to read. Applicable for football, baseball and softball only. All other sports will be read from one table.
        $statType = explode("_", $requestedStat);
        //var_dump($statType);
        if ($statType[0] == "hit") $table = "sb_boxscores_hitting";
        if ($statType[0] == "fld") $table = "sb_boxscores_fielding";
        if ($statType[0] == "pit") $table = "sb_boxscores_pitching";
        // Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
        $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL']);
        $oldRecords = readOldDatabase($db_name, "sb_oldrecords", $requestedStat, $_GET['LEVEL']);
        if ($requestedLevel == "GAM" || $requestedLevel == "TMG") {
            printCurrentRecordsDatabase($currentRecords, $oldRecords, "sb", FALSE);
        } elseif ($requestedLevel == "TMS") {
            printSeasonDatabase($currentRecords, $oldRecords, "sb", FALSE);
        } elseif ($requestedLevel == "SEA") {
            if ($requestedStat == "hit_avg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Hits,
					AtBats,
					ROUND(Hits / AtBats, 3) AS BattingAverage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'hit_h' THEN Value ELSE 0 END) AS Hits,
						SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END) AS AtBats,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					BattingAverage DESC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "hit_sbpct") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					StolenBases,
					StealAttempts,
					ROUND(StolenBases / StealAttempts, 3) AS StealAverage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'hit_sb' THEN Value ELSE 0 END) AS StolenBases,
						SUM(CASE WHEN `Stat` = 'hit_cs' OR `Stat` = 'hit_sb' THEN Value ELSE 0 END) AS StealAttempts,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					StealAverage DESC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "hit_slug") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					TotalBases,
					AtBats,
					ROUND(TotalBases / AtBats, 3) AS SluggingPercentage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'hit_totbase' THEN Value ELSE 0 END) AS TotalBases,
						SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END) AS AtBats,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					SluggingPercentage DESC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "hit_obp") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Hits,
					Walks,
					HBP,
					SF,
					AtBats,
					ROUND((Hits + Walks + HBP) / NULLIF((AtBats + Walks + HBP + SF), 0), 3) AS OnBasePercentage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'hit_h' THEN Value ELSE 0 END) AS Hits,
						SUM(CASE WHEN `Stat` = 'hit_bb' THEN Value ELSE 0 END) AS Walks,
						SUM(CASE WHEN `Stat` = 'hit_hbp' THEN Value ELSE 0 END) AS HBP,
						SUM(CASE WHEN `Stat` = 'hit_sf' THEN Value ELSE 0 END) AS SF,
						SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END) AS AtBats,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
					ORDER BY
						(SUM(CASE WHEN `Stat` = 'hit_h' THEN Value ELSE 0 END) + SUM(CASE WHEN `Stat` = 'hit_bb' THEN Value ELSE 0 END) + SUM(CASE WHEN `Stat` = 'hit_hbp' THEN Value ELSE 0 END)) /
						NULLIF((SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END) + SUM(CASE WHEN `Stat` = 'hit_bb' THEN Value ELSE 0 END) + SUM(CASE WHEN `Stat` = 'hit_hbp' THEN Value ELSE 0 END) + SUM(CASE WHEN `Stat` = 'hit_sf' THEN Value ELSE 0 END)), 0) DESC
				) AS x;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_era") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					EarnedRuns,
					Innings,
					SeasonsActive,
					Season,
					IsActive,
					ROUND(((EarnedRuns / Innings) * 7), 2) AS ERA
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_er' THEN Value ELSE 0 END) AS EarnedRuns,
						SUM(CASE WHEN `Stat` = 'pit_ip' THEN ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3) ELSE 0 END) AS Innings,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					ERA ASC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            }
            else if ($requestedStat == "pit_oppavg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Hits,
					AtBats,
					ROUND(Hits / AtBats, 3) AS BattingAverageAgainst,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_h' THEN Value ELSE 0 END) AS Hits,
						SUM(CASE WHEN `Stat` = 'pit_ab' THEN Value ELSE 0 END) AS AtBats,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					BattingAverageAgainst ASC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_bbperg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Walks,
					Innings,
					SeasonsActive,
					Season,
					IsActive,
					ROUND(((Walks / Innings) * 7), 2) AS WalksPerSevenInnings
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_bb' THEN Value ELSE 0 END) AS Walks,
						SUM(CASE WHEN `Stat` = 'pit_ip' THEN ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3) ELSE 0 END) AS Innings,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				WHERE
					Innings > 24.9
				ORDER BY
					WalksPerSevenInnings ASC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_kperg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Strikeouts,
					Innings,
					SeasonsActive,
					Season,
					IsActive,
					ROUND(((Strikeouts / Innings) * 7), 2) AS StrikeoutsPerSevenInnings
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_so' THEN Value ELSE 0 END) AS Strikeouts,
						SUM(CASE WHEN `Stat` = 'pit_ip' THEN ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3) ELSE 0 END) AS Innings,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				WHERE
					Innings > 24.9
				ORDER BY
					StrikeoutsPerSevenInnings DESC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_ip") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT Name,SUM(ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3)) AS Value,SeasonsActive,IsActive,Season FROM `sb_boxscores_pitching` AS b LEFT OUTER JOIN sb_athletes AS a on b.AthleteID = a.AthleteID RIGHT OUTER JOIN sb_games AS g on b.GameID = g.GameID WHERE `Stat`='pit_ip' AND `Name`!='TEAM' GROUP BY Name,Season ORDER BY Value DESC");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 2);
            } 
			else if ($requestedStat == "fld_pct") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					PctNumerator,
					TotalChances,
					ROUND(PctNumerator / TotalChances, 3) AS FieldingPercent,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'fld_a' OR `Stat` = 'fld_po' THEN Value ELSE 0 END) AS PctNumerator,
						SUM(CASE WHEN `Stat` = 'fld_tc' THEN Value ELSE 0 END) AS TotalChances,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_fielding` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				WHERE
					TotalChances > 9
				ORDER BY
					FieldingPercent DESC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "fld_sbpct") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					StolenBases,
					StealAttempts,
					ROUND(StolenBases / StealAttempts, 3) AS StealPercentageAgainst,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'fld_sba' THEN Value ELSE 0 END) AS StolenBases,
						SUM(CASE WHEN `Stat` = 'fld_sba' OR `Stat` = 'fld_csb' THEN Value ELSE 0 END) AS StealAttempts,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_fielding` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, games.Season, athletes.SeasonsActive, athletes.IsActive
				) AS x
				WHERE
					StealAttempts >= 10
				ORDER BY
					StealPercentageAgainst ASC;");
                printSeasonDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else printSeasonDatabase($currentRecords, $oldRecords, "sb", FALSE);
        } elseif ($requestedLevel == "CAR") {
            if ($requestedStat == "hit_avg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Hits,
					AtBats,
					ROUND(Hits / AtBats, 3) AS BattingAverage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'hit_h' THEN Value ELSE 0 END) AS Hits,
						SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END) AS AtBats,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					BattingAverage DESC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "hit_sbpct") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					StolenBases,
					StealAttempts,
					ROUND(StolenBases / StealAttempts, 3) AS StealAverage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'hit_sb' THEN Value ELSE 0 END) AS StolenBases,
						SUM(CASE WHEN `Stat` = 'hit_cs' OR `Stat` = 'hit_sb' THEN Value ELSE 0 END) AS StealAttempts,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					StealAverage DESC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "hit_slug") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					TotalBases,
					AtBats,
					SluggingAverage as Value,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						Name,
						SUM(CASE WHEN `Stat` = 'hit_totbase' THEN Value ELSE 0 END) as TotalBases,
						SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END) as AtBats,
						ROUND(SUM(CASE WHEN `Stat` = 'hit_totbase' THEN Value ELSE 0 END) / SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END), 3) as SluggingAverage,
						SeasonsActive,
						Season,
						IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					LEFT OUTER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					RIGHT OUTER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						`Name` != 'TEAM'
					GROUP BY
						Name
					ORDER BY
						SluggingAverage DESC
				) as x");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "hit_obp") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Hits,
					Walks,
					HBP,
					SF,
					AtBats,
					ROUND((Hits + Walks + HBP) / (AtBats + Walks + HBP + SF), 3) AS OnBasePercentage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'hit_h' THEN Value ELSE 0 END) AS Hits,
						SUM(CASE WHEN `Stat` = 'hit_bb' THEN Value ELSE 0 END) AS Walks,
						SUM(CASE WHEN `Stat` = 'hit_hbp' THEN Value ELSE 0 END) AS HBP,
						SUM(CASE WHEN `Stat` = 'hit_sf' THEN Value ELSE 0 END) AS SF,
						SUM(CASE WHEN `Stat` = 'hit_ab' THEN Value ELSE 0 END) AS AtBats,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_hitting` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					OnBasePercentage DESC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_era") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					EarnedRuns,
					Innings,
					SeasonsActive,
					Season,
					IsActive,
					ROUND(((EarnedRuns / Innings) * 7), 2) AS ERAperSevenInnings
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_er' THEN Value ELSE 0 END) AS EarnedRuns,
						SUM(CASE WHEN `Stat` = 'pit_ip' THEN ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3) ELSE 0 END) AS Innings,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					ERAperSevenInnings ASC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            }
            else if ($requestedStat == "pit_oppavg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Hits,
					AtBats,
					ROUND(Hits / AtBats, 3) AS BattingAverageAgainst,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_h' THEN Value ELSE 0 END) AS Hits,
						SUM(CASE WHEN `Stat` = 'pit_ab' THEN Value ELSE 0 END) AS AtBats,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive
				) AS x
				ORDER BY
					BattingAverageAgainst ASC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_bbperg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Walks,
					Innings,
					SeasonsActive,
					Season,
					IsActive,
					ROUND(((Walks / Innings) * 7), 2) AS WalksPerSevenInnings
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_bb' THEN Value ELSE 0 END) AS Walks,
						SUM(CASE WHEN `Stat` = 'pit_ip' THEN ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3) ELSE 0 END) AS Innings,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive
				) AS x
				WHERE
					Innings > 24.9
				ORDER BY
					WalksPerSevenInnings ASC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_kperg") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					Strikeouts,
					Innings,
					SeasonsActive,
					Season,
					IsActive,
					ROUND(((Strikeouts / Innings) * 7), 2) AS StrikeoutsPerSevenInnings
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'pit_so' THEN Value ELSE 0 END) AS Strikeouts,
						SUM(CASE WHEN `Stat` = 'pit_ip' THEN ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3) ELSE 0 END) AS Innings,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_pitching` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive
				) AS x
				WHERE
					Innings > 24.9
				ORDER BY
					StrikeoutsPerSevenInnings DESC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "pit_ip") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					SUM(ROUND(Value, 0) + (10 * (Value - ROUND(Value, 0)) / 3)) AS TotalInningsPitched,
					SeasonsActive,
					IsActive,
					Season
				FROM
					`sb_boxscores_pitching` AS b
				INNER JOIN
					sb_athletes AS a ON b.AthleteID = a.AthleteID
				INNER JOIN
					sb_games AS g ON b.GameID = g.GameID
				WHERE
					`Stat` = 'pit_ip' AND `Name` != 'TEAM'
				GROUP BY
					Name, SeasonsActive, IsActive, Season
				ORDER BY
					TotalInningsPitched DESC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 2);
            } 
			else if ($requestedStat == "fld_pct") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					PctNumerator,
					TotalChances,
					ROUND(PctNumerator / TotalChances, 3) AS FieldingPercent,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'fld_a' OR `Stat` = 'fld_po' THEN Value ELSE 0 END) AS PctNumerator,
						SUM(CASE WHEN `Stat` = 'fld_tc' THEN Value ELSE 0 END) AS TotalChances,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_fielding` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive, games.Season
				) AS x
				WHERE
					TotalChances > 9
				ORDER BY
					FieldingPercent DESC;");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else if ($requestedStat == "fld_sbpct") {
                $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL'], "SELECT
					Name,
					StolenBases,
					StealAttempts,
					ROUND(StolenBases / StealAttempts, 3) AS StealPercentage,
					SeasonsActive,
					Season,
					IsActive
				FROM (
					SELECT
						athletes.Name,
						SUM(CASE WHEN `Stat` = 'fld_sba' THEN Value ELSE 0 END) AS StolenBases,
						SUM(CASE WHEN `Stat` = 'fld_sba' OR `Stat` = 'fld_csb' THEN Value ELSE 0 END) AS StealAttempts,
						athletes.SeasonsActive,
						games.Season,
						athletes.IsActive
					FROM
						`sb_boxscores_fielding` AS boxscores
					INNER JOIN
						sb_athletes AS athletes ON boxscores.AthleteID = athletes.AthleteID
					INNER JOIN
						sb_games AS games ON boxscores.GameID = games.GameID
					WHERE
						athletes.Name != 'TEAM'
					GROUP BY
						athletes.Name, athletes.SeasonsActive, athletes.IsActive, games.Season
				) AS x
				WHERE
					StealAttempts >= 10
				ORDER BY
					StealPercentage ASC; ");
                printCareerDatabase($currentRecords, $oldRecords, "sb", 1);
            } 
			else printCareerDatabase($currentRecords, $oldRecords, "sb", FALSE);
        }
    } else echo "<h2>Please select Category.</h2>";
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
						<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="sb_GAM">Individual Game Highs</option>
						<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="sb_SEA">Individual Season Highs</option>
						<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="sb_CAR">Career Highs</option>
						<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="sb_TMG">Team Game Highs</option>
						<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="sb_TMS">Team Season Highs</option>
					  </select>
				  </td>
				  </tr>
				  <tr>
				  <td>
				  <?php
    if ($requestedLevel == "GAM" || $requestedLevel == "TMG") {
?>
	<label for="indexStat">Select Stat</label>
	<select name="indexStat" id="indexStat">
	<option value=""></option>
		<option>-----Hitting Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ab") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ab">Most At Bats</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_r">Most Runs Scored</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_h">Most Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rbi") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rbi">Most Runs Batted In</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_double">Most Doubles</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_triple">Most Triples</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hr">Most Home Runs</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_totbase") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_totbase">Most Total Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_bb">Most Times Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hbp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hbp">Most Times Hit By Pitch</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_so") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_so">Most Strikeouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gdp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gdp">Most Times Grounded Into Double Play</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sf">Most Sacrifice Flies</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sh") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sh">Most Sacrifice Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sb">Most Stolen Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_cs">Most Times Caught Stealing</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sbatt") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sbatt">Most Steal Attempts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ibb">Most Times Intentionally Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_kl">Most Times Struck Out Looking</option>
		<?php
        if ($requestedLevel == "TMG") {
?>
				<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_lob") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_lob">Most Runners Left On Base</option>
		<?php
        }
?>
		<option>-----Pitching Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ip") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ip">Most Innings Pitched</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_h">Most Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_r">Most Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_er") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_er">Most Earned Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bb">Most Batters Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_so") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_so">Most Batters Struck Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_double">Most Doubles Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_triple">Most Triples Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_hr">Most Home Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bf">Most Batters Faced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_wp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_wp">Most Wild Pitches</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sfa") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sfa">Most Sacrifice Flies Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sha") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sha">Most Sacrifice Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_kl">Most Batters Struck Out Looking</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ibb">Most Batters Intentionally Walked</option>
		<option>-----Fielding Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_tc") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_tc">Most Total Chances</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_po") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_po">Most Putouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_a") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_a">Most Assists</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_e") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_e">Most Errors Committed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_pb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_pb">Most Passed Balls Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_sba") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_sba">Most Stolen Bases Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_cs">Most Runners Caught Stealing</option>
		<?php
        if ($requestedLevel == "TMG") {
?>
				<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_dp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_dp">Most Double Plays</option>
		<?php
        }
?>
	</select>
					  
			  </td>
			  </tr>
				<hr>
			  <tr>
			  <td>
		<?php
    } elseif ($requestedLevel == "SEA" || $requestedLevel == "TMS" || $requestedLevel == "CAR") {
?>
		<label for="indexStat">Select Stat</label>
		<select name="indexStat" id="indexStat">
		<option value=""></option>
			<option>-----Hitting Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_avg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_avg">Highest Batting Average</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_slug") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_slug">Highest Slugging Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_obp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_obp">Highest On-Base Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gp">Most Games Played</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gs">Most Games Started</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ab") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ab">Most At Bats</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_r">Most Runs Scored</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_h">Most Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rbi") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rbi">Most Runs Batted In</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_double">Most Doubles</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_triple">Most Triples</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hr">Most Home Runs</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_totbase") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_totbase">Most Total Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_bb">Most Times Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hbp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hbp">Most Times Hit By Pitch</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_so") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_so">Most Strikeouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gdp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gdp">Most Times Grounded Into Double Play</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hittp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hittp">Most Times Hit Into Triple Play</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sf">Most Sacrifice Flies</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sh") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sh">Most Sacrifice Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sb">Most Stolen Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_cs">Most Times Caught Stealing</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sbatt") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sbatt">Most Steal Attempts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sbpct") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sbpct">Highest Steal Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_pickoff") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_pickoff">Most Times Picked Off</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rcherr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rcherr">Most Times Reached via Error</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rchci") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rchci">Most Times Reached via Catcher's Interference</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ground") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ground">Most Times Grounded Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_fly") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_fly">Most Times Flew Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ibb">Most Times Intentionally Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_kl">Most Times Struck Out Looking</option>
		<?php
        if ($requestedLevel == "TMS") {
?>
				<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_lob") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_lob">Most Runners Left On Base</option>
		<?php
        }
?>
		<!-- --------------------------------------------------------------------------------------------------------------------------- -->
		<option>-----Pitching Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_era") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_era">Lowest Earned Run Average (7 Inning Game)</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_oppavg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_oppavg">Lowest Opposing Batting Average</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_win") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_win">Most Wins</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_loss") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_loss">Most Losses</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_save") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_save">Most Saves</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_appear") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_appear">Most Appearances</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_gs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_gs">Most Games Started</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_cg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_cg">Most Complete Games</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_gf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_gf">Most Games Finished</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sho") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sho">Most Solo Shutouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_cbo") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_cbo">Most Combined Shutouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ip") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ip">Most Innings Pitched</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_h">Most Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_r">Most Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_er") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_er">Most Earned Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bb">Most Batters Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_so") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_so">Most Batters Struck Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bbperg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bbperg">Fewest Walks Per Game (7 Innings)</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_kperg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_kperg">Most Strikeouts Per Game (7 Innings)</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_double">Most Doubles Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_triple">Most Triples Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_hr">Most Home Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bf">Most Batters Faced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ab") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ab">Most At-Bats Against</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_wp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_wp">Most Wild Pitches</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_hbp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_hbp">Most Batters Hit</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ground") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ground">Most Ground Ball Outs Induced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_fly") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_fly">Most Fly Ball Outs Induced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_pickoff") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_pickoff">Most Runners Picked Off</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sf">Most Sacrifice Flies Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sh") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sh">Most Sacrifice Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_kl">Most Batters Struck Out Looking</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ibb">Most Batters Intentionally Walked</option>
		<!-- --------------------------------------------------------------------------------------------------------------------------- -->	
		<option>-----Fielding Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_pct") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_pct">Highest Fielding Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_tc") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_tc">Most Total Chances</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_po") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_po">Most Putouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_a") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_a">Most Assists</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_e") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_e">Most Errors Committed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_pb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_pb">Most Passed Balls Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_sba") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_sba">Most Stolen Bases Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_cs">Most Runners Caught Stealing</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_sbpct") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_sbpct">Lowest Opposing Steal Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_ci") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_ci">Most Catcher's Interference</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_dp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_dp">Most Double Plays</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_tp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_tp">Most Triple Plays</option>
	  </select>
</td>
			  </tr>
					<hr>
			  <tr>
			  <td>
	<?php
    }
}

function editsbrecords() {
    if (array_key_exists('STAT', $_GET)) $requestedStat = $_GET['STAT'];
    else $requestedStat = NULL;
    if (array_key_exists('LEVEL', $_GET)) $requestedLevel = $_GET['LEVEL'];
    else $requestedLevel = NULL;
?>
	<table width="100%">
	<tr>
	<?php
    if (array_key_exists('SPORT', $_GET)) { ?>
		<td valign="top" width="50%">
			<table width="500" border="0">
		  		<tbody>
		  			<tr>
			  			<td>
				  <label for="adminLevel">Make a selection:</label>
				<select name="adminLevel" id="adminLevel">
					<option value="NoSelection"></option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "GAM") echo "selected" ?> value="sb_GAM">Individual Game Highs</option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "SEA") echo "selected" ?> value="sb_SEA">Individual Season Highs</option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "CAR") echo "selected" ?> value="sb_CAR">Career Highs</option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "TMG") echo "selected" ?> value="sb_TMG">Team Game Highs</option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "TMS") echo "selected" ?> value="sb_TMS">Team Season Highs</option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "ATH") echo "selected" ?> value="sb_ATH">Active Athletes</option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "ATR") echo "selected" ?> value="sb_ATR">Retired Athletes</option>
					<option <?php if (array_key_exists('LEVEL', $_GET) && $_GET['LEVEL'] == "VGM") echo "selected" ?> value="sb_VGM">View Uploaded Games</option>
				  </select>
							<br>
			  <?php
    } 
	if ($requestedLevel == "VGM") {
        getGamesBySeasonForSport($_GET['SPORT']);
    }
    elseif ($requestedLevel == "GAM") {
?>
	<label for="adminStat">Select Stat</label>
	<select name="adminStat" id="adminStat">
	<option value=""></option>
		<option>-----Hitting Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ab") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ab">Most At Bats</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_r">Most Runs Scored</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_h">Most Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rbi") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rbi">Most Runs Batted In</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_double">Most Doubles</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_triple">Most Triples</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hr">Most Home Runs</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_totbase") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_totbase">Most Total Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_bb">Most Times Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hbp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hbp">Most Times Hit By Pitch</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_so") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_so">Most Strikeouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gdp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gdp">Most Times Grounded Into Double Play</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sf">Most Sacrifice Flies</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sh") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sh">Most Sacrifice Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sb">Most Stolen Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_cs">Most Times Caught Stealing</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sbatt") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sbatt">Most Steal Attempts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ibb">Most Times Intentionally Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_kl">Most Times Struck Out Looking</option>
		<?php
        if ($requestedLevel == "TMG") {
?>
				<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_lob") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_lob">Most Runners Left On Base</option>
		<?php
        }
?>
		<option>-----Pitching Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ip") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ip">Most Innings Pitched</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_h">Most Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_r">Most Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_er") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_er">Most Earned Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bb">Most Batters Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_k") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_k">Most Batters Struck Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_double">Most Doubles Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_triple">Most Triples Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_hr">Most Home Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bf">Most Batters Faced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_wp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_wp">Most Wild Pitches</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sf">Most Sacrifice Flies Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sh") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sh">Most Sacrifice Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_kl">Most Batters Struck Out Looking</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ibb">Most Batters Intentionally Walked</option>
		<option>-----Fielding Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_tc") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_tc">Most Total Chances</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_po") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_po">Most Putouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_a") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_a">Most Assists</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_e") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_e">Most Errors Committed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_pb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_pb">Most Passed Balls Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_sba") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_sba">Most Stolen Bases Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_cs">Most Runners Caught Stealing</option>
		<?php
        if ($requestedLevel == "TMG") {
?>
				<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_dp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_dp">Most Double Plays</option>
		<?php
        }
?>
	</select>
			  </td>
			  </tr>
										<hr>
			  <tr>
			  <td>
		<?php
    } 
	elseif ($requestedLevel == "SEA") {
?>
		<label for="adminStat">Select Stat</label>
		<select name="adminStat" id="adminStat">
		<option value=""></option>
			<option>-----Hitting Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_avg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_avg">Highest Batting Average</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_slug") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_slug">Highest Slugging Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_obp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_obp">Highest On-Base Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gp">Most Games Played</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gs">Most Games Started</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ab") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ab">Most At Bats</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_r">Most Runs Scored</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_h">Most Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rbi") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rbi">Most Runs Batted In</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_double">Most Doubles</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_triple">Most Triples</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hr">Most Home Runs</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_totbase") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_totbase">Most Total Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_bb">Most Times Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hbp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hbp">Most Times Hit By Pitch</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_so") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_so">Most Strikeouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_gdp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_gdp">Most Times Grounded Into Double Play</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_hittp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_hittp">Most Times Hit Into Triple Play</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sf">Most Sacrifice Flies</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sh") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sh">Most Sacrifice Hits</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sb">Most Stolen Bases</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_cs">Most Times Caught Stealing</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sbatt") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sbatt">Most Steal Attempts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_sbpct") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_sbpct">Highest Steal Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_pickoff") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_pickoff">Most Times Picked Off</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rcherr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rcherr">Most Times Reached via Error</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_rchci") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_rchci">Most Times Reached via Catcher's Interference</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ground") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ground">Most Times Grounded Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_fly") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_fly">Most Times Flew Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_ibb">Most Times Intentionally Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_kl">Most Times Struck Out Looking</option>
		<?php
        if ($requestedLevel == "TMS") {
?>
				<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "hit_lob") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-hit_lob">Most Runners Left On Base</option>
		<?php
        }
?>
		<!-- --------------------------------------------------------------------------------------------------------------------------- -->
		<option>-----Pitching Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_era") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_era">Lowest Earned Run Average</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_oppavg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_oppavg">Lowest Opposing Batting Average</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_win") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_win">Most Wins</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_loss") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_loss">Most Losses</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_save") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_save">Most Saves</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_appear") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_appear">Most Appearances</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_gs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_gs">Most Games Started</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_cg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_cg">Most Complete Games</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_gf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_gf">Most Games Finished</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sho") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sho">Most Solo Shutouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_cbo") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_cbo">Most Combined Shutouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ip") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ip">Most Innings Pitched</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_h") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_h">Most Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_r") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_r">Most Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_er") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_er">Most Earned Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bb">Most Batters Walked</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_so") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_so">Most Batters Struck Out</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bbperg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bbperg">Fewest Walks Per Game (7 Innings)</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_kperg") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_kperg">Most Strikeouts Per Game (7 Innings)</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_double") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_double">Most Doubles Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_triple") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_triple">Most Triples Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_hr") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_hr">Most Home Runs Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_bf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_bf">Most Batters Faced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ab") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ab">Most At-Bats Against</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_wp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_wp">Most Wild Pitches</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_hbp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_hbp">Most Batters Hit</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ground") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ground">Most Ground Ball Outs Induced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_fly") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_fly">Most Fly Ball Outs Induced</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_pickoff") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_pickoff">Most Runners Picked Off</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sf") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sf">Most Sacrifice Flies Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_sh") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_sh">Most Sacrifice Hits Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_kl") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_kl">Most Batters Struck Out Looking</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "pit_ibb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-pit_ibb">Most Batters Intentionally Walked</option>
		<!-- --------------------------------------------------------------------------------------------------------------------------- -->	
		<option>-----Fielding Stats-----</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_pct") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_pct">Highest Fielding Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_tc") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_tc">Most Total Chances</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_po") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_po">Most Putouts</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_a") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_a">Most Assists</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_e") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_e">Most Errors Committed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_pb") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_pb">Most Passed Balls Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_sba") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_sba">Most Stolen Bases Allowed</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_cs") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_cs">Most Runners Caught Stealing</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_sbpct") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_sbpct">Lowest Opposing Steal Percentage</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_ci") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_ci">Most Catcher's Interference</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_dp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_dp">Most Double Plays</option>
		<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "fld_tp") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-fld_tp">Most Triple Plays</option>
	  </select>
</td>
			  </tr>
					<hr>
			  <tr>
			  <td>
	<?php
    } 
	elseif ($requestedLevel == "CAR") {
?>
		<label for="adminStat">Select Stat</label>
		<select name="adminStat" id="adminStat">
		<option value=""></option>
			<option <?php if (array_key_exists('STAT', $_GET) && $_GET['STAT'] == "points") echo "selected" ?> value="sb_<?php echo $_GET['LEVEL'] ?>-points">Most Points</option>
			
	  </select>
</td>
			  </tr>
					<hr>
			  <tr>
			  <td>
	<?php
    } 
	elseif ($requestedLevel == "ATH") {
        echo "Active Athlete Administration";
        athleteAdministration("sb", "A");
    } 
	elseif ($requestedLevel == "ATR") {
        echo "Retired Athlete Administration";
        athleteAdministration("sb", "R");
    } 
	else {
        echo "<h2>Please select Stat Category.</h2>";
?>
			</td>
		  <?php
    }
	
    if ($requestedStat != NULL) {
        if ($requestedLevel != NULL && $requestedStat != NULL && $requestedLevel != "ATH") {
            $explodedStat = explode("_", $requestedStat);
            //			var_dump ($explodedStat);
            $db_name = "kcmsathl_wvsu";
            if ($explodedStat[0] == "hit") $table = "sb_boxscores_hitting";
            if ($explodedStat[0] == "fld") $table = "sb_boxscores_fielding";
            if ($explodedStat[0] == "pit") $table = "sb_boxscores_pitching";
            // Read both old record databases and new records. Old Records are manual, new records are drawn from XML files.
            $currentRecords = readCurrentDatabase($db_name, $table, $requestedStat, $_GET['LEVEL']);
            $oldRecords = readOldDatabase($db_name, "sb_oldrecords", $requestedStat, $_GET['LEVEL']);
            if ($requestedStat != NULL) {
                $currentRecords = readCurrentDatabase($db_name, $table, $_GET['STAT'], $_GET['LEVEL']);
                echo "<hr>";
                echo ("Manual Record Editing - " . $_GET['LEVEL'] . ": " . $_GET['STAT']);
                $oldResultsString = readOldDatabase("kcmsathl_WVSU", "sb_oldrecords", $_GET['STAT'], $_GET['LEVEL']);
                if ($requestedLevel == "CAR") editCareerDatabase($oldResultsString, TRUE);
                else if ($requestedLevel == "TMS" || $requestedLevel == "SEA") editSeasonDatabase($oldResultsString, TRUE);
                else editCurrentDatabase($oldResultsString, TRUE); ?>
				<td>
					Records Code <br>
					<input type="textarea" value = "<?php echo htmlspecialchars($recordsCode) ?>">
				</td>
			<?php
            }
        }
    }
}

function uploadsbBox($ArrayRead) {
    $db_name = "sb";
    //Format Date for use in database
    $date = $ArrayRead['venue']['@attributes']['date'];
    $time = $ArrayRead['venue']['@attributes']['start'];
    $time_input = strtotime($date);
    $formattedDate = date('Y-m-d', $time_input);
    $locationOfGame = NULL;
    if ($ArrayRead["team"][0]["@attributes"]["code"] == 1439) { // Look for good guys by NCAA RPI number. Should eliminate variance on how the team name is typed.
        if (array_key_exists('neutralgame', $ArrayRead["team"][0]["@attributes"]) && $ArrayRead["team"][0]["@attributes"]['neutralgame'] == "Y") $homeAway = "N";
        else $homeAway = "A";
        $teamArray = $ArrayRead['team'][0];
        $opponentName = $ArrayRead['team'][1]['@attributes']['name'];
        $opponent = $ArrayRead['team'][1]['@attributes']['code'];
        $opponentTotals = $ArrayRead['team'][1]['totals'];
        $homeScore = $ArrayRead['team'][0]['linescore']['@attributes']['runs']; // Not actually Home Team, but the team we are looking for AKA the good guys.
        $awayScore = $ArrayRead['team'][1]['linescore']['@attributes']['runs']; // Opponent, regardless of location
        
    } else if ($ArrayRead["team"][1]["@attributes"]["code"] == 1439) {
        if (array_key_exists('neutralgame', $ArrayRead["team"][0]["@attributes"]) && $ArrayRead["team"][0]["@attributes"]['neutralgame'] == "Y") $homeAway = "N";
        else $homeAway = "H";
        $teamArray = $ArrayRead['team'][1];
        $opponentName = $ArrayRead['team'][0]['@attributes']['name'];
        $opponent = $ArrayRead['team'][0]['@attributes']['code'];
        $opponentTotals = $ArrayRead['team'][0]['totals'];
        $homeScore = $ArrayRead['team'][1]['linescore']['@attributes']['runs'];
        $awayScore = $ArrayRead['team'][0]['linescore']['@attributes']['runs'];
    }
    if ($homeScore > $awayScore) $result = "W";
    else $result = "L";
    $explodedDate = explode("-", $formattedDate);
    $season = $explodedDate[0];
    // Need to Check for duplicate games
    $formattedLocation = checkName($ArrayRead['venue']['@attributes']['location']);
    $GameID = createNewGame($formattedDate, $time, $homeAway, $opponent, $result, "$homeScore-$awayScore", $ArrayRead['venue']['@attributes']['attend'], $formattedLocation, $season, $db_name);
    echo ("<h3>Date of Game: $date | Opponent: $opponentName | RPI: $opponent | GameID: $GameID</h3><h3>Query Results:</h3><br>");
    if (checkOpponent($opponent, $opponentName)) echo "<h2>Opponent RPI Added to database</h2>";
    $teamTotals = $teamArray['totals'];
    //Team and Hitting Stats
    createPlayerStatsb("TEAM", "hit_lob", "hitting", $teamArray['linescore']['@attributes']['lob'], $GameID, $season);
    createPlayerStatsb("TEAM", "hit_ab", "hitting", $teamTotals['hitting']['@attributes']['ab'], $GameID, $season);
    createPlayerStatsb("TEAM", "hit_r", "hitting", $teamTotals['hitting']['@attributes']['r'], $GameID, $season);
    createPlayerStatsb("TEAM", "hit_h", "hitting", $teamTotals['hitting']['@attributes']['h'], $GameID, $season);
    createPlayerStatsb("TEAM", "hit_rbi", "hitting", $teamTotals['hitting']['@attributes']['rbi'], $GameID, $season);
    if (array_key_exists('double', $teamTotals['hitting']['@attributes'])) {
        createPlayerStatsb("TEAM", "hit_double", "hitting", $teamTotals['hitting']['@attributes']['double'], $GameID, $season);
        $doubles = $teamTotals['hitting']['@attributes']['double'];
    } else $doubles = 0;
    if (array_key_exists('triple', $teamTotals['hitting']['@attributes'])) {
        createPlayerStatsb("TEAM", "hit_triple", "hitting", $teamTotals['hitting']['@attributes']['triple'], $GameID, $season);
        $triples = $teamTotals['hitting']['@attributes']['triple'];
    } else $triples = 0;
    if (array_key_exists('hr', $teamTotals['hitting']['@attributes'])) {
        createPlayerStatsb("TEAM", "hit_hr", "hitting", $teamTotals['hitting']['@attributes']['hr'], $GameID, $season);
        $homeRuns = $teamTotals['hitting']['@attributes']['hr'];
    } else $homeRuns = 0;
    createPlayerStatsb("TEAM", "hit_totbase", "hitting", (($homeRuns * 3) + ($triples * 2) + ($doubles) + $teamTotals['hitting']['@attributes']['h']), $GameID, $season);
    if (array_key_exists('bb', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_bb", "hitting", $teamTotals['hitting']['@attributes']['bb'], $GameID, $season);
    if (array_key_exists('ibb', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_ibb", "hitting", $teamTotals['hitting']['@attributes']['ibb'], $GameID, $season);
    if (array_key_exists('so', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_so", "hitting", $teamTotals['hitting']['@attributes']['so'], $GameID, $season);
    if (array_key_exists('kl', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_kl", "hitting", $teamTotals['hitting']['@attributes']['kl'], $GameID, $season);
    if (array_key_exists('hbp', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_hbp", "hitting", $teamTotals['hitting']['@attributes']['hbp'], $GameID, $season);
    if (array_key_exists('sb', $teamTotals['hitting']['@attributes'])) {
        $stolenBases = $teamTotals['hitting']['@attributes']['sb'];
        createPlayerStatsb("TEAM", "hit_sb", "hitting", $teamTotals['hitting']['@attributes']['sb'], $GameID, $season);
    } else $stolenBases = 0;
    if (array_key_exists('cs', $teamTotals['hitting']['@attributes'])) {
        $caughtStealing = $teamTotals['hitting']['@attributes']['cs'];
        createPlayerStatsb("TEAM", "hit_cs", "hitting", $teamTotals['hitting']['@attributes']['cs'], $GameID, $season);
    } else $caughtStealing = 0;
    createPlayerStatsb("TEAM", "hit_sbatt", "hitting", $stolenBases + $caughtStealing, $GameID, $season);
    if (array_key_exists('sh', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_sh", "hitting", $teamTotals['hitting']['@attributes']['sh'], $GameID, $season);
    if (array_key_exists('sf', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_sf", "hitting", $teamTotals['hitting']['@attributes']['sf'], $GameID, $season);
    if (array_key_exists('rchci', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_rchci", "hitting", $teamTotals['hitting']['@attributes']['rchci'], $GameID, $season);
    if (array_key_exists('rcherr', $teamTotals['hsitsummary']['@attributes'])) createPlayerStatsb("TEAM", "hit_rcherr", "hitting", $teamTotals['hsitsummary']['@attributes']['rcherr'], $GameID, $season);
    if (array_key_exists('rchfc', $teamTotals['hihsitsummarytting']['@attributes'])) createPlayerStatsb("TEAM", "hit_rchfc", "hitting", $teamTotals['hsitsummary']['@attributes']['rchfc'], $GameID, $season);
    if (array_key_exists('ground', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_ground", "hitting", $teamTotals['hitting']['@attributes']['ground'], $GameID, $season);
    if (array_key_exists('fly', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_fly", "hitting", $teamTotals['hitting']['@attributes']['fly'], $GameID, $season);
    if (array_key_exists('gdp', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_gdp", "hitting", $teamTotals['hitting']['@attributes']['gdp'], $GameID, $season);
    if (array_key_exists('pickoff', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_pickoff", "hitting", $teamTotals['hitting']['@attributes']['pickoff'], $GameID, $season);
    if (array_key_exists('hitdp', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_hitdp", "hitting", $teamTotals['hitting']['@attributes']['hitdp'], $GameID, $season);
    if (array_key_exists('hittp', $teamTotals['hitting']['@attributes'])) createPlayerStatsb("TEAM", "hit_hittp", "hitting", $teamTotals['hitting']['@attributes']['hittp'], $GameID, $season);
    //Fielding Stats
    if (array_key_exists('po', $teamTotals['fielding']['@attributes'])) {
        createPlayerStatsb("TEAM", "fld_po", "fielding", $teamTotals['fielding']['@attributes']['po'], $GameID, $season);
        $putouts = $teamTotals['fielding']['@attributes']['po'];
    } else $putouts = 0;
    if (array_key_exists('a', $teamTotals['fielding']['@attributes'])) {
        createPlayerStatsb("TEAM", "fld_a", "fielding", $teamTotals['fielding']['@attributes']['a'], $GameID, $season);
        $assists = $teamTotals['fielding']['@attributes']['a'];
    } else $assists = 0;
    if (array_key_exists('e', $teamTotals['fielding']['@attributes'])) {
        createPlayerStatsb("TEAM", "fld_e", "fielding", $teamTotals['fielding']['@attributes']['e'], $GameID, $season);
        $errors = $teamTotals['fielding']['@attributes']['e'];
    } else $errors = 0;
    createPlayerStatsb("TEAM", "fld_tc", "fielding", ($putouts + $assists + $errors), $GameID, $season);
    if (array_key_exists('pb', $teamTotals['fielding']['@attributes'])) createPlayerStatsb("TEAM", "fld_pb", "fielding", $teamTotals['fielding']['@attributes']['pb'], $GameID, $season);
    if (array_key_exists('ci', $teamTotals['fielding']['@attributes'])) createPlayerStatsb("TEAM", "fld_ci", "fielding", $teamTotals['fielding']['@attributes']['ci'], $GameID, $season);
    if (array_key_exists('sba', $teamTotals['fielding']['@attributes'])) createPlayerStatsb("TEAM", "fld_sba", "fielding", $teamTotals['fielding']['@attributes']['sba'], $GameID, $season);
    if (array_key_exists('csb', $teamTotals['fielding']['@attributes'])) createPlayerStatsb("TEAM", "fld_csb", "fielding", $teamTotals['fielding']['@attributes']['csb'], $GameID, $season);
    if (array_key_exists('indp', $teamTotals['fielding']['@attributes'])) createPlayerStatsb("TEAM", "fld_indp", "fielding", $teamTotals['fielding']['@attributes']['indp'], $GameID, $season);
    if (array_key_exists('intp', $teamTotals['fielding']['@attributes'])) createPlayerStatsb("TEAM", "fld_intp", "fielding", $teamTotals['fielding']['@attributes']['intp'], $GameID, $season);
    //Pitching Stats
    if (array_key_exists('appear', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_appear", "pitching", 1, $GameID, $season);
    //Check for Pitching Result Format
    if (array_key_exists('win', $teamTotals['pitching']['@attributes']) && array_key_exists('loss', $teamTotals['pitching']['@attributes']) && array_key_exists('save', $teamTotals['pitching']['@attributes'])) //DakStats Format...Maybe Presto?
    {
        if ($teamTotals['pitching']['@attributes']['win'] == 1) createPlayerStatsb("TEAM", "pit_win", "pitching", $teamTotals['pitching']['@attributes']['win'], $GameID, $season);
        elseif ($teamTotals['pitching']['@attributes']['loss'] == 1) createPlayerStatsb("TEAM", "pit_loss", "pitching", $teamTotals['pitching']['@attributes']['loss'], $GameID, $season);
        if ($teamTotals['pitching']['@attributes']['save'] == 1) createPlayerStatsb("TEAM", "pit_save", "pitching", $teamTotals['pitching']['@attributes']['save'], $GameID, $season);
    } else { // Statcrew and Presto format...puts the current record in the XML attribute so it's not usable data.
        if (array_key_exists('win', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_win", "pitching", 1, $GameID, $season);
        if (array_key_exists('loss', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_loss", "pitching", 1, $GameID, $season);
        if (array_key_exists('save', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_save", "pitching", 1, $GameID, $season);
    }
    if (array_key_exists('gs', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_gs", "pitching", $teamTotals['pitching']['@attributes']['gs'], $GameID, $season);
    if (array_key_exists('cg', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_cg", "pitching", $teamTotals['pitching']['@attributes']['cg'], $GameID, $season);
    if (array_key_exists('sho', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_sho", "pitching", $teamTotals['pitching']['@attributes']['sho'], $GameID, $season);
    if (array_key_exists('cbo', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_cbo", "pitching", $teamTotals['pitching']['@attributes']['cbo'], $GameID, $season);
    if (array_key_exists('ip', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_ip", "pitching", $teamTotals['pitching']['@attributes']['ip'], $GameID, $season);
    if (array_key_exists('h', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_h", "pitching", $teamTotals['pitching']['@attributes']['h'], $GameID, $season);
    if (array_key_exists('r', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_r", "pitching", $teamTotals['pitching']['@attributes']['r'], $GameID, $season);
    if (array_key_exists('er', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_er", "pitching", $teamTotals['pitching']['@attributes']['er'], $GameID, $season);
    if (array_key_exists('bb', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_bb", "pitching", $teamTotals['pitching']['@attributes']['bb'], $GameID, $season);
    if (array_key_exists('so', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_so", "pitching", $teamTotals['pitching']['@attributes']['so'], $GameID, $season);
    if (array_key_exists('bf', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_bf", "pitching", $teamTotals['pitching']['@attributes']['bf'], $GameID, $season);
    if (array_key_exists('ab', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_ab", "pitching", $teamTotals['pitching']['@attributes']['ab'], $GameID, $season);
    if (array_key_exists('double', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_double", "pitching", $teamTotals['pitching']['@attributes']['double'], $GameID, $season);
    if (array_key_exists('triple', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_triple", "pitching", $teamTotals['pitching']['@attributes']['triple'], $GameID, $season);
    if (array_key_exists('hr', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_hr", "pitching", $teamTotals['pitching']['@attributes']['hr'], $GameID, $season);
    if (array_key_exists('wp', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_wp", "pitching", $teamTotals['pitching']['@attributes']['wp'], $GameID, $season);
    if (array_key_exists('bk', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_bk", "pitching", $teamTotals['pitching']['@attributes']['bk'], $GameID, $season);
    if (array_key_exists('hbp', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_hbp", "pitching", $teamTotals['pitching']['@attributes']['hbp'], $GameID, $season);
    if (array_key_exists('kl', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_kl", "pitching", $teamTotals['pitching']['@attributes']['kl'], $GameID, $season);
    if (array_key_exists('ibb', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_ibb", "pitching", $teamTotals['pitching']['@attributes']['ibb'], $GameID, $season);
    if (array_key_exists('sfa', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_sfa", "pitching", $teamTotals['pitching']['@attributes']['sfa'], $GameID, $season);
    if (array_key_exists('sha', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_sha", "pitching", $teamTotals['pitching']['@attributes']['sha'], $GameID, $season);
    if (array_key_exists('fly', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_fly", "pitching", $teamTotals['pitching']['@attributes']['fly'], $GameID, $season);
    if (array_key_exists('ground', $teamTotals['pitching']['@attributes'])) createPlayerStatsb("TEAM", "pit_ground", "pitching", $teamTotals['pitching']['@attributes']['ground'], $GameID, $season);
    $numOfPlayers = sizeof($teamArray['player']);
    $lastPitcher = array(0, NULL);
    for ($currPlayer = 0;$currPlayer < $numOfPlayers;$currPlayer++):
        $currArray = $teamArray['player'][$currPlayer];
        $athleteName = $currArray['@attributes']['name'];
        if ($athleteName == "TEAM" || $currArray['@attributes']['gp'] == 0 || $currArray['@attributes']['uni'] == "TM") continue;
        else {
            if (array_key_exists('gs', $currArray['@attributes'])) createPlayerStatsb("$athleteName", "hit_gs", "hitting", $currArray['@attributes']['gs'], $GameID, $season);
            if (array_key_exists('gp', $currArray['@attributes'])) createPlayerStatsb("$athleteName", "hit_gp", "hitting", $currArray['@attributes']['gp'], $GameID, $season);
            createPlayerStatsb("$athleteName", "hit_ab", "hitting", $currArray['hitting']['@attributes']['ab'], $GameID, $season);
            createPlayerStatsb("$athleteName", "hit_r", "hitting", $currArray['hitting']['@attributes']['r'], $GameID, $season);
            createPlayerStatsb("$athleteName", "hit_h", "hitting", $currArray['hitting']['@attributes']['h'], $GameID, $season);
            createPlayerStatsb("$athleteName", "hit_rbi", "hitting", $currArray['hitting']['@attributes']['rbi'], $GameID, $season);
            if (array_key_exists('double', $currArray['hitting']['@attributes'])) {
                createPlayerStatsb("$athleteName", "hit_double", "hitting", $currArray['hitting']['@attributes']['double'], $GameID, $season);
                $doubles = $currArray['hitting']['@attributes']['double'];
            } else $doubles = 0;
            if (array_key_exists('triple', $currArray['hitting']['@attributes'])) {
                createPlayerStatsb("$athleteName", "hit_triple", "hitting", $currArray['hitting']['@attributes']['triple'], $GameID, $season);
                $triples = $currArray['hitting']['@attributes']['triple'];
            } else $triples = 0;
            if (array_key_exists('hr', $currArray['hitting']['@attributes'])) {
                createPlayerStatsb("$athleteName", "hit_hr", "hitting", $currArray['hitting']['@attributes']['hr'], $GameID, $season);
                $homeRuns = $currArray['hitting']['@attributes']['hr'];
            } else $homeRuns = 0;
            createPlayerStatsb("$athleteName", "hit_totbase", "hitting", (($homeRuns * 3) + ($triples * 2) + ($doubles) + $currArray['hitting']['@attributes']['h']), $GameID, $season);
            if (array_key_exists('bb', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_bb", "hitting", $currArray['hitting']['@attributes']['bb'], $GameID, $season);
            if (array_key_exists('ibb', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_ibb", "hitting", $currArray['hitting']['@attributes']['ibb'], $GameID, $season);
            if (array_key_exists('so', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_so", "hitting", $currArray['hitting']['@attributes']['so'], $GameID, $season);
            if (array_key_exists('kl', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_kl", "hitting", $currArray['hitting']['@attributes']['kl'], $GameID, $season);
            if (array_key_exists('hbp', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_hbp", "hitting", $currArray['hitting']['@attributes']['hbp'], $GameID, $season);
            if (array_key_exists('sb', $currArray['hitting']['@attributes'])) {
                $stolenBases = $currArray['hitting']['@attributes']['sb'];
                createPlayerStatsb("$athleteName", "hit_sb", "hitting", $currArray['hitting']['@attributes']['sb'], $GameID, $season);
            } else $stolenBases = 0;
            if (array_key_exists('cs', $currArray['hitting']['@attributes'])) {
                $caughtStealing = $currArray['hitting']['@attributes']['cs'];
                createPlayerStatsb("$athleteName", "hit_cs", "hitting", $currArray['hitting']['@attributes']['cs'], $GameID, $season);
            } else $caughtStealing = 0;
            createPlayerStatsb("$athleteName", "hit_sbatt", "hitting", $stolenBases + $caughtStealing, $GameID, $season);
            if (array_key_exists('sh', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_sh", "hitting", $currArray['hitting']['@attributes']['sh'], $GameID, $season);
            if (array_key_exists('sf', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_sf", "hitting", $currArray['hitting']['@attributes']['sf'], $GameID, $season);
            if (array_key_exists('rchci', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_rchci", "hitting", $currArray['hitting']['@attributes']['rchci'], $GameID, $season);
            if (array_key_exists('rcherr', $currArray['hsitsummary']['@attributes'])) createPlayerStatsb("$athleteName", "hit_rcherr", "hitting", $currArray['hsitsummary']['@attributes']['rcherr'], $GameID, $season);
            if (array_key_exists('rchfc', $currArray['hsitsummary']['@attributes'])) createPlayerStatsb("$athleteName", "hit_rchfc", "hitting", $currArray['hsitsummary']['@attributes']['rchfc'], $GameID, $season);
            if (array_key_exists('ground', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_ground", "hitting", $currArray['hitting']['@attributes']['ground'], $GameID, $season);
            if (array_key_exists('fly', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_fly", "hitting", $currArray['hitting']['@attributes']['fly'], $GameID, $season);
            if (array_key_exists('gdp', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_gdp", "hitting", $currArray['hitting']['@attributes']['gdp'], $GameID, $season);
            if (array_key_exists('pickoff', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_pickoff", "hitting", $currArray['hitting']['@attributes']['pickoff'], $GameID, $season);
            if (array_key_exists('hitdp', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_hitdp", "hitting", $currArray['hitting']['@attributes']['hitdp'], $GameID, $season);
            if (array_key_exists('hittp', $currArray['hitting']['@attributes'])) createPlayerStatsb("$athleteName", "hit_hittp", "hitting", $currArray['hitting']['@attributes']['hittp'], $GameID, $season);
            //Fielding Stats
            if (array_key_exists('po', $currArray['fielding']['@attributes'])) {
                createPlayerStatsb("$athleteName", "fld_po", "fielding", $currArray['fielding']['@attributes']['po'], $GameID, $season);
                $putouts = $currArray['fielding']['@attributes']['po'];
            } else $putouts = 0;
            if (array_key_exists('a', $currArray['fielding']['@attributes'])) {
                createPlayerStatsb("$athleteName", "fld_a", "fielding", $currArray['fielding']['@attributes']['a'], $GameID, $season);
                $assists = $currArray['fielding']['@attributes']['a'];
            } else $assists = 0;
            if (array_key_exists('e', $currArray['fielding']['@attributes'])) {
                createPlayerStatsb("$athleteName", "fld_e", "fielding", $currArray['fielding']['@attributes']['e'], $GameID, $season);
                $errors = $currArray['fielding']['@attributes']['e'];
            } else $errors = 0;
            createPlayerStatsb("$athleteName", "fld_tc", "fielding", ($putouts + $assists + $errors), $GameID, $season);
            if (array_key_exists('pb', $currArray['fielding']['@attributes'])) createPlayerStatsb("$athleteName", "fld_pb", "fielding", $currArray['fielding']['@attributes']['pb'], $GameID, $season);
            if (array_key_exists('ci', $currArray['fielding']['@attributes'])) createPlayerStatsb("$athleteName", "fld_ci", "fielding", $currArray['fielding']['@attributes']['ci'], $GameID, $season);
            if (array_key_exists('sba', $currArray['fielding']['@attributes'])) createPlayerStatsb("$athleteName", "fld_sba", "fielding", $currArray['fielding']['@attributes']['sba'], $GameID, $season);
            if (array_key_exists('csb', $currArray['fielding']['@attributes'])) createPlayerStatsb("$athleteName", "fld_csb", "fielding", $currArray['fielding']['@attributes']['csb'], $GameID, $season);
            if (array_key_exists('indp', $currArray['fielding']['@attributes'])) createPlayerStatsb("$athleteName", "fld_indp", "fielding", $currArray['fielding']['@attributes']['indp'], $GameID, $season);
            if (array_key_exists('intp', $currArray['fielding']['@attributes'])) createPlayerStatsb("$athleteName", "fld_intp", "fielding", $currArray['fielding']['@attributes']['intp'], $GameID, $season);
            if (array_key_exists('pitching', $currArray)) {
                //Pitching Stats
                if (array_key_exists('appear', $currArray['pitching']['@attributes'])) {
                    createPlayerStatsb("$athleteName", "pit_appear", "pitching", 1, $GameID, $season);
                    if ($currArray['pitching']['@attributes']['appear'] > $lastPitcher[0]) $lastPitcher = array($currArray['pitching']['@attributes']['appear'], $athleteName);
                }
                //Check for Pitching Result Format
                if (array_key_exists('win', $currArray['pitching']['@attributes']) && array_key_exists('loss', $currArray['pitching']['@attributes']) && array_key_exists('save', $currArray['pitching']['@attributes'])) //DakStats Format
                {
                    if ($currArray['pitching']['@attributes']['win'] == 1) createPlayerStatsb("$athleteName", "pit_win", "pitching", $currArray['pitching']['@attributes']['win'], $GameID, $season);
                    elseif ($currArray['pitching']['@attributes']['loss'] == 1) createPlayerStatsb("$athleteName", "pit_loss", "pitching", $currArray['pitching']['@attributes']['loss'], $GameID, $season);
                    if ($currArray['pitching']['@attributes']['save'] == 1) createPlayerStatsb("$athleteName", "pit_save", "pitching", $currArray['pitching']['@attributes']['save'], $GameID, $season);
                } else { // Statcrew and Presto format...puts the current record in the XML attribute so it's not usable data.
                    if (array_key_exists('win', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_win", "pitching", 1, $GameID, $season);
                    if (array_key_exists('loss', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_loss", "pitching", 1, $GameID, $season);
                    if (array_key_exists('save', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_save", "pitching", 1, $GameID, $season);
                }
                if (array_key_exists('gs', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_gs", "pitching", $currArray['pitching']['@attributes']['gs'], $GameID, $season);
                if (array_key_exists('cg', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_cg", "pitching", $currArray['pitching']['@attributes']['cg'], $GameID, $season);
                if (array_key_exists('sho', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_sho", "pitching", $currArray['pitching']['@attributes']['sho'], $GameID, $season);
                if (array_key_exists('cbo', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_cbo", "pitching", $currArray['pitching']['@attributes']['cbo'], $GameID, $season);
                if (array_key_exists('ip', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_ip", "pitching", $currArray['pitching']['@attributes']['ip'], $GameID, $season);
                if (array_key_exists('h', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_h", "pitching", $currArray['pitching']['@attributes']['h'], $GameID, $season);
                if (array_key_exists('r', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_r", "pitching", $currArray['pitching']['@attributes']['r'], $GameID, $season);
                if (array_key_exists('er', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_er", "pitching", $currArray['pitching']['@attributes']['er'], $GameID, $season);
                if (array_key_exists('bb', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_bb", "pitching", $currArray['pitching']['@attributes']['bb'], $GameID, $season);
                if (array_key_exists('so', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_so", "pitching", $currArray['pitching']['@attributes']['so'], $GameID, $season);
                if (array_key_exists('bf', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_bf", "pitching", $currArray['pitching']['@attributes']['bf'], $GameID, $season);
                if (array_key_exists('ab', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_ab", "pitching", $currArray['pitching']['@attributes']['ab'], $GameID, $season);
                if (array_key_exists('double', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_double", "pitching", $currArray['pitching']['@attributes']['double'], $GameID, $season);
                if (array_key_exists('triple', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_triple", "pitching", $currArray['pitching']['@attributes']['triple'], $GameID, $season);
                if (array_key_exists('hr', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_hr", "pitching", $currArray['pitching']['@attributes']['hr'], $GameID, $season);
                if (array_key_exists('wp', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_wp", "pitching", $currArray['pitching']['@attributes']['wp'], $GameID, $season);
                if (array_key_exists('bk', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_bk", "pitching", $currArray['pitching']['@attributes']['bk'], $GameID, $season);
                if (array_key_exists('hbp', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_hbp", "pitching", $currArray['pitching']['@attributes']['hbp'], $GameID, $season);
                if (array_key_exists('kl', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_kl", "pitching", $currArray['pitching']['@attributes']['kl'], $GameID, $season);
                if (array_key_exists('ibb', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_ibb", "pitching", $currArray['pitching']['@attributes']['ibb'], $GameID, $season);
                if (array_key_exists('sfa', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_sfa", "pitching", $currArray['pitching']['@attributes']['sfa'], $GameID, $season);
                if (array_key_exists('sha', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_sha", "pitching", $currArray['pitching']['@attributes']['sha'], $GameID, $season);
                if (array_key_exists('fly', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_fly", "pitching", $currArray['pitching']['@attributes']['fly'], $GameID, $season);
                if (array_key_exists('ground', $currArray['pitching']['@attributes'])) createPlayerStatsb("$athleteName", "pit_ground", "pitching", $currArray['pitching']['@attributes']['ground'], $GameID, $season);
            }
        }
    endfor;
    createPlayerStatsb($lastPitcher[1], "pit_gf", "pitching", 1, $GameID, $season);
}

function createPlayerStatsb($name, $stat, $table, $value, $gameID, $season = "", $remarks = "") {
    if ($value > 0) {
        $config = parse_ini_file('config.ini');
        $sb_milestones = parse_ini_file('sb.ini');
        //var_dump($vb_milestones);
        $db_name = "kcmsathl_WVSU";
        $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
        if ($name == "TEAM") {
            $athleteID = 0;
            $statType = "TMG";
        } else {
            $statType = "GAM";
            $name = checkName($name);
            $athlQuery = "SELECT * FROM `sb_athletes` WHERE `Name`='$name' AND `IsActive`='1'"; // Search for athlete in database. If found, get Athlete ID, otherwise create a new athlete in the database and assign an ID number to him/her.
            $athlResults = mysqli_query($conn, $athlQuery);
            $finalAthlResult = mysqli_fetch_assoc($athlResults);
            if (mysqli_num_rows($athlResults) > 0) $athleteID = $finalAthlResult['AthleteID'];
            else $athleteID = createAthlete($name, 'sb', $season);
        }
        //If duplicate is found, update existing record instead of creating an new one. This will reduce the numbers in the records database which will start to get massive quickly.
        $dupQuery = "SELECT * FROM `sb_boxscores_" . $table . "` WHERE `GameID`='$gameID' AND `Stat`='$stat' AND `AthleteID`='$athleteID' ORDER BY `EntryID` DESC";
        $dupResults = mysqli_query($conn, $dupQuery);
        $finalDupResult = mysqli_fetch_assoc($dupResults);
        if (mysqli_num_rows($dupResults) > 0) $duplicateResult = $finalDupResult['EntryID'];
        else $duplicateResult = - 1;
        //Initialize all variables...just for shits and giggles.
        if ($duplicateResult < 0) {
            if ($name != "TEAM") {
                /*		switch ($stat){
                //Check for Season and Career Milestones
                
                case ('points'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                $newSeasonTotal = $seasonTotal + $value;
                if (($seasonTotal / $sb_milestones['sea_points']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_points']){
                if ($newSeasonTotal > $sb_milestones['sea_points'])
                echo "<h4>***** Season Milestone Achieved for $name in Points: $newSeasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Points: $newSeasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                $newCareerTotal = $careerTotal + $value;
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if ($careerTotal != NULL && ($careerTotal / $sb_milestones['car_points'][0]) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_points'][0]) {
                if ($newCareerTotal > $sb_milestones['car_points'][0])
                echo "<h4>***** Career Milestone Achieved for $name in Points: $newCareerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Points: $newCareerTotal -----<br>";
                }
                elseif ($careerTotal != NULL && ($careerTotal / $sb_milestones['car_points'][1]) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_points'][1]){
                if ($newCareerTotal > $sb_milestones['car_points'][1])
                echo "<h4>***** Career Milestone Achieved for $name in Points: $newCareerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Points: $newCareerTotal -----<br>";
                
                }
                elseif($careerTotal != NULL && ($careerTotal / $sb_milestones['car_points'][2]) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_points'][2]){
                if ($newCareerTotal > $sb_milestones['car_points'][2])
                echo "<h4>***** Career Milestone Achieved for $name in Points: $newCareerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Points: $newCareerTotal -----<br>";
                }
                
                // echo "DEBUG: $name Points - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
                break;
                
                case ('fgm3'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                $newSeasonTotal = $seasonTotal + $value;
                if (($seasonTotal / $sb_milestones['sea_fgm3']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_fgm3']){
                if ($newSeasonTotal > $sb_milestones['sea_fgm3'])
                echo "<h4>***** Season Milestone Achieved for $name in 3-Point Field Goals: $newSeasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in 3-Point Field Goals: $newSeasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                $newCareerTotal = $careerTotal + $value;
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_fgm3']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_fgm3'])){
                if ($newCareerTotal > $sb_milestones['car_fgm3'])
                echo "<h4>***** Career Milestone Achieved for $name in 3-Point Field Goals: $newCareerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in 3-Point Field Goals: $newCareerTotal -----<br>";
                }
                // echo "DEBUG: $name 3-Point Field Goals - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
                break;
                
                case ('treb'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                $newSeasonTotal = $seasonTotal + $value;
                if (($seasonTotal / $sb_milestones['sea_treb']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_treb']){
                if ($newSeasonTotal > $sb_milestones['sea_treb'])
                echo "<h4>***** Season Milestone Achieved for $name in Rebounds: $newSeasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Rebounds: $newSeasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                $newCareerTotal = $careerTotal + $value;
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_treb']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_treb'])){
                if ($newCareerTotal > $sb_milestones['car_treb'])
                echo "<h4>***** Career Milestone Achieved for $name in Rebounds: $newCareerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Rebounds: $newCareerTotal -----<br>";
                }
                // echo "DEBUG: $name Rebounds - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
                break;
                
                case ('ast'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                $newSeasonTotal = $seasonTotal + $value;
                if (($seasonTotal / $sb_milestones['sea_ast']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_ast']){
                if ($newSeasonTotal > $sb_milestones['sea_ast'])
                echo "<h4>***** Season Milestone Achieved for $name in Assists: $newSeasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Assists: $newSeasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                $newCareerTotal = $careerTotal + $value;
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_ast']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_ast'])){
                if ($newCareerTotal > $sb_milestones['car_ast'])
                echo "<h4>***** Career Milestone Achieved for $name in Assists: $newCareerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Assists: $newCareerTotal -----<br>";
                }
                // echo "DEBUG: $name Assists - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
                break;
                
                case ('stl'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                $newSeasonTotal = $seasonTotal + $value;
                if (($seasonTotal / $sb_milestones['sea_stl']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_stl']){
                if ($newSeasonTotal > $sb_milestones['sea_stl'])
                echo "<h4>***** Season Milestone Achieved for $name in Steals: $newSeasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Steals: $newSeasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                $newCareerTotal = $careerTotal + $value;
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_stl']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_stl'])){
                if ($newCareerTotal > $sb_milestones['car_stl'])
                echo "<h4>***** Career Milestone Achieved for $name in Steals: $newCareerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Steal: $newCareerTotal -----<br>";
                }
                // echo "DEBUG: $name Steals - SEA: $newSeasonTotal | CAR: $newCareerTotal<br>";
                break;
                
                case ('blk'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                $newSeasonTotal = $seasonTotal + $value;
                if (($seasonTotal / $sb_milestones['sea_blk']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_blk']){
                if ($newSeasonTotal > $sb_milestones['sea_blk'])
                echo "<h4>***** Season Milestone Achieved for $name in Steals: $newSeasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Steals: $newSeasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                $newCareerTotal = $careerTotal + $value;
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($newCareerTotal != NULL && ($careerTotal / $sb_milestones['car_blk']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_blk'])){
                if ($newCareerTotal > $sb_milestones['car_blk'])
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
                } */
            }
            $sql = "INSERT INTO `sb_boxscores_" . $table . "`(`Value`, `AthleteID`, `GameID`, `Remarks`, `Stat`, `StatType`) VALUES ('$value','$athleteID','$gameID','$remarks','$stat','$statType')";
            if (!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
            echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn) . "<br>";
        } else {
            $sql = "UPDATE `sb_boxscores_" . $table . "` SET `Value`='$value', `Remarks`='$remarks' WHERE `EntryID`='$duplicateResult'";
            if (!mysqli_query($conn, $sql)) // Only output anything if an error was generated.
            echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn) . "<br>";
            if ($name != "TEAM") {
                /*	switch ($stat){
                //Check for Season and Career Milestones
                case ('points'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                if (($seasonTotal / $sb_milestones['sea_points']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_points']){
                if ($seasonTotal > $sb_milestones['sea_points'])
                echo "<h4>***** Season Milestone Achieved for $name in Points: $seasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Points: $seasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if ($careerTotal != NULL && ($careerTotal / $sb_milestones['car_points'][0]) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_points'][0]) {
                if ($careerTotal > $sb_milestones['car_points'][0])
                echo "<h4>***** Career Milestone Achieved for $name in Points: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Points: $careerTotal -----<br>";
                }
                elseif ($careerTotal != NULL && ($careerTotal / $sb_milestones['car_points'][1]) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_points'][1]){
                if ($careerTotal > $sb_milestones['car_points'][1])
                echo "<h4>***** Career Milestone Achieved for $name in Points: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Points: $careerTotal -----<br>";
                
                }
                elseif($careerTotal != NULL && ($careerTotal / $sb_milestones['car_points'][2]) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_points'][2]){
                if ($careerTotal > $sb_milestones['car_points'][2])
                echo "<h4>***** Career Milestone Achieved for $name in Points: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Points: $careerTotal -----<br>";
                }
                
                // echo "DEBUG: $name Points - SEA: $seasonTotal | CAR: $careerTotal<br>";
                break;
                
                case ('fgm3'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                if (($seasonTotal / $sb_milestones['sea_fgm3']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_fgm3']){
                if ($seasonTotal > $sb_milestones['sea_fgm3'])
                echo "<h4>***** Season Milestone Achieved for $name in 3-Point Field Goals: $seasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in 3-Point Field Goals: $seasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_fgm3']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_fgm3'])){
                if ($careerTotal > $sb_milestones['car_fgm3'])
                echo "<h4>***** Career Milestone Achieved for $name in 3-Point Field Goals: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in 3-Point Field Goals: $careerTotal -----<br>";
                }
                // echo "DEBUG: $name 3-Point Field Goals - SEA: $seasonTotal | CAR: $careerTotal<br>";
                break;
                
                case ('treb'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                if (($seasonTotal / $sb_milestones['sea_treb']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_treb']){
                if ($seasonTotal > $sb_milestones['sea_treb'])
                echo "<h4>***** Season Milestone Achieved for $name in Rebounds: $seasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Rebounds: $seasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_treb']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_treb'])){
                if ($careerTotal > $sb_milestones['car_treb'])
                echo "<h4>***** Career Milestone Achieved for $name in Rebounds: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Rebounds: $careerTotal -----<br>";
                }
                // echo "DEBUG: $name Rebounds - SEA: $seasonTotal | CAR: $careerTotal<br>";
                break;
                
                case ('ast'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                if (($seasonTotal / $sb_milestones['sea_ast']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_ast']){
                if ($seasonTotal > $sb_milestones['sea_ast'])
                echo "<h4>***** Season Milestone Achieved for $name in Assists: $seasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Assists: $seasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_ast']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_ast'])){
                if ($careerTotal > $sb_milestones['car_ast'])
                echo "<h4>***** Career Milestone Achieved for $name in Assists: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Assists: $careerTotal -----<br>";
                }
                // echo "DEBUG: $name Assists - SEA: $seasonTotal | CAR: $careerTotal<br>";
                break;
                
                case ('stl'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                if (($seasonTotal / $sb_milestones['sea_stl']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_stl']){
                if ($seasonTotal > $sb_milestones['sea_stl'])
                echo "<h4>***** Season Milestone Achieved for $name in Steals: $seasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Steals: $seasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_stl']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_stl'])){
                if ($careerTotal > $sb_milestones['car_stl'])
                echo "<h4>***** Career Milestone Achieved for $name in Steals: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Steal: $careerTotal -----<br>";
                }
                // echo "DEBUG: $name Steals - SEA: $seasonTotal | CAR: $careerTotal<br>";
                break;
                
                case ('blk'):
                $seasonTotal = findSeasonStats($athleteID,'sb',$stat,$season,'sb_boxscores');
                if (($seasonTotal / $sb_milestones['sea_blk']) * 100 > $sb_milestones['percent'] && $seasonTotal < $sb_milestones['sea_blk']){
                if ($seasonTotal > $sb_milestones['sea_blk'])
                echo "<h4>***** Season Milestone Achieved for $name in Steals: $seasonTotal *****</h4>";
                else
                echo "----- Season Milestone Approaching for $name in Steals: $seasonTotal -----<br>";
                }
                $careerTotal = findCareerStats($athleteID,$stat,'sb_boxscores');
                //					var_dump($careerTotal);
                //var_dump ($vb_milestones['car_attack_k']);
                if (($careerTotal != NULL && ($careerTotal / $sb_milestones['car_blk']) * 100 > $sb_milestones['percent'] && $careerTotal < $sb_milestones['car_blk'])){
                if ($careerTotal > $sb_milestones['car_blk'])
                echo "<h4>***** Career Milestone Achieved for $name in Blocks: $careerTotal *****</h4>";
                else
                echo "----- Career Milestone Approaching for $name in Blocks: $careerTotal -----<br>";
                }
                // echo "DEBUG: $name Blocks - SEA: $seasonTotal | CAR: $careerTotal<br>";
                break;
                
                default:
                break;
                } */
            }
        }
    }
}

?>