// JavaScript Document

function madeRecordsSelection() {
	var e = document.getElementById("indexLevel");
	var elMsg = document.getElementById('feedback');
	var value = e.value;
	var sportPosition = value.indexOf("_");
	var sport = value.substring(0,sportPosition);
	var level = value.substring(sportPosition+1);
	var newLocation = "index.php?SPORT="+sport+"&LEVEL="+level;
	
	elMsg.textContent = 'Reload Script Triggered...';
	window.location.replace(newLocation);
}

function madeStatSelection(){
	var e = document.getElementById("indexStat");
	var elMsg = document.getElementById('feedback');
	var value = e.value;
	var sportPosition = value.indexOf("_");
	var statPosition = value.indexOf("-");
	var sport = value.substring(0,sportPosition);
	var level = value.substring(sportPosition+1,statPosition);
	var stat = value.substring(statPosition+1);
	var newLocation = "index.php?SPORT="+sport+"&LEVEL="+level+"&STAT="+stat;
	
	elMsg.textContent = 'Reload Script Triggered by Stat...';
	window.location.replace(newLocation);
}

var levelSelector = document.getElementById('indexLevel');
var statSelector = document.getElementById('indexStat');

levelSelector.addEventListener("change", madeRecordsSelection, false);
statSelector.addEventListener("change", madeStatSelection, false);