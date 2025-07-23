<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', './error_log');
//global functions
$config = parse_ini_file('config.ini');
$conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $config['db_name'], $config['db_port']);

function debug_to_console($data) {
    $output = $data;
    if (is_array($output)) $output = implode(',', $output);
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

/* readOldDataBase: Will read any manual inputs from the oldrecords database.
*/
function readOldDatabase($db_name, $table, $stat, $level, $sort = "DESC", $sqlOverRide = NULL) {
    $config = parse_ini_file('config.ini');
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    if ($level == "TMS" || $level == "TMG") $sql = "SELECT * FROM `$table` WHERE `Stat`='$stat' AND `Name`='TEAM' AND `StatType`='$level' ORDER BY `Value` $sort";
    else $sql = "SELECT * FROM `$table` WHERE `Stat`='$stat' AND `Name`!='TEAM' AND `StatType`='$level' ORDER BY `Value` $sort";
	
	if ($sqlOverRide != NULL){
		$sql = $sqlOverRide;
	}
    //echo $sql;
    $result = mysqli_query($conn, $sql); // Perform Check
    //var_dump($result);
    if (mysqli_num_rows($result) > 0) { // Check successful
        return ($result);
        echo "<script>document.getElementById('Success').textContent='Query Successful.'</script>";
        echo "<script>document.getElementById('Warning').textContent=''</script>";
        echo "<script>document.getElementById('Danger').textContent=''</script>";
    } else {
        return (0);
        echo "<script>document.getElementById('Success').textContent=''</script>";
        echo "<script>document.getElementById('Warning').textContent=''</script>";
        echo "<script>document.getElementById('Danger').textContent='Query Failed.'</script>";
    }
}
/* 1.1 	readCurrentDatabase: Reads called database and pulls all records from it.
 *		$db_name: 	Sport Specific name. Format: kcmsathl_wvsu_$SPORT
 *		$table:		Table to call. Usually either "athletes, boxscores, games, or oldrecords, but could have an additional identifier after boxscores
 *		$stat:		Specific stat looking for. TBD if it's spelled out or abbreviated.
 *		$level:		Must be one of five three letter codes:
 *					CAR = Individual Career | SEA = Individual Season | GAM = Individual Game
 *					TMS = Team Season		| TMG = Team Game High
*/
function readCurrentDatabase($db_name, $table, $stat, $level, $sql_override = NULL) // Reads called database and pulls all records from it.
{
    $config = parse_ini_file('config.ini');
    $delimiter = stripos($table, '_');
    $sport = substr($table, 0, $delimiter);
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    //echo $sql_override;
    if ($sql_override == NULL) {
        if ($level == "TMG") $sql = "SELECT * FROM `$table` as b LEFT OUTER JOIN " . $sport . "_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN " . $sport . "_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='$stat' AND `Name`='TEAM' ORDER BY `Value` DESC";
        else if ($level == "GAM") $sql = "SELECT * FROM `$table` as b LEFT OUTER JOIN " . $sport . "_games AS g on b.GameID=g.GameID LEFT OUTER JOIN `NCAA_RPI` as r on g.RPI=r.RPI LEFT OUTER JOIN " . $sport . "_athletes as a on b.AthleteID=a.AthleteID WHERE `Stat`='$stat' AND `Name`!='TEAM' ORDER BY `Value` DESC";
        else if ($level == "CAR") $sql = "SELECT Name,SUM(Value) AS Value,SeasonsActive,IsActive FROM `$table` AS b LEFT OUTER JOIN " . $sport . "_athletes AS a on b.AthleteID = a.AthleteID WHERE `Stat`='$stat' AND `Name`!='TEAM' GROUP BY Name ORDER BY Value DESC";
        else if ($level == "SEA") $sql = "SELECT Name,SUM(Value) AS Value,SeasonsActive,IsActive,Season FROM `$table` AS b LEFT OUTER JOIN " . $sport . "_athletes AS a on b.AthleteID = a.AthleteID RIGHT OUTER JOIN " . $sport . "_games AS g on b.GameID = g.GameID WHERE `Stat`='$stat' AND `Name`!='TEAM' GROUP BY Name,Season ORDER BY Value DESC";
        else if ($level == "TMS") $sql = "SELECT Name,SUM(Value) AS Value,SeasonsActive,IsActive,Season FROM `$table` AS b LEFT OUTER JOIN " . $sport . "_athletes AS a on b.AthleteID = a.AthleteID RIGHT OUTER JOIN " . $sport . "_games AS g on b.GameID = g.GameID WHERE `Stat`='$stat' AND `Name`='TEAM' GROUP BY Name,Season ORDER BY Value DESC";
        else { // Unknown Code should never appear, but if it does, kill the application.
            echo "ERROR: Unknown Level Code....Exiting. Please return to the previous page and follow the page prompts.";
            die();
        }
    } else $sql = $sql_override;
    //echo $sql;
    $result = mysqli_query($conn, $sql); // Perform Check
    //var_dump($result);
    if (mysqli_num_rows($result) > 0) { // Check successful
        echo "<script>document.getElementById('Success').textContent='Query Successful.'</script>";
        echo "<script>document.getElementById('Warning').textContent=''</script>";
        echo "<script>document.getElementById('Danger').textContent=''</script>";
		return ($result);
    } else {
        
        echo "<script>document.getElementById('Success').textContent=''</script>";
        echo "<script>document.getElementById('Warning').textContent=''</script>";
        echo "<script>document.getElementById('Danger').textContent='Query Failed.'</script>";
		return (0);
    }
}
/*	1.2	checkName: Checks to see if a name has an apostrophe in it and if it does, ensure that the apostrophe gets escaped.
 *		$name: 	Name to check
*/
function checkName($name, $isTeam = FALSE, $isHTML = FALSE) {
	if ($name == "TEAM")
		return("TEAM");
    $singleQuotePosition = strpos($name, "'");
    $commaPosition = strpos($name, ",");
    if ($isHTML) {
        $singleQuotePosition = strpos($name, "''");
        if ($singleQuotePosition != NULL) $formattedName = substr_replace($name, "&#39;", $singleQuotePosition, 1);
        else $formattedName = $name;
    } else {
        if ($singleQuotePosition != NULL) $formattedName = substr_replace($name, "'", $singleQuotePosition, 0);
        else $formattedName = $name;
        if ($commaPosition == NULL && !$isTeam) {
            $explodedName = explode(" ", $formattedName);
            //var_dump($explodedName);
            $formattedName = $explodedName[1] . ", " . $explodedName[0];
        }
    }
    /*	$spacePosition = strpos($formattedName," ");
    if ($spacePosition != NULL){
    $commaSpacePosition = strpos($formattedName,", ");
    if ($commaSpacePosition != NULL){
    $explodedName = explode(", ",$formattedName);
    $formattedName = $explodedName[0].",".$explodedName[1];
    }
    else{
    $explodedName = explode(" ",$formattedName);
    $formattedName = $explodedName[1].",".$explodedName[0];
    }
    
    }
    */
    return ($formattedName);
}

/* 1.3 	printCurrentRecordsDatabase: Prints to the page a single record query and all entries for that query
 *		$resultsString:	The string to interpret. Should be a raw SQL string of data.
 *		$decimal:		If TRUE, then the decimal places will be shown. If FALSE, the decimal places will be cropped from the data.
 *		ADDED: Also compares the oldrecords database against all of the game stat entries and outputs one combined query amongst all of them.
*/
function printCurrentRecordsDatabase($newResultsString, $oldResultsString, $sport, $decimal = FALSE) {
    if ($newResultsString == NULL && $oldResultsString == NULL) {
        echo "<div style = \"color: red\"><strong>No Records Found.</strong></div>";
        return (0);
    }
    echo "<br>";
    echo "<table width='90%' style=\"border-collapse: collapse\">";
?>
		<th scope = "col" class = "dataheader" width = "10%">Rank</th>
		<th scope = "col" class = "dataheader" width = "12%">Value</th>
		<th scope = "col" class = "dataheader" width = "25%">Name</th>
		<th scope = "col" class = "dataheader" width = "17%">Date</th>
		<th scope = "col" class = "dataheader" width = "25%">Opponent</th>
		<th scope = "col" class = "dataheader" width = "21%">Remarks</th>
<?php
    $p = 0; // Previous Result
    $prevRank = 0; //Previous Result Rank
    $r = 1; // Result Number
    $old = 0; //Old Records Database Row number
    $new = 0; //New Records Database Row Number
    $oldArray = array();
    $newArray = array();
    $showResult = 1;
    while ($newRow = mysqli_fetch_assoc($newResultsString)) {
       // var_dump($newRow);
        $newArray+= array($new => $newRow);
        $new++;
    }
    if ($oldResultsString) {
        while ($oldRow = mysqli_fetch_assoc($oldResultsString)) {
            //var_dump($oldRow);
            $oldArray+= array($old => $oldRow);
            $old++;
        }
    }
    $new = 0;
    $old = 0;
    $size = sizeof($oldArray) + sizeof($newArray);
    while ($r <= $size) { // Important line, returns assoc array
        $c = 0;
        if (!isset($oldArray[$old]['Value'])) // Eliminate thrown errors for being lazy and not inputting all of the old records.
        $oldArrayValue = 0;
        else $oldArrayValue = $oldArray[$old]['Value'];
        if ($oldArrayValue > $newArray[$new]['Value']) {
            $row = $oldArray[$old];
            //var_dump($row);
            $old++;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                $prevRank = $r;
                if ($showResult > 10) break;
                else echo "<tr style=\"border-top: 1pt solid black\">";
            }
            foreach ($row as $field => $value) {
                if ($showResult > 10) return (0);
                switch ($c) {
                    case (0): // Rank
                        echo "<td>$showResult</td>";
                    break;
                    case (1): // Record Value
                        if ($decimal == 0) $value = round($value, 0);
                        elseif ($decimal == 2) $value = round($value, 1);
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                        break;
                    case (2): // AthleteID..needs interpreted to get name.
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                        break;
                    case (3): // GameID.. needs interpreted to get date and opponent.
                        $dateOfGame = date("M j, Y", strtotime($value));
                        echo "<td>$dateOfGame</td>";
                        break;
                    case (4):
                        echo "<td>$value</td>";
                        break;
                    case (5): // Remarks
                        echo "<td>$value</td>";
                        break;
                    default: // If C is not any of the values, then we have reached the end and need to end the loop.
                        continue;
                    }
                    $c++;
                }
                $r++;
                echo "</tr>";
        } 
		else {
            $row = $newArray[$new];
            $new++;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                $prevRank = $r;
                if ($showResult > 10) break;
                else echo "<tr style=\"border-top: 1pt solid black\">";
            }
            echo "<tr>";
            $c = 0; // Reset the counter for each row
			if ($row["IsActive"] == "1")
				echo "<b>";
            // Rank
            echo "<td>$showResult</td>";

            // Value
            $c++;
            if ($decimal == 0) $value = round($row["Value"], 0);
			elseif ($decimal == 1) $value = number_format($row["Value"], 3);
            elseif ($decimal == 2) $value = round($row["Value"], 1);
            echo "<td>" . htmlspecialchars($value) . "</td>";

            // Name
            $c++;
            echo "<td>" . htmlspecialchars($row["Name"]) . "</td>";

            // Date and Opponent
            $c++;
            $dateOfGame = date("M j, Y", strtotime($row["Date"]));
            echo "<td>$dateOfGame</td>";

            if ($row['Home/Away'] == "A") $opponent = "at " . $row['Opponent'];
            elseif ($row['Home/Away'] == "N") $opponent = "vs. " . $row['Opponent'];
            else $opponent = $row['Opponent'];
            echo "<td>$opponent</td>";

            // Remarks
            $c++;
            echo "<td>" . htmlspecialchars($row["Remarks"]) . "</td>";
			
			if ($row["IsActive"] == "1")
				echo "</b>";
            echo "</tr>";
            $r++;
        }
    }
    echo "</table>";
}

