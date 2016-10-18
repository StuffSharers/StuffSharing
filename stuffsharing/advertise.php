<?php 
	require("include/auth.php");
	require("include/functions.php");
?>

<!DOCTYPE html>
<html lang="en">
	
	<head>
		<title>StuffSharing | a place to share stuff</title>
		
		<style>
			.textarea-limit-width{
				width:100%;
				resize:vertical;
			}
			
			.buffer-s{
				margin-top:25px;
				margin-bottom:25px;
			}	
			
			.buffer-m{
				margin-top:50px; 
				margin-bottom:50px;
			}	
			
			.buffer-l{
				margin-top:75px;
				margin-bottom:75px;
			}				
		</style>
	</head>
	
	<?php
		include("partials/head.html");
		include("partials/navigation.php");
	?>	
	
	<body>
		
		<div class="container">
		
			<div class="row text-center">
				<h1>Create Advertisements Here!</h1>
			</div>
			
			<hr>
			
			<div class="row form-group">
			  <label for="item-name-input-form" class="col-xs-2 col-form-label">Item Name</label>
			  <div class="col-xs-10">
				<input class="form-control" type="text" value="Item Name Here" id="item-name-input-form">
			  </div>
			</div>
			
			<div class="row form-group">
			  <label for="item-description-input-text-area" class="col-xs-2 col-form-label">Item Description</label>
			  <div class="col-xs-10">
				<textarea class="textarea-limit-width form-control" type="text" rows="3" id="item-description-input-text-area"> </textarea>
			  </div>
			</div>
			
			<?php
				include("partials/footer.html");
			?>
			
		</div>
		
	</body>
	
</html>
	