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
function queue_ajax(){
	$.ajax({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "queue"},
		success: function(result){$("#content_panel").html(result);}
	});
}

function status_ajax(){
	$.ajax({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "status"},
		success: function(result){$("#content_panel").html(result);}
	});
}

function submit_ajax(){
	$.ajax({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "submit"},
		success: function(result){$("#content_panel").html(result);}
	});
}

function submit_form_ajax(){
	$("#file_form").ajaxSubmit({
		url: "content_control.php",
		type: "POST",
		data: {menu_1: "advanced", menu_2: "submit"},
		success: function(result){$("#content_panel").html(result);}
	});
}

//izvede se po celotno zgeneriranem html dokumentu
$(document).ready(function (){
	
	//globalne spremenljivke in dogodki - izvedejo na zacetku
	var refreshIntervalId;
	
	errorHandler();
	
	$("#error_prompt").ajaxComplete(function() {
		errorHandler(3500);
	});
	
	//ajax event funkcije
	$(document).on("click", "#queue_button", function (){
		clearInterval(refreshIntervalId);
		queue_ajax();
		refreshIntervalId = setInterval(function(){
			queue_ajax();
		},2000);
	});
	
	$(document).on("click", "#status_button", function (){
		clearInterval(refreshIntervalId);
		status_ajax();
		refreshIntervalId = setInterval(function(){
			status_ajax();
		},2000);
	});
	
	$(document).on("click", "#submit_button", function (){
		clearInterval(refreshIntervalId);
		submit_ajax();
	});
	
	$(document).on("click", "#confirm_submit", function (){
		clearInterval(refreshIntervalId);
		submit_form_ajax();
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
	
	//globalne spremenljivke in dogodki - izvedejo na zacetku
	$("#status_button").click();
});