/* 1.4 	editCurrentDatabase: Prints to the page a single record query and allows editing of that query.
 *		$resultsString:	The string to interpret. Should be a raw SQL string of data.
 *
*/
function editCurrentDatabase($resultsString, $isOldRecords = FALSE) // Prints Administration page to edit the records
{
    echo "<br>";
    echo "<table width='75%' style=\"border-collapse: collapse\"><tr>";
?>
		<th scope = "col" class = "dataheader" width = "5%">Record Number<br>(Not Visible)</th>
		<th scope = "col" class = "dataheader" width = "8%">Value</th>
		<th scope = "col" class = "dataheader" width = "25%">Name</th>
		<th scope = "col" class = "dataheader" width = "17%">Date</th>
		<th scope = "col" class = "dataheader" width = "30%">Opponent</th>
		<th scope = "col" class = "dataheader" width = "10%">Remarks</th>
		<th scope = "col" class = "dataheader" width = "5%">Delete</th>
		<form action = "create_form.php" method="post">
		<input type="hidden" name = "sport" value = <?php echo $_GET['SPORT'] ?>>
		<input type="hidden" name = "stat" value = <?php echo $_GET['STAT'] ?>>
		<input type="hidden" name="oldRecords" value="<?php if ($isOldRecords == TRUE) echo "TRUE";
    else echo "FALSE" ?>">
			<input type="hidden" name="statType" value="<?php echo $_GET['LEVEL'] ?>"</tr>
<?php
    $p = 0; // Previous Result
    $r = 1; // Result Number
    $f = 0; //Final Row Number
    $showResult = 1;
    if ($resultsString != NULL && mysqli_num_rows($resultsString) > 0) {
        while ($row = mysqli_fetch_assoc($resultsString)) { // Important line, returns assoc array
            $c = 0;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                echo "<tr style=\"border-top: 1pt solid black\">";
            }
            foreach ($row as $field => $value) {
                //echo "$field = $value";
                $$field = $value;
            }
?>
			<td valign="middle"><input type = "text" value="<?php echo $RecordID ?>" disabled>
          	<input type ="hidden" name = "<?php echo $r ?>[RecordID]" value = "<?php echo $RecordID ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Value]" value = "<?php echo $Value ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Name]" value = "<?php echo $Name ?>"></td>
			<td valign="middle"><input type = "date" name = "<?php echo $r ?>[Date]" value = "<?php echo $Date ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Opponent]" value = "<?php echo $Opponent ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Remarks]" value = "<?php echo $Remarks ?>"></td>
			<td valign="middle"><input type = "checkbox" name = "<?php echo $r ?>[Erase]" value = 'TRUE' >
		
<?php
            $r++;
            $f = $r;
            echo "</tr>";
        } //end assoc array
        
    }
    $f++;
    for ($e = $r;$e < $f + 10;$e++):
?>
      <tr>
          <td valign="middle"><input type = "text" value="NEW" disabled>
		  <input type ="hidden" name = "<?php echo $e ?>[RecordID]"></td>
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Value]"></td>
		  <?php
        if ($_GET['LEVEL'] == "TMG" || $_GET['LEVEL'] == "TMS") {
            echo "<td valign=\"middle\"><input type = \"text\" value=\"TEAM\" disabled>";
?>
		  		<input type = "hidden" value="TEAM" name="<?php echo $e ?>[Name]"></td><?php
        } else {
?><td valign="middle"><input type = "text" name="<?php echo $e ?>[Name]"></td>
			<?php
        }
