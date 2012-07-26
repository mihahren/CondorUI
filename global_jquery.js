//funkcija za hendlanje error sporocil
function errorHandler(delay, fade){
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

//izvede se po celotno zgeneriranem html dokumentu
$(document).ready(function (){
	errorHandler();

	//globalni dogodki, ki lahko sprozijo error sporocilo
	$("#error_prompt").ajaxComplete(function() {
		errorHandler(2500);
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
});

