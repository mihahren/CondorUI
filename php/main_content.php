<div id="input_box">
	<!--
	<p>Check condor queue:
	<button id="queue_button" type="button">queue</button></p>
	<p>Check condor status:
	<button id="status_button" type="button" onclick="status()">status</button></p>
	Submit to condor queue: <button id="submit_button">submit job</button>
	<form id="upload_form" action="main_content.php" method="post" enctype="multipart/form-data">
		<input id="file" type="file" name="file" multiple />
	</form>
	<p>Remove from condor queue:
	<button id="remove_button" type="button" onclick="remove()">remove</button></p>
	-->
	<div class="button_wrapper" id="queue_button">
		<img src="..\images\menu_button.png" />
		<span class="button_text">Condor Queue</span>
	</div>
	<div class="button_wrapper" id="status_button">
		<img src="..\images\menu_button.png" />
		<span class="button_text">Condor Status</span>
	</div>
	<div class="button_wrapper" id="submit_button">
		<img src="..\images\menu_button.png" />
		<span class="button_text">Submit...</span>
	</div>
</div>
<div id="output_box">
	<?php include 'upload_file.php'?>
</div>