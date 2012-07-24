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
	$("#queue_button").click(function(){
		$.ajax({
			url: "php/main_content.php",
			type: "POST",
			data: {podatek: "queue"},
			success: function(result){$("#output_box").html(result);}
		});
	});
	
	$("#status_button").click(function(){
		$.ajax({
			url: "php/main_content.php",
			type: "POST",
			data: {podatek: "status"},
			success: function(result){$("#output_box").html(result);}
		});
	});
	
	$("#submit_button").click(function(){
		$.ajax({
			url: "php/main_content.php",
			type: "POST",
			data: {podatek: "submit"},
			success: function(result){$("#output_box").html(result);}
		});
	});
	
	$(document).on("click", "#confirm_submit", function (){
		$("#file_form").ajaxSubmit({
			type: "POST",
			data: {podatek: "submit"},
			success: function(result){$("#output_box").html(result);}
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