?>
          
          <td valign="middle"><input type = "date" name = "<?php echo $e ?>[Date]"></td>
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Opponent]" maxlength="30"></td>
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Remarks]" maxlength="30"></td>
		  <td valign="middle"><input type = "checkbox" name = "<?php echo $e ?>[Erase]" value = 'TRUE'  disabled>
			  <input type = "hidden" name = "<?php echo $e ?>[NewRecord]" value = 'TRUE'> </td>
			  
			</tr>
      <?php
    endfor;
?>
<tr>
	<td valign = "middle" colspan ="5">Paste Formatted Statcrew String Here:<br><textarea name = "PastedString" rows="5" cols="100"></textarea></td>
</tr>
<?php
    echo "</table>";
    echo "<input type=\"submit\" name = \"submit\" value=\"Submit Changes\">";
    echo "</form>";
}
/* 1.5 locatePlayer: Finds a player in their respective sport's database and if found, returns the athlete ID from the database.
 *					If not, it creates the player then returns the ID number created.
*/
function locatePlayer($playername, $sport, $season) {
    $db_name = "kcmsathl_wvsu";
    $config = parse_ini_file('config.ini');
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $sql = "SELECT * FROM `" . $sport . "_athletes` WHERE `Name`=$playername";
    $results = mysqli_query($conn, $sql);
    //echo "Checking for Duplicate Record for $name and $stat.<br>";
    $result = mysqli_fetch_assoc($results);
    if (mysqli_num_rows($results) > 0) return ($result['AthleteID']); // Duplicate found.
    else {
        $sql = "INSERT INTO `" . $sport . "_athletes`(`Name`,`SeasonsActive`,`IsActive`) VALUES ('$playername','$season','1')";
        $results = mysqli_query($conn, $sql);
        $result = mysqli_fetch_assoc($results);
        return ($result['AthleteID']);
    }
}
function createNewGame($date, $time, $ha, $opp, $result, $score, $attend, $site, $season, $sport) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_wvsu";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $dup_check = checkDuplicateGames($sport, $date, $opp, $time);
    if ($dup_check == - 1) {
        $query = "INSERT INTO `" . $sport . "_games`(`Date`, `Time`, `Home/Away`,`RPI`, `W/L`, `Score`, `Attendance`, `Site`, `Season`) VALUES ('$date','$time','$ha','$opp','$result','$score','$attend','$site','$season')";
        if (mysqli_query($conn, $query)) {
            echo "Game successfully created.";
            $dup_check = checkDuplicateGames($sport, $date, $opp, $time);
        } else {
            echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn) . "<br>";
        }
    } else {
        $query = "UPDATE `" . $sport . "_games` SET `Date`='$date',`Time`='$time',`Home/Away`='$ha',`RPI`='$opp',`W/L`='$result',`Score`='$score',`Attendance`='$attend',`Site`='$site',`Season`='$season' WHERE `GameID`='$dup_check'";
        if (mysqli_query($conn, $query)) {
            echo "GameID $dup_check successfully updated.";
        } else {
            echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn) . "<br>";
        }
    }
    // echo $query;
    return ($dup_check);
}
function checkDuplicateGames($sport, $date, $opponent, $time) {
    $db_name = "kcmsathl_wvsu";
    $config = parse_ini_file('config.ini');
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $sql = "SELECT * FROM `" . $sport . "_games` WHERE `RPI`='$opponent' AND `Time`='$time' AND `Date`='$date' ORDER BY `GameID` DESC";
    $results = mysqli_query($conn, $sql);
    //echo "Checking for Duplicate Record for $name and $stat.<br>";
    $result = mysqli_fetch_assoc($results);
    if (mysqli_num_rows($results) > 0) return ($result['GameID']); // Duplicate found.
    else return (-1); // No record found.
    
}
function outputRecordsCode($newResultsString, $oldResultsString, $sport, $level, $decimal = 0) {
    $r = 1;
    $p = 0;
    $prev = 0;
    if ($level == "GAM" || $level == "TMG") $finalCode = "<table style = \"border-spacing: 10px\"><tr><th>Rank</th><th>Date</th><th>Name</th><th>Value</th><th>Opponent</th></tr>";
    else if ($level == "SEA" || $level == "TMS") $finalCode = "<table style = \"border-spacing: 10px\"><tr><th>Rank</th><th>Name</th><th>Value</th><th>Season</th></tr>";
    else if ($level == "CAR") $finalCode = "<table style = \"border-spacing: 10px\"><tr><th>Rank</th><th>Name</th><th>Value</th><th>Seasons Active</th></tr>";
    $p = 0; // Previous Result
    $prevRank = 0; //Previous Result Rank
    $r = 1; // Result Number
    $old = 0; //Old Records Database Row number
    $new = 0; //New Records Database Row Number
    $oldArray = array();
    $newArray = array();
    $showResult = 1;
    //var_dump($newResultsString);
    if ($newResultsString) {
        while ($newRow = mysqli_fetch_assoc($newResultsString)) {
            //var_dump($newRow);
            $newArray+= array($new => $newRow);
            $new++;
        }
    }
    if ($oldResultsString) {
        while ($oldRow = mysqli_fetch_assoc($oldResultsString)) {
            //var_dump($oldRow);
            $oldArray+= array($old => $oldRow);
            $old++;
        }
    }
    $new = 0;
    $old = 0;
    $size = sizeof($oldArray) + sizeof($newArray);
    //var_dump($newArray);
    //	echo "<br>";
    while ($r <= $size) {
        $c = 0;
        if (!isset($oldArray[$old]['Value'])) // Eliminate thrown errors for being lazy and not inputting all of the old records.
        $oldArrayValue = 0;
        else $oldArrayValue = $oldArray[$old]['Value'];
		if (!isset($newArray[$new]['Value'])) // Eliminate thrown errors for being lazy and not inputting all of the old records.
        $newArrayValue = 0;
        else $newArrayValue = $newArray[$new]['Value'];
        if ($oldArrayValue > $newArrayValue) {
            $row = $oldArray[$old];
            $old++;
        } else {
            $row = $newArray[$new];
            $new++;
        }
        //var_dump($row);
        //echo "<br><br><br>";
        if ($row["Value"] == $p) {
            $showResult = "";
            //echo "<tr>";
            
        } else {
            $p = $row["Value"];
            $showResult = $r;
            //echo $showResult;
            $prevRank = $r;
        }
        if ($showResult > 10) break;
        else $rowCode = "<tr style=\"border-top: 1pt solid black\">";
        //echo "<br>Row Number: $r<br>";
        $formattedName = $row['Name'];
        if ($level == "GAM" || $level == "TMG") {
            $formattedDate = date("M j, Y", strtotime($row['Date']));
            $rowCode = "<tr><td>$showResult</td><td>$formattedDate</td><td>" . $formattedName . "</td><td>";
            if (array_key_exists('Home/Away', $row)) {
                if ($decimal == 1) {
                    if ($row['Home/Away'] == "A") $rowCode = $rowCode . $row['Value'] . "</td><td>at " . $row['Opponent'] . "</td></tr>";
                    else $rowCode = $rowCode . $row['Value'] . "</td><td>" . $row['Opponent'] . "</td></tr>";
                } elseif ($decimal == 2) {
                    $Value = round($row['Value'], 1);
                    if ($row['Home/Away'] == "A") $rowCode = $rowCode . $Value . "</td><td>at " . $row['Opponent'] . "</td></tr>";
                    else $rowCode = $rowCode . $Value . "</td><td>" . $row['Opponent'] . "</td></tr>";
                } else {
                    $Value = round($row['Value'], 0);
                    if ($row['Home/Away'] == "A") $rowCode = $rowCode . $Value . "</td><td>at " . $row['Opponent'] . "</td></tr>";
                    else $rowCode = $rowCode . $Value . "</td><td>" . $row['Opponent'] . "</td></tr>";
                }
            } else {
				if ($decimal == 0) $Value = round($row['Value'], 0);
				elseif ($decimal == 2) $Value = round($row['Value'], 1);
				else $Value = $row['Value'];
                $rowCode = $rowCode . $Value . "</td><td>" . $row['Opponent'] . "</td></tr>";
            }
        } 
		else if ($level == "TMS") {
            $rowCode = "<tr><td>$showResult</td><td>TEAM</td><td>";
            if ($decimal == 1) $rowCode = $rowCode . $row['Value'] . "</td><td>" . $row['Season'] . "</td></tr>";
            elseif ($decimal == 2) {
                $Value = round($row['Value'], 1);
                $rowCode = $rowCode . $row['Value'] . "</td><td>" . $row['Season'] . "</td></tr>";
            } else {
                $Value = round($row['Value'], 0);
                $rowCode = $rowCode . $Value . "</td><td>" . $row['Season'] . "</td></tr>";
            }
        } 
		else if ($level == "SEA") {
			//var_dump($row);
			if ($row['SeasonsActive'] != NULL){
				$row['Season'] = $row['Season']."-".$row['Season']+1;
			}
            $rowCode = "<tr><td>$showResult</td><td>" . $formattedName . "</td><td>";
            if ($decimal == 2) {
                $Value = round($row['Value'], 1);
                $rowCode = $rowCode . $row['Value'] . "</td><td>" . $row['Season'] . "</td></tr>";
            } elseif ($decimal == 0) {
                $Value = round($row['Value'], 0);
                $rowCode = $rowCode . $Value . "</td><td>" . $row['Season'] . "</td></tr>";
            } else $rowCode = $rowCode . $row['Value'] . "</td><td>" . $row['Season'] . "</td></tr>";
        } 
		else if ($level == "CAR") {
			if (array_key_exists('IsActive',$row) && $row['IsActive']){
				$rowCode = "<tr><td><strong>$showResult</strong></td><td><strong>" . $formattedName . "</strong></td><td>";
				if ($decimal == 1) $rowCode = $rowCode ."<strong>". $row['Value'] . "</strong></td><td><strong>" . $row['SeasonsActive'] . "</strong></td></tr>";
				elseif ($decimal == 2) {
					$Value = round($row['Value'], 1);
					$rowCode = $rowCode ."<strong>". $row['Value'] . "</strong></td><td><strong>" . $row['SeasonsActive'] . "</strong></td></tr>";
				} else {
					$Value = round($row['Value'], 0);
					$rowCode = $rowCode ."<strong>". $Value . "</strong></td><td><strong>" . $row['SeasonsActive'] . "</strong></td></tr>";
				}
			}
			else{
				$rowCode = "<tr><td>$showResult</td><td>" . $formattedName . "</td><td>";
				if ($decimal == 1) $rowCode = $rowCode . $row['Value'] . "</td><td>" . $row['SeasonsActive'] . "</td></tr>";
				elseif ($decimal == 2) {
					$Value = round($row['Value'], 1);
					$rowCode = $rowCode . $row['Value'] . "</td><td>" . $row['SeasonsActive'] . "</td></tr>";
				} else {
					$Value = round($row['Value'], 0);
					$rowCode = $rowCode . $Value . "</td><td>" . $row['SeasonsActive'] . "</td></tr>";
				}
			}
        }
        //echo $finalCode;
        $finalCode = $finalCode . $rowCode;
        $r++;
    }
    $finalCode = $finalCode . "</table>";
    return ($finalCode);
}
function createNewRecord($value, $name, $date = NULL, $opp = NULL, $remarks = NULL, $sport, $stat, $table, $statType, $SeasonsActive = NULL, $Season = NULL) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    if ($statType == "CAR") {
        $query = "INSERT INTO `" . $sport . "_" . $table . "`(`Value`, `Name`, `Remarks`, `Stat`,`StatType`,`SeasonsActive`) VALUES ('$value','$name','$remarks','$stat','$statType','$SeasonsActive')";
    } elseif ($statType == "SEA" || $statType == "TMS") {
        $query = "INSERT INTO `" . $sport . "_" . $table . "`(`Value`, `Name`, `Remarks`,`Stat`,`StatType`,`Season`) VALUES ('$value','$name','$remarks','$stat','$statType','$Season')";
    } else $query = "INSERT INTO `" . $sport . "_" . $table . "`(`Value`, `Name`, `Date`, `Opponent`, `Remarks`, `Stat`,`StatType`) VALUES ('$value','$name','$date','$opp','$remarks','$stat','$statType')";
    echo "<br><br>QUERY: $query<br><br>";
    if (mysqli_query($conn, $query)) {
        echo "*****Record successfully created for $name.<br>";
    } else {
        echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn) . "<br>";
    }
    //echo $query;
    
}
function updateRecord($recordNumber, $value, $name, $date = NULL, $opp = NULL, $remarks = NULL, $sport, $stat, $table, $statType, $SeasonsActive = NULL, $Season = NULL) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    if ($statType == "CAR") {
        $query = "UPDATE `" . $sport . "_" . $table . "` SET `Value`='$value',`Name`='$name',`Remarks`='$remarks',`Stat`='$stat',`StatType`='$statType',`SeasonsActive`='$SeasonsActive' WHERE `RecordID`='$recordNumber'";
    } elseif ($statType == "SEA" || $statType == "TMS") {
        $query = "UPDATE `" . $sport . "_" . $table . "` SET `Value`='$value',`Name`='$name',`Remarks`='$remarks',`Stat`='$stat',`StatType`='$statType',`Season`='$Season' WHERE `RecordID`='$recordNumber'";
    } else $query = "UPDATE `" . $sport . "_" . $table . "` SET `Value`='$value',`Name`='$name',`Date`='$date',`Opponent`='$opp',`Remarks`='$remarks',`Stat`='$stat',`StatType`='$statType' WHERE `RecordID`='$recordNumber'";
    if (mysqli_query($conn, $query)) {
        echo "*****Record # $recordNumber successfully updated for $name.<br>";
    } else {
        echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn) . "<br>";
    }
    //echo $query;
    
}
//	getGame: Returns all relevant data for a gameNumber.
function getGame($gameNumber, $sport) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $query = "SELECT * FROM `" . $sport . "_games` as game JOIN `NCAA_RPI` as rpi on game.RPI=rpi.RPI WHERE `GameID` = '$gameNumber'";
    $results = mysqli_query($conn, $query);
    return (mysqli_fetch_assoc($results));
}
//	getAthlete: Returns all relevant data for an athleteID.
function getAthlete($athleteID, $sport) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $query = "SELECT * FROM `" . $sport . "_athletes` WHERE `AthleteID` = '$athleteID'";
    $results = mysqli_query($conn, $query);
    return (mysqli_fetch_assoc($results));
}
function createAthlete($name, $sport, $season) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $sql = "INSERT INTO `" . $sport . "_athletes`(`Name`, `SeasonsActive`, `IsActive`) VALUES ('$name','$season','1')";
    if (mysqli_query($conn, $sql)) {
        echo "*****Record successfully created for $name.<br>";
    } else {
        echo "Record was not created. - Error: " . $query . "<br>" . mysqli_error($conn) . "<br>";
    }
    $athlQuery = "SELECT * FROM `" . $sport . "_athletes` WHERE `Name`='$name' AND `IsActive`='1'";
    $athlResults = mysqli_query($conn, $athlQuery);
    $finalAthlResult = mysqli_fetch_assoc($athlResults);
    if (mysqli_num_rows($athlResults) > 0) $athleteID = $finalAthlResult['AthleteID'];
    return ($athleteID);
}
function findCareerStats($athlete, $stat, $table) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $query = "SELECT SUM(Value) AS TotalStats FROM `$table` WHERE `AthleteID`='$athlete' AND `Stat`='$stat'";
    $queryResults = mysqli_query($conn, $query);
    if (mysqli_num_rows($queryResults) > 0) {
        $finalQueryResult = mysqli_fetch_assoc($queryResults);
        return ($finalQueryResult['TotalStats']);
    } else return (0);
}
function findSeasonStats($athlete, $sport, $stat, $season, $table) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $query = "SELECT SUM(Value) AS TotalStats FROM `$table` AS b LEFT OUTER JOIN " . $sport . "_games AS g ON b.GameID = g.GameID WHERE `AthleteID`='$athlete' AND `Stat`='$stat' AND `Season`='$season'";
    //.echo $query;
    $queryResults = mysqli_query($conn, $query);
    if (mysqli_num_rows($queryResults) > 0) {
        $finalQueryResult = mysqli_fetch_assoc($queryResults);
        return ($finalQueryResult['TotalStats']);
    } else return (0);
}
function printCareerDatabase($newResultsString, $oldResultsString, $sport, $decimal = FALSE) {
    if ($newResultsString == NULL && $oldResultsString == NULL) {
        echo "<div style = \"color: red\"><strong>No Records Found.</strong></div>";
        return (0);
    }
    echo "<br>";
    echo "<table width='75%' style=\"border-collapse: collapse\">";
?>
		<th scope = "col" class = "dataheader" width = "10%">Rank</th>
		<th scope = "col" class = "dataheader" width = "12%">Value</th>
		<th scope = "col" class = "dataheader" width = "25%">Name</th>
		<th scope = "col" class = "dataheader" width = "25%">Seasons Active</th>
		<th scope = "col" class = "dataheader" width = "21%">Remarks</th>
<?php
    $p = 0; // Previous Result
    $prevRank = 0; //Previous Result Rank
    $r = 1; // Result Number
    $old = 0; //Old Records Database Row number
    $new = 0; //New Records Database Row Number
    $oldArray = array();
    $newArray = array();
    $showResult = 1;
    while ($newRow = mysqli_fetch_assoc($newResultsString)) {
        //var_dump($newRow);
        $newArray+= array($new => $newRow);
        $new++;
    }
    if ($oldResultsString) {
        while ($oldRow = mysqli_fetch_assoc($oldResultsString)) {
            //var_dump($oldRow);
            $oldArray+= array($old => $oldRow);
            $old++;
        }
    }
    $new = 0;
    $old = 0;
    $size = sizeof($oldArray) + sizeof($newArray);
    if ($size > 10) $maxSize = 10;
    else $maxSize = $size;
    while ($r <= $maxSize) { // Important line, returns assoc array
        $c = 0;
        if (!isset($oldArray[$old]['Value'])) // Eliminate thrown errors for being lazy and not inputting all of the old records.
        $oldArrayValue = 0;
        else $oldArrayValue = $oldArray[$old]['Value'];
        if ($oldArrayValue > $newArray[$new]['Value']) {
            $row = $oldArray[$old];
            $old++;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                $prevRank = $r;
                echo "<tr style=\"border-top: 1pt solid black\">";
            }
            //var_dump($row);
            echo "<td>$showResult</td>";
            $value = $row['Value'];
			if ($decimal == 0) $value = round($value, 0);
            elseif ($decimal == 2) $value = round($value, 1);
            echo "<td>$value</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['SeasonsActive'] . "</td>";
            echo "<td>" . $row['Remarks'] . "</td>";
            $r++;
            echo "</tr>";
        } else {
            $row = $newArray[$new];
            $new++;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                $prevRank = $r;
                echo "<tr style=\"border-top: 1pt solid black\">";
            }
            if ($row['IsActive']) {
                echo "<td><strong>$showResult</strong></td>";
                $value = $row['Value'];
                if ($decimal == 0) $value = round($value, 0);
            	elseif ($decimal == 2) $value = round($value, 1);
                echo "<td><strong>$value</strong></td>";
                echo "<td><strong>" . $row['Name'] . "</strong></td>";
                echo "<td><strong>" . $row['SeasonsActive'] . "</strong></td>";
                //echo "<td><strong>".$row['Remarks']."</strong></td>";
                
            } else {
                echo "<td>$showResult</td>";
                $value = $row['Value'];
                if ($decimal == 0) $value = round($value, 0);
            	elseif ($decimal == 2) $value = round($value, 1);
                echo "<td>$value</td>";
                echo "<td>" . $row['Name'] . "</td>";
                echo "<td>" . $row['SeasonsActive'] . "</td>";
                //echo "<td>".$row['Remarks']."</td>";
                
            }
            $r++;
            echo "</tr>";
        }
    }
    echo "</table>";
}
function editCareerDatabase($resultsString, $isOldRecords = FALSE) // Prints Administration page to edit the records
{
    echo "<br>";
    echo "<table width='75%' style=\"border-collapse: collapse\"><tr>";
?>
		<th scope = "col" class = "dataheader" width = "5%">Record Number<br>(Not Visible)</th>
		<th scope = "col" class = "dataheader" width = "8%">Value</th>
		<th scope = "col" class = "dataheader" width = "25%">Name</th>
		<th scope = "col" class = "dataheader" width = "17%">Seasons Active</th>
		<th scope = "col" class = "dataheader" width = "10%">Remarks</th>
		<th scope = "col" class = "dataheader" width = "5%">Delete</th>
		<th scope = "col" class = "dataheader" width = "30%"></th>
		<form action = "create_form.php" method="post">
		<input type="hidden" name = "sport" value = <?php echo $_GET['SPORT'] ?>>
		<input type="hidden" name = "stat" value = <?php echo $_GET['STAT'] ?>>
		<input type="hidden" name="oldRecords" value="<?php if ($isOldRecords == TRUE) echo "TRUE";
    else echo "FALSE" ?>">
			<input type="hidden" name="statType" value="<?php echo $_GET['LEVEL'] ?>"</tr>
<?php
    $p = 0; // Previous Result
    $r = 1; // Result Number
    $f = 0; //Final Row Number
    $showResult = 1;
    if ($resultsString != NULL && mysqli_num_rows($resultsString) > 0) {
        while ($row = mysqli_fetch_assoc($resultsString)) { // Important line, returns assoc array
            $c = 0;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                echo "<tr style=\"border-top: 1pt solid black\">";
            }
            foreach ($row as $field => $value) {
                //echo "$field = $value";
                $$field = $value;
            }
?>
			<td valign="middle"><input type = "text" value="<?php echo $RecordID ?>" disabled>
          	<input type ="hidden" name = "<?php echo $r ?>[RecordID]" value = "<?php echo $RecordID ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Value]" value = "<?php echo $Value ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Name]" value = "<?php echo $Name ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[SeasonsActive]" value = "<?php echo $SeasonsActive ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Remarks]" value = "<?php echo $Remarks ?>"></td>
			<td valign="middle"><input type = "checkbox" name = "<?php echo $r ?>[Erase]" value = 'TRUE' >
		
<?php
            $r++;
            $f = $r;
            echo "</tr>";
        } //end assoc array
        
    }
    $f++;
    for ($e = $r;$e < $f + 10;$e++):
