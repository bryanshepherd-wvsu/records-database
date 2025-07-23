<?php 

include("database.php"); // Main database functions
include("mbb.php"); // Loading Men's basketball
include("wbb.php"); // Loading Women's basketball
include("fb.php"); //  Loading Football
include("wsoc.php"); //  Loading Women's Soccer
include("vb.php"); //  Loading Volleyball
include("sb.php"); //  Loading Softball
include("bs.php"); //  Loading Baseball

?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>WVSU Athletics Records</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body style="font-family: Arial, 'sans-serif'">
	<main role="main">
		<div class="container">
	<h1 align="center">Welcome to the WVSU Athletics Records Database</h1>
	
	<h2>Select a Sport</h2>
		<a href="index.php?SPORT=fb"><button>Football</button></a>
		<a href="index.php?SPORT=vb"><button>Volleyball</button></a>
		<a href="index.php?SPORT=wsoc"><button>Women's Soccer</button></a>
		<a href="index.php?SPORT=mbb"><button>Men's Basketball</button></a>
		<a href="index.php?SPORT=wbb"><button>Women's Basketball</button></a>
		<a href="index.php?SPORT=bs"><button>Baseball</button></a>
		<a href="index.php?SPORT=sb"><button>Softball</button></a>
	<hr>
	<h4>Query Results</h4>
	<div id="feedback"></div>
	<div class="Danger" id="warning"></div>
	<div class="Warning" id="alert"></div>
	<div class="Success" id="success"></div>
	<div class ="row">
	<?php
	
	if (array_key_exists('SPORT', $_GET))
	    $requestedSport = $_GET['SPORT'];
	else 
	    $requestedSport = NULL;
	//echo $requestedSport;
	if ($requestedSport != NULL){
		switch($requestedSport){
			case "fb":
				echo "Football<br>";
				fbRecords();
				break;
			case "mbb":
				echo "Men's Basketball<br>";
				mbbRecords();
				break;
			case "wbb":
			    echo "Women's Basketball<br>";
			    wbbRecords();
			    break;
			case "wsoc":
				echo "Women's Soccer<br>";
				wsocRecords();
				break;
			case "vb":
				echo "Women's Volleyball<br>";
				vbRecords();
				break;
			case "bs":
				echo "Baseball<br>";
				bsRecords();
				break;
			case "sb":
				echo "Softball<br>";
				sbRecords();
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
		</main>
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="makeselection.js"></script>
	
</body>
</html>