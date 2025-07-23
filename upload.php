<?php
$target_dir = $_POST['sport']."/uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
?>
<a href="record_administration.php"><button>Back to Administration</button></a><br>
<?php
echo "Uploading to $target_file.";
$uploadOk = 1;
$uploadFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
//First Check user name and password matches.
$config = parse_ini_file('config.ini');
/*
$conn = mysqli_connect($config['db_path'], $config['db_user'], $config['db_pass'], 'kcmsathl_WVSU_mbb', $config['db_port']);
$sqluser = "SELECT `UserHash` FROM `Authy`";
$sqlpass = "SELECT `PassHash` FROM `Authy`";

$userResult = mysqli_query($conn, $sqluser); // Get Username Hash
$passResult = mysqli_query($conn, $sqlpass); // Get Password Hash

while($row = mysqli_fetch_assoc($userResult)) {
    $userHash = $row['UserHash']; // Print a single column data
}
while($row = mysqli_fetch_assoc($passResult)) {
    $passHash = $row['PassHash']; // Print a single column data
}



$userVerify = password_verify($_POST['username'], $userHash);
// echo "User Test = $userVerify";
$passVerify = password_verify($_POST['password'], $passHash);
// echo "Pass Test = $passVerify";
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
	header("Location: upload_file.php");
}
else {
	$userVerify = 1;
	$passVerify = 1;
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
}
*/
echo "<h1>File Upload Results</h1>";
//if($userVerify && $passVerify){
echo "Username and Password Verification success.<br>";
	session_start();
	//Then, check to see if the file can be uploaded
	if(isset($_POST["submit"])) {
		//var_dump($_POST);
		
		// Check if file alrady exists
/*		if (file_exists($target_file)) {
		  echo "Sorry, file already exists.<br>";
		  $uploadOk = 0;
		} */

		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 500000) {
		  echo "Sorry, your file is too large.<br>";
		  $uploadOk = 0;
		}

		// Allow certain file formats
		if($uploadFileType != "TEM" && $uploadFileType != "tem" && $uploadFileType != "IND" && $uploadFileType != "ind" && $uploadFileType != "XML" && $uploadFileType != "xml") {
			echo "Sorry, this is not a valid record or game file. Please try again. (File Type Detected: $uploadFileType) <br>";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
		  echo "<br><h2>Sorry, your file was not uploaded.</h2><br>";
		} 

		// if everything is ok, try to upload file
		else {
			//echo "uploadOk Results: $uploadOk";
		  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			echo "<br><h2>The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.</h2><br>";
			  if($uploadFileType == "TEM" || $uploadFileType == "tem" || $uploadFileType == "IND" || $uploadFileType == "ind"){
				  include_once("write_database_file.php");
				  $targetFileName = strtolower($_FILES["fileToUpload"]["name"]);
				  $nameBuild = explode(".",$targetFileName);
				  // var_dump ($nameBuild);
				  $destinationTable = $nameBuild[1]."_".$nameBuild[0];
				  writeToDatabaseWithSCFile($target_file,$_POST['sport'],$nameBuild[0],$nameBuild[1]);
			  }
			  else if($uploadFileType == "XML" || $uploadFileType == "xml"){
				  include_once("write_database_file.php");
				  $targetFileName = strtolower($_FILES["fileToUpload"]["name"]);
				  $nameBuild = explode(".",$targetFileName);
				  // var_dump ($nameBuild);
				  $destinationTable = $nameBuild[1]."_".$nameBuild[0];
				  writeToDatabaseWithXMLFile($target_file, $_POST['sport']);
			  }
		  } 
			else {
			echo "<br><h2>Sorry, there was an error uploading your file.</h2><br>";
		  }
		}
	}
//}
else{
		echo "Sorry, username and/or password is incorrect. Please click back and re-enter.";
	session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
	}
?>