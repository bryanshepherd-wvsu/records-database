<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>WVSU Athletics Records</title>
	</head>
	
	<body style="font-family: Arial, 'sans-serif'">
		<h1 align="center">Welcome to the WVSU Athletics Records Database</h1>
		<h2 align="center">File Management System</h2>
		<a href="record_administration.php"><button>Back to Administration</button></a>
<?php var_dump ($_SESSION);
		if ($_SESSION == NULL || (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800))) {
		echo "<form action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\">";
		echo "User Name: <input type = \"text\" name = \"username\"><br>";
		echo "Password: <input type = \"password\" name = \"password\"><br>";
} 
	else { 
		echo "<form> Session still active. Last Activity: ".$_SESSION['LAST_ACTIVITY'];
			}?>
		
			<br>
		  Select file to upload:
		  <input type="file" name="fileToUpload" id="fileToUpload">
			<label for="sport">Select a sport to upload file to:</label>
			<select name="sport" id = "sport">
				<option value="fb">Football</option>
				<option value="vb">Volleyball</option>
				<option value="wsoc">Women's Soccer</option>
				<option value="mbb">Men's Basketball</option>
				<option value="wbb">Women's Basketball</option>
				<option value="bs">Baseball</option>
				<option value="sb">Softball</option>
			</select>
		  <input type="submit" value="Upload File" name="submit">
		</form>
		
	</body>
</html>