<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Untitled Document</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
		<script type="text/javascript">
		$(document).on("click", "#submit1", function (){
			$.ajax({
				url: "<?php echo $_SERVER['PHP_SELF'];?>",
				type: "POST",
				data: {test_txt: "nekaj"},
				success: function(result){$("body").append(result);}
			});
		});
		</script>
	</head>
	<body>
		<button id="submit1">submit</button><br />
		<div id="test_output1">
		</div>
	</body>
</html>