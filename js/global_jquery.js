//globalne spremenljivke
var refreshIntervalId;

//funkcija za hendlanje error sporocil
function errorHandlerMobile(){
	
	if(document.getElementById("custom_error_mobile"))
	{	
		$("#error_prompt_mobile").empty()
			.html($("#custom_error_mobile").html())
			.show();
		$("#custom_error_mobile").remove();
	}
}


function errorHandlerDesktop(){
	
	if(document.getElementById("custom_error_desktop"))
	{	
		$("#error_prompt_desktop").empty()
			.html($("#custom_error_desktop").html())
			.show();
		$("#custom_error_desktop").remove();
	}
}

//ajax funkcije za navigiranje po datotekah
function submitAjax(phpPostFile, resultDivID){
	$.ajax({
		url: phpPostFile,
		type: "POST",
		success: function(result){$(resultDivID).html(result);}
	});
}

//ajax funkcije za navigiranje po condor manager tab meniju
function submitCmMenuAjax(phpPostFile, resultDivID, info){
	$.ajax({
		url: phpPostFile,
		type: "POST",
		data: {cm_menu: info},
		success: function(result){$(resultDivID).html(result);}
	});
}

//ajax funkcija za sprozitev ajax form submita
function submitFormAjax(formID, phpPostFile, resultDivID){
	$(formID).ajaxSubmit({
		url: phpPostFile,
		type: "POST",
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

//funkcija za sprozitev avtomatskega refresha
function refreshCondor(){
	if(document.getElementById("computers_selector"))
	{
		refreshIntervalId = setInterval(function(){
			submitAjax("ajax/status_ajax_computers.php", "#output_box_condor_computers");
			submitAjax("ajax/status_ajax_q.php", "#output_box_condor_q");
			submitAjax("ajax/status_ajax_status.php", "#output_box_condor_status");
			submitAjax("ajax/status_ajax_status_total.php", "#output_box_condor_status_total");
		},5000);
	}
	else if(document.getElementById("control_panel_ajax_condor_manager"))
	{
		refreshIntervalId = setInterval(function(){
			submitAjax("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel");
		},5000);
	}
}

//funkcije za pavzo refresha, ko je alert na zaslonu
function stopRefresh(){
	if ($('#error_prompt_mobile').is(":visible") || $('#error_prompt_desktop').is(":visible"))
	{
		clearInterval(refreshIntervalId);
	}
}

//funkcija za spremembo barve gumbov v control panel
function refreshButtons(){
	$("#zip_upload_button").css({"background-color":"", "color":""});
	$("#file_manager_button").css({"background-color":"", "color":""});
	$("#condor_manager_button").css({"background-color":"", "color":""});
	$("#ida_curves_button").css({"background-color":"", "color":""});
	
	if(document.getElementById("control_panel_ajax_zip"))
	{
		$("#zip_upload_button").css({"background-color":"#a10010", "color":"#ffffff"});
	}
	else if(document.getElementById("control_panel_ajax_file_manager"))
	{
		$("#file_manager_button").css({"background-color":"#a10010", "color":"#ffffff"});
	}
	else if(document.getElementById("control_panel_ajax_condor_manager"))
	{
		$("#condor_manager_button").css({"background-color":"#a10010", "color":"#ffffff"});
	}
	else if(document.getElementById("control_panel_ajax_ida"))
	{
		$("#ida_curves_button").css({"background-color":"#a10010", "color":"#ffffff"});
	}
}

//izvede se po celotno zgeneriranem html dokumentu
$(document).ready(function (){
	
	//globalni dogodki - izvedejo na zacetku
	clearInterval(refreshIntervalId);
	errorHandlerMobile()
	errorHandlerDesktop();
	refreshButtons();
	refreshCondor();
	stopRefresh();
	
	//izvede vsakic po zakljucenem ajax dogodku
	$(document).ajaxComplete(function() {
		clearInterval(refreshIntervalId);
		errorHandlerMobile();
		errorHandlerDesktop();
		refreshButtons();
		refreshCondor();
		stopRefresh();
	});
	
	//navigiranje control panel predela	
	$(document).on("click", "#condor_manager_button", function (){
		submitAjax("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel");
	});
	
	$(document).on("click", "#file_manager_button", function (){
		submitAjax("ajax/control_panel_ajax_file_manager.php", "#output_box_control_panel");
	});
	
	$(document).on("click", "#zip_upload_button", function (){
		submitAjax("ajax/control_panel_ajax_zip.php", "#output_box_control_panel");
	});
	
	$(document).on("click", "#ida_curves_button", function (){
		submitAjax("ajax/control_panel_ajax_ida.php", "#output_box_control_panel");
	});
	
	//navigiranje condor manager predela
	$(document).on("click", "#button_all_q", function (){
		submitCmMenuAjax("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", "all_q");
	});
	
	$(document).on("click", "#button_all_q_cluster", function (){
		submitCmMenuAjax("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", "all_q_cluster");
	});
	
	$(document).on("click", "#button_user_q", function (){
		submitCmMenuAjax("ajax/control_panel_ajax_condor_manager.php", "#output_box_control_panel", "user_q");
	});
	
	//control panel file form submit
	$(document).on("click", "#ctr_pnl_file_button", function (){
		$("#ctr_pnl_file_upload").click();
	});
		
	$(document).on("change", "#ctr_pnl_file_upload", function (){
		submitFileAjax("#ctr_pnl_file_form", "ajax/control_panel_ajax_file_manager.php", "#output_box_control_panel", "NULL");
	});
	
	//control panel new folder form submit	
	$(document).on("click", "#new_folder_button_desktop", function (){
		submitFormAjax("#new_folder_form_desktop", "ajax/control_panel_ajax_file_manager.php", "#output_box_control_panel");
	});
	
	$(document).on("click", "#new_folder_button_mobile", function (){
		submitFormAjax("#new_folder_form_mobile", "ajax/control_panel_ajax_file_manager.php", "#output_box_control_panel");
	});
	
	//control panel zip file form submit
	$(document).on("click", "#ctr_pnl_zip_file_button", function (){
		$("#ctr_pnl_zip_upload").click();
	});
		
	$(document).on("change", "#ctr_pnl_zip_upload", function (){
		submitFileAjax("#ctr_pnl_zip_form", "ajax/control_panel_ajax_zip.php", "#output_box_control_panel", "NULL");	
	});
	
	//control panel ida form submit		
	$(document).on("click", "#ida_submit_button", function (){
		submitFormAjax("#ida_form", "ajax/control_panel_ajax_ida.php", "#output_box_control_panel");
	});
	
	$(document).on("click", "#ida_plus_sign", function (){
		$("#ida_default_row").clone().appendTo("#ida_result_row");
	});
	
	$(document).on("click", "#ida_minus_sign", function (){
		$("#ida_result_row tr:last").remove()
	});
	
	//nadaljuje z refreshanjem po zaprtju 
	/*$(document).on("click", "#alert_button_mobile", function (){
		if (!$('#mobile_alert').is(":visible"))
		{
			refreshCondor();
		}
	});
	
	$(document).on("click", "#alert_button_desktop", function (){
		if (!$('#desktop_alert').is(":visible"))
		{
			refreshCondor();
		}
	});*/

	//upravljanje z login predelom
	$(document).on("click", "#logout_button", function (){
		$("#logout_form").submit();
	});
	
	//zapri popover
	$(document).on("click", "#alert_button_desktop", function (){
		$("#error_prompt_desktop").hide();
	});
	
	
	//alert button
	$(document).on("click", "#alert_button", function (){
		if($('#alert_button').hasClass("active")){
			$.ajax({
				url: "lib/error_tracking.php",
				type: "POST",
				data: {alert_popup: "toggled"},
				success: function(result){$("").html(result);}
			});
		}else{
			$.ajax({
				url: "lib/error_tracking.php",
				type: "POST",
				data: {alert_popup: "default"},
				success: function(result){$("").html(result);}
			});
		}
	});
	
	//globalne spremenljivke in dogodki - izvedejo na koncu
});