?>
      <tr>
          <td valign="middle"><input type = "text" value="NEW" disabled>
		  <input type ="hidden" name = "<?php echo $e ?>[RecordID]"></td>
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Value]"></td>
		  <?php
        if ($_GET['LEVEL'] == "TMG" || $_GET['LEVEL'] == "TMS") {
            echo "<td valign=\"middle\"><input type = \"text\" value=\"TEAM\" disabled>";
?>
		  		<input type = "hidden" value="TEAM" name="<?php echo $e ?>[Name]"></td><?php
        } else {
?><td valign="middle"><input type = "text" name="<?php echo $e ?>[Name]"></td>
			<?php
        }
?>
          
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[SeasonsActive]"></td>
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Remarks]" maxlength="30"></td>
		  <td valign="middle"><input type = "checkbox" name = "<?php echo $e ?>[Erase]" value = 'TRUE'  disabled>
			  <input type = "hidden" name = "<?php echo $e ?>[NewRecord]" value = 'TRUE'> </td>
			  
      </tr>
      <?php
    endfor;
    echo "</table>";
    echo "<input type=\"submit\" name = \"submit\" value=\"Submit Changes\">";
    echo "</form>";
}
function editSeasonDatabase($resultsString, $isOldRecords = FALSE) // Prints Administration page to edit the records
{
    echo "<br>";
    echo "<table width='75%' style=\"border-collapse: collapse\"><tr>";
?>
		<th scope = "col" class = "dataheader" width = "5%">Record Number<br>(Not Visible)</th>
		<th scope = "col" class = "dataheader" width = "8%">Value</th>
		<th scope = "col" class = "dataheader" width = "25%">Name</th>
		<th scope = "col" class = "dataheader" width = "17%">Season</th>
		<th scope = "col" class = "dataheader" width = "10%">Remarks</th>
		<th scope = "col" class = "dataheader" width = "5%">Delete</th>
		<th scope = "col" class = "dataheader" width = "30%"></th>
		<form action = "create_form.php" method="post">
		<input type="hidden" name = "sport" value = <?php echo $_GET['SPORT'] ?>>
		<input type="hidden" name = "stat" value = <?php echo $_GET['STAT'] ?>>
		<input type="hidden" name="oldRecords" value="<?php if ($isOldRecords == TRUE) echo "TRUE";
    else echo "FALSE" ?>">
			<input type="hidden" name="statType" value="<?php echo $_GET['LEVEL'] ?>"</tr>
<?php
    $p = 0; // Previous Result
    $r = 1; // Result Number
    $f = 0; //Final Row Number
    $showResult = 1;
    if ($resultsString != NULL && mysqli_num_rows($resultsString) > 0) {
        while ($row = mysqli_fetch_assoc($resultsString)) { // Important line, returns assoc array
            $c = 0;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                echo "<tr style=\"border-top: 1pt solid black\">";
            }
            foreach ($row as $field => $value) {
                //echo "$field = $value";
                $$field = $value;
            }
?>
			<td valign="middle"><input type = "text" value="<?php echo $RecordID ?>" disabled>
          	<input type ="hidden" name = "<?php echo $r ?>[RecordID]" value = "<?php echo $RecordID ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Value]" value = "<?php echo $Value ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Name]" value = "<?php echo $Name ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Season]" value = "<?php echo $Season ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Remarks]" value = "<?php echo $Remarks ?>"></td>
			<td valign="middle"><input type = "checkbox" name = "<?php echo $r ?>[Erase]" value = 'TRUE' >
		
<?php
            $r++;
            $f = $r;
            echo "</tr>";
        } //end assoc array
        
    }
    $f++;
    for ($e = $r;$e < $f + 10;$e++):
