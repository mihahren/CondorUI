$(document).ready(function(){
	$("#queue_button").click(function(){
		$.ajax({
			url: "php/main_content.php",
			type: "GET",
			data: {podatek: "queue"},
			success: function(result){$("#output_box").html(result);}
		});
	});
	
	$("#status_button").click(function(){
		$.ajax({
			url: "php/main_content.php",
			type: "GET",
			data: {podatek: "status"},
			success: function(result){$("#output_box").html(result);}
		});
	});
	
	$("#submit_button").click(function(){
		$.ajax({
			url: "php/main_content.php",
			type: "GET",
			data: {podatek: "submit"},
			success: function(result){$("#output_box").html(result);}
		});
	});
	
	$("#remove_button").click(function(){
		$.ajax({
			url: "php/main_content.php",
			type: "GET",
			data: {podatek: "remove"},
			success: function(result){$("#output_box").html(result);}
		});
	});
	
	$("#file").change(function(){
		$("#upload_form").submit();
	});
	
	$("#login_button").click(function(){
		$("#login_form").submit();
	});
	
	$("#logout_button").click(function(){
		$("#logout_form").submit();
	});
});