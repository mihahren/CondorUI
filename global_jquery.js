$(document).ready(function (){
	//koda za hendlanje error funkcije
	$("#error_prompt").hide();
	
	$("#error_prompt").ajaxComplete(function() {
		if(document.getElementById("custom_error"))
		{
			$("#error_prompt").show();
			$("#error_prompt").html($("#custom_error").html());
			$("#custom_error").hide();
		}
		else
		{
			$("#error_prompt").hide();
		}
	});
	
	//ajax funkcije
	$(document).on("click", "#queue_button", function (){
		$.ajax({
			url: "content_control.php",
			type: "POST",
			data: {menu_1: "advanced", menu_2: "queue"},
			success: function(result){$("#content_panel").html(result);}
		});
	});
	
	$(document).on("click", "#status_button", function (){
		$.ajax({
			url: "content_control.php",
			type: "POST",
			data: {menu_1: "advanced", menu_2: "status"},
			success: function(result){$("#content_panel").html(result);}
		});
	});
	
	$(document).on("click", "#submit_button", function (){
		$.ajax({
			url: "content_control.php",
			type: "POST",
			data: {menu_1: "advanced", menu_2: "submit"},
			success: function(result){$("#content_panel").html(result);}
		});
	});
	
	$(document).on("click", "#confirm_submit", function (){
		$("#file_form").ajaxSubmit({
			url: "content_control.php",
			type: "POST",
			data: {menu_1: "advanced", menu_2: "submit"},
			success: function(result){$("#content_panel").html(result);}
		});
	});
	
	//upravljanje z login predelom
	$("#login_button").click(function(){
		$("#login_form").submit();
	});
	
	$("#logout_button").click(function(){
		$("#logout_form").submit();
	});
});

