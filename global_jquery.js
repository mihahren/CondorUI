//globalne spremenljivke
var refreshIntervalId;
var show_bar = 0; 

//funkcija za hendlanje error sporocil
function errorHandler(delay, fade){
	
	//default vrednosti
	delay = typeof delay !== 'undefined' ? delay : 2000;
	fade = typeof fade !== 'undefined' ? fade : 1000;
	
	$("#error_prompt").hide();
	
	if(document.getElementById("custom_error"))
	{
		$("#error_prompt").empty()
			.css("background-color","red")
			.html($("#custom_error").html())
			.show();
		$("#custom_error").remove();
		$("#error_prompt").delay(delay).fadeOut(fade);
	}
}

//ajax funkcije
function submitAjax(phpPostFile, resultDivID, info){
	$.ajax({
		url: phpPostFile,
		type: "POST",
		data: {menu: info},
		success: function(result){$(resultDivID).html(result);}
	});
}

function submitFileAjax(formID, phpPostFile, resultDivID, info){
	$(formID).ajaxSubmit({
		beforeSend: function() {
			
				$("#error_prompt").empty()
					.html("<div id='progress_bar'></div><span id='progress_number'></span>")
					.css("background-color","white")
					.show();
				var percentVal = '0%';
				$("#progress_bar").width(percentVal);
				$("#progress_number").html(percentVal);
		
		},
		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			$("#progress_bar").width(percentVal);
			$("#progress_number").html(percentVal);
		},
		url: phpPostFile,
		type: "POST",
		data: {menu: info},
		success: function(result){
			$(resultDivID).html(result);
			$("#error_prompt").hide();
		}
	});
}

function submitFormAjax(formID, phpPostFile, resultDivID, info){
	$(formID).ajaxSubmit({
		url: phpPostFile,
		type: "POST",
		data: {menu: info},
		success: function(result){$(resultDivID).html(result);}
	});
}

//funkcije za sprozitev avtomatskega refresha
function refreshQueue(){
	if(document.getElementById("queue_selector"))
	{
		refreshIntervalId = setInterval(function(){
			submitAjax("advanced_ajax_content.php", "#output_box", "queue");
		},2000);
	}
}

function refreshStatus(){
	if(document.getElementById("status_selector"))
	{
		refreshIntervalId = setInterval(function(){
			submitAjax("advanced_ajax_content.php", "#output_box", "status");
		},2000);
	}
}

//funkcija za spremembo barve gumbov v advanced nacinu
function refreshButtonBorders(){
	$("#queue_button").css("border","");
	$("#status_button").css("border","");
	$("#submit_button").css("border","");
	
	if(document.getElementById("queue_selector"))
	{
		$("#queue_button").css("border","1px solid red");
	}
	else if(document.getElementById("status_selector"))
	{
		$("#status_button").css("border","1px solid red");
	}
	else if(document.getElementById("submit_selector"))
	{
		$("#submit_button").css("border","1px solid red");
	}
}

//izvede se po celotno zgeneriranem html dokumentu
$(document).ready(function (){
	
	//globalni dogodki - izvedejo na zacetku
	clearInterval(refreshIntervalId);
	errorHandler(4000);
	refreshQueue();
	refreshStatus();
	
	//izvede vsakic po zakljucenem ajax dogodku
	$(document).ajaxComplete(function() {
		clearInterval(refreshIntervalId);
		errorHandler(4000);
		refreshQueue();
		refreshStatus();
		refreshButtonBorders()
	});
	
	//basic form submit
	$(document).on("click", "#basic_file_button", function (){
		$("#basic_file_upload").click();
	});
	
	$(document).on("change", "#basic_file_upload", function (){
		submitFileAjax("#basic_file_form", "basic_ajax_content.php", "#basic_output_wrapper", "NULL");
	});
	
	//advanced menu navigation
	$(document).on("click", "#queue_button", function (){
		submitAjax("advanced_ajax_content.php", "#output_box", "queue");
	});
	
	$(document).on("click", "#status_button", function (){
		submitAjax("advanced_ajax_content.php", "#output_box", "status");
	});
	
	$(document).on("click", "#submit_button", function (){
		submitAjax("advanced_ajax_content.php", "#output_box", "submit");
	});
	
	//advanced form submit
	$(document).on("click", "#advanced_file_button", function (){
		$("#advanced_file_upload").click();
	});
		
	$(document).on("change", "#advanced_file_upload", function (){
		submitFileAjax("#file_form", "advanced_ajax_content.php", "#output_box", "submit");
	});
	
	$(document).on("click", "#advanced_submit_button", function (){
		submitFormAjax("#file_form", "advanced_ajax_content.php", "#output_box", "submit");
	});
	
	//advanced checkbox checking
	$(document).on("click", ".select_all_uploads", function (){
		$(".upload_delete_checkbox").attr('checked', $('.select_all_uploads').is(":checked"));
	});
	
	$(document).on("click", ".select_all_results", function (){
		$(".result_delete_checkbox").attr('checked', $('.select_all_results').is(":checked"));
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
	
	//globalne spremenljivke in dogodki - izvedejo na koncu
});

