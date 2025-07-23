<?php
include ("database.php");
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>WVSU Records Administration</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
	</head>
<body style="font-family: Arial, 'sans-serif'">
	<main role="main">
		<div class="container">
			<div class="row">
				<h1 align="center">Welcome to the WVSU Athletics Records Database Administration</h1>
			</div>
			<a href="record_administration.php"><button>Back to Administration</button></a>
			<div class="row" id="rowtwo">
				<a href="upload_file.php"><button>Upload File</button></a>
				<br>
				<br>
				<h2>Select a Sport</h2>
				<div class="col">
					<a href="record_administration.php?SPORT=fb"><button>Football</button></a>
				</div>
				<div class="col">
					<a href="record_administration.php?SPORT=vb"><button>Volleyball</button></a>
				</div>
				<div class="col">
					<a href="record_administration.php?SPORT=wsoc"><button>Women's Soccer</button></a>
				</div>
				<div class="col">
					<a href="record_administration.php?SPORT=mbb"><button>Men's Basketball</button></a>
				</div>
				<div class="col">
					<a href="record_administration.php?SPORT=wbb"><button>Women's Basketball</button></a>
				</div>
				<div class="col">
					<a href="record_administration.php?SPORT=bs"><button>Baseball</button></a>
				</div>
				<div class="col">
					<a href="record_administration.php?SPORT=sb"><button>Softball</button></a>
				</div>

				<hr>
			</div>
			<div class="row">
				<h4>Query Results</h4>
				<br>
				<div id="feedback"></div>
				<div class="Danger" id="warning"></div>
				<div class="Warning" id="alert"></div>
				<div class="Success" id="success"></div>
				<?php
				if (array_key_exists('SPORT',$_GET))
				   $requestedSport = $_GET['SPORT'];
			   else 
				   $requestedSport = NULL;
				//echo $requestedSport;
				if ($requestedSport != NULL){
					switch($requestedSport){
						case "mbb":
							include "mbb.php";
							echo "Men's Basketball<br>";
							editmbbRecords();
							break;
						case "wbb":
							include "wbb.php";
							echo "Women's Basketball<br>";
							editwbbRecords();
							break;
						case "fb":
							include "fb.php";
							echo "Football<br>";
							editfbRecords();
							break;
						case "wsoc":
							include "wsoc.php";
							echo "Women's Soccer<br>";
							editwsocrecords();
							break;
						case "vb":
							include "vb.php";
							echo "Women's Volleyball<br>";
							editvbrecords();
							break;
						case "sb":
							include "sb.php";
							echo "Softball<br>";
							editsbrecords();
							break;
						case "bs":
							include "bs.php";
							echo "Baseball<br>";
							editbsrecords();
							break;
					}
				}
				else
				{
					echo "Please select a sport for results.";
				}
				?>
			</div>
			</div>
		<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="makeselectionadmin.js"></script>
	</main>
</body>
</html>