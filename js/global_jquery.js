//globalne spremenljivke
var refreshIntervalId;

//funkcija za hendlanje error sporocil
function errorHandler(){
	$("#error_prompt").hide();
	
	if(document.getElementById("custom_error"))
	{	
		$("#error_prompt").empty()
			.html($("#custom_error").html())
			.show();
		$("#custom_error").remove();
	}
}

//ajax funkcije za navigiranje po menijih
function submitAjax(phpPostFile, resultDivID, info){
	$.ajax({
		url: phpPostFile,
		type: "POST",
		data: {menu: info},
		success: function(result){$(resultDivID).html(result);}
	});
}

//ajax funkcija za sprozitev ajax form submita
function submitFormAjax(formID, phpPostFile, resultDivID, info){
	$(formID).ajaxSubmit({
		url: phpPostFile,
		type: "POST",
		data: {menu: info},
		success: function(result){$(resultDivID).html(result);}
	});
}

// ajax funkcija za uploadanje file-a s progress barom
function submitFileAjax(formID, phpPostFile, resultDivID, info){
	$(formID).ajaxSubmit({
		beforeSend: function() {
				$("#error_prompt").empty()
					.html("<div class='progress progress-striped active'><div id='progress_bar' class='bar'></div></div>")
					.show();
		},
		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			$("#progress_bar").width(percentVal);
		},
		url: phpPostFile,
		type: "POST",
		data: {menu: info},
		success: function(result){
			$(resultDivID).html(result);
			$("#error_prompt").empty().hide();
		}
	});
}

//funkcije za navigacijo znotraj tabele z datotekami
function goToPath(file_path){
	$.ajax({
		url: "ajax/advanced_ajax_content.php",
		type: "POST",
		data: {menu: "submit", directory: file_path},
		success: function(result){$("#output_box").html(result);}
	});
}

//funkcija za brisanje datotek znotraj tabele z datotekami
function submitFormAjaxDelete(del_info){
	$("#file_form").ajaxSubmit({
		url: "ajax/advanced_ajax_content.php",
		type: "POST",
		data: {menu: "submit", delete_file: del_info},
		success: function(result){$("#output_box").html(result);}
	});
}

//funkcija za submitanje datotek znotraj tabele z datotekami
function submitFormAjaxSubmit(submit_info){
	$("#file_form").ajaxSubmit({
		url: "ajax/advanced_ajax_content.php",
		type: "POST",
		data: {menu: "submit", submit_file: submit_info},
		success: function(result){$("#output_box").html(result);}
	});
}

//funkcija za brisanje submitanih datotek na index strani
function homeAjaxDelete(menu_info, output_id, del_info){
	$.ajax({
		url: "ajax/home_ajax_content.php",
		type: "POST",
		data: {menu: menu_info, delete_submited_file: del_info},
		success: function(result){$(output_id).html(result);}
	});
}

//funkcija za brisanje submitanih datotek na advanced strani
function submitAjaxDelete(del_info){
	$.ajax({
		url: "ajax/advanced_ajax_content.php",
		type: "POST",
		data: {menu: "queue", delete_submited_file: del_info},
		success: function(result){$("#output_box").html(result);}
	});
}

//funkcija za sprozitev avtomatskega refresha
function refreshCondor(){
	if(document.getElementById("status_selector"))
	{
		refreshIntervalId = setInterval(function(){
			submitAjax("ajax/advanced_ajax_content.php", "#output_box", "status");
		},2000);
	}
	else if(document.getElementById("queue_selector"))
	{
		refreshIntervalId = setInterval(function(){
			submitAjax("ajax/advanced_ajax_content.php", "#output_box", "queue");
		},2000);
	}
}

//funkcije za pavzo refresha, ko je alert na zaslonu
function stopRefresh(){
	if ($('#error_prompt').is(":visible"))
	{
		clearInterval(refreshIntervalId);
	}
}

//funkcija za spremembo barve gumbov v advanced nacinu
function refreshButtonBorders(){
	$("#queue_button").css({"background-color":"", "color":""});
	$("#status_button").css({"background-color":"", "color":""});
	$("#submit_button").css({"background-color":"", "color":""});
	
	if(document.getElementById("queue_selector"))
	{
		$("#queue_button").css({"background-color":"#0088cc", "color":"#ffffff"});
	}
	else if(document.getElementById("status_selector"))
	{
		$("#status_button").css({"background-color":"#0088cc", "color":"#ffffff"});
	}
	else if(document.getElementById("submit_selector"))
	{
		$("#submit_button").css({"background-color":"#0088cc", "color":"#ffffff"});
	}
}

//izvede se po celotno zgeneriranem html dokumentu
$(document).ready(function (){
	
	//globalni dogodki - izvedejo na zacetku
	clearInterval(refreshIntervalId);
	errorHandler();
	refreshCondor();
	refreshButtonBorders();
	stopRefresh();
	
	//izvede vsakic po zakljucenem ajax dogodku
	$(document).ajaxComplete(function() {
		clearInterval(refreshIntervalId);
		errorHandler();
		refreshCondor();
		refreshButtonBorders();
		stopRefresh();
	});
	
	//home menu navigation
	$(document).on("click", "#button_last_submits", function (){
		submitAjax("ajax/home_ajax_content.php", "#tab_last_submits", "last_submits");
	});
	
	$(document).on("click", "#button_computer_status", function (){
		submitAjax("ajax/home_ajax_content.php", "#tab_computer_status", "computer_status");
	});
	
	//advanced menu navigation
	$(document).on("click", "#queue_button", function (){
		submitAjax("ajax/advanced_ajax_content.php", "#output_box", "queue");
	});
	
	$(document).on("click", "#status_button", function (){
		submitAjax("ajax/advanced_ajax_content.php", "#output_box", "status");
	});
	
	$(document).on("click", "#submit_button", function (){
		submitAjax("ajax/advanced_ajax_content.php", "#output_box", "submit");
	});
	
	//home form submit
	$(document).on("click", "#home_file_button", function (){
		$("#home_file_upload").click();
	});
	
	$(document).on("change", "#home_file_upload", function (){
		submitFileAjax("#home_form", "ajax/home_ajax_content.php", "NULL", "NULL");
	});
	
	//basic form submit
	$(document).on("click", "#basic_file_button", function (){
		$("#basic_file_upload").click();
	});
	
	$(document).on("change", "#basic_file_upload", function (){
		submitFileAjax("#basic_file_form", "ajax/basic_ajax_content.php", "#output_box", "NULL");
	});
	
	//advanced form submit
	$(document).on("click", "#advanced_file_button", function (){
		$("#advanced_file_upload").click();
	});
		
	$(document).on("change", "#advanced_file_upload", function (){
		submitFileAjax("#file_form", "ajax/advanced_ajax_content.php", "#output_box", "submit");
	});
	
	//nadaljuje z refreshanjem po zaprtju 
	$(document).on("click", "#main_alert_button", function (){
		if (!$('#main_alert').is(":visible"))
		{
			refreshCondor();
		}
	});

	//upravljanje z login predelom	
	$(document).on("click", "#logout_button", function (){
		$("#logout_form").submit();
	});
	
	//globalne spremenljivke in dogodki - izvedejo na koncu
});

