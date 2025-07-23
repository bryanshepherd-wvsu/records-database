// JavaScript Document
function madeRecordsAdminSelection() {
	var e = document.getElementById("adminLevel");
	var elMsg = document.getElementById('feedback');
	var value = e.value;
	var sportPosition = value.indexOf("_");
	var sport = value.substring(0,sportPosition);
	var level = value.substring(sportPosition+1);
	var newLocation = "record_administration.php?SPORT="+sport+"&LEVEL="+level;
	
	elMsg.textContent = 'Reload Script Triggered...';
	window.location.replace(newLocation);
}

function madeStatAdminSelection(){
	var e = document.getElementById("adminStat");
	var elMsg = document.getElementById('feedback');
	var value = e.value;
	var sportPosition = value.indexOf("_");
	var statPosition = value.indexOf("-");
	var sport = value.substring(0,sportPosition);
	var level = value.substring(sportPosition+1,statPosition);
	var stat = value.substring(statPosition+1);
	var newLocation = "record_administration.php?SPORT="+sport+"&LEVEL="+level+"&STAT="+stat;
	
	elMsg.textContent = 'Reload Script Triggered by Stat...';
	window.location.replace(newLocation);
}

var adminLevelSelector = document.getElementById('adminLevel');
var adminStatSelector = document.getElementById('adminStat');

adminLevelSelector.addEventListener("change", madeRecordsAdminSelection, false);
adminStatSelector.addEventListener("change", madeStatAdminSelection, false);