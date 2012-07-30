//globalne spremenljivke
var refreshIntervalId;

//funkcija za hendlanje error sporocil
function errorHandler(delay, fade){
	
	//default vrednosti
	delay = typeof delay !== 'undefined' ? delay : 2000;
	fade = typeof fade !== 'undefined' ? fade : 1000;
	
	$("#error_prompt").hide();
	
	if(document.getElementById("custom_error"))
	{
		$("#error_prompt").show();
		$("#error_prompt").html($("#custom_error").html());
		$("#custom_error").hide();
		$("#error_prompt").delay(delay).fadeOut(fade);
	}
}

//ajax funkcije
function queueAjax(){
	$.ajax({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "queue"},
		success: function(result){$("#content_panel").html(result);}
	});
}

function statusAjax(){
	$.ajax({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "status"},
		success: function(result){$("#content_panel").html(result);}
	});
}

function submitAjax(){
	$.ajax({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "submit"},
		success: function(result){$("#content_panel").html(result);}
	});
}

function submitFormAjax(){
	$("#file_form").ajaxSubmit({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "submit"},
		success: function(result){$("#content_panel").html(result);}
	});
}

//funkcije za sprozitev avtomatskega refresha
function refreshQueue(){
	if(document.getElementById("queue_selector"))
	{
		refreshIntervalId = setInterval(function(){
			queueAjax();
		},2000);
	}
}

function refreshStatus(){
	if(document.getElementById("status_selector"))
	{
		refreshIntervalId = setInterval(function(){
			statusAjax();
		},2000);
	}
}

//izvede se po celotno zgeneriranem html dokumentu
$(document).ready(function (){
	
	//globalni dogodki - izvedejo na zacetku
	clearInterval(refreshIntervalId);
	errorHandler();
	refreshQueue();
	refreshStatus();
	
	//izvede vsakic po zakljucenem ajax dogodku
	$(document).ajaxComplete(function() {
		clearInterval(refreshIntervalId);
		errorHandler(3500);
		refreshQueue();
		refreshStatus();
	});
	
	//ajax event funkcije
	$(document).on("click", "#queue_button", function (){
		queueAjax();
	});
	
	$(document).on("click", "#status_button", function (){
		statusAjax();
	});
	
	$(document).on("click", "#submit_button", function (){
		submitAjax();
	});
	
	$(document).on("click", "#confirm_submit", function (){
		submitFormAjax();
	});
	
	//upravljanje z login predelom
	$(document).on("click", "#login_button", function (){
		$("#login_form").submit();
	});
	
	$(document).on("click", "#logout_button", function (){
		$("#logout_form").submit();
	});
	
	$(document).on("keyup", "#username_input, #password_input", function (event){
		if (event.keyCode == 13)
		{
			$("#login_button").click();
		}
	});
	
	//globalne spremenljivke in dogodki - izvedejo na kuncu
});

