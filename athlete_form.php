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

for ($i = 1; $i < $recordCount; $i ++){
	echo "<br>";
	if (!array_key_exists($i,$_POST))
		break;
	if($_POST[$i]['Name'] != "" )
        {
		if ($_POST[$i]['CombineWith'] != ""){
			combineAthletes($_POST[$i]['CombineWith'],$_POST[$i]['AthleteID'],$_POST['sport']);
		}
		else{
			if(!array_key_exists('IsActive', $_POST[$i]))
				$_POST[$i]['IsActive'] = 0;
			else 
				$_POST[$i]['IsActive'] = 1;
			if(!array_key_exists('Erase', $_POST[$i]))
				$_POST[$i]['Erase'] = 0;
			else 
				$_POST[$i]['Erase'] = 1;
			//var_dump ($_POST[$i]);
			updateAthletes($_POST[$i],$_POST['sport']);
		}
	}
}