?>
      <tr>
          <td valign="middle"><input type = "text" value="NEW" disabled>
		  <input type ="hidden" name = "<?php echo $e ?>[RecordID]"></td>
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Value]"></td>
		  <?php
        if ($_GET['LEVEL'] == "TMG" || $_GET['LEVEL'] == "TMS") {
            echo "<td valign=\"middle\"><input type = \"text\" value=\"TEAM\" disabled>";
?>
		  		<input type = "hidden" value="TEAM" name="<?php echo $e ?>[Name]"></td><?php
        } else {
?><td valign="middle"><input type = "text" name="<?php echo $e ?>[Name]"></td>
			<?php
        }
?>
          
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Season]"></td>
          <td valign="middle"><input type = "text" name = "<?php echo $e ?>[Remarks]" maxlength="30"></td>
		  <td valign="middle"><input type = "checkbox" name = "<?php echo $e ?>[Erase]" value = 'TRUE'  disabled>
			  <input type = "hidden" name = "<?php echo $e ?>[NewRecord]" value = 'TRUE'> </td>
			  
      </tr>
      <?php
    endfor;
    echo "</table>";
    echo "<input type=\"submit\" name = \"submit\" value=\"Submit Changes\">";
    echo "</form>";
}
function printSeasonDatabase($newResultsString, $oldResultsString, $sport, $decimal = FALSE, $sort = "DESC") {
    if ($newResultsString == NULL && $oldResultsString == NULL) {
        echo "<div style = \"color: red\"><strong>No Records Found.</strong></div>";
        return (0);
    }
    echo "<br>";
    echo "<table width='75%' style=\"border-collapse: collapse\">";
?>
		<th scope = "col" class = "dataheader" width = "10%">Rank</th>
		<th scope = "col" class = "dataheader" width = "12%">Value</th>
		<th scope = "col" class = "dataheader" width = "25%">Name</th>
		<th scope = "col" class = "dataheader" width = "25%">Season</th>
		<th scope = "col" class = "dataheader" width = "21%">Remarks</th>
<?php
    $p = 0; // Previous Result
    $prevRank = 0; //Previous Result Rank
    $r = 1; // Result Number
    $old = 0; //Old Records Database Row number
    $new = 0; //New Records Database Row Number
    $oldArray = array();
    $newArray = array();
    $showResult = 1;
    while ($newRow = mysqli_fetch_assoc($newResultsString)) {
        //var_dump($newRow);
        $newArray+= array($new => $newRow);
        $new++;
    }
    if ($oldResultsString) {
        while ($oldRow = mysqli_fetch_assoc($oldResultsString)) {
            //var_dump($oldRow);
            $oldArray+= array($old => $oldRow);
            $old++;
        }
    }
    $new = 0;
    $old = 0;
    $size = sizeof($oldArray) + sizeof($newArray);
    while ($r <= $size) {
        $c = 0;
        //var_dump($row);
		if (!isset($newArray[$new]['Value'])) // Eliminate thrown errors for being lazy and not inputting all of the old records.
        	$newArrayValue = NULL;
		else $newArrayValue = $newArray[$new]['Value'];
		//echo "New Array Comparison: $newArrayValue | ";
        if (!isset($oldArray[$old]['Value'])) // Eliminate thrown errors for being lazy and not inputting all of the old records.
        	$oldArrayValue = NULL;
        else $oldArrayValue = $oldArray[$old]['Value'];
		//echo "Old Array Comparison: $oldArrayValue<br>";
        if (($oldArrayValue > $newArrayValue && $sort == "DESC") || (($oldArrayValue < $newArrayValue && $oldArrayValue != NULL) && $sort == "ASC")) { // If Old Value is Higher than New Value
            $row = $oldArray[$old];
            $old++;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                $prevRank = $r;
                echo "<tr style=\"border-top: 1pt solid black\">";
                if ($showResult > 10) break;
            }
            //var_dump($row);
            echo "<td>$showResult</td>";
            $value = $row['Value'];
            if ($decimal == 0) $value = round($value, 0);
            elseif ($decimal == 2) $value = round($value, 1);
            echo "<td>$value</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['Season'] . "</td>";
            echo "<td>" . $row['Remarks'] . "</td>";
            $r++;
            echo "</tr>";
        } else { // If New Value is Higher Than Old Value
            $row = $newArray[$new];
            $new++;
            if ($row["Value"] == $p) {
                $showResult = "";
                echo "<tr>";
            } else {
                $p = $row["Value"];
                $showResult = $r;
                $prevRank = $r;
                echo "<tr style=\"border-top: 1pt solid black\">";
                //var_dump($row);
                if ($showResult > 10) break;
            }
            if ($row['Name'] == "TEAM") {
                echo "<td>$showResult</td>";
                $value = $row['Value'];
                if ($decimal == 0) $value = round($value, 0);
                elseif ($decimal == 2) $value = round($value, 1);
                echo "<td>$value</td>";
                echo "<td>TEAM</td>";
				if ($sport == "mbb" || $sport == "wbb") {
					$currYear = $row['Season'];
					echo "<td>" . $currYear . "-" . ($currYear + 1) . "</td>";
				}
				else
                	echo "<td>" . $row['Season'] . "</td>";
            } else {
                if ($row['IsActive']) {
                    echo "<td><strong>$showResult</strong></td>";
                    $value = $row['Value'];
                    if ($decimal == 0) $value = round($value, 0);
                    elseif ($decimal == 2) $value = round($value, 1);
                    echo "<td><strong>$value</strong></td>";
                    echo "<td><strong>" . $row['Name'] . "</strong></td>";
                    if ($sport == "mbb" || $sport == "wbb") {
                        $currYear = $row['Season'];
                        echo "<td><strong>" . $currYear . "-" . ($currYear + 1) . "</strong></td>";
                    } else echo "<td><strong>" . $row['Season'] . "</strong></td>";
                    //echo "<td><strong>".$row['Remarks']."</strong></td>";
                    
                } else {
                    echo "<td>$showResult</td>";
                    $value = $row['Value'];
                    if ($decimal == 0) $value = round($value, 0);
                    elseif ($decimal == 2) $value = round($value, 1);
                    echo "<td>$value</td>";
                    echo "<td>" . $row['Name'] . "</td>";
                    if ($sport == "mbb" || $sport == "wbb") {
                        $currYear = $row['Season'];
                        echo "<td>" . $currYear . "-" . ($currYear + 1) . "</td>";
                    } else echo "<td>" . $row['Season'] . "</td>";
                    //echo "<td>".$row['Remarks']."</td>";
                    
                }
            }
            $r++;
            echo "</tr>";
        }
    }
    echo "</table>";
}
// Check opponent Database. If there, return 0. Else add them to database and return 1 to signal team added.
function checkOpponent($RPI, $Name) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_WVSU";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $sql = "SELECT * FROM `NCAA_RPI` WHERE `RPI`='$RPI'";
    $results = mysqli_query($conn, $sql);
    //echo "Checking for Duplicate Record for $name and $stat.<br>";
    $result = mysqli_fetch_assoc($results);
    $name = checkName($Name, TRUE);
    if (mysqli_num_rows($results) > 0) return (0); // Opponent there. Do nothing else.
    else {
        $query = "INSERT INTO `NCAA_RPI`(`RPI`, `Opponent`) VALUES ('$RPI','$name')";
        if (mysqli_query($conn, $query)) return (1); // Opponent not there. Signal that opponent has been added to database.
        else {
            echo ("Error when inserting opponent into database. Please manually add to SQL.");
            return (2);
        }
    }
}
function athleteAdministration($sport, $sortType) {
    echo "<br>";
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_wvsu";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    if ($sortType == "R") //Retired Athletes only
    $sql = "SELECT * FROM `" . $sport . "_athletes` WHERE `IsActive` != 1 AND `AthleteID`>0 ORDER BY `Name` ASC";
    else if ($sortType == "A") // Active Athletes only
    $sql = "SELECT * FROM `" . $sport . "_athletes` WHERE `IsActive` != 0 AND `AthleteID`>0 ORDER BY `Name` ASC";
    else die();
    $resultsString = mysqli_query($conn, $sql);
    echo "<table width='75%' style=\"border-collapse: collapse\"><tr>";
?>
		<th scope = "col" class = "dataheader" width = "5%">Athlete ID<br>(Not Visible)</th>
		<th scope = "col" class = "dataheader" width = "15%">Name</th>
		<th scope = "col" class = "dataheader" width = "15%">Seasons Active</th>
		<th scope = "col" class = "dataheader" width = "5%">Is Active?</th>
		<th scope = "col" class = "dataheader" width = "15%">Combine With<br>(AthleteID - Reassigns stats to the athlete entered in box)</th>
		<th scope = "col" class = "dataheader" width = "5%">Delete</th>
		<th scope = "col" class = "dataheader" width = "25%"></th>
</tr>
		<form action = "athlete_form.php" method="post">
		<input type="hidden" name = "sport" value = <?php echo $_GET['SPORT'] ?>>
<?php
    $p = 0; // Previous Result
    $r = 1; // Result Number
    $f = 0; //Final Row Number
    $showResult = 1;
    if ($resultsString != NULL && mysqli_num_rows($resultsString) > 0) {
        while ($row = mysqli_fetch_assoc($resultsString)) { // Important line, returns assoc array
            $c = 0;
            foreach ($row as $field => $value) {
                //echo "$field = $value<br>";
                $$field = $value;
            }
?>
			<td valign="middle"><input type = "text" value="<?php echo $AthleteID ?>" disabled>
          	<input type ="hidden" name = "<?php echo $r ?>[AthleteID]" value = "<?php echo $AthleteID ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[Name]" value = "<?php echo $Name ?>"></td>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[SeasonsActive]" value = "<?php echo $SeasonsActive ?>"></td>
			<td valign="middle"><input type = "checkbox" name = "<?php echo $r ?>[IsActive]" value = '1' <?php if ($IsActive == 1) echo "checked" ?>></input>
			<td valign="middle"><input type = "text" name = "<?php echo $r ?>[CombineWith]" value = ""></td>
			<td valign="middle"><input type = "checkbox" name = "<?php echo $r ?>[Erase]" value = '1' >
<?php
            $r++;
            $f = $r;
            echo "</tr>";
        } //end assoc array
        
    }
    $f++;
    echo "</table>";
    echo "<input type=\"submit\" name = \"submit\" value=\"Submit Changes\">";
    echo "</form>";
}
function updateAthletes($string, $sport) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_wvsu";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    $name = checkName($string['Name']);
	if ($string['Erase'])
		$sql = "DELETE FROM ".$sport."_athletes WHERE `".$sport."_athletes`.`AthleteID` = ".$string['AthleteID'];
	else
    	$sql = "UPDATE `" . $sport . "_athletes` SET `AthleteID`='" . $string['AthleteID'] . "',`Name`='$name',`SeasonsActive`='" . $string['SeasonsActive'] . "',`IsActive`='" . $string['IsActive'] . "' WHERE `AthleteID`=" . $string['AthleteID'];
    if (!mysqli_query($conn, $sql)) echo "Error when updating Athlete #" . $string['AthleteID'] . ".";
}
function combineAthletes($newNumber, $oldNumber, $sport) {
    $config = parse_ini_file('config.ini');
    $db_name = "kcmsathl_wvsu";
    $conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], $db_name, $config['db_port']);
    if ($sport == "fb") {
        $sql = "UPDATE `fb_boxscores_offense` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $resultsA = mysqli_query($conn, $sql);
        $sql = "UPDATE `fb_boxscores_defense` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $resultsB = mysqli_query($conn, $sql);
        $sql = "UPDATE `fb_boxscores_specteams` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $resultsC = mysqli_query($conn, $sql);
        $sql = "UPDATE `fb_boxscores_scoring` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $resultsD = mysqli_query($conn, $sql);
        if ($resultsA && $resultsB && $resultsC && $resultsD) {
            "$oldNumber successfully combined with $newNumber. Athlete #$oldNumber has been deleted.";
            $sql = "DELETE FROM `fb_athletes` WHERE `AthleteID`='$oldNumber'";
            mysqli_query($conn, $sql);
        }
    } // End Football
    else if ($sport == "wsoc") {
        $sql = "UPDATE `wsoc_boxscores` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $results = mysqli_query($conn, $sql);
        if ($results) {
            "$oldNumber successfully combined with $newNumber. Athlete #$oldNumber has been deleted.";
            $sql = "DELETE FROM `wsoc_athletes` WHERE `AthleteID`='$oldNumber'";
            mysqli_query($conn, $sql);
        }
    } else if ($sport == "vb") {
        $sql = "UPDATE `vb_boxscores` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $results = mysqli_query($conn, $sql);
        if ($results) {
            echo "$oldNumber successfully combined with $newNumber. Athlete #$oldNumber has been deleted.";
            $sql = "DELETE FROM `vb_athletes` WHERE `AthleteID`='$oldNumber'";
            mysqli_query($conn, $sql);
        }
    } else if ($sport == "mbb") {
        $sql = "UPDATE `mbb_boxscores` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $results = mysqli_query($conn, $sql);
        if ($results) {
            echo "$oldNumber successfully combined with $newNumber. Athlete #$oldNumber has been deleted.";
            $sql = "DELETE FROM `mbb_athletes` WHERE `AthleteID`='$oldNumber'";
            mysqli_query($conn, $sql);
        }
    } else if ($sport == "wbb") {
        $sql = "UPDATE `wbb_boxscores` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $results = mysqli_query($conn, $sql);
        if ($results) {
            echo "$oldNumber successfully combined with $newNumber. Athlete #$oldNumber has been deleted.";
            $sql = "DELETE FROM `wbb_athletes` WHERE `AthleteID`='$oldNumber'";
            mysqli_query($conn, $sql);
        }
    } else if ($sport == "sb") {
        $sqlA = "UPDATE `sb_boxscores_hitting` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $resultsA = mysqli_query($conn, $sqlA);
        $sqlB = "UPDATE `sb_boxscores_pitching` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $resultsB = mysqli_query($conn, $sqlB);
        $sqlC = "UPDATE `sb_boxscores_fielding` SET `AthleteID`='$newNumber' WHERE `AthleteID`='$oldNumber'";
        $resultsC = mysqli_query($conn, $sqlC);
        if ($resultsA && $resultsB && $resultsC) {
            echo "$oldNumber successfully combined with $newNumber. Athlete #$oldNumber has been deleted.";
            $sql = "DELETE FROM `sb_athletes` WHERE `AthleteID`='$oldNumber'";
            mysqli_query($conn, $sql);
        }
    }
}
function getGamesBySeasonForSport($sport) {
    global $conn;

      $tablePrefix = '';
    switch ($sport) {
        case 'mbb':
            $tablePrefix = 'mbb_';
            break;
        case 'fb':
            $tablePrefix = 'fb_';
            break;
        case 'sb':
            $tablePrefix = 'sb_';
            break;
        case 'vb':
            $tablePrefix = 'vb_';
            break;
        case 'wbb':
            $tablePrefix = 'wbb_';
            break;
        case 'wsoc':
            $tablePrefix = 'wsoc_';
            break;
        case 'bs':
            $tablePrefix = 'bs_';
            break;
        default:
            echo "Invalid sport specified.";
            return;
    }

    $tableName = $tablePrefix . 'games';

    $sql = "
        SELECT
            g.Season,
            g.GameID,
            g.Date,
            g.`W/L`,
            g.Score,
            g.Attendance,
            r.Opponent
        FROM
            $tableName g
        INNER JOIN
            NCAA_RPI r ON g.RPI = r.RPI
        ORDER BY
            g.Season DESC, g.Date DESC
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2 style='text-align: center;'>Games for $sport</h2>";
        $currentSeason = null;
        echo "<div style='margin: 0 auto; width: 800px;'>";
        echo "<style>
                table tr:nth-child(odd) {
                    background-color: #FFFFFF;
                }
                table tr:nth-child(even) {
                    background-color: #DDDDDD;
                }
              </style>";
        echo "<table border='1' style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <th style='padding: 2px; text-align: left;'>Season</th>
                    <th style='padding: 2px; text-align: left;'>Game ID</th>
                    <th style='padding: 2px; text-align: left;'>Date</th>
                    <th style='padding: 2px; text-align: left;'>Result</th>
                    <th style='padding: 2px; text-align: left;'>Score</th>
                    <th style='padding: 2px; text-align: left;'>Attendance</th>
                    <th style='padding: 2px; text-align: left;'>Opponent</th>
                </tr>";
        // Output data of each row
        $totalGames = 0;
        $totalWins = 0;
        $totalLosses = 0;
        $totalAttendance = 0;
        while($row = $result->fetch_assoc()) {
            if ($row["Season"] != $currentSeason) {
                if ($currentSeason !== null) {
                    echo "</table><br><br><h3 style='text-align: center;'>Season: " . $currentSeason . " Totals</h3>
                    <p style='text-align: center;'>Total Games: $totalGames, Wins: $totalWins, Losses: $totalLosses, Total Attendance: $totalAttendance</p><br><br>
                    <h3 style='text-align: center;'>Season: " . $row["Season"] . "</h3><table border='1' style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <th style='padding: 2px; text-align: left;'>Season</th>
                        <th style='padding: 2px; text-align: left;'>Game ID</th>
                        <th style='padding: 2px; text-align: left;'>Date</th>
                        <th style='padding: 2px; text-align: left;'>Result</th>
                        <th style='padding: 2px; text-align: left;'>Score</th>
                        <th style='padding: 2px; text-align: left;'>Attendance</th>
                        <th style='padding: 2px; text-align: left;'>Opponent</th>
                    </tr>";
                    $totalGames = 0;
                    $totalWins = 0;
                    $totalLosses = 0;
                    $totalAttendance = 0;
                }
            }
            echo "<tr>
                    <td style='padding: 2px;'>" . $row["Season"] . "</td>
                    <td style='padding: 2px;'>" . $row["GameID"] . "</td>
                    <td style='padding: 2px;'>" . $row["Date"] . "</td>
                    <td style='padding: 2px;'>" . $row["W/L"] . "</td>
                    <td style='padding: 2px;'>" . $row["Score"] . "</td>
                    <td style='padding: 2px;'>" . $row["Attendance"] . "</td>
                    <td style='padding: 2px;'>" . $row["Opponent"] . "</td>
                  </tr>";
            $currentSeason = $row["Season"];
            $totalGames++;
            if ($row["W/L"] == 'W') {
                $totalWins++;
            } else {
                $totalLosses++;
            }
            $totalAttendance += $row["Attendance"];
        }
        // Final check for the last season
        if ($currentSeason !== null) {
            echo "</table><br><br><h3 style='text-align: center;'>Season: " . $currentSeason . " Totals</h3>
            <p style='text-align: center;'>Total Games: $totalGames, Wins: $totalWins, Losses: $totalLosses, Total Attendance: $totalAttendance</p>";
        }
        echo "</div>";
    } else {
        echo "No games found for $sport.";
    }
    $conn->close();
}