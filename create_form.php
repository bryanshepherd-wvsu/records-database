<?php 
/********************************************
*	create_form.php							*
*	Form Processor for editing records. 	*
*	- Calls file_processor functions to 	*
*	update SQL database.					*
*											*
*											*
*											*
*											*
********************************************/

include "database.php";
?>
<a href="record_administration.php"><button>Back to Administration</button></a>
<?php

$recordCount = sizeof($_POST);
//var_dump($_POST);
$date = NULL;
$opponent = NULL;
$remarks = NULL;
$seasonsActive = NULL;
$season = NULL;

for ($i = 1; $i < $recordCount; $i ++){
	echo "<br>";
	if (!array_key_exists($i,$_POST))
		continue;
	if ($_POST['statType'] == 'TMS' || $_POST['statType'] == 'SEA'){
		$remarks = $_POST[$i]['Remarks'];
		$season = $_POST[$i]['Season'];
	}
	else if ($_POST['statType'] == 'TMG' || $_POST['statType'] == 'GAM'){
		$date = $_POST[$i]['Date'];
		$opponent = $_POST[$i]['Opponent'];
		$remarks = $_POST[$i]['Remarks'];
	}
	else if ($_POST['statType'] == 'CAR'){
		$seasonsActive = $_POST[$i]['SeasonsActive'];
	}
	if($_POST[$i]['Value'] != "" && $_POST[$i]['Name'] != "" )
        {
		if(!array_key_exists('Erase', $_POST[$i]))
			$_POST[$i]['Erase'] = 0;
		else 
			$_POST[$i]['Erase'] = 1;
		if(!array_key_exists('NewRecord', $_POST[$i])){
                $_POST[$i]['NewRecord'] = 0;
				updateRecord($_POST[$i]['RecordID'],$_POST[$i]['Value'],$_POST[$i]['Name'],$date,$opponent,$remarks,$_POST['sport'],$_POST['stat'],"oldRecords",$_POST['statType'],$seasonsActive,$season);
		}
		else {
			$_POST[$i]['NewRecord'] = 1;
			$name = checkName($_POST[$i]['Name']);
			if ($_POST['oldRecords'])
				createNewRecord ($_POST[$i]['Value'],$name,$date,$opponent,$remarks,$_POST['sport'],$_POST['stat'],"oldRecords",$_POST['statType'],$seasonsActive,$season);
		}
		//var_dump ($_POST[$i]);
	}
}
if ($_POST['PastedString'] != NULL){
	$pastedString = $_POST['PastedString'];
	$formattedPaste = explode("\n",$pastedString);
	var_dump ($formattedPaste);
	$numOfRecords = sizeof ($formattedPaste);
	for ($p = 0; $p < $numOfRecords; $p++):
		$explodedPaste = explode(";",$formattedPaste[$p]);
		echo "<br><br>";
		var_dump($explodedPaste);
		$name = checkName(trim($explodedPaste[1]));
		if ($_POST['statType'] == 'TMS' || $_POST['statType'] == 'SEA'){
			$remarks = $_POST[$i]['Remarks'];
			$season = $_POST[$i]['Season'];
		}
		else if ($_POST['statType'] == 'TMG' || $_POST['statType'] == 'GAM'){
			$date = date("y/m/d",strtotime(trim($explodedPaste[3])));
			$opponent = checkName(trim($explodedPaste[2]),TRUE);
			if ($explodedPaste[4] != NULL)
				$remarks = $explodedPaste[4];
			else
				$remarks = NULL;
		}
		else if ($_POST['statType'] == 'CAR'){
			$seasonsActive = $_POST[$i]['SeasonsActive'];
		}
		createNewRecord (trim($explodedPaste[0]),$name,$date,$opponent,$remarks,$_POST['sport'],$_POST['stat'],"oldRecords",$_POST['statType'],$seasonsActive,$season);
	endfor;
